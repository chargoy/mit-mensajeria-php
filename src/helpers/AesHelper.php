<?php


namespace Asymmetric\SdkClient\helpers;


/**
 * Class AesHelper
 * @package Asymmetric\SdkClient\helpers
 */
class AesHelper
{

    const CIPHER_METHOD = MCRYPT_RIJNDAEL_128;
    const CBC_LENGTH_IV = 16;
    const CBC = MCRYPT_MODE_CBC;


    const GCM = 'aes-256-gcm';
    const GCM_TAG_LENGTH = 16;


    public function encryptGCM($key, $data)
    {
        if (in_array(self::GCM, openssl_get_cipher_methods())) {
            $ivLength = openssl_cipher_iv_length(self::GCM);
            $iv = openssl_random_pseudo_bytes($ivLength);
            $ciphertext = openssl_encrypt($data, self::GCM, $key, OPENSSL_NO_PADDING, $iv, $tag);
            return $iv . $ciphertext . $tag;
        }


        return null;
    }

    public static function deCryptGCM($key, $data)
    {
        $encrypt = base64_decode($data);
        $ivLength = openssl_cipher_iv_length(self::GCM);
        $iv = substr($encrypt, 0, $ivLength);
        $tagLength = self::GCM_TAG_LENGTH;
        $tag = substr($encrypt, -$tagLength);
        $ciphertext = substr($encrypt, $ivLength, -$tagLength);
        return openssl_decrypt($ciphertext, self::GCM, $key, OPENSSL_NO_PADDING, $iv, $tag);

    }


    /**
     * @param $key
     * @param $data
     * @return string
     */
    public function encrypt($key, $data)
    {
        try {
            $blockSize = mcrypt_get_block_size(self::CIPHER_METHOD, self::CBC);
            $dataPadded = $this->pkcs5_pad($data, $blockSize);
            $iv = mcrypt_create_iv(mcrypt_get_iv_size(self::CIPHER_METHOD, self::CBC), MCRYPT_RAND);
            $encryptedData = mcrypt_encrypt(self::CIPHER_METHOD, $key, $dataPadded, self::CBC, $iv);
            return $iv . $encryptedData;
        } catch (\Exception $e) {
            return $e->getMessage();
        }

    }

    /**
     * @param $text
     * @param $blockSize
     * @return string
     */
    private function pkcs5_pad($text, $blockSize)
    {
        $pad = $blockSize - (strlen($text) % $blockSize);
        return $text . str_repeat(chr($pad), $pad);
    }


    /**
     * @param $data
     * @param $key
     * @return string
     */
    public static function deCypherData($key, $data)
    {

        $newData = base64_decode($data);
        $iv = substr($newData, 0, self::CBC_LENGTH_IV);
        $dataRaw = substr($newData, self::CBC_LENGTH_IV, strlen($newData));
        try {
            $decipherMessage = self::pkcs5_unPad(mcrypt_decrypt(self::CIPHER_METHOD, $key, $dataRaw, self::CBC, $iv));
            if (!$decipherMessage) {
                throw new \InvalidArgumentException('The message can not be decipher');
            }
            return $decipherMessage;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * @param $text
     * @return false|string
     */
    private static function pkcs5_unPad($text)
    {
        $pad = ord($text[strlen($text) - 1]);
        if ($pad > strlen($text)) {
            return false;
        }
        if (strspn($text, chr($pad), strlen($text) - $pad) != $pad) {
            return false;
        }
        return substr($text, 0, -1 * $pad);
    }
}