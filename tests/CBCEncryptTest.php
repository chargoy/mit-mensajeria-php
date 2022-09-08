<?php

use Asymmetric\SdkClient\SdkClient;
use \Asymmetric\SdkClient\models\Environment;

class CBCEncryptTest extends PHPUnit_Framework_TestCase
{

    public function testRunConexion()
    {

        print_r("\nEjecutando prueba con encriptación AES/CBC/PKCS5PADDING\n");
        print_r("\n-------------------------------------------------------\n");
        $urlCharge = "https://dev3.mitec.com.mx/pgs/vs/cobroXml";
        $data  = "{\"Business\":{\"IdCompany\":\"N10A\",\"IdBranch\":\"0001\",\"Country\":\"MEX\",\"User\":\"N10AREAD0\",\"Pwd\":\"TEMPORAL01\"},\"Transaction\":{\"Merchant\":\"261559\",\"Reference\":\"VENTAT1\",\"TpOperation\":\"30\",\"Creditcard\":{\"Crypto\":\"0\",\"Type\":\"V/MC\",\"Name\":\"DANIEL\",\"Number\":\"5470467019548602\",\"ExpMonth\":\"12\",\"ExpYear\":\"25\",\"CvvCsc\":\"123\"},\"Amount\":1500.50,\"Currency\":\"MXN\",\"ConfirmationEmail\":\"daniel.alonso@mitec.com.mx\",\"UsrTransacction\":\"CobrosQA\",\"Version\":\"Postman 1.0\"}}";
        try {
            $sdkClient = new  SdkClient(Environment::DEV, '1dpv2nll72fb9v29cvu8joj6qp', '5f1agt5mjbkkjh79f8nsbc19rnpo5orkl8sfmvc8gegkpeo4dnq', "msec/genKey pgs/cobro");
            $request = $sdkClient->postRequest($urlCharge, $data, SdkClient::VERSION_192CEAD6E);
            print_r("\nRespuesta : \n");
            print_r($request->getBody()->getContents());
            print_r("\n-------------------------------------------------------\n");
        } catch (\Exception $e) {
            print($e);
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
        }
    }
}
