<?php

namespace NuocGanSoi\LaravelOnepay\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use NuocGanSoi\LaravelOnepay\Models\OnepayPayment;
use NuocGanSoi\LaravelOnepay\Models\OnepayResult;

class OnepayController extends Controller
{
    public $itemModel;

    use CanCreateOnepayResult;

    public function __construct()
    {
        $this->itemModel = app(config('onepay.items.model'));
    }

    public function shop()
    {
        $items = $this->itemModel->all();
        return view('onepay::shop', compact('items'));
    }

    public function pay(Request $request, $itemId)
    {
        //  Validate request
        $item = $this->itemModel->find($itemId);
        if (!$item) return redirect('/');

        //  Make hash data
        $amount = $item->{config('onepay.items.price')} * 100;
        $ticketNo = $request->ip();
        $hashData = OnepayPayment::makeHashData($amount, $ticketNo);

        //  Encode secure hash
        $stringHashData = '';
        $url = config('onepay.url');
        $url .= '?Title=' . urlencode(config('onepay.title'));
        foreach ($hashData as $key => $value) {
            $url .= '&' . urlencode($key) . '=' . urlencode($value);
            $stringHashData .= $key . '=' . $value . '&';
        }
        $stringHashData = trim($stringHashData, '&');
        $secureHash = $this->secureHashEncode($stringHashData);

        $url .= '&vpc_SecureHash=' . $secureHash;

        //  Save payment information to database
        OnepayPayment::createFromHashData($request->user(), $item, $hashData, $secureHash, $url);

        return redirect($url);
    }

    public function result(Request $request)
    {
        $validator = $this->validateOnepayResult($request);
        if (!$validator['success']) return $validator['message'];

        OnepayResult::createFromRequest($request);

        $message = $validator['message'];
        $items = $this->itemModel->all();

        return view('onepay::result', compact('message', 'items'));
    }

    private function secureHashEncode($stringHashData)
    {
        return strtoupper(hash_hmac('SHA256', $stringHashData, pack('H*', config('onepay.secure_secret'))));
    }
}
