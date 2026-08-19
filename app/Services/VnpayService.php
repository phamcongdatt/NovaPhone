<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;

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
        $this->tmnCode = (string) config('services.vnpay.tmn_code');
        $this->hashSecret = (string) config('services.vnpay.hash_secret');
        $this->baseUrl = (string) config('services.vnpay.url');
        $this->returnUrl = (string) config('services.vnpay.return_url');
    }

    /**
     * Tao URL thanh toan VNPay cho mot don hang.
     */
    public function createPaymentUrl(Order $order, string $ipAddr): string
    {
        $createDate = now('Asia/Ho_Chi_Minh');

        $inputData = [
            'vnp_Version' => '2.1.0',
            'vnp_Command' => 'pay',
            'vnp_TmnCode' => $this->tmnCode,
            // VNPay yeu cau so tien * 100 va khong co phan thap phan
            'vnp_Amount' => (int) round($order->total_amount * 100),
            'vnp_CurrCode' => 'VND',
            'vnp_TxnRef' => $order->order_code,
            'vnp_OrderInfo' => 'Thanh toan don hang '.$order->order_code,
            'vnp_OrderType' => 'other',
            'vnp_Locale' => 'vn',
            'vnp_ReturnUrl' => $this->returnUrl,
            'vnp_IpAddr' => $ipAddr,
            'vnp_CreateDate' => $createDate->format('YmdHis'),
            'vnp_ExpireDate' => $createDate->copy()->addMinutes(15)->format('YmdHis'),
        ];

        ksort($inputData);

        $hashData = [];
        $query = [];
        foreach ($inputData as $key => $value) {
            $hashData[] = urlencode($key).'='.urlencode($value);
            $query[] = urlencode($key).'='.urlencode($value);
        }

        $hashString = implode('&', $hashData);
        $secureHash = hash_hmac('sha512', $hashString, $this->hashSecret);

        return $this->baseUrl.'?'.implode('&', $query).'&vnp_SecureHash='.$secureHash;
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
            $hashData[] = urlencode($key).'='.urlencode($value);
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

    /**
     * Sandbox VNPay có thể giữ giao dịch hoàn ở 05 vô thời hạn. Cho phép môi trường
     * local/testing coi 05 là kết quả cuối để chạy trọn luồng nghiệp vụ thử nghiệm.
     * Production luôn chỉ chấp nhận trạng thái 00.
     *
     * @param array<string, mixed> $result
     */
    public function isRefundCompleted(array $result): bool
    {
        $type = (string) ($result['vnp_TransactionType'] ?? '');
        $status = (string) ($result['vnp_TransactionStatus'] ?? '');

        if (! in_array($type, ['02', '03'], true)) {
            return false;
        }

        if ($status === '00') {
            return true;
        }

        $refundHost = strtolower((string) parse_url(
            (string) config('services.vnpay.refund_url'),
            PHP_URL_HOST,
        ));

        return $status === '05'
            && (bool) config('services.vnpay.sandbox_complete_on_status_05', false)
            && app()->environment(['local', 'testing'])
            && $refundHost === 'sandbox.vnpayment.vn';
    }

    /**
     * Hoàn tiền về đúng phương thức thanh toán VNPay gốc.
     *
     * @return array<string, mixed>
     */
    public function refund(Order $order, float $amount, string $requestId, string $createdBy, string $ipAddress): array
    {
        $payment = $order->payments()
            ->where('gateway', 'vnpay')
            ->where('status', 'success')
            ->latest('paid_at')
            ->first();

        if (! $payment || ! $payment->transaction_code) {
            throw new RuntimeException('Không tìm thấy giao dịch VNPay gốc đã thanh toán.');
        }

        if ($amount <= 0 || $amount > (float) $payment->amount) {
            throw new RuntimeException('Số tiền hoàn VNPay không hợp lệ hoặc vượt quá giao dịch gốc.');
        }

        $now = now('Asia/Ho_Chi_Minh');
        $transactionDate = $payment->payload['vnp_PayDate']
            ?? $payment->paid_at?->timezone('Asia/Ho_Chi_Minh')->format('YmdHis');
        if (! $transactionDate) {
            throw new RuntimeException('Giao dịch VNPay gốc thiếu thời gian thanh toán để hoàn tiền.');
        }

        $data = [
            'vnp_RequestId' => preg_replace('/[^A-Za-z0-9]/', '', $requestId),
            'vnp_Version' => '2.1.0',
            'vnp_Command' => 'refund',
            'vnp_TmnCode' => $this->tmnCode,
            'vnp_TransactionType' => abs($amount - (float) $payment->amount) < 0.01 ? '02' : '03',
            'vnp_TxnRef' => $order->order_code,
            'vnp_Amount' => (int) round($amount * 100),
            'vnp_TransactionNo' => $payment->transaction_code,
            'vnp_TransactionDate' => $transactionDate,
            'vnp_CreateBy' => preg_replace('/[^\pL\pN ]/u', '', $createdBy) ?: 'NovaPhone Admin',
            'vnp_CreateDate' => $now->format('YmdHis'),
            'vnp_IpAddr' => $ipAddress,
            'vnp_OrderInfo' => 'Hoan tien don hang '.$order->order_code,
        ];
        $data['vnp_SecureHash'] = hash_hmac('sha512', implode('|', array_values($data)), $this->hashSecret);

        $caBundle = (string) config('services.vnpay.ca_bundle');
        if (! is_file($caBundle) || ! is_readable($caBundle)) {
            throw new RuntimeException('Không tìm thấy CA bundle để xác minh kết nối bảo mật tới VNPay.');
        }

        try {
            $response = Http::asJson()
                ->withOptions(['verify' => $caBundle])
                ->timeout(30)
                ->post((string) config('services.vnpay.refund_url'), $data);
        } catch (ConnectionException $exception) {
            throw new RuntimeException('Không thể kết nối bảo mật tới VNPay: '.$exception->getMessage(), 0, $exception);
        }
        if (! $response->successful()) {
            throw new RuntimeException('Không thể kết nối API hoàn tiền VNPay.');
        }

        $result = $response->json();
        if (! is_array($result)) {
            throw new RuntimeException('VNPay trả về dữ liệu không đúng định dạng JSON.');
        }

        Log::info('VNPay refund response', $this->safeTransactionLog($result));

        // VNPay trả lỗi tối giản chỉ gồm ResponseCode + Message và không ký response.
        // Chỉ yêu cầu/chấp nhận chữ ký đầy đủ khi API báo xử lý thành công.
        $responseCode = (string) ($result['vnp_ResponseCode'] ?? '');
        if ($responseCode !== '00') {
            $messages = [
                '02' => 'Mã merchant VNPay không hợp lệ.',
                '03' => 'Dữ liệu yêu cầu hoàn tiền không đúng định dạng.',
                '91' => 'VNPay không tìm thấy giao dịch cần hoàn.',
                '94' => 'Yêu cầu hoàn tiền đã được gửi trước đó và VNPay đang xử lý. Không gửi lại yêu cầu.',
                '95' => 'Giao dịch gốc không thành công nên VNPay từ chối hoàn tiền.',
                '97' => 'Chữ ký yêu cầu gửi tới VNPay không hợp lệ.',
                '99' => 'VNPay không thể xử lý yêu cầu hoàn tiền lúc này.',
            ];

            throw new RuntimeException(
                $messages[$responseCode]
                    ?? 'VNPay từ chối yêu cầu: '.($result['vnp_Message'] ?? 'Lỗi không xác định'),
                (int) $responseCode,
            );
        }

        if (! $this->validateRefundResponse($result)) {
            Log::warning('VNPay refund response signature mismatch', [
                'response_id' => $result['vnp_ResponseId'] ?? null,
                'response_code' => $result['vnp_ResponseCode'] ?? null,
                'transaction_status' => $result['vnp_TransactionStatus'] ?? null,
                'txn_ref' => $result['vnp_TxnRef'] ?? null,
                'amount' => $result['vnp_Amount'] ?? null,
                'message' => $result['vnp_Message'] ?? null,
                'present_fields' => array_keys($result),
            ]);
            throw new RuntimeException('Phản hồi hoàn tiền VNPay có chữ ký không hợp lệ.');
        }

        return $result;
    }

    /**
     * Truy vấn trạng thái giao dịch sau khi VNPay đã tiếp nhận yêu cầu hoàn.
     *
     * @return array<string, mixed>
     */
    public function queryRefund(Order $order, string $ipAddress, ?string $refundTransactionNo = null): array
    {
        $payment = $order->payments()
            ->where('gateway', 'vnpay')->where('status', 'success')
            ->latest('paid_at')->first();
        if (! $payment) {
            throw new RuntimeException('Không tìm thấy giao dịch VNPay gốc để đối soát.');
        }

        $now = now('Asia/Ho_Chi_Minh');
        $transactionDate = $payment->payload['vnp_PayDate']
            ?? $payment->paid_at?->timezone('Asia/Ho_Chi_Minh')->format('YmdHis');
        $data = [
            'vnp_RequestId' => strtoupper(Str::random(16)),
            'vnp_Version' => '2.1.0',
            'vnp_Command' => 'querydr',
            'vnp_TmnCode' => $this->tmnCode,
            'vnp_TxnRef' => $order->order_code,
            // Sau khi Refund API trả mã giao dịch hoàn, QueryDR phải truy vấn mã
            // này; dùng mã thanh toán gốc chỉ trả TransactionType=01.
            'vnp_TransactionNo' => $refundTransactionNo ?: $payment->transaction_code,
            'vnp_TransactionDate' => $transactionDate,
            'vnp_CreateDate' => $now->format('YmdHis'),
            'vnp_IpAddr' => $ipAddress,
            'vnp_OrderInfo' => 'Kiem tra hoan tien don hang '.$order->order_code,
        ];
        $queryHashFields = [
            'vnp_RequestId', 'vnp_Version', 'vnp_Command', 'vnp_TmnCode',
            'vnp_TxnRef', 'vnp_TransactionDate', 'vnp_CreateDate',
            'vnp_IpAddr', 'vnp_OrderInfo',
        ];
        $data['vnp_SecureHash'] = hash_hmac(
            'sha512',
            implode('|', array_map(fn ($field) => (string) $data[$field], $queryHashFields)),
            $this->hashSecret,
        );

        $result = $this->postTransactionApi($data);
        Log::info('VNPay querydr response', $this->safeTransactionLog($result));
        $responseCode = (string) ($result['vnp_ResponseCode'] ?? '');
        if ($responseCode !== '00') {
            $message = $responseCode === '94'
                ? 'VNPay giới hạn truy vấn lặp trong thời gian ngắn. Giao dịch vẫn đang được xử lý.'
                : 'Không thể truy vấn VNPay: '.($result['vnp_Message'] ?? 'mã '.$responseCode);

            throw new RuntimeException($message, (int) $responseCode);
        }
        if (! $this->validateQueryResponse($result)) {
            throw new RuntimeException('Phản hồi truy vấn VNPay có chữ ký không hợp lệ.');
        }

        return $result;
    }

    /** @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    private function postTransactionApi(array $data): array
    {
        $caBundle = (string) config('services.vnpay.ca_bundle');
        if (! is_file($caBundle) || ! is_readable($caBundle)) {
            throw new RuntimeException('Không tìm thấy CA bundle để xác minh kết nối bảo mật tới VNPay.');
        }

        try {
            $response = Http::asJson()->withOptions(['verify' => $caBundle])->timeout(30)
                ->post((string) config('services.vnpay.refund_url'), $data);
        } catch (ConnectionException $exception) {
            throw new RuntimeException('Không thể kết nối bảo mật tới VNPay: '.$exception->getMessage(), 0, $exception);
        }
        if (! $response->successful() || ! is_array($response->json())) {
            throw new RuntimeException('VNPay trả về dữ liệu không hợp lệ khi đối soát.');
        }

        return $response->json();
    }

    /** @param array<string, mixed> $data */
    private function validateRefundResponse(array $data): bool
    {
        $fields = [
            'vnp_ResponseId', 'vnp_Command', 'vnp_ResponseCode', 'vnp_Message',
            'vnp_TmnCode', 'vnp_TxnRef', 'vnp_Amount', 'vnp_BankCode',
            'vnp_PayDate', 'vnp_TransactionNo', 'vnp_TransactionType',
            'vnp_TransactionStatus', 'vnp_OrderInfo',
        ];
        $hashData = implode('|', array_map(fn ($field) => (string) ($data[$field] ?? ''), $fields));

        $expected = strtolower(hash_hmac('sha512', $hashData, $this->hashSecret));
        $received = strtolower(trim((string) ($data['vnp_SecureHash'] ?? '')));

        return $received !== '' && hash_equals($expected, $received);
    }

    /** @param array<string, mixed> $data */
    private function validateQueryResponse(array $data): bool
    {
        $fields = [
            'vnp_ResponseId', 'vnp_Command', 'vnp_ResponseCode', 'vnp_Message',
            'vnp_TmnCode', 'vnp_TxnRef', 'vnp_Amount', 'vnp_BankCode',
            'vnp_PayDate', 'vnp_TransactionNo', 'vnp_TransactionType',
            'vnp_TransactionStatus', 'vnp_OrderInfo', 'vnp_PromotionCode',
            'vnp_PromotionAmount',
        ];
        $hashData = implode('|', array_map(fn ($field) => (string) ($data[$field] ?? ''), $fields));
        $expected = strtolower(hash_hmac('sha512', $hashData, $this->hashSecret));
        $received = strtolower(trim((string) ($data['vnp_SecureHash'] ?? '')));

        return $received !== '' && hash_equals($expected, $received);
    }

    /** @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    private function safeTransactionLog(array $data): array
    {
        return collect($data)->only([
            'vnp_ResponseId', 'vnp_Command', 'vnp_ResponseCode', 'vnp_Message',
            'vnp_TmnCode', 'vnp_TxnRef', 'vnp_Amount', 'vnp_BankCode',
            'vnp_PayDate', 'vnp_TransactionNo', 'vnp_TransactionType',
            'vnp_TransactionStatus', 'vnp_OrderInfo',
        ])->all();
    }
}
