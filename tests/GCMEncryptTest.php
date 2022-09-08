<?php

use Asymmetric\SdkClient\SdkClient;
use \Asymmetric\SdkClient\models\Environment;

class GCMEncryptTest extends PHPUnit_Framework_TestCase
{

    public function testRunConexion()
    {
        $urlCharge = "https://dev3.mitec.com.mx/wscobroairlines/pymtAgenciesNoDates";

        $data = '{"paymentService": {
		"idCompany": "0999",
		"idBranch": "86812353",
		"country": "MEX",
		"user": "0999AGSA",
		"password": "Rr4Y8vbQ",
		"merchant": "631",
		"reference": "SABRE INTERJET3-9",
		"tpOperation": "30",
		"creditCardType": "V/MC",
		"creditCardName": "USUARIO TEST MIT",
		"creditCardNumber": "5471570018226937",
		"creditCardExpMonth": "02",
		"creditCardExpYear": "24",
		"creditCardCVV": "345",
		"initialDeferment": "00",
		"numberOfPayments": "00",
		"planType": "00"
	},
	"transaction": {
		"transactionTS": "2022-04-11",
		"currency": "MXN",
		"amount": "6890.45",
		"totalTickets": "1",
		"orderSource": "000",
        "ticketNumber":"9O8O9S",
        "departureDate":"2022/12/15",
        "completionDate":"2022/12/28",
        "usrtransaction":"SABRE GDS",
        "response":"0",
        "customField1": "9O8O9S"
	}
}';
        print_r("\nEjecutando prueba con encriptación AES/GCM/NoPadding\n");
        print_r("\n-------------------------------------------------------\n");
        try {
            $sdkClient = new  SdkClient(Environment::DEV, '29veq40t2o09kosv9el08avgjn', 'upsk36p3des36b5frb502fmj4mitim37a5gstkdht57j24afcii', "msec/genKey pgs/cobro wscobroairlines/pymtAgenciesNoDates");
            $request = $sdkClient->postRequest($urlCharge, $data);
            print_r("\nRespuesta : \n");
            echo $request;
            print_r("\n-------------------------------------------------------\n");

        } catch (\Exception $e) {
            print($e);
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
        }
    }
}
