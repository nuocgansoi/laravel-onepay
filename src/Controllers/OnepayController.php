<?php

namespace NuocGanSoi\LaravelOnepay\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use NuocGanSoi\LaravelOnepay\Models\OnepayIpn;
use NuocGanSoi\LaravelOnepay\Models\OnepayPayment;
use NuocGanSoi\LaravelOnepay\Models\OnepayResult;

class OnepayController extends Controller
{
    use CanPayItem, CanCreateOnepayResult, HasOnepayIpn;

    public function shop($model)
    {
        $shopInstance = onepay_helper()->get_shop_instance($model);
        if (!$shopInstance) return redirect('/');

        $items = $shopInstance->all();

        return view('onepay::shop', compact('items'));
    }

    public function pay(Request $request, $model, $itemId)
    {
        $validator = $this->validatePayRequest($model, $itemId);
        if (!$validator['success']) {
            return redirect($validator['redirect']);
        }

        $item = $validator['item'];
        $price = onepay_helper()->get_price($item);
        if (!$price) return abort(Response::HTTP_FAILED_DEPENDENCY, "Check your config price of {$model}!!!");

        //  Make hash data
        $amount = onepay_helper()->price_2_amount($price);
        $ticketNo = $request->ip();
        $hashData = OnepayPayment::makeHashData($model, $amount, $ticketNo, $request->get('order_info'));

        list($url, $secureHash) = $this->makePayUrl($hashData);

        //  Create order record
        $orderCreator = onepay_helper()->create_or_update_order($request->user(), $item);
        if (!$orderCreator['success']) {
            return view('onepay::rejected', [
                'message' => $orderCreator['message'],
                'rejectedCode' => $orderCreator['rejected_code'],
                'order' => $orderCreator['order'],
            ]);
        }

        //  Save payment information to database
        OnepayPayment::createFromHashData($request->user(), $item, $orderCreator['order'], $hashData, $secureHash, $url);

        return redirect($url);
    }

    public function result(Request $request, $model)
    {
        $merchTxnRef = $request->get('vpc_MerchTxnRef');

        //  Validate request
        /** @var OnepayPayment $onepayPayment */
        $onepayPayment = OnepayPayment::where('merch_txn_ref', $merchTxnRef)->first();
        if (!$onepayPayment) return response('Invalid payment, check vpc_MerchTxnRef', Response::HTTP_BAD_REQUEST);


        $validator = $this->validateResultRequest($request, $model);
        if ($validator['status']) {
            $onepayPayment->update([
                'status' => $validator['status'],
            ]);
        }

        $availableOnepayResult = OnepayResult::where('merch_txn_ref', $merchTxnRef)->first();
        $order = $onepayPayment->getOrder();
        if (!$availableOnepayResult && $order && $orderStatus = $validator['order_status']) {
            $order->update([
                'status' => $orderStatus,
            ]);
        }
        OnepayResult::createFromRequest($request);

        $view = $validator['success'] ? 'onepay::success' : 'onepay::failed';

        return view($view, [
            'model' => $model,
            'message' => $validator['message'],
            'response' => $request->all(),
        ]);
    }

    public function ipn(Request $request)
    {
        $merchTxnRef = $request->get('vpc_MerchTxnRef');

        //  Validate request
        /** @var OnepayPayment $onepayPayment */
        $onepayPayment = OnepayPayment::where('merch_txn_ref', $merchTxnRef)->first();
        if (!$onepayPayment) return response('Invalid payment, check vpc_MerchTxnRef', Response::HTTP_BAD_REQUEST);

        $item = $onepayPayment->getItem();
        if (!$item) return abort(Response::HTTP_BAD_REQUEST, 'Item is null, check onepayPayment data');

        $validator = $this->validateIpnRequest($request, $item);
        if ($validator['status']) {
            $onepayPayment->update([
                'status' => $validator['status'],
            ]);
        }

        $availableOnepayIpn = OnepayIpn::where('merch_txn_ref', $merchTxnRef)->first();
        if (!$availableOnepayIpn) {
            $this->ipnUpdateOrderStatus($onepayPayment->getOrder(), $validator['order_status']);
        }
        OnepayIpn::createFromRequest($request);

        $responseCode = $validator['order_status'] ? 1 : 0;
        $desc = $validator['success'] ? 'success' : 'fail';

        return "responsecode={$responseCode}&desc=confirm-{$desc}";
    }
}
