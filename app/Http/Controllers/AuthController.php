<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    // ─── Đăng ký ──────────────────────────────────────────────

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Auth::login($user);

        return redirect('/')
            ->with('success', 'Đăng ký thành công! Chào mừng ' . $user->name);
    }

    // ─── Đăng nhập ────────────────────────────────────────────

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');
        $remember    = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            // Chặn tài khoản bị khoá
            if (Auth::user()->isBlocked()) {
                Auth::logout();
                return back()->withErrors(['email' => 'Tài khoản của bạn đã bị khoá.']);
            }

            return redirect()->intended('/')
                ->with('success', 'Đăng nhập thành công!');
        }

        return back()
            ->withInput($request->only('email', 'remember'))
            ->withErrors(['email' => 'Email hoặc mật khẩu không chính xác.']);
    }

    // ─── Đăng xuất ────────────────────────────────────────────

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')
            ->with('success', 'Đã đăng xuất thành công.');
    }

    // ─── Quên mật khẩu (dùng bảng password_reset_tokens) ─────

    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(
            ['email' => ['required', 'email']],
            ['email.required' => 'Vui lòng nhập email.', 'email.email' => 'Email không hợp lệ.']
        );

        // Laravel tự lưu token vào bảng password_reset_tokens và gửi mail
        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', 'Chúng tôi đã gửi link đặt lại mật khẩu vào email của bạn.')
            : back()->withErrors(['email' => 'Không tìm thấy tài khoản với email này.']);
    }

    // ─── Đặt lại mật khẩu ─────────────────────────────────────

    public function showResetPassword(Request $request, string $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token'    => ['required'],
            'email'    => ['required', 'email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'password.required'  => 'Vui lòng nhập mật khẩu mới.',
            'password.min'       => 'Mật khẩu phải có ít nhất 8 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
        ]);

        // Laravel tự xác thực token trong bảng password_reset_tokens rồi xoá đi
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill(['password' => Hash::make($password)])
                     ->setRememberToken(Str::random(60));
                $user->save();
                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('success', 'Đặt lại mật khẩu thành công! Vui lòng đăng nhập.')
            : back()->withErrors(['email' => __($status)]);
    }

    // ─── Dashboard ────────────────────────────────────────────

    public function dashboard()
    {
        return view('auth.dashboard');
    }
}