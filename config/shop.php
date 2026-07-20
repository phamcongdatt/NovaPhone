<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Ngưỡng bắt buộc thanh toán trực tuyến
    |--------------------------------------------------------------------------
    |
    | Đơn hàng có tổng giá trị vượt qua ngưỡng này sẽ không được phép chọn
    | phương thức thanh toán COD, bắt buộc phải thanh toán trực tuyến (VNPay).
    |
    */
    'cod_max_amount' => env('COD_MAX_AMOUNT', 20000000),

    /*
    |--------------------------------------------------------------------------
    | Thời gian chờ thanh toán trước khi tự động hủy đơn
    |--------------------------------------------------------------------------
    |
    | Số phút tối đa một đơn hàng thanh toán trực tuyến được phép ở trạng thái
    | "pending" trước khi bị lệnh orders:cancel-stale tự động hủy.
    |
    */
    'pending_order_timeout_minutes' => env('PENDING_ORDER_TIMEOUT_MINUTES', 10),

];
