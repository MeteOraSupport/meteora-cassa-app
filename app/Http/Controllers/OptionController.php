<?php

namespace App\Http\Controllers;

use App\Models\Option;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class OptionController extends Controller
{
    public function index()
    {
        $fields = [
            "endpoint_meteora",
            "numero_archivio",
            "stampante_ip",
            "stampante_porta",
            "reparto_1",
            "reparto_2",
            "reparto_3",
            "reparto_4",
            "reparto_5",
            "token_cassa"
        ];

        // crea array OPTIONS stile WordPress
        $options = Option::whereIn("option_key", $fields)
            ->pluck("option_value", "option_key")
            ->toArray();

        return view("option.index", compact("options"));
    }

    public function store(Request $request)
    {
        // VALIDAZIONE
        $request->validate([
            "endpoint_meteora" => "required",
            "numero_archivio" => "required",
            "stampante_ip" => "required",
            "stampante_porta" => "required",
            "token_cassa" => "required"
        ], [
            "endpoint_meteora.required" => "Endpoint meteora è obbligatorio.",
            "numero_archivio.required" => "Il numero archivio è obbligatorio.",
            "stampante_ip.required" => "L'indirizzo TCP/IP è obbligatorio.",
            "stampante_porta.required" => "La porta è obbligatoria.",
            "token_cassa.required" => "Il token è obbligatorio."
        ]);

        $fields = [
            "numero_archivio",
            "endpoint_meteora",
            "stampante_ip",
            "stampante_porta",
            "reparto_1",
            "reparto_2",
            "reparto_3",
            "reparto_4",
            "reparto_5",
            "token_cassa",
        ];

        foreach ($fields as $field) {

            if (!$request->has($field)) {
                continue;
            }

            $value = $request->$field;

            // 🔐 Applica encrypt SOLO a token_cassa
            if ($field === 'token_cassa' && !empty($value)) {
                $value = Crypt::encryptString($value);
            }

            Option::updateOrCreate(
                ['option_key' => $field],
                ['option_value' => $value]
            );
        }

        return redirect()
            ->route('option.index')
            ->with('success', 'Configurazione salvata correttamente!');
    }

}
