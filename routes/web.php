<?php

use App\Http\Controllers\CassaController;
use App\Http\Controllers\OptionController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');


Route::controller(CassaController::class)->group(function () {
    Route::get('cassa/index', 'index')->name('cassa.index');
    Route::get('cassa/check/code', 'checkCode')->name('cassa.check.code');
    Route::get('cassa/show/order/{order_id}', 'show')->name('cassa.show.order');
    Route::get('/cassa/logout', 'logout')->name('cassa.logout');
});

Route::controller(OptionController::class)->group(function () {
    Route::get('option/index', 'index')->name('option.index');
    Route::post('option/store', 'store')->name('option.store');
});

Route::controller(OrderController::class)->group(function () {
    Route::get('order/{order_id}/product/index', 'productOrder')->name('order.product.index');
    Route::post('order/store/cassa', 'storeOrderCassa')->name('order.store.cassa');
    Route::post('order/product/store', 'storeOrdeProductCassa')->name('order.product.store');
    Route::put('order/{order_id}/product/discount/update', 'updateOrderProductDiscount')->name('order.product.discount.update');
    Route::put('order/{order_id}/product/qty/update', 'updateOrderProductQty')->name('order.product.discount.qty');
    Route::put('order/{order_id}/cassa/update', 'updateOrderCassaIncassa')->name('order.cassa.update');
    Route::delete('order/procut/cassa/delete', 'deleteProductOrderCassa')->name('order.product.cassa.delete');
});

