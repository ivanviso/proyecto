<?php


function ApiRequest(array $data, string $uri, string $query, string $method) // las credenciales se deben incorporar en data. 
{
    require __DIR__.'/vendor/autoload.php';
    $client = new GuzzleHttp\Client(
        ['base_uri' => $uri,
            'headers' => [
                'Content-Type' => 'application/json',
            ],
        ]
    );
    $response = $client->request($method,$query,['json' => $data] );


    return $response->getBody()->getContents(); // true es necesario para crear un array asociativo en lugar de un objeto.
}

?>