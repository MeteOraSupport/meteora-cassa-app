<?php

namespace App\Http\Controllers;

use App\Lib\Request\Cassa\RequestCassaData;
use App\Lib\Request\Order\RequestOrderData;
use App\Lib\Request\User\RequestUserDdata;
use App\Models\Option;
use Illuminate\Http\Request;

class CassaController extends Controller
{
    public function index(Request $request)
    {
        $user = session('user');
        $cassa = session('cassa');

        $page = $request->get('page', 1);

        $requestUserDdata = new RequestOrderData();
        $orders = $requestUserDdata->ordersCassa($cassa['id'], $page);
        // dd($orders);

        return view("cassa.index", compact("user", "cassa", "orders"));
    }

    public function checkCode(Request $request)
    {
        $request->validate([
            'access_code' => [
                'required',
                'digits:6',
                'numeric',
            ],
        ], [
            'access_code.required' => 'Il codice di accesso è obbligatorio.',
            'access_code.numeric' => 'Il codice di accesso deve contenere solo numeri.',
            'access_code.digits' => 'Il codice di accesso deve contenere esattamente 6 cifre.',
        ]);

        $requestUserDdata = new RequestUserDdata();
        $result = $requestUserDdata->checkUserCode($request->access_code);

        // dd($result);
        if ($result['status'] == 404) {
            return redirect()->back()->with('error', 'Nessun operatore trovato con questo codice di accesso');
        }elseif ($result['status'] == 401) {
             return redirect()->back()->with('error', 'Token non valido');
        }


        $userData = $result['data']['user'];
        $userCassaData = $result['data']['cassa'];

        session([
            'user' => $userData,
            'cassa' => $userCassaData
        ]);

        // dd(
        //     session('user.id'),
        //     session('cassa.id'),
        // );

        return redirect()->route('cassa.index')->with('success', 'Accesso effettuato da operatore ' . $result['data']['user']['name'] . ' ' . $result['data']['user']['surname']);
    }

    public function logout()
    {
        session()->forget(['user', 'cassa']);
        return redirect()->route('welcome');
    }

    public function show($order_id)
    {
        $user = session('user');
        $cassa = session('cassa');
        $requestOrderData = new RequestOrderData();
        // dd($requestOrderData->orderCassa($order_id));
        $order = $requestOrderData->orderCassa($order_id)['data']['order'];
        $endpoint = Option::getOption("endpoint_meteora");

        return view("cassa.show", compact("user", "cassa", "order", "endpoint"));
    }
}
