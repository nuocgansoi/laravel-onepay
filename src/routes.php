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
    Route::get('onepay/pay/{item}', 'OnepayController@pay')->name('onepay.pay');
    Route::get('onepay/shop', 'OnepayController@shop')->name('onepay.shop');
    Route::get('onepay/result', 'OnepayController@result')->name('onepay.result');
});
