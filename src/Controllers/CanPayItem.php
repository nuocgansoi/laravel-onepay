<?php
/**
 * Created by IntelliJ IDEA.
 * User: nuocgansoi
 * Date: 11/3/2017
 * Time: 2:40 PM
 */

namespace NuocGanSoi\LaravelOnepay\Controllers;


trait CanPayItem
{
    /**
     * @param $model
     * @param $itemId
     * @return array
     */
    private function validatePayRequest($model, $itemId)
    {
        $shopInstance = onepay_helper()->get_shop_instance($model);
        if (!$shopInstance) return [
            'success' => false,
            'redirect' => '/',
        ];

        $item = $shopInstance->find($itemId);
        if (!$item) return [
            'success' => false,
            'redirect' => '/',
        ];

        return [
            'success' => true,
            'item' => $item,
        ];
    }

    /**
     * @param $hashData
     * @return array
     */
    private function makePayUrl($hashData)
    {
        $stringHashData = '';
        $url = config('onepay.do_url');
        $url .= '?Title=' . urlencode(config('onepay.title'));
        foreach ($hashData as $key => $value) {
            $url .= '&' . urlencode($key) . '=' . urlencode($value);
            $stringHashData .= $key . '=' . $value . '&';
        }
        $stringHashData = trim($stringHashData, '&');
        $secureHash = onepay_helper()->secure_hash_encode($stringHashData);

        $url .= '&vpc_SecureHash=' . $secureHash;

        return [$url, $secureHash];
    }
}
