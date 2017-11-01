<?php

namespace NuocGanSoi\LaravelOnepay\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use NuocGanSoi\LaravelOnepay\Models\OnepayPayment;
use NuocGanSoi\LaravelOnepay\Models\OnepayResult;

class OnepayController extends Controller
{
    use CanCreateOnepayResult;

    public function __construct()
    {
        $orderConfig = [
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

        foreach ($orderConfig as $attribute) {
            if (!config("onepay.order.{$attribute}")) {
                abort(Response::HTTP_FAILED_DEPENDENCY, 'Check your order config: ' . $attribute);
            }
        }
    }

    public function shop($model)
    {
        $shopInstance = get_shop_instance($model);
        if (!$shopInstance) return redirect('/');

        $items = $shopInstance->all();

        return view('onepay::shop', compact('items'));
    }

    public function pay(Request $request, $model, $itemId)
    {
        //  Validate request
        $shopInstance = get_shop_instance($model);
        if (!$shopInstance) return redirect('/');

        $item = $shopInstance->find($itemId);
        if (!$item) return redirect('/');

        //  Make hash data
        $price = get_price($item);
        if (!$price) return abort(Response::HTTP_FAILED_DEPENDENCY, "Check your config price of {$model}!!!");

        $amount = price_2_amount($price);
        $ticketNo = $request->ip();
        $hashData = OnepayPayment::makeHashData($model, $amount, $ticketNo, $request->get('order_info'));

        //  Encode secure hash
        $stringHashData = '';
        $url = config('onepay.url');
        $url .= '?Title=' . urlencode(config('onepay.title'));
        foreach ($hashData as $key => $value) {
            $url .= '&' . urlencode($key) . '=' . urlencode($value);
            $stringHashData .= $key . '=' . $value . '&';
        }
        $stringHashData = trim($stringHashData, '&');
        $secureHash = secure_hash_encode($stringHashData);

        $url .= '&vpc_SecureHash=' . $secureHash;

        //  Create order record
        $order = create_order($request->user(), $item);
        if (!$order) return abort(Response::HTTP_FAILED_DEPENDENCY, 'Can not create order, check your order config.');

        //  Save payment information to database
        OnepayPayment::createFromHashData($request->user(), $item, $order, $hashData, $secureHash, $url);

        return redirect($url);
    }

    public function result(Request $request, $model)
    {
        //  Validate request
        /** @var OnepayPayment $onepayPayment */
        $onepayPayment = OnepayPayment::where('merch_txn_ref', $request->get('vpc_MerchTxnRef'))->first();
        if (!$onepayPayment) return response('Invalid payment, check vpc_MerchTxnRef', Response::HTTP_BAD_REQUEST);

        OnepayResult::createFromRequest($request);

        $validator = $this->validateOnepayResult($request);
        if ($validator['status']) {
            $onepayPayment->update([
                'status' => $validator['status'],
            ]);
        }

        $order = $onepayPayment->getOrder();
        if ($order && $orderStatus = $validator['order_status']) {
            $order->update([
                'status' => $orderStatus,
            ]);
        }

        $view = $validator['success'] ? 'onepay::success' : 'onepay::failed';

        return view($view, [
            'model' => $model,
            'message' => $validator['message'],
            'response' => $request->all(),
        ]);
    }
}
