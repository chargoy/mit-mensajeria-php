<?php


namespace Asymmetric\SdkClient\Helpers;

/**
 * Class HmacHelper
 * @package Asymmetric\SdkClient\Helpers
 */
class HmacHelper
{

    const ALGORITHM = 'sha256';


    /**
     * @param $key
     * @param $data
     * @return \Exception|string
     */
    public static function encode($key, $data)
    {
        try {

            return hash_hmac(
                self::ALGORITHM,
                $data,
                $key,
                true
            );
        } catch (\Exception $e) {
            return $e;
        }


    }
}
