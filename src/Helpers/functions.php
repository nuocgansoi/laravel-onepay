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
function get_shop_instance($model)
{
    $model = strtolower($model);
    $class = config("onepay.shop.{$model}.model");

    return $class ? app($class) : null;
}

/**
 * @param $model
 * @return \Illuminate\Config\Repository|mixed
 */
function get_price_attribute($model)
{
    $model = strtolower($model);

    return config("onepay.shop.{$model}.price");
}

/**
 * @param $stringHashData
 * @return string
 */
function secure_hash_encode($stringHashData)
{
    return strtoupper(hash_hmac('SHA256', $stringHashData, pack('H*', config('onepay.secure_secret'))));
}

/**
 * @param $item
 * @return null
 */
function get_price($item)
{
    $model = strtolower(class_basename($item));
    $attr = get_price_attribute($model);

    return $attr ? $item->{$attr} : null;
}

function price_2_amount($price)
{
    return $price * config('onepay.amount_exchange');
}

/**
 * @return \Illuminate\Foundation\Application|mixed|null
 * @internal param $model
 */
function get_order_instance()
{
    $class = config("onepay.order.model");

    return $class ? app($class) : null;
}

function create_order($user, $item)
{
    $statusAttr = config('onepay.order.status.attribute');
    $customerIdAttr = config('onepay.order.customer_id');
    $itemIdAttr = config('onepay.order.item_id');
    $orderStatusPending = config('onepay.order.status.pending');

    return get_order_instance()->create([
        $statusAttr => $orderStatusPending,
        $customerIdAttr => $user->id,
        $itemIdAttr => $item->id,
    ]);
}
