# Sdk Php Client

Un conjunto de clases que permiten intercambiar mensajes de forma segura con los servicios de MIT.  
  
  
## Comenzando

Estas instrucciones te permitirán implementar el flujo de comunicación establecido en MIT para intercambio seguro de mensajes.
El modelo de autorización está basado en el protocolo [Oauth 2.0](https://oauth.net/) y algoritmos de cifrado robusto.
 
 
## Pre-requisitos

0. php 7.4 o superior
1. Identificar la aplicación a registrar:
2. Registrar la aplicación en el servidor de autorización para obtener un **ClientID** y opcionalmente un **ClientSecret**
> La aplicación por lo menos debe tener habilitado el scope _"msec/genKey"_
 
 
## Modo de uso 

El SDK tiene como base los componentes de httpClient de la libreria Guzzle, PHP HTTP client.
Para mas información visite el siguiente enlace.

> Guzzle: https://docs.guzzlephp.org/en/stable/#

 
Pasos para usar la libreria.

1. Inicializar el Objeto SdkClient de la libreria como a continuacion se presenta.

    ```$sdkClient = new  SdkClient(Entorno, clientId, secretId, scopes);```
2. El sdk permite la opcion de establecer la url para obtener el token de cognito mediante la siguiente funcion, 
es importante mencionar que este parametro debe ser enviado antes de llamar al postRequest.

    ```$sdkClient->setOAuthUrl(URL_COGNITO);```
3. Se puede establecer el tiempo de espera de conexion y de respuesta por parte del OAuth con las siguientes funciones.
    ```
    $sdkClient->setOauthConnectionTimeout(4000);   Default 20000
   $sdkClient->setOauthSocketTimeout(5000);    Default 30000
   ```
   
4. Se puede establecer el tiempo de espera de conexion y de respuesta para obtener las llaves para firmar y encriptar con las siguientes funciones.
    ```
    $sdkClient->setApiKeyConnectionTimeout(4000);   Default 20000
   $sdkClient->setApiKeySocketTimeout(5000);    Default 30000
   ```
   
5. Se puede establecer el tiempo de espera de conexion y de respuesta por parte del servicio de cobro con las siguientes funciones.
    ```
    $sdkClient->setServiceConnectionTimeout(4000);   Default 20000
   $sdkClient->setServiceSocketTimeout(5000);    Default 30000
   ```

6. Llamar al postRequest que se encargara de procesar la transacción, a continación se presenta un ejemplo.:

```
El metodo postRequest tiene la siguiente firma:

postRequest($url, $data, $cipherVersion = SdkClient::VERSION_GCM)

por lo que se puede enviar en 2 formatos diferentes, por default se usara el cifrado GCM.

1.- si desea usar el cifrado GCM se envia de la siguiente manera
        $request = $sdkClient->postRequest(URL_COBRO, DATOS);

2.- si desea usar el cifrado CBC se envia post request de la siguiente manera: 
        $request = $sdkClient->postRequest(URL, DATOS, SdkClient::VERSION_192CEAD6E);
```

