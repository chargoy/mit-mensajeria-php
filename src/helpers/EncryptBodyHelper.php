<?php

namespace Asymmetric\SdkClient\helpers;


use Asymmetric\SdkClient\SdkClient;

/**
 * Class EncryptBodyHelper
 * @package Asymmetric\SdkClient\helpers
 */
class EncryptBodyHelper
{

    private $tokenStorageHelper;
    private $hmacHelper;
    private $aesHelper;

    /**
     * EncryptBodyHelper constructor.
     * @param $tokenStorageHelper
     */
    public function __construct($tokenStorageHelper)
    {
        $this->tokenStorageHelper = $tokenStorageHelper;
        $this->hmacHelper = new HmacHelper();
        $this->aesHelper = new AesHelper();
    }

    /**
     * @param $data
     * @param $cipherMethod
     * @return array
     */
    public function protectData($data, $cipherMethod)
    {

        $hmacBytes = $this->hmacHelper->encode($this->tokenStorageHelper->getHmacKey(), $data);

        if ($cipherMethod === SdkClient::VERSION_192CEAD6E) {
            $body = $this->aesHelper->encrypt($this->tokenStorageHelper->getAesKey(), $data);
        } else {
            $body = $this->aesHelper->encryptGCM($this->tokenStorageHelper->getAesKey(), $data);
        }
        return array('mit-hs' => base64_encode($hmacBytes), 'body' => base64_encode($body));
    }

}
