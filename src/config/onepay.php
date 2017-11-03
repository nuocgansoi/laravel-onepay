<?php
/**
 * Created by IntelliJ IDEA.
 * User: nuocgansoi
 * Date: 10/30/2017
 * Time: 3:12 PM
 */

return [
    'version' => env('ONEPAY_VERSION', 2),
    'do_url' => env('ONEPAY_DO_URL', 'https://mtf.onepay.vn/onecomm-pay/vpc.op'),
    'return_url' => env('ONEPAY_RETURN_URL', 'http://localhost/return'),
    'ipn_url' => env('ONEPAY_IPN_URL', 'http://localhost/ipn'),
    'merchant_id' => env('ONEPAY_MERCHANT_ID', 'ONEPAY'),
    'access_code' => env('ONEPAY_ACCESS_CODE', 'D67342C2'),
    'secure_secret' => env('ONEPAY_SECURE_SECRET', 'A3EFDFABA8653DF2342E8DAC29B51AF0'),
    'command' => env('ONEPAY_COMMAND', 'pay'),
    'currency' => env('ONEPAY_CURRENCY', 'VND'),
    'locale' => env('ONEPAY_LOCALE', 'vn'),
    'title' => env('ONEPAY_TITLE', 'OnePay Gate'),
    'amount_exchange' => env('ONEPAY_AMOUNT_EXCHANGE', 100),
    'shop' => [
        'book' => [
            'model' => App\Book::class,
            'price' => 'price',
            'order' => [
                'model' => App\BookOrder::class,
                'customer_id' => 'user_id',
                'item_id' => 'book_id',
                'status' => [
                    'attribute' => 'status',
                    'waiting' => App\BookOrder::STATUS_WAITING,
                    'pending' => App\BookOrder::STATUS_PENDING,
                    'paid' => App\BookOrder::STATUS_PAID,
                    'canceled' => App\BookOrder::STATUS_CANCELED,
                    'rejected' => App\BookOrder::STATUS_REJECTED,
                ],
            ],
        ],
    ],
];
