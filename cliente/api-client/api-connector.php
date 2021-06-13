<?php


function ApiRequest(array $data, string $uri, string $query, string $method) // las credenciales se deben incorporar en data. 
{
    require '../../vendor/autoload.php';
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

$data[] = ([
    'client-id' => 'admin',
    'client-token' => 'abcABC123'
]);

$result=ApiRequest($data,"http://localhost:5000","login","POST");
print_r($result);
