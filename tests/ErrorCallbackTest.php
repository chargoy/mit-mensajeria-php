<?php

use Asymmetric\SdkClient\SdkClient;
use \Asymmetric\SdkClient\models\Environment;

class ErrorCallbackTest extends PHPUnit_Framework_TestCase
{
    public function testRunConexion()
    {
        print_r("\nEjecutando prueba con error\n");
        print_r("\n-------------------------------------------------------\n");
        $urlCharge = "https://dev3.mitec.com.mx/pgs/vs/cobroXml";
        $data = "<?xml version='1.0' encoding='UTF-8' ?><VMCAMEXM><business><id_company>I754</id_company><id_branch>001</id_branch><country>MEX</country><user>user1234</user><pwd>A9C3353B0A8F65B9290A</pwd></business><transacction><merchant>1234567</merchant><reference>ref</reference><tp_operation>1</tp_operation><creditcard><crypto>2</crypto><type>V/MC</type><name></name><number>1F053CF26F14FA07C45F3EAB849FA126</number><expmonth>190C</expmonth><expyear>190C</expyear><cvv-csc>1111</cvv-csc></creditcard><amount>1</amount><currency>MXN</currency><confirmation_email></confirmation_email><version></version></transacction></VMCAMEXM>";
        try {
            $sdkClient = new  SdkClient(Environment::DEV, '2g0hvakvprp7rouejnb03mfeug', 'ap4ag1esheqmmm4kdokdl76506049n75poi6d2grul9u6at5oc7', "msec/genKey pgs/cobro");

            $request = $sdkClient->postRequest($urlCharge, $data, SdkClient::VERSION_192CEAD6E);
            print_r("\nRespuesta : \n");
            print_r($request->getBody()->getContents());
            $this->assertNotNull($request->getBody());
            $this->assertNotNull($request->getStatusCode());
            print_r("\n-------------------------------------------------------\n");

        } catch (\Exception $e) {
            print($e);
        }
    }

}