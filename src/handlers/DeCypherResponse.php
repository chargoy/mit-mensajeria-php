<?php


namespace Asymmetric\SdkClient\handlers;

use Asymmetric\SdkClient\helpers\AesHelper;
use Asymmetric\SdkClient\Helpers\HmacHelper;
use Asymmetric\SdkClient\SdkClient;
use GuzzleHttp\Psr7\Stream;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class DeCypherResponse
 * @package Asymmetric\SdkClient\handlers
 */
class DeCypherResponse
{

    private $storageHelper;
    private $bodyCypher;
    private $cipherMethod;


    /**
     * DeCypherResponse constructor.
     * @param $storageHelper
     * @param $bodyCypher
     * @param $cipherMethod
     */
    public function __construct($storageHelper, $bodyCypher, $cipherMethod)
    {
        $this->storageHelper = $storageHelper;
        $this->bodyCypher = $bodyCypher;
        $this->cipherMethod = $cipherMethod;
    }

    /**
     * @param callable $handler
     * @return \Closure
     */
    public function __invoke(callable $handler)
    {
        return function (RequestInterface $request, array $options) use ($handler) {
            $promise = $handler($request, $options);
            return $promise->then(
                function (ResponseInterface $response) {
                    $responseBody = $response->getBody()->getContents();
                    if ($this->cipherMethod === SdkClient::VERSION_192CEAD6E) {
                        $deCypherBody = AesHelper::deCypherData($this->storageHelper->getAesKey(), $responseBody);
                    } else {
                        $deCypherBody = AesHelper::deCryptGCM($this->storageHelper->getAesKey(), $responseBody);
                    }
                    $this->verifyHmac($deCypherBody, $response->getHeaderLine('MIT-HS'));
                    $stream = fopen('data://text/plain,' . $deCypherBody, 'r');
                    return $response->withBody(new Stream($stream));
                }
            );
        };
    }


    /**
     * @param $message
     * @param $expectedHmac
     * @throws \Exception
     */
    private function verifyHmac($message, $expectedHmac)
    {

        $receiveMessageHmac = HmacHelper::encode($this->storageHelper->getHmacKey(), $message);
        if (strcasecmp(base64_encode($receiveMessageHmac), $expectedHmac) != 0) {
            throw new \Exception("Response Hmac not match");
        }
    }

}