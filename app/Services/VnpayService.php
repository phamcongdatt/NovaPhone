<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Http\Request;

/**
 * Tich hop cong thanh toan VNPay (sandbox/production).
 *
 * Luong:
 *  1. createPaymentUrl(): tao URL redirect sang VNPay kem chu ky vnp_SecureHash.
 *  2. validateReturn(): xac thuc du lieu VNPay tra ve (HMAC-SHA512).
 */
class VnpayService
{
    protected string $tmnCode;
    protected string $hashSecret;
    protected string $baseUrl;
    protected string $returnUrl;

    public function __construct()
    {
        $this->tmnCode    = (string) config('services.vnpay.tmn_code');
        $this->hashSecret = (string) config('services.vnpay.hash_secret');
        $this->baseUrl    = (string) config('services.vnpay.url');
        $this->returnUrl  = (string) config('services.vnpay.return_url');
    }

    /**
     * Tao URL thanh toan VNPay cho mot don hang.
     */
    public function createPaymentUrl(Order $order, string $ipAddr): string
    {
        $createDate = now('Asia/Ho_Chi_Minh');

        $inputData = [
            'vnp_Version'    => '2.1.0',
            'vnp_Command'    => 'pay',
            'vnp_TmnCode'    => $this->tmnCode,
            // VNPay yeu cau so tien * 100 va khong co phan thap phan
            'vnp_Amount'     => (int) round($order->total_amount * 100),
            'vnp_CurrCode'   => 'VND',
            'vnp_TxnRef'     => $order->order_code,
            'vnp_OrderInfo'  => 'Thanh toan don hang ' . $order->order_code,
            'vnp_OrderType'  => 'other',
            'vnp_Locale'     => 'vn',
            'vnp_ReturnUrl'  => $this->returnUrl,
            'vnp_IpAddr'     => $ipAddr,
            'vnp_CreateDate' => $createDate->format('YmdHis'),
            'vnp_ExpireDate' => $createDate->copy()->addMinutes(15)->format('YmdHis'),
        ];

        ksort($inputData);

        $hashData = [];
        $query    = [];
        foreach ($inputData as $key => $value) {
            $hashData[] = urlencode($key) . '=' . urlencode($value);
            $query[]    = urlencode($key) . '=' . urlencode($value);
        }

        $hashString  = implode('&', $hashData);
        $secureHash  = hash_hmac('sha512', $hashString, $this->hashSecret);

        return $this->baseUrl . '?' . implode('&', $query) . '&vnp_SecureHash=' . $secureHash;
    }

    /**
     * Xac thuc chu ky du lieu VNPay tra ve.
     */
    public function validateReturn(Request $request): bool
    {
        $data = $request->query();

        $secureHash = $data['vnp_SecureHash'] ?? '';
        unset($data['vnp_SecureHash'], $data['vnp_SecureHashType']);

        ksort($data);

        $hashData = [];
        foreach ($data as $key => $value) {
            $hashData[] = urlencode($key) . '=' . urlencode($value);
        }

        $calculated = hash_hmac('sha512', implode('&', $hashData), $this->hashSecret);

        return hash_equals($calculated, (string) $secureHash);
    }

    /**
     * Giao dich thanh cong khi vnp_ResponseCode = 00 va vnp_TransactionStatus = 00.
     */
    public function isSuccessful(Request $request): bool
    {
        return $request->query('vnp_ResponseCode') === '00'
            && $request->query('vnp_TransactionStatus') === '00';
    }
}
