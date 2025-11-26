<?php

namespace App\Lib\Request\Cassa;

use App\Lib\Helper\UrlApi;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class RequestCassaData
{
    // public $token;
    // public function __construct($token)
    // {
    //     $this->token = $token;
    // }

    // public function ordersCassa($cassa_id)
    // {
    //     $client = new Client([
    //         'base_uri' => UrlApi::url()
    //     ]);

    //     try {
    //         $response = $client->get("/api/v1/orders/cassa/".$cassa_id, [
    //             'headers' => [
                //     'Accept' => 'application/json',
                //     'Authorization' => 'Bearer ' . TokenCassa::set()
                // ],
    //         ]);

    //         $data = json_decode($response->getBody(), true);

    //         return [
    //             'success' => true,
    //             'status' => $response->getStatusCode(),
    //             'orders' => $data,
    //         ];

    //     } catch (RequestException $e) {
    //         if ($e->hasResponse()) {
    //             $error = json_decode($e->getResponse()->getBody(), true);

    //             return [
    //                 'success' => false,
    //                 'status' => $e->getResponse()->getStatusCode(),
    //                 'error' => $error,
    //             ];
    //         }

    //         // Errore di rete / timeout
    //         return [
    //             'success' => false,
    //             'status' => 500,
    //             'error' => 'Errore di connessione all’API.',
    //         ];
    //     }
    // }
}
