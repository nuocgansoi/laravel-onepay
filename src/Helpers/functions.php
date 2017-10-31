<?php
/**
 * Created by IntelliJ IDEA.
 * User: nuocgansoi
 * Date: 10/31/2017
 * Time: 2:06 PM
 */

/**
 * @param $model
 * @return \Illuminate\Foundation\Application|mixed|null
 */
function getShopInstance($model)
{
    $model = strtolower($model);
    $class = config("onepay.shop.{$model}.model");

    return $class ? app($class) : null;
}

/**
 * @param $model
 * @return \Illuminate\Config\Repository|mixed
 */
function getPriceAttribute($model)
{
    $model = strtolower($model);

    return config("onepay.shop.{$model}.price");
}

/**
 * @param $stringHashData
 * @return string
 */
function secureHashEncode($stringHashData)
{
    return strtoupper(hash_hmac('SHA256', $stringHashData, pack('H*', config('onepay.secure_secret'))));
}

/**
 * @param $item
 * @return null
 */
function getPrice($item)
{
    $model = strtolower(class_basename($item));
    $attr = getPriceAttribute($model);

    return $attr ? $item->{$attr} : null;
}

function price2Amount($price)
{
    return $price * config('onepay.amount_exchange');
}
