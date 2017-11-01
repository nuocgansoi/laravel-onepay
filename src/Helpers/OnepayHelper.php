<?php
/**
 * Created by IntelliJ IDEA.
 * User: nuocgansoi
 * Date: 11/1/2017
 * Time: 4:50 PM
 */

namespace NuocGanSoi\LaravelOnepay\Helpers;

class OnepayHelper
{
    public function __construct()
    {
        //  Check order configs
        $orderConfigs = [
            'model',
            'customer_id',
            'item_id',
            'status',
            'status.attribute',
            'status.pending',
            'status.paid',
            'status.canceled',
            'status.rejected',
        ];

        foreach ($orderConfigs as $orderConfig) {
            if (!config("onepay.order.{$orderConfig}")) {
                $this->throwError('order ' . $orderConfig);
            }
        }

        //  Check shop configs
        $itemConfigs = [
            'model',
            'price',
        ];

        $shop = array_keys(config('onepay.shop'));
        if (!count($shop)) {
            $this->throwError('shop');
        }

        foreach ($shop as $item) {
            foreach ($itemConfigs as $itemConfig) {
                if (!config("onepay.shop.{$item}.{$itemConfig}")) {
                    $this->throwError("shop {$item} {$itemConfig}");
                }
            }
        }
    }

    private function throwError($attribute)
    {
        abort(\Illuminate\Http\Response::HTTP_FAILED_DEPENDENCY, "Check your config: {$attribute}!!!");
    }

    /**
     * @param $model
     * @return \Illuminate\Foundation\Application|mixed|null
     */
    public function get_shop_instance($model)
    {
        $model = strtolower($model);
        $class = config("onepay.shop.{$model}.model");
        if (!$class) $this->throwError($model);

        return app($class);
    }

    /**
     * @param $model
     * @return \Illuminate\Config\Repository|mixed
     */
    public function get_price_attribute($model)
    {
        $model = strtolower($model);
        $priceAttr = config("onepay.shop.{$model}.price");
        if (!$priceAttr) $this->throwError($model);

        return $priceAttr;
    }

    /**
     * @param $stringHashData
     * @return string
     */
    public function secure_hash_encode($stringHashData)
    {
        return strtoupper(hash_hmac('SHA256', $stringHashData, pack('H*', config('onepay.secure_secret'))));
    }

    /**
     * @param $item
     * @return null
     */
    public function get_price($item)
    {
        $model = strtolower(class_basename($item));
        $attr = $this->get_price_attribute($model);

        return $item->{$attr};
    }

    public function price_2_amount($price)
    {
        return $price * config('onepay.amount_exchange');
    }

    /**
     * @return \Illuminate\Foundation\Application|mixed|null
     * @internal param $model
     */
    public function get_order_instance()
    {
        return app(config("onepay.order.model"));
    }

    /**
     * @param $user
     * @param $item
     * @return mixed
     */
    public function create_order($user, $item)
    {
        $statusAttr = config('onepay.order.status.attribute');
        $customerIdAttr = config('onepay.order.customer_id');
        $itemIdAttr = config('onepay.order.item_id');
        $orderStatusPending = config('onepay.order.status.pending');

        return $this->get_order_instance()->create([
            $statusAttr => $orderStatusPending,
            $customerIdAttr => $user->id,
            $itemIdAttr => $item->id,
        ]);
    }

}