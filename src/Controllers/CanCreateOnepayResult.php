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

trait CanCreateOnepayResult
{
    private function validateResultRequest(Request $request, $model)
    {
        $response = $request->except(['vpc_SecureHash']);
        ksort($response);
        $stringHashData = '';
        foreach ($response as $key => $value) {
            if ((strlen($value) > 0) && ((substr($key, 0, 4) == "vpc_") || (substr($key, 0, 5) == "user_"))) {
                $stringHashData .= $key . "=" . $value . "&";
            }
        }
        $stringHashData = trim($stringHashData, "&");
        $secureHash = onepay_helper()->secure_hash_encode($stringHashData);
        if ($secureHash != $request->get('vpc_SecureHash')) {
            return [
                'success' => false,
                'message' => 'Invalid Hash',
                'status' => null,
                'order_status' => null,
            ];
        }

        $responseCode = $request->get('vpc_TxnResponseCode');
        if ($responseCode == '0') {
            return [
                'success' => true,
                'message' => $this->getResponseDescription($responseCode),
                'status' => OnepayPayment::STATUS_PAID,
                'order_status' => config("onepay.shop.{$model}.order.status.paid"),
            ];
        }

        $onepayStatus = OnepayPayment::getStatusFromResponseCode($responseCode);
        $orderStatus = $onepayStatus === OnepayPayment::STATUS_REJECTED
            ? config("onepay.shop.{$model}.order.status.rejected")
            : config("onepay.shop.{$model}.order.status.canceled");

        return [
            'success' => false,
            'message' => $this->getResponseDescription($responseCode),
            'status' => $onepayStatus,
            'order_status' => $orderStatus,
        ];
    }

    private function getResponseDescription($responseCode)
    {
        switch ($responseCode) {
            case "0" :
                $result = "Giao dịch thành công - Approved";
                break;
            case "1" :
                $result = "Ngân hàng từ chối giao dịch - Bank Declined";
                break;
            case "3" :
                $result = "Mã đơn vị không tồn tại - Merchant not exist";
                break;
            case "4" :
                $result = "Không đúng access code - Invalid access code";
                break;
            case "5" :
                $result = "Số tiền không hợp lệ - Invalid amount";
                break;
            case "6" :
                $result = "Mã tiền tệ không tồn tại - Invalid currency code";
                break;
            case "7" :
                $result = "Lỗi không xác định - Unspecified Failure ";
                break;
            case "8" :
                $result = "Số thẻ không đúng - Invalid card Number";
                break;
            case "9" :
                $result = "Tên chủ thẻ không đúng - Invalid card name";
                break;
            case "10" :
                $result = "Thẻ hết hạn/Thẻ bị khóa - Expired Card";
                break;
            case "11" :
                $result = "Thẻ chưa đăng ký sử dụng dịch vụ - Card Not Registed Service(internet banking)";
                break;
            case "12" :
                $result = "Ngày phát hành/Hết hạn không đúng - Invalid card date";
                break;
            case "13" :
                $result = "Vượt quá hạn mức thanh toán - Exist Amount";
                break;
            case "21" :
                $result = "Số tiền không đủ để thanh toán - Insufficient fund";
                break;
            case "99" :
                $result = "Người sủ dụng hủy giao dịch - User cancel";
                break;
            default :
                $result = "Giao dịch thất bại - Failured";
        }

        return $result;
    }
}
