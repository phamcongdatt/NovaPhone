<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;


class AuthController extends Controller
{
    /**
     * Hiển thị form đăng ký.
     */
    public function showRegisterForm(): View
    {
        return view('auth.register');
    }

    /**
     * Xử lý đăng ký tài khoản mới.
     */
    public function register(Request $request): RedirectResponse
    {
        // 1) Validate. Đặt tên thuộc tính tiếng Việt để thông báo lỗi dễ đọc.
        $validated = $request->validate(
            [
                'name'     => ['required', 'string', 'max:255'],
                'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
                'phone'    => ['nullable', 'string', 'max:15', 'unique:users,phone'],
                'password' => ['required', 'confirmed', 'min:8'],
                'terms'    => ['accepted'],
            ],
            [
                'terms.accepted' => 'Bạn cần đồng ý với điều khoản sử dụng.',
            ],
            [
                'name'     => 'họ tên',
                'email'    => 'email',
                'phone'    => 'số điện thoại',
                'password' => 'mật khẩu',
            ]
        );

        // 2) Tạo user. Nhờ cast "hashed" trong Model, password được hash tự động.
        //    role mặc định "user" (khách hàng) theo enum của bảng users.
        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'phone'    => $validated['phone'] ?? null,
            'password' => $validated['password'],
            'role'     => 'user',
        ]);

        // 3) Bắn event Registered -> Laravel gửi email xác thực (VerifyEmail notification).
        event(new Registered($user));

        // 4) Đăng nhập ngay để user vào được trang thông báo xác thực.
        Auth::login($user);

        // 5) Điều hướng tới trang "vui lòng kiểm tra email".
        return redirect()->route('verification.notice');
    }
}
