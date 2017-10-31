<?php

namespace NuocGanSoi\LaravelOnepay\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use NuocGanSoi\LaravelOnepay\Models\OnepayPayment;
use NuocGanSoi\LaravelOnepay\Models\OnepayResult;

class OnepayController extends Controller
{
    use CanCreateOnepayResult;

    public function shop($model)
    {
        $shopInstance = getShopInstance($model);
        if (!$shopInstance) return redirect('/');

        $items = $shopInstance->all();

        return view('onepay::shop', compact('items'));
    }

    public function pay(Request $request, $model, $itemId)
    {
        //  Validate request
        $shopInstance = getShopInstance($model);
        if (!$shopInstance) return redirect('/');

        $item = $shopInstance->find($itemId);
        if (!$item) return redirect('/');

        //  Make hash data
        $price = getPrice($item);
        if (!$price) return "Check your config price of {$model}!!!";

        $amount = price2Amount($price);
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
        $secureHash = secureHashEncode($stringHashData);

        $url .= '&vpc_SecureHash=' . $secureHash;

        //  Save payment information to database
        OnepayPayment::createFromHashData($request->user(), $item, $hashData, $secureHash, $url);

        return redirect($url);
    }

    public function result(Request $request, $model)
    {
        OnepayResult::createFromRequest($request);

        $validator = $this->validateOnepayResult($request);
        $view = $validator['success'] ? 'onepay::success' : 'onepay::failed';

        return view($view, [
            'model' => $model,
            'message' => $validator['message']
        ]);
    }
}
