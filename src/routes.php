<?php
/**
 * Created by IntelliJ IDEA.
 * User: nuocgansoi
 * Date: 10/30/2017
 * Time: 2:38 PM
 */

Route::group([
    'namespace' => 'NuocGanSoi\LaravelOnepay\Controllers',
    'middleware' => ['web', 'auth'],
], function () {
    Route::get('onepay/shop/{model}', 'OnepayController@shop')->name('onepay.shop');
    Route::get('onepay/pay/{model}/{item}', 'OnepayController@pay')->name('onepay.pay');
    Route::get('onepay/result/{model}', 'OnepayController@result')->name('onepay.result');
    Route::post('onepay/ipn', 'OnepayController@ipn')->name('onepay.ipn');
});
