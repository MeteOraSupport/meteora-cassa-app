<?php

namespace App\Lib\Request\User;

use App\Lib\Helper\TokenCassa;
use App\Lib\Helper\UrlApi;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class RequestUserDdata
{
    // public $token;
    // public function __construct($token)
    // {
    //     $this->token = $token;
    // }

    public function checkUserCode($code)
    {
        $client = new Client([
            'base_uri' => UrlApi::url()
        ]);

        try {
            $response = $client->get("/api/v1/user/{$code}", [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . TokenCassa::set()
                ],
            ]);

            $data = json_decode($response->getBody(), true);

            return [
                'success' => true,
                'status' => $response->getStatusCode(),
                'data' => $data,
            ];

        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $error = json_decode($e->getResponse()->getBody(), true);

                return [
                    'success' => false,
                    'status' => $e->getResponse()->getStatusCode(),
                    'error' => $error,
                ];
            }

            // Errore di rete / timeout
            return [
                'success' => false,
                'status' => 500,
                'error' => 'Errore di connessione all’API.',
            ];
        }
    }
}
