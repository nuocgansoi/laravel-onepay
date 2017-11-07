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
    private function validateIpnRequest(Request $request)
    {
        return $this->validateResultRequest($request);
    }

    private function parseIpn(Request $request, $item)
    {
        $model = strtolower(class_basename($item));

        return $this->parseResult($request, $model);
    }
}
