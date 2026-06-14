<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;


class AuthController extends Controller
{
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate(
            [
                'email' => ['required', 'string', 'email'],
                'password' => ['required', 'string'],
            ],
            [],
            [
                'email' => 'email',
                'password' => 'mật khẩu',
            ]
        );

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => 'Email hoặc mật khẩu không chính xác.',
            ]);
        }

        if ($request->user()->isBlocked()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            throw ValidationException::withMessages([
                'email' => 'Tài khoản của bạn đang bị khóa.',
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('home'));
    }

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

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
