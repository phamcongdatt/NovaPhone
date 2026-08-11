<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Services\AdministrativeAddressResolver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    public function __construct(private readonly AdministrativeAddressResolver $addressResolver)
    {
    }

    private function checkOwnership(Address $address): void
    {
        if ($address->user_id !== Auth::id()) {
            abort(403, 'Không có quyền truy cập tài nguyên này.');
        }
    }

    public function store(Request $request)
    {
        $data = $this->addressData($request);

        if ($data['is_default']) {
            Auth::user()->addresses()->update(['is_default' => false]);
        }

        Auth::user()->addresses()->create($data + ['user_id' => Auth::id()]);

        return redirect()->route('account.addresses')->with('success', 'Thêm địa chỉ thành công!');
    }

    public function show(Address $address)
    {
        $this->checkOwnership($address);

        return response()->json([
            'id' => $address->id,
            'name' => $address->full_name,
            'phone' => $address->phone,
            'street' => $address->address,
            'province_code' => $address->province_code,
            'ward_code' => $address->ward_code,
            'is_default' => $address->is_default,
        ]);
    }

    public function update(Request $request, Address $address)
    {
        $this->checkOwnership($address);
        $data = $this->addressData($request);

        if ($data['is_default']) {
            Auth::user()->addresses()->where('id', '!=', $address->id)->update(['is_default' => false]);
        }

        $address->update($data);

        return redirect()->route('account.addresses')->with('success', 'Cập nhật địa chỉ thành công!');
    }

    public function destroy(Address $address)
    {
        $this->checkOwnership($address);
        $address->delete();

        return redirect()->route('account.addresses')->with('success', 'Xóa địa chỉ thành công!');
    }

    public function setDefault(Address $address)
    {
        $this->checkOwnership($address);

        Auth::user()->addresses()->update(['is_default' => false]);
        $address->update(['is_default' => true]);

        return redirect()->route('account.addresses')->with('success', 'Đặt địa chỉ mặc định thành công!');
    }

    /**
     * @return array<string, mixed>
     */
    private function addressData(Request $request): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:100'],
            'phone' => ['required', 'regex:/^(?:\\+84|0)(?:3|5|7|8|9)\\d{8}$/'],
            'street' => ['required', 'string', 'min:8', 'max:255'],
            'province_code' => ['required', 'regex:/^\\d{2}$/'],
            'ward_code' => ['required', 'regex:/^\\d{5}$/'],
            'is_default' => ['nullable', 'boolean'],
        ]);

        $location = $this->addressResolver->resolve(
            $validated['province_code'],
            $validated['ward_code'],
            $validated['street'],
        );

        return [
            'full_name' => $validated['name'],
            'phone' => $validated['phone'],
            'address' => $location['street_address'],
            'ward' => $location['ward_name'],
            'district' => null,
            'province' => $location['province_name'],
            'province_code' => $location['province_code'],
            'ward_code' => $location['ward_code'],
            'administrative_version' => $location['administrative_version'],
            'validated_at' => now(),
            'is_default' => (bool) ($validated['is_default'] ?? false),
        ];
    }
}
