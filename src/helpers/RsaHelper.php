<?php


namespace Asymmetric\SdkClient\helpers;

/**
 * Class RsaHelper
 * @package Asymmetric\SdkClient\helpers
 */
class RsaHelper
{

    const LENGTH = 2048;
    const ENCODING = "UTF-8";
    const MODE = "ECB";
    const PADDING = OPENSSL_PKCS1_PADDING;
    const ALGO_SHA256 = OPENSSL_ALGO_SHA256;

    private $keys;

    /**
     * RsaHelper constructor.
     */
    public function __construct(){
        $this->generateKeys();
    }


    /**
     * @return false|resource|string
     */
    public function generateKeys()
    {

        $config = array(
            "private_key_bits" => self::LENGTH,
            "private_key_type" => OPENSSL_KEYTYPE_RSA,
        );

        try {
            $this->keys = openssl_pkey_new($config);
            //$this->keys = RSA::createKey();
        } catch (\Exception $e) {
            return $e->getMessage();
        }
        return $this->keys;
    }

    /**
     * @return mixed
     */
    public function getKeys(){
        return $this->keys;
    }


    /**
     * @param false $convert
     * @return false|mixed|string
     */
    public function getPublicKey($convert = false){
        $publicKey = openssl_pkey_get_details($this->keys)['key'];
        //$publicKey = $this->keys->getPublicKey();
        if($convert){
            return ConverterHelper::pemToDer($publicKey);
        }
        return $publicKey;
    }

    /**
     * @return mixed
     */
    public function getPrivateKey(){
        openssl_pkey_export($this->keys, $privateKey);
        return $privateKey;
    }


    /**
     * @param $text
     * @return string
     */
    public function cypher($text)
    {

        if(!isset($text)) {
            throw new \Exception("Text can not be null or blank");
        }

        try{
            openssl_public_encrypt($text, $cypherText, $this->getPublicKey(), OPENSSL_PKCS1_PADDING);
            return base64_encode($cypherText);
        }
        catch(\Exception $e){
            return $e->getMessage();
        }
    }


    /**
     * @param $text
     * @return string
     * @throws \Exception
     */
    public function deCypher($text)
    {
        if(!isset($text)) {
            throw new \Exception('Cypher Text is null');
        }

        $textRaw = null;
        $textRaw = base64_decode($text);


        try{
            $privateKey = openssl_pkey_get_private($this->getPrivateKey());
            $result = openssl_private_decrypt($textRaw, $deCypherText , $privateKey, OPENSSL_PKCS1_PADDING);

            if(!$result) {
                throw new \Exception("Message can't be decipher");
            }
            return $deCypherText;
        } catch(\Exception $e){
            return $e->getMessage();
        }

    }

    /**
     * @param $message
     * @return string
     * @throws \Exception
     */
    public function sign($message)
    {
        if(!isset($message)) {
            throw new \Exception('Text is null');
        }

        try{
            openssl_sign($message, $signedMessage, $this->getPrivateKey(), self::ALGO_SHA256 );
            return $signedMessage;
        }catch (\Exception $e){
            return $e->getMessage();
        }
    }

    /**
     * @param $message
     * @param $signature
     * @return int|string
     * @throws \Exception
     */
    public function verify($message, $signature)
    {
        if(!isset($message)) {
            throw new \Exception('Text is null');
        }

        try{
            return openssl_verify($message, $signature, $this->getPublicKey(), self::ALGO_SHA256 );
        }catch (\Exception $e){
            return $e->getMessage();
        }
    }



}
