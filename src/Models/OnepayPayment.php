<?php

namespace NuocGanSoi\LaravelOnepay\Models;

use Illuminate\Database\Eloquent\Model;

class OnepayPayment extends Model
{
    protected $guarded = ['id'];

    public static function makeHashData($amount, $ticketNo, $orderInfo = null)
    {
        $merchTxnRef = 'ONEPAY_' . round(microtime(true));
        $orderInfo = $orderInfo ?? $merchTxnRef;

        $hashData = [
            'vpc_AccessCode' => config('onepay.access_code'),
            'vpc_Currency' => config('onepay.currency'),
            'vpc_Command' => config('onepay.command'),
            'vpc_Locale' => config('onepay.locale'),
            'vpc_Merchant' => config('onepay.merchant_id'),
            'vpc_ReturnURL' => config('onepay.return_url'),
            'vpc_Version' => config('onepay.version'),
            'vpc_Amount' => $amount,
            'vpc_MerchTxnRef' => $merchTxnRef,
            'vpc_OrderInfo' => $orderInfo,
            'vpc_TicketNo' => $ticketNo,
        ];
        ksort($hashData);

        return $hashData;
    }

    public static function createFromHashData($user, $item, $hashData, $secureHash, $url)
    {
        return static::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'access_code' => $hashData['vpc_AccessCode'],
            'currency' => $hashData['vpc_Currency'],
            'command' => $hashData['vpc_Command'],
            'locale' => $hashData['vpc_Locale'],
            'merchant' => $hashData['vpc_Merchant'],
            'return_url' => $hashData['vpc_ReturnURL'],
            'version' => $hashData['vpc_Version'],
            'amount' => $hashData['vpc_Amount'],
            'merch_txn_ref' => $hashData['vpc_MerchTxnRef'],
            'order_info' => $hashData['vpc_OrderInfo'],
            'ticket_no' => $hashData['vpc_TicketNo'],
            'secure_hash' => $secureHash,
            'url' => $url,
        ]);
    }
}
