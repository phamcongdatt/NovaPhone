<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    private function checkOwnership(Address $address)
    {
        if ($address->user_id !== Auth::id()) {
            abort(403, 'Không có quyền truy cập tài nguyên này.');
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'street' => 'required|string|max:255',
            'ward' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'province' => 'required|string|max:255',
            'is_default' => 'nullable|boolean',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['full_name'] = $validated['name'];
        $validated['address'] = $validated['street'];
        unset($validated['name'], $validated['street']);

        if ($validated['is_default'] ?? false) {
            Auth::user()->addresses()->update(['is_default' => false]);
        }

        Auth::user()->addresses()->create($validated);

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
            'ward' => $address->ward,
            'district' => $address->district,
            'province' => $address->province,
            'is_default' => $address->is_default,
        ]);
    }

    public function update(Request $request, Address $address)
    {
        $this->checkOwnership($address);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'street' => 'required|string|max:255',
            'ward' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'province' => 'required|string|max:255',
            'is_default' => 'nullable|boolean',
        ]);

        $validated['full_name'] = $validated['name'];
        $validated['address'] = $validated['street'];
        unset($validated['name'], $validated['street']);

        if ($validated['is_default'] ?? false) {
            Auth::user()->addresses()->where('id', '!=', $address->id)->update(['is_default' => false]);
        }

        $address->update($validated);

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
}
