<?php

namespace App\Http\Controllers;

use App\Lib\Request\Order\RequestOrderData;
use App\Models\Option;
use Illuminate\Http\Request;
use LDAP\Result;
use Str;
use Yajra\DataTables\Facades\DataTables;


class OrderController extends Controller
{
    public function productOrder($order_id)
    {
        // dd($order_id);
        $requestOrderDdata = new RequestOrderData();
        $requestOrder = $requestOrderDdata->orderCassa($order_id);

        $url = Option::getOption('endpoint_meteora');

        $order = $requestOrder['data']['order'];
        $order_item = $requestOrder['data']['order_items'];

        $orderCombArray = [];
        foreach ($order_item as $orderComb) {
            $orderCombArray[] = [
                'site_id' => $orderComb['site_id'],
                'archive_id' => $orderComb['archive_id'],
                'product_id' => $orderComb['product_id'],
                'id_order' => $order['id'],
                'id_order_product_combination' => $orderComb['id_order_product_combination'],
                'id_comb' => $orderComb['id_comb'],
                'checbox_comb_Id' => $orderComb['checbox_comb_Id'],
                'sku' => $orderComb['sku'] ?? '',
                'name_product' => $orderComb['name_product'] ?? '',
                'image' => $orderComb['image'] ?? '',
                'default_photo' => $orderComb['default_photo'] ?? '',
                'first_photo' => $orderComb['first_photo'] ?? '',
                'combination' => $orderComb['combination'] ?? '',
                'ean13' => $orderComb['ean13'] ?? '',
                'iva_prod' => $orderComb['iva_prod'] ?? 22,
                'net_selling_price' => $orderComb['net_selling_price'] ?? '',
                'gross_selling_price' => $orderComb['gross_selling_price'] ?? '',
                'gross_selling_price_discount' => $orderComb['gross_selling_price_discount'] ?? '',
                'price_order_single' => $orderComb['price_order_single'] ?? '',
                'net_selling_price_def' => $orderComb['net_selling_price_def'] ?? '',
                'gross_selling_price_def' => $orderComb['gross_selling_price_def'] ?? '',
                'net_selling_price_var' => $orderComb['net_selling_price_var'] ?? '',
                'gross_selling_price_var' => $orderComb['gross_selling_price_var'] ?? '',
                'gross_selling_price_var_sale' => $orderComb['gross_selling_price_var_sale'] ?? '',
                'stock_order_single' => $orderComb['stock_order_single'] ?? '',
                'product_combination_id' => $orderComb['product_combination_id'] ?? '',
                'stock_variation_id' => $orderComb['stock_variation_id'] ?? '',
                'discount' => $orderComb['discount'] ?? '',
                'list_price_id' => $orderComb['list_price_id'] ?? ''
            ];
        }

        $totalSubGross = collect($orderCombArray)->sum(function ($item) {
            $grossSellingPrice = $item['gross_selling_price_discount'] ? $item['gross_selling_price_discount'] : $item['gross_selling_price'];
            $grossSellingPriceVar = $item['gross_selling_price_var_sale'] ? $item['gross_selling_price_var_sale'] : $item['gross_selling_price_var'];
            $qty = (int) $item['stock_order_single'];
            $price = (float) ($grossSellingPriceVar ?: $grossSellingPrice);
            return $price * $qty;
        });

        $totalDiscountGross = collect($orderCombArray)->sum(function ($item) {
            $grossSellingPrice = $item['gross_selling_price_discount'] ? $item['gross_selling_price_discount'] : $item['gross_selling_price'];
            $grossSellingPriceVar = $item['gross_selling_price_var_sale'] ? $item['gross_selling_price_var_sale'] : $item['gross_selling_price_var'];
            $qty = (int) $item['stock_order_single'];
            $price = (float) ($grossSellingPriceVar ?: $grossSellingPrice);
            $discountPercent = isset($item['discount']) ? (float) $item['discount'] : 0;
            $discount = ($discountPercent / 100) * $price;
            return $discount * $qty;
        });

        $totalGross = collect($orderCombArray)->sum(function ($item) {
            $grossSellingPrice = $item['gross_selling_price_discount'] ? $item['gross_selling_price_discount'] : $item['gross_selling_price'];
            $grossSellingPriceVar = $item['gross_selling_price_var_sale'] ? $item['gross_selling_price_var_sale'] : $item['gross_selling_price_var'];
            $qty = (int) $item['stock_order_single'];
            $price = (float) ($grossSellingPriceVar ?: $grossSellingPrice);
            $discountPercent = isset($item['discount']) ? (float) $item['discount'] : 0;
            $discount = ($discountPercent / 100) * $price;
            return ($price - $discount) * $qty;
        });
        // dd($orderCombArray);
        return DataTables::of(collect($orderCombArray))
            ->addColumn('name_product', function ($orderCombArray) {
                $nameProduct = Str::limit($orderCombArray['name_product'], 45);
                if (!isset($orderCombArray['image'])) {
                    $photoUrl = url('/default_image/not_available_200x200.png');
                } else {
                    $photoUrl = $orderCombArray['image'];
                }
                // Genera immagine o placeholder
                $photo = $photoUrl
                    ? '<img src="' . $photoUrl . '" class="rounded-2 me-3 border" style="width:60px;height:60px;object-fit:cover;">'
                    : '<div class="rounded-2 bg-light d-flex align-items-center justify-content-center me-3 border" style="width:60px;height:60px;">
             <i class="bi bi-card-image text-secondary fs-4"></i>
                </div>';

                // Testo titolo e combinazione
                $title = '
                <div>
                    <div class="fw-classic text-dark">' . e($orderCombArray['sku']) . '</div>
                    <div class="fw-bold text-dark">' . $nameProduct . '</div>
                    <div class="text-muted small">' . e(str_replace(['[', '"', ']'], ' ', $orderCombArray['combination'])) . '</div>
                </div>
            ';

                return '<div class="d-flex align-items-center">' . $photo . $title . '</div>';
            })
            ->addColumn('quantity_order', function ($orderCombArray) use ($order) {
                if ($order['read'] == 1) {
                    $response = '<input disabled type="number" class="quantity_order_client form-control form-control-sm" min="0" name="quantity_order_client[]" value="' . $orderCombArray['stock_order_single'] . '">
                     <input class="product_comb_id" type="hidden" name="product_comb_id[]" value="' . $orderCombArray['product_combination_id'] . '">
                     <input class="stock_variation_id" type="hidden" name="stock_variation_id[]" value="' . $orderCombArray['stock_variation_id'] . '">';
                } else {
                    $response = '<input type="number" class="quantity_order_client form-control form-control-sm" min="0" name="quantity_order_client[]" value="' . $orderCombArray['stock_order_single'] . '">
                     <input class="product_comb_id" type="hidden" name="product_comb_id[]" value="' . $orderCombArray['product_combination_id'] . '">
                     <input class="stock_variation_id" type="hidden" name="stock_variation_id[]" value="' . $orderCombArray['stock_variation_id'] . '">';
                }

                return $response;
            })
            //Prezzo vendita singolo lordo
            ->addColumn('gross_selling_price_var', function ($orderCombArray) {
                if ($orderCombArray['gross_selling_price_var']) {
                    if ($orderCombArray['gross_selling_price_var_sale']) {
                        return number_format((float) $orderCombArray['gross_selling_price_var_sale'], 2);
                    } else {
                        return number_format((float) $orderCombArray['gross_selling_price_var'], 2);
                    }
                } else {
                    if ($orderCombArray['gross_selling_price_discount']) {
                        return number_format((float) $orderCombArray['gross_selling_price_discount'], 2);
                    } else {
                        return number_format((float) $orderCombArray['gross_selling_price'], 2);
                    }
                }
            })
            //Prezzo vendita lordo totale
            ->addColumn('gross_selling_price_total', function ($orderCombArray) {
                if ($orderCombArray['discount']) {
                    if ($orderCombArray['gross_selling_price_var'] > 0) {
                        if ($orderCombArray['gross_selling_price_var_sale']) {
                            $resultDiscount = ($orderCombArray['discount'] / 100) * $orderCombArray['gross_selling_price_var_sale'];
                        } else {
                            $resultDiscount = ($orderCombArray['discount'] / 100) * $orderCombArray['gross_selling_price_var'];
                        }
                    } else {
                        if ($orderCombArray['gross_selling_price_discount']) {
                            $resultDiscount = ($orderCombArray['discount'] / 100) * $orderCombArray['gross_selling_price_discount'];
                        } else {
                            $resultDiscount = ($orderCombArray['discount'] / 100) * $orderCombArray['gross_selling_price'];
                        }
                    }
                    $orderPriceQtyGross = $orderCombArray['gross_selling_price_discount'] ? $orderCombArray['gross_selling_price_discount'] : $orderCombArray['gross_selling_price'];
                    $orderPriceQtyGross = ((float) $orderPriceQtyGross - (float) $resultDiscount) * $orderCombArray['stock_order_single'];

                    $orderPriceQtyGrossVar = $orderCombArray['gross_selling_price_var_sale'] ? $orderCombArray['gross_selling_price_var_sale'] : $orderCombArray['gross_selling_price_var'];
                    $orderPriceQtyGrossVar = ((float) $orderPriceQtyGrossVar - (float) $resultDiscount) * intval($orderCombArray['stock_order_single']);
                } else {
                    $orderPriceQtyGross = $orderCombArray['gross_selling_price_discount'] ? $orderCombArray['gross_selling_price_discount'] : $orderCombArray['gross_selling_price'];
                    $orderPriceQtyGross = (float) $orderPriceQtyGross * $orderCombArray['stock_order_single'];

                    $orderPriceQtyGrossVar = $orderCombArray['gross_selling_price_var_sale'] ? $orderCombArray['gross_selling_price_var_sale'] : $orderCombArray['gross_selling_price_var'];
                    $orderPriceQtyGrossVar = (float) $orderPriceQtyGrossVar * intval($orderCombArray['stock_order_single']);
                }

                if ($orderPriceQtyGrossVar > 0) {
                    return number_format((float) $orderPriceQtyGrossVar, 2);
                } else {
                    return number_format((float) $orderPriceQtyGross, 2);
                }

            })
            ->addColumn('delete_prod_comb', function ($orderCombArray) use ($order) {
                $disabled = ($order['read'] == 1) ? 'disabled' : '';
                return '
                    <form class="form_product_single">
                        <input type="hidden" name="order_id" value="' . $orderCombArray['id_order'] . '">
                        <input type="hidden" name="prod_combination_delete_id" value="' . $orderCombArray['product_combination_id'] . '">
                        <input type="hidden" name="stock_variation_id" value="' . $orderCombArray['stock_variation_id'] . '">
                        <div class="col-6" style="display: flex;">
                            <button type="submit" ' . $disabled . ' class="stockIdVariation btn btn-danger btn-sm btncustom"
                                    style="border-radius: 5px;">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    </form>';
            })
            ->addColumn('discount', function ($orderCombArray) use ($order) {
                $disabled = ($order['read'] == 1) ? 'disabled' : '';
                return '
                    <div class="input-group input-group-sm">
                        <input ' . $disabled . ' type="text" class="discount form-control"
                            name="discount[]"
                            value="' . $orderCombArray['discount'] . '">
                        <span class="input-group-text">%</span>
                    </div>';
            })
            ->addColumn('discount_value', function ($orderCombArray) {
                return $orderCombArray['discount'];
            })
            ->rawColumns(['name_product', 'checbox_comb_Id', 'quantity_order', 'delete_prod_comb', 'net_selling_price', 'discount', 'discount_value'])
            ->with([
                'totalSubGross' => number_format($totalSubGross, 2),
                'totalDiscountGross' => number_format($totalDiscountGross, 2),
                'totalGross' => number_format($totalGross, 2),
            ])
            ->make(true);
    }

    public function storeOrderCassa(Request $request)
    {
        $requestOrderData = new RequestOrderData();
        $result = $requestOrderData->storeOrderCassa(
            $request->archive_id,
            $request->cassa_id,
        );

        $order_id = $result['data']['order_id'];
        return redirect()->route('cassa.show.order', ['order_id' => $order_id]);
    }

    public function storeOrdeProductCassa(Request $request)
    {
        // dd($request->order_id, $request->product_combination_id, $request->product_combination_qty);
        $requestOrderData = new RequestOrderData();
        return $requestOrderData->storeProductOrder(
            $request->order_id,
            $request->product_combination_id,
            $request->product_combination_qty
        );
    }

    public function updateOrderCassaIncassa(Request $request)
    {
        try {
            $requestOrderData = new RequestOrderData();
            $result = $requestOrderData->updateOrderCassaIncassa(
                $request->order_id
            );

            try {
                $scontrino = new ScontrinoController();
                $scontrino->stampaScontrino($request->order_id);
            } catch (\Throwable $th) {
                //throw $th;
            }


            return $result;
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function updateOrderProductQty(Request $request)
    {
        $requestOrderData = new RequestOrderData();
        return $requestOrderData->updateOrderProductQty(
            $request->order_id
        );
    }

    public function updateOrderProductDiscount(Request $request)
    {
        $requestOrderData = new RequestOrderData();
        return $requestOrderData->updateOrderProductDiscount(
            $request->order_id
        );
    }

    public function deleteProductOrderCassa(Request $request)
    {
        $requestOrderData = new RequestOrderData();
        return $requestOrderData->deleteProductOrderCassa();
    }
}
