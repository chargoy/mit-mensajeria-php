<?php

namespace Asymmetric\SdkClient\Helpers;

/**
 * Class TokenStorageHelper
 * @package Asymmetric\SdkClient\Helpers
 */
class TokenStorageHelper
{
    private $aesToken;
    private $hmacToken;
    private $accessToken;
    private $oAuthUrl;

    /**
     * @return mixed
     */
    public function getOAuthUrl()
    {
        return $this->oAuthUrl;
    }

    /**
     * @param mixed $oAuthUrl
     */
    public function setOAuthUrl($oAuthUrl)
    {
        $this->oAuthUrl = $oAuthUrl;
    }

    /**
     * TokenStorageHelper constructor.
     */
    public function __construct()
    {
    }


    /**
     * @return mixed
     */
    public function getToken()
    {
        return $this->accessToken;
    }

    /**
     * @param $token
     */
    public function setAccessToken($token)
    {
        $this->accessToken = $token;
    }


    /**
     * @return mixed
     */
    public function getAesKey()
    {
        return $this->aesToken;
    }

    /**
     * @param $token
     */
    public function setAesKey($token)
    {
        $this->aesToken = $token;
    }


    /**
     * @return mixed
     */
    public function getHmacKey()
    {
        return $this->hmacToken;
    }

    /**
     * @param $token
     */
    public function setHmacKey($token){
        $this->hmacToken = $token;
    }

}
