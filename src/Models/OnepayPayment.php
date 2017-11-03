<?php

namespace NuocGanSoi\LaravelOnepay\Models;

use Illuminate\Database\Eloquent\Model;

class OnepayPayment extends Model
{
    const STATUS_PENDING = 1;
    const STATUS_PROCESSING = 2;
    const STATUS_PAID = 3;
    const STATUS_REJECTED = 4;
    const STATUS_CANCELED = 5;
    const STATUS_REFUNDED = 9;

    protected $guarded = ['id'];

    public function getOrder()
    {
        $orderInstance = app($this->order_type);

        return $orderInstance ? $orderInstance->where('id', $this->order_id)->first() : null;
    }

    public function getItem()
    {
        $itemInstance = app($this->item_type);

        return $itemInstance ? $itemInstance->where('id', $this->item_id)->first() : null;
    }

    public static function makeHashData($model, $amount, $ticketNo, $orderInfo = null)
    {
        $merchTxnRef = 'ONEPAY_' . $ticketNo . round(microtime(true));
        $orderInfo = $orderInfo ?? $merchTxnRef;

        $hashData = [
            'vpc_AccessCode' => config('onepay.access_code'),
            'vpc_Currency' => config('onepay.currency'),
            'vpc_Command' => config('onepay.command'),
            'vpc_Locale' => config('onepay.locale'),
            'vpc_Merchant' => config('onepay.merchant_id'),
            'vpc_ReturnURL' => config('onepay.return_url') . '/' . strtolower($model),
            'vpc_Version' => config('onepay.version'),
            'vpc_Amount' => $amount,
            'vpc_MerchTxnRef' => $merchTxnRef,
            'vpc_OrderInfo' => $orderInfo,
            'vpc_TicketNo' => $ticketNo,
        ];
        ksort($hashData);

        return $hashData;
    }

    public static function createFromHashData($user, $item, $order, $hashData, $secureHash, $url)
    {
        return static::create([
            'order_type' => get_class($order),
            'order_id' => $order->id,
            'user_id' => $user->id,
            'item_type' => get_class($item),
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

    public static function getStatusFromResponseCode($responseCode)
    {
        switch ($responseCode) {
            case "0" :
                $status = static::STATUS_PAID;
                break;
            case "1" :
                $status = static::STATUS_REJECTED;
                break;
            default :
                $status = static::STATUS_CANCELED;
        }

        return $status;
    }
}
