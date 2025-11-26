<?php

namespace App\Http\Controllers;

use App\Lib\Request\Order\RequestOrderData;
use App\Models\Option;
use App\Models\Order;

class ScontrinoController extends Controller
{
    public function stampaScontrino($order_id)
    {
        $requestOrder = new RequestOrderData();
        $order = $requestOrder->orderCassa($order_id);

        $ip = Option::getOption('stampante_ip');
        $port = intval(Option::getOption('stampante_porta')); // Porta
        $type_payment = strtolower($order['data']['order']['payment']['name_payment']); // elettronico o contanti

        $prodotti = [];
        // dd($order['data']['order_items']);
        foreach ($order['data']['order_items'] as $productsCombination) {

            $repartoProd = str_replace('%', '', $productsCombination['iva_prod']);
            $combination = trim(str_replace(['[', '"', ']', ' '], '', $productsCombination['combination']));
            $name = trim($productsCombination['name_product']) ?? '';

            if ($combination === 'Semplice') {
                // Solo nome, max 24 caratteri
                $descr = strtoupper(substr($name, 0, 24));
                if (isset($productsCombination['gross_selling_price_discount'])) {
                    $price = $productsCombination['gross_selling_price_discount'];
                } else if (isset($productsCombination['gross_selling_price'])) {
                    $price = $productsCombination['gross_selling_price'];
                }
            } else {
                // Variante: nome accorciato + variante senza spazi
                $nameShort = strtoupper(substr($name, 0, 14));
                $variant = strtoupper($combination);
                $descr = $nameShort . '-' . $variant;
                // Rimuovi spazi multipli e taglia a 24 totali
                $descr = preg_replace('/\s+/', '', trim($descr));
                $descr = substr($descr, 0, 24);
                // dd($productsCombination);
                if ($productsCombination['gross_selling_price_var_sale']) {
                    $price = $productsCombination['gross_selling_price_var_sale'];
                } else if ($productsCombination['gross_selling_price_var']) {
                    $price = $productsCombination['gross_selling_price_var'];
                } else if ($productsCombination['gross_selling_price_discount']) {
                    $price = $productsCombination['gross_selling_price_discount'];
                } else if ($productsCombination['gross_selling_price']) {
                    $price = $productsCombination['gross_selling_price'];
                }
            }

            $prodotti[] = [
                'descr' => $descr,
                'reparto' => $this->reparto($repartoProd),
                'prezzo' => (float) $price,
                'qta' => (int) ($productsCombination['stock_order_single']),
                'sconto' => (float) ($productsCombination['discount'] ?? 0),
                'tipo_sconto' => '%'
            ];
        }

        // 💰 Totale pagato (deposito ordine)
        $pagamentoTotale = (float) $order['data']['order']['order_deposit'];
        $paagamentoElettronico = 0;

        // K => clear
        // 1O => operatore 1
        // Quantità = 1* 1 sta per quantità 1
        // Prezzo = 250H 2,50 euro
        // Reparto = 1R
        // Tipo di pagamento 1T contanti con il prezzo che sarà quanto il cliente ci da in contanti in questo caso 1000H che sono 10 euro elettronico H4T
        // Sconto in % 10*1M dove 10 è il valore dello sconto e 1M significa %

        //Esempio funzionante
        // $seq = 'K1Oc1*"STIVALETTO UOMO BLUNDSTO"22500H1R1*"STIVALETTO UOMO BLUNDSTO"22500H1R15*1M2*"SNEAKER UOMO KEEN TREKKI"17000H1R=80000H1T'; contanti
        // $seq = 'K1Oc1*"STIVALETTOUOM-BROWN,44"22500H1R1*"STIVALETTOUOM-BROWN,42"22500H1R15*1M1*"CAPPELLO CON VISIERA 47"4350H1R=45975H4T'; elettronico

        // Calcolo totale netto e costruzione sequenza articoli
        $seq = 'K1Oc'; // Reset + operatore
        $totEuro = 0.0;

        foreach ($prodotti as $p) {
            $descr = $p['descr'];
            $rep = (int) $p['reparto'];
            $prezzoCents = (int) round($p['prezzo'] * 100);
            $qta = (float) $p['qta'];
            $sconto = (float) ($p['sconto'] ?? 0);

            // Riga articolo con o senza sconto
            if ($sconto > 0.0) {
                // Sconto percentuale inline (es. 10*1M)
                $seq .= "{$qta}*\"{$descr}\"{$prezzoCents}H{$rep}R{$sconto}*1M";
                $totEuro += ($p['prezzo'] * $qta) * (1 - $sconto / 100);
            } else {
                $seq .= "{$qta}*\"{$descr}\"{$prezzoCents}H{$rep}R";
                $totEuro += $p['prezzo'] * $qta;
            }
        }

        // Totale effettivo in euro e in centesimi
        $totCents = (int) round($totEuro * 100);

        // Tipo pagamento
        $isElettronico = str_contains($type_payment, 'elettronico')
            || str_contains($type_payment, 'pos')
            || str_contains($type_payment, 'carta');

        $paymentCode = $isElettronico ? 'H4T' : 'H1T';

        // Se è contanti → prendo quanto pagato nel deposito
        // Se è elettronico → calcolo dal totale scontato
        if ($isElettronico) {
            $pagamentoCents = $totCents;
        } else {
            $pagamentoCents = (int) round($pagamentoTotale * 100);
        }

        // Subtotale + chiusura con tipo pagamento
        $seq .= '=' . $pagamentoCents . $paymentCode;

/*         dd($seq);
 */
        // === Invio byte per byte (necessario in XON/XOFF) ===
        $socket = @socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if (!$socket || !@socket_connect($socket, $ip, $port)) {
            return response('❌ Connessione alla stampante fallita', 500);
        }

        for ($i = 0; $i < strlen($seq); $i++) {
            @socket_write($socket, $seq[$i], 1);
            usleep(5000);
        }

        @socket_close($socket);

        return response(
            "✅ Scontrino inviato a {$ip}:{$port}\n\n" .
            "{$seq}\n\n" .
            "Totale netto: €" . number_format($totEuro, 2, '.', ',') . "\n" .
            "Pagamento {$type_payment}: €" . number_format($pagamentoTotale, 2, '.', ',')
        )->header('Content-Type', 'text/plain');
    }

    private function reparto($iva)
    {
        $reparti = [
            1 => Option::getOption('reparto_1'),
            2 => Option::getOption('reparto_2'),
            3 => Option::getOption('reparto_3'),
            4 => Option::getOption('reparto_4'),
            5 => Option::getOption('reparto_5'),
        ];

        $reparto = array_search($iva, $reparti, true);
        return $reparto ?: 1;
    }
}
