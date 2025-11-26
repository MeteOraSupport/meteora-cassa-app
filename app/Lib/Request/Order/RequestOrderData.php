<?php

namespace App\Lib\Request\Order;

use App\Lib\Helper\TokenCassa;
use App\Lib\Helper\UrlApi;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class RequestOrderData
{
    public function ordersCassa($cassa_id, $page = 1)
    {
        $client = new Client([
            'base_uri' => UrlApi::url()
        ]);

        try {
            $response = $client->get("/api/v1/orders/cassa/{$cassa_id}", [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . TokenCassa::set()
                ],
                'query' => [
                    'page' => $page // <<--- AGGIUNTO
                ]
            ]);

            $data = json_decode($response->getBody(), true);

            return [
                'success' => true,
                'status' => $response->getStatusCode(),
                'orders' => $data,
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

            return [
                'success' => false,
                'status' => 500,
                'error' => 'Errore di connessione all’API.',
            ];
        }
    }

    public function orderCassa($order_id)
    {
        $client = new Client([
            'base_uri' => UrlApi::url()
        ]);

        try {
            $response = $client->get("/api/v1/order/" . $order_id . "/cassa", [
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

    public function storeOrderCassa($archive_id, $cassa_id)
    {
        $client = new Client([
            'base_uri' => UrlApi::url()
        ]);

        try {
            $response = $client->post("/api/v1/order/cassa/store", [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . TokenCassa::set()
                ],
                'json' => [
                    'archive_id' => $archive_id,
                    'cassa_id' => $cassa_id
                ]
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

            return [
                'success' => false,
                'status' => 500,
                'error' => 'Errore di connessione all’API.',
            ];
        }
    }

    public function storeProductOrder($order_id, $product_combination_id, $product_combination_qty)
    {
        // dd($order_id);
        $client = new Client([
            'base_uri' => UrlApi::url()
        ]);

        try {
            $response = $client->post("/api/v1/order/" . $order_id . "/product/cassa", [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . TokenCassa::set()
                ],
                'json' => [
                    'order_id' => $order_id,
                    'product_combination_id' => $product_combination_id,
                    'product_combination_qty' => $product_combination_qty,
                ]
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

            return [
                'success' => false,
                'status' => 500,
                'error' => 'Errore di connessione all’API.',
            ];
        }
    }

    public function updateOrderProductDiscount($order_id)
    {
        $client = new Client([
            'base_uri' => UrlApi::url()
        ]);

        try {
            $response = $client->put("api/v1/order/" . $order_id . "/product/discount/cassa", [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . TokenCassa::set()
                ],
                'json' => [
                    'product_combination_id' => request()->product_combination_id,
                    'discount' => request()->discount
                ]
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

            return [
                'success' => false,
                'status' => 500,
                'error' => 'Errore di connessione all’API.',
            ];
        }
    }

    public function updateOrderProductQty($order_id)
    {
        $client = new Client([
            'base_uri' => UrlApi::url()
        ]);

        try {
            $response = $client->put("/api/v1/order/" . $order_id . "/product/qty/cassa", [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . TokenCassa::set()
                ],
                'json' => [
                    'stock_order_single' => request()->stock_order_single,
                    'product_combination_id' => request()->product_combination_id,
                    'stock_combination_id' => request()->stock_combination_id,
                    'discount' => request()->discount
                ]
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

            return [
                'success' => false,
                'status' => 500,
                'error' => 'Errore di connessione all’API.',
            ];
        }
    }

    public function updateOrderCassaIncassa($order_id)
    {
        $client = new Client([
            'base_uri' => UrlApi::url()
        ]);

        try {
            $response = $client->put("/api/v1/order/" . $order_id . "/cassa/update", [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . TokenCassa::set()
                ],
                'json' => [
                    'order_deposit' => request()->order_deposit,
                    'type_payment_value' => request()->type_payment_value,
                    'total_order' => request()->total_order
                ]
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

            return [
                'success' => false,
                'status' => 500,
                'error' => 'Errore di connessione all’API.',
            ];
        }
    }

    public function deleteProductOrderCassa()
    {
        $client = new Client([
            'base_uri' => UrlApi::url()
        ]);

        try {
            $response = $client->delete("/api/v1/order/delete/product/cassa", [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . TokenCassa::set()
                ],
                'json' => [
                    'stock_combination_delete_id' => request()->stock_combination_delete_id,
                    'product_combination_id' => request()->product_combination_id,
                    'order_id' => request()->order_id
                ]
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

            return [
                'success' => false,
                'status' => 500,
                'error' => 'Errore di connessione all’API.',
            ];
        }
    }
}
