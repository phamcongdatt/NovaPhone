<?php

namespace App\Services;

use App\Models\AdministrativeProvince;
use App\Models\AdministrativeWard;
use Illuminate\Validation\ValidationException;

class AdministrativeAddressResolver
{
    /**
     * Resolve mã hành chính thành snapshot đáng tin cậy để lưu vào Address/Order.
     * Tên tỉnh và xã/phường luôn được lấy từ dữ liệu nội bộ, không từ client.
     *
     * @return array{
     *     province_code: string,
     *     province_name: string,
     *     ward_code: string,
     *     ward_name: string,
     *     street_address: string,
     *     administrative_version: string,
     *     full_address: string
     * }
     */
    public function resolve(string $provinceCode, string $wardCode, string $streetAddress): array
    {
        $province = AdministrativeProvince::query()
            ->where('code', $provinceCode)
            ->where('is_active', true)
            ->first();

        if (! $province) {
            throw ValidationException::withMessages([
                'province_code' => 'Tỉnh/thành phố đã chọn không hợp lệ hoặc không còn được hỗ trợ.',
            ]);
        }

        $ward = AdministrativeWard::query()
            ->where('code', $wardCode)
            ->where('province_code', $province->code)
            ->where('is_active', true)
            ->first();

        if (! $ward) {
            throw ValidationException::withMessages([
                'ward_code' => 'Phường/xã đã chọn không thuộc tỉnh/thành phố hoặc không còn được hỗ trợ.',
            ]);
        }

        $streetAddress = trim($streetAddress);
        if ($streetAddress === '') {
            throw ValidationException::withMessages([
                'shipping_address' => 'Vui lòng nhập số nhà, tên đường hoặc thông tin địa chỉ chi tiết.',
            ]);
        }

        return [
            'province_code' => $province->code,
            'province_name' => $province->name,
            'ward_code' => $ward->code,
            'ward_name' => $ward->name,
            'street_address' => $streetAddress,
            'administrative_version' => $ward->source_version,
            'full_address' => collect([
                $streetAddress,
                $ward->name,
                $province->name,
            ])->filter()->implode(', '),
        ];
    }
}
