<?php

namespace Asymmetric\SdkClient;

use Asymmetric\SdkClient\handlers\DeCypherResponse;
use Asymmetric\SdkClient\helpers\AesHelper;
use Asymmetric\SdkClient\helpers\EncryptBodyHelper;
use Asymmetric\SdkClient\Handlers\MitAuthentication;
use Asymmetric\SdkClient\Helpers\RsaHelper;
use Asymmetric\SdkClient\Helpers\TokenStorageHelper;
use Asymmetric\SdkClient\models\Environment;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\HandlerStack;

class SdkClient
{

    private $clientId;
    private $clientSecret;
    private $environment;
    private $scopes;
    private $storageHelper;
    private $rsaHelper;
    private $oAuthUrl;
    private $oauthConnectionTimeout = 20000;
    private $oauthSocketTimeout = 30000;
    private $apiKeyConnectionTimeout = 20000;
    private $apiKeySocketTimeout = 30000;
    private $serviceConnectionTimeout = 20000;
    private $serviceSocketTimeout = 30000;

    const VERSION_192CEAD6E = "V192CEAD6E";
    const VERSION_GCM = "VERSION_GCM";

    /**
     * SdkClient constructor.
     * @param $environment
     * @param $clientId
     * @param $clientSecret
     * @param $scopes
     * @param null $oAuthUrl
     */
    public function __construct($environment, $clientId, $clientSecret, $scopes)
    {
        $this->environment = new Environment($environment);
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->scopes = $scopes;
        $this->storageHelper = new TokenStorageHelper();
        $this->storageHelper->setOAuthUrl($this->environment::getUrlToken());
        $this->rsaHelper = new RsaHelper();
        $this->initialize();
    }

    /**
     *
     */
    public function initialize()
    {
        try {
            $options = [
                'oauthConnectionTimeout' => $this->getOauthConnectionTimeout(),
                'oauthSocketTimeout' => $this->getOauthSocketTimeout(),
                'apiKeyConnectionTimeout' => $this->getApiKeyConnectionTimeout(),
                'apiKeySocketTimeout' => $this->getApiKeySocketTimeout()
            ];
            new MitAuthentication($this->clientId, $this->clientSecret, $this->storageHelper, $this->scopes, $this->rsaHelper, $options);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * @param $url
     * @param $data
     * @param string $cipherMethod
     * @return string
     * @throws GuzzleException
     */
    public function postRequest($url, $data, $cipherVersion = self::VERSION_GCM)
    {
        try {

            if ($this->oAuthUrl !== null && trim($this->oAuthUrl) !== "") {
                echo "We are here";
                $this->storageHelper->setAccessToken(null);
                $this->storageHelper->setOAuthUrl($this->oAuthUrl);
                $this->initialize();
            }

            $encryptBodyHelper = new EncryptBodyHelper($this->storageHelper);
            $dataEncrypted = $encryptBodyHelper->protectData($data, $cipherVersion);
            $body = $dataEncrypted['body'];

            $headers = [
                'MIT-HS' => $dataEncrypted['mit-hs'],
                'Authorization' => 'Bearer ' . $this->storageHelper->getToken(),
                'Content-Type' => 'text/plain; charset=ISO-8859-1',
                'accept-encoding' => 'gzip, x-gzip, deflate',
            ];

            if ($cipherVersion === self::VERSION_192CEAD6E) {
                $headers["VERSION"] = base64_encode("V192CEAD6E");
            }

            $stack = HandlerStack::create(new CurlHandler());
            $stack->push(new DeCypherResponse($this->storageHelper, $dataEncrypted, $cipherVersion));
            $client = new Client(['handler' => $stack]);

            return $client->request('POST', $url, [
                'headers' => $headers,
                'body' => $body,
                'timeout' => (int)($this->getServiceSocketTimeout() / 1000),
                'connect_timeout' => (int)($this->getServiceConnectionTimeout() / 1000),
            ]);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * @return mixed
     */
    public function getOAuthUrl()
    {
        return $this->oAuthUrl;
    }

    /**
     * @param $oAuthUrl
     * @return mixed
     */
    public function setOAuthUrl($oAuthUrl)
    {
        return $this->oAuthUrl = $oAuthUrl;
    }

    /**
     * @return mixed
     */
    public function getOauthConnectionTimeout()
    {
        return $this->oauthConnectionTimeout;
    }

    /**
     * @param mixed $oauthConnectionTimeout
     */
    public function setOauthConnectionTimeout($oauthConnectionTimeout)
    {
        $this->oauthConnectionTimeout = $oauthConnectionTimeout;
    }

    /**
     * @return mixed
     */
    public function getOauthSocketTimeout()
    {
        return $this->oauthSocketTimeout;
    }

    /**
     * @param mixed $oauthSocketTimeout
     */
    public function setOauthSocketTimeout($oauthSocketTimeout)
    {
        $this->oauthSocketTimeout = $oauthSocketTimeout;
    }

    /**
     * @return mixed
     */
    public function getApiKeyConnectionTimeout()
    {
        return $this->apiKeyConnectionTimeout;
    }

    /**
     * @param mixed $apiKeyConnectionTimeout
     */
    public function setApiKeyConnectionTimeout($apiKeyConnectionTimeout)
    {
        $this->apiKeyConnectionTimeout = $apiKeyConnectionTimeout;
    }

    /**
     * @return mixed
     */
    public function getApiKeySocketTimeout()
    {
        return $this->apiKeySocketTimeout;
    }

    /**
     * @param mixed $apiKeySocketTimeout
     */
    public function setApiKeySocketTimeout($apiKeySocketTimeout)
    {
        $this->apiKeySocketTimeout = $apiKeySocketTimeout;
    }

    /**
     * @return mixed
     */
    public function getServiceConnectionTimeout()
    {
        return $this->serviceConnectionTimeout;
    }

    /**
     * @param mixed $serviceConnectionTimeout
     */
    public function setServiceConnectionTimeout($serviceConnectionTimeout)
    {
        $this->serviceConnectionTimeout = $serviceConnectionTimeout;
    }

    /**
     * @return mixed
     */
    public function getServiceSocketTimeout()
    {
        return $this->serviceSocketTimeout;
    }

    /**
     * @param mixed $serviceSocketTimeout
     */
    public function setServiceSocketTimeout($serviceSocketTimeout)
    {
        $this->serviceSocketTimeout = $serviceSocketTimeout;
    }
}
