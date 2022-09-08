<?php


namespace Asymmetric\SdkClient\helpers;


class ConverterHelper
{

    /**
     * @param $publicKey
     * @return false|string
     */

    static function pemToDer($publicKey){
        $lines = explode("\n", trim($publicKey));
        unset($lines[count($lines)-1]);
        unset($lines[0]);
        $result = implode('', $lines);
        $result = base64_decode($result);
        return $result;
    }


    /**
     * @param $der
     * @param false $private
     * @return string
     */
    static function derToPem($der, $private=false)
    {
        $der = base64_encode($der);
        $lines = str_split($der, 65);
        $body = implode("\n", $lines);
        $title = $private? 'PRIVATE RSA KEY' : 'PUBLIC RSA KEY';
        $result = "-----BEGIN {$title}-----\n";
        $result .= $body . "\n";
        $result .= "-----END {$title}-----\n";

        return $result;
    }

}