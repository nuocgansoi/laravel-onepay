<?php

namespace NuocGanSoi\LaravelOnepay\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class OnepayResult extends Model
{
    protected $guarded = ['id'];

    public static function createFromRequest(Request $request)
    {
        return static::create([
            'addition_data' => $request->get('vpc_AdditionData'),
            'amount' => $request->get('vpc_Amount'),
            'command' => $request->get('vpc_Command'),
            'currency_code' => $request->get('vpc_CurrencyCode'),
            'locale' => $request->get('vpc_Locale'),
            'merch_txn_ref' => $request->get('vpc_MerchTxnRef'),
            'merchant' => $request->get('vpc_Merchant'),
            'order_info' => $request->get('vpc_OrderInfo'),
            'transaction_no' => $request->get('vpc_TransactionNo'),
            'txn_response_code' => $request->get('vpc_TxnResponseCode'),
            'version' => $request->get('vpc_Version'),
            'message' => $request->get('vcp_Message'),
            'secure_hash' => $request->get('vpc_SecureHash'),
        ]);
    }
}
