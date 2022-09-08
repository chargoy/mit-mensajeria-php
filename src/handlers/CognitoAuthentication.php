<?php

namespace Asymmetric\SdkClient\Handlers;

use Asymmetric\SdkClient\models\Environment;
use CakeDC\OAuth2\Client\Provider\Cognito;
use Closure;
use League\OAuth2\Client\Grant\ClientCredentials;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Psr\Http\Message\RequestInterface;
use GuzzleHttp\Client as HttpClient;

/**
 * Class CognitoAuthentication
 * @package Asymmetric\SdkClient\Handlers
 */
class CognitoAuthentication
{

    private $clientId;
    private $clientSecret;
    private $tokenStorageHelper;
    private $scopes;
    private $oauthConnectionTimeout;
    private $oauthSocketTimeout;
    const REDIRECT_URI = 'none';

    /**
     * CognitoAuthentication constructor.
     * @param $clientId
     * @param $clientSecret
     * @param $scopes
     * @param $tokenStorageHelper
     * @param $options
     */
    public function __construct($clientId, $clientSecret, $scopes, $tokenStorageHelper, $options)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->scopes = $scopes;
        $this->oauthConnectionTimeout = $options["oauthConnectionTimeout"];
        $this->oauthSocketTimeout = $options["oauthConnectionTimeout"];
        $this->tokenStorageHelper = $tokenStorageHelper;
    }


    /**
     * @param callable $handler
     * @return Closure
     */
    public function __invoke(callable $handler)
    {
        return function (RequestInterface $request, array $options) use ($handler) {
            $token = $this->tokenStorageHelper->getToken();
            if (!isset($token)) {
                $token = $this->getToken();
            }
            return $handler(
                $request->withAddedHeader('Authorization', 'Bearer ' . $token),
                $options
            );
        };
    }


    /**
     * @return \League\OAuth2\Client\Token\AccessToken|\League\OAuth2\Client\Token\AccessTokenInterface|string
     * @throws \Exception
     */
    private function getToken()
    {

        if (!isset($this->clientId)) {
            throw new \InvalidArgumentException("Client Id can't be null");
        }

        if (!isset($this->clientSecret)) {
            throw new \InvalidArgumentException("Client Secret can't be null");
        }

        try {
            $httpClient = new HttpClient([
                'timeout' => ($this->oauthConnectionTimeout / 1000),
                'connect_timeout' => ($this->oauthSocketTimeout / 1000),
            ]);
            $provider = new Cognito([
                'clientId' => $this->clientId,
                'clientSecret' => $this->clientSecret,
                'redirectUri' => self::REDIRECT_URI,
                'hostedDomain' => $this->tokenStorageHelper->getOAuthUrl(),
                'scope' => $this->scopes,
            ], [
                'httpClient' => $httpClient,
            ]);

            $grant = new ClientCredentials();

            $token = $provider->getAccessToken($grant);
            $this->tokenStorageHelper->setAccessToken($token);
            return $token;
        } catch (\UnexpectedValueException $e) {
            print_r($e->getMessage()."\n" .$e->getTraceAsString());
            exit();
        } catch (IdentityProviderException $e) {
            print_r($e->getMessage()."\n" .$e->getTraceAsString());
            exit();
        } catch (\Exception $e) {
            print_r($e->getMessage()."\n" .$e->getTraceAsString());
            exit();
        }
    }
}
