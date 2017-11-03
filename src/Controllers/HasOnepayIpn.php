<?php
/**
 * Created by IntelliJ IDEA.
 * User: nuocgansoi
 * Date: 10/31/2017
 * Time: 9:43 AM
 */

namespace NuocGanSoi\LaravelOnepay\Controllers;


use Illuminate\Http\Request;
use NuocGanSoi\LaravelOnepay\Models\OnepayPayment;

trait HasOnepayIpn
{
    private function ipnUpdateOrderStatus($order, $orderStatus)
    {
        if ($order && $orderStatus) {
            $order->update([
                'status' => $orderStatus,
            ]);
        }
    }

    private function validateIpnRequest(Request $request, $item)
    {
        $model = strtolower(class_basename($item));

        return $this->validateResultRequest($request, $model);
    }
}
