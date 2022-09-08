<?php

namespace Asymmetric\SdkClient\Handlers;


use Asymmetric\SdkClient\models\Environment;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\HandlerStack;

/**
 * Class MitAuthentication
 * @package Asymmetric\SdkClient\Handlers
 */
class MitAuthentication
{

    private $tokenStorageHelper;
    private $clientId;
    private $clientSecret;
    private $scopes;
    private $rsaHelper;
    private $options;

    /**
     * MitAuthentication constructor.
     * @param $clientId
     * @param $clientSecret
     * @param $tokenStorageHelper
     * @param $scopes
     * @param $rsaHelper
     * @param $options
     */
    public function __construct($clientId, $clientSecret, $tokenStorageHelper, $scopes, $rsaHelper, $options)
    {
        $this->clientId = $clientId;
        $this->tokenStorageHelper = $tokenStorageHelper;
        $this->clientSecret = $clientSecret;
        $this->scopes = $scopes;
        $this->rsaHelper = $rsaHelper;
        $this->options = $options;

        if ($this->tokenStorageHelper->getAesKey() == null || $this->tokenStorageHelper->getHmacKey() == null) {
            try {
                $this->getAccessKey();
            } catch (\Exception $e) {
                exit(0);
            }
        }
    }


    /**
     * @return string
     */
    private function getAccessKey()
    {

        try {

            $stack = HandlerStack::create(new CurlHandler());
            $optionsOAuth = array(
                'oauthConnectionTimeout' => $this->options["oauthConnectionTimeout"],
                'oauthSocketTimeout' => $this->options["oauthSocketTimeout"],
            );
            $stack->push(new CognitoAuthentication($this->clientId, $this->clientSecret, $this->scopes, $this->tokenStorageHelper, $optionsOAuth));

            $client = new Client(['handler' => $stack]);


            $pKey = base64_encode($this->rsaHelper->getPublicKey(true));
            $data = array("rsa" => $pKey);

            $request = $client->post(Environment::getUrlKeys(), [
                'json' => $data,
                'timeout' => ($this->options["apiKeyConnectionTimeout"] / 1000),
                'connect_timeout' => ($this->options["apiKeySocketTimeout"] / 1000),
            ]);

            if ($request->getStatusCode() !== 200 && $request->getStatusCode() !== 201) {
                throw new \Exception('An error occurred while processing the information');
            }

            $body = json_decode($request->getBody());
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Error('Invalid json object on response');
            }
            $aes = $this->rsaHelper->deCypher($body->aes);
            $hmac = $this->rsaHelper->deCypher($body->hmac);

            $this->tokenStorageHelper->setAesKey($aes);
            $this->tokenStorageHelper->setHmacKey($hmac);
            return $this->tokenStorageHelper;
        } catch (\Exception $e) {
            print_r($e->getMessage());
            return $e->getMessage();
        } catch (GuzzleException $e) {
            return $e->getMessage();
        }
    }
}
