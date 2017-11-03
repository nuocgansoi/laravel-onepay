# laravel-onepay

## publish this package and change config
* php artisan migrate
* php artisan vendor:publish

## Order status
```php
const STATUS_PENDING = 1;
const STATUS_PROCESSING = 2;
const STATUS_PAID = 3;
const STATUS_REJECTED = 4;
const STATUS_CANCELED = 5;
const STATUS_REFUNDED = 9;
```

## Add to .env:
```
ONEPAY_VERSION=2
ONEPAY_MERCHANT_ID=ONEPAY
ONEPAY_ACCESS_CODE=D67342C2
ONEPAY_SECURE_SECRET=A3EFDFABA8653DF2342E8DAC29B51AF0
ONEPAY_COMMAND=pay
ONEPAY_CURRENCY=VND
ONEPAY_LOCALE=vn
ONEPAY_TITLE="OnePay Gate"
ONEPAY_AMOUNT_EXCHANGE=100
ONEPAY_DO_URL=https://mtf.onepay.vn/onecomm-pay/vpc.op
ONEPAY_RETURN_URL=http://onepay.dev/onepay/result
ONEPAY_IPN_URL=http://onepay.dev/onepay/ipn
```

## config/onepay.php:
```php
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
                    'pending' => App\BookOrder::STATUS_PENDING,
                    'processing' => App\BookOrder::STATUS_PROCESSING,
                    'paid' => App\BookOrder::STATUS_PAID,
                    'canceled' => App\BookOrder::STATUS_CANCELED,
                    'rejected' => App\BookOrder::STATUS_REJECTED,
                ],
            ],
        ],
    ],
];
```

## Test card
```
Thẻ VCB:
Tên: NGUYEN HONG NHUNG
Số thẻ: 6868682607535021 
Tháng/Năm phát hành: 12/08 
Mã OTP: 123456 
```

## Return in result view
```php
$view = $validator['success'] ? 'onepay::success' : 'onepay::failed';

return view($view, [
    'model' => $model,
    'message' => $validator['message'],
    'response' => $request->all(),
]);
```

## Migrations
```php
Schema::create('onepay_payments', function (Blueprint $table) {
    $table->increments('id');
    $table->unsignedInteger('order_id')->nullable();
    $table->string('order_type')->nullable();
    $table->unsignedInteger('user_id')->nullable();
    $table->string('item_type')->nullable();
    $table->unsignedInteger('item_id')->nullable();
    $table->tinyInteger('status')->default(\NuocGanSoi\LaravelOnepay\Models\OnepayPayment::STATUS_PENDING);
    $table->string('access_code', 8);
    $table->string('currency', 3);
    $table->string('command', 16);
    $table->string('locale', 2);
    $table->string('merchant', 12);
    $table->string('return_url', 64);
    $table->string('version', 2);
    $table->string('amount', 21);
    $table->string('merch_txn_ref', 40)->index();
    $table->string('order_info', 40);
    $table->string('ticket_no', 16);
    $table->string('secure_hash', 64);
    $table->text('url')->nullable();
    $table->timestamps();
});
Schema::create('onepay_results', function (Blueprint $table) {
    $table->increments('id');
    $table->string('addition_data')->nullable();
    $table->string('amount', 21);
    $table->string('command', 16);
    $table->string('currency_code', 3);
    $table->string('locale', 2);
    $table->string('merch_txn_ref', 40)->index();
    $table->string('merchant', 12);
    $table->string('order_info', 40);
    $table->string('transaction_no', 12);
    $table->string('txn_response_code', 64);
    $table->string('version', 2)->nullable();
    $table->string('message', 200)->nullable();
    $table->string('secure_hash', 64);
    $table->text('response')->nullable();
    $table->timestamps();
});
Schema::create('onepay_ipns', function (Blueprint $table) {
    $table->increments('id');
    $table->string('addition_data')->nullable();
    $table->string('amount', 21);
    $table->string('command', 16);
    $table->string('currency_code', 3);
    $table->string('locale', 2);
    $table->string('merch_txn_ref', 40)->index();
    $table->string('merchant', 12);
    $table->string('order_info', 40);
    $table->string('transaction_no', 12);
    $table->string('txn_response_code', 64);
    $table->string('version', 2)->nullable();
    $table->string('message', 200)->nullable();
    $table->string('secure_hash', 64);
    $table->text('response')->nullable();
    $table->timestamps();
});
```
