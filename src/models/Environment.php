<?php


namespace Asymmetric\SdkClient\models;



/**
 * Class Environment
 * @package Asymmetric\SdkClient\models
 */
class Environment
{

    const DEV = 'dev';
    const PROD = 'prod';
    const QA = 'qa';
    const ENVIRONMENTS = [self::DEV, self::PROD, self::QA];
    private $environment;

    public function __construct($environment)
    {
        if(!in_array($environment, self::ENVIRONMENTS)){
            throw new \InvalidArgumentException("The environment is not recognized");
        }

        $_ENV['env'] = $environment;
    }


    /**
     * @return string|null
     */
    public static function getUrlToken(){
        $url = null;
        switch ($_ENV['env']){
            case self::DEV:
                $url = "devauth.mit.com.mx";
                break;
            case self::PROD:
                $url = "auth.mit.com.mx";
                break;
            case self::QA:
                $url = "qaauth.mit.com.mx";
                break;
        }
        return $url;
    }

    /**
     * @return string|null
     */
    public static function getUrlKeys(){
        $url = null;
        switch ($_ENV['env']){
            case self::DEV:
                $url = "https://devsigma.mitec.com.mx/msgTknCore/genKeys";
                break;
            case self::PROD:
                $url = "https://sigma.mit.com.mx:6064/msgTknCore/genKeys";
                break;
            case self::QA:
                $url = "https://qasigma.mitec.com.mx/msgTknCore/genKeys";
                break;
        }
        return $url;
    }

}