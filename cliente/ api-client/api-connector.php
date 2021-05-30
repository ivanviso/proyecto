<?php

require_once '../../vendor/autoload.php';

function ApiRequest(array $data, string $uri, string $query, string $method) // las credenciales se deben incorporar en data. 
{
    $client = new GuzzleHttp\Client(
        ['base_uri' => $uri,
            'headers' => [
                'Content-Type' => 'application/json',
            ],
        ]
    );
    switch ($method) {
        case 'GET':
            $response = $client->get($query, [
                GuzzleHttp\RequestOptions::JSON => $data,
            ]);
            break;
        case 'POST':
            $response = $client->post($query, [
                GuzzleHttp\RequestOptions::JSON => $data,
            ]);
            break;
        case 'PUT':
            $response = $client->put($query, [
                GuzzleHttp\RequestOptions::JSON => $data,
            ]);
            break;

    }

    return json_decode($response->getBody(),true); // true es necesario para crear un array asociativo en lugar de un objeto.
}

$data = ([
    'client-id' => 'admin',
    'client-token' => 'abcABC123',
]);



?>