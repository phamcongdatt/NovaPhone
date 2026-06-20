<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use App\Services\CartService;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }
        return view('auth.register');
    }

    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'password' => Hash::make($request->password),
        ]);

        // Bắn event Registered -> listener SendEmailVerificationNotification gửi mail xác thực
        event(new Registered($user));

        Auth::login($user);
        $this->cartService->mergeSessionCartToDb();

        return redirect()->route('verification.notice')
            ->with('success', 'Đăng ký thành công! Vui lòng kiểm tra email để xác thực tài khoản.');
    }

    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }
        return view('auth.login');
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');
        $remember    = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            if (Auth::user()->isBlocked()) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors(['email' => 'Tài khoản của bạn đã bị khoá.']);
            }

            $this->cartService->mergeSessionCartToDb();

            return redirect()->intended(route('home'))
                ->with('success', 'Đăng nhập thành công! Chào mừng bạn quay trở lại.');
        }

        return back()
            ->withInput($request->only('email', 'remember'))
            ->withErrors(['email' => 'Email hoặc mật khẩu không chính xác.']);
    }

    public function quickLogin(Request $request)
    {
        // Chỉ cho phép đăng nhập nhanh ở môi trường phát triển (tránh lỗ hổng chiếm tài khoản ở production)
        if (! app()->environment('local')) {
            abort(404);
        }

        $email = $request->input('email', 'user@novaphone.vn');
        $user  = User::where('email', $email)->first();

        if ($user) {
            Auth::login($user, true);
            $request->session()->regenerate();
            $this->cartService->mergeSessionCartToDb();

            return redirect()->route('home')
                ->with('success', 'Đăng nhập nhanh thành công với tài khoản: ' . $user->name);
        }

        return redirect()->route('login')->with('error', 'Tài khoản giả lập không tồn tại.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Đã đăng xuất thành công.');
    }

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

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', 'Chúng tôi đã gửi link đặt lại mật khẩu vào email của bạn.')
            : back()->withErrors(['email' => 'Không tìm thấy tài khoản với email này.']);
    }

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

    public function dashboard()
    {
        return view('auth.dashboard');
    }

    public function redirectToProvider(string $provider)
    {
        if (!in_array($provider, ['google', 'facebook'])) {
            return redirect()->route('login')->with('error', 'Phương thức đăng nhập không hỗ trợ.');
        }

        try {
            return \Laravel\Socialite\Facades\Socialite::driver($provider)->redirect();
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Có lỗi xảy ra khi chuyển hướng: ' . $e->getMessage());
        }
    }

    public function handleProviderCallback(string $provider, Request $request)
    {
        if (!in_array($provider, ['google', 'facebook'])) {
            return redirect()->route('login')->with('error', 'Phương thức đăng nhập không hỗ trợ.');
        }

        try {
            $socialUser = \Laravel\Socialite\Facades\Socialite::driver($provider)->user();
            
            // Tìm hoặc tạo người dùng
            $user = User::where('provider', $provider)
                ->where('provider_id', $socialUser->getId())
                ->first();

            if (!$user) {
                // Kiểm tra xem email có tồn tại chưa (nếu đăng ký bằng password trước đó)
                $user = User::where('email', $socialUser->getEmail())->first();

                if ($user) {
                    // Liên kết tài khoản
                    $user->update([
                        'provider' => $provider,
                        'provider_id' => $socialUser->getId(),
                        'avatar' => $user->avatar ?? $socialUser->getAvatar(),
                    ]);
                } else {
                    // Tạo tài khoản mới
                    $user = User::create([
                        'name' => $socialUser->getName(),
                        'email' => $socialUser->getEmail(),
                        'avatar' => $socialUser->getAvatar(),
                        'provider' => $provider,
                        'provider_id' => $socialUser->getId(),
                        'email_verified_at' => now(), // Đã xác thực qua mạng xã hội
                        'password' => Hash::make(Str::random(24)),
                    ]);
                    
                    event(new Registered($user));
                }
            }

            if ($user->isBlocked()) {
                return redirect()->route('login')->with('error', 'Tài khoản của bạn đã bị khoá.');
            }

            Auth::login($user, true);
            $request->session()->regenerate();
            $this->cartService->mergeSessionCartToDb();

            return redirect()->intended(route('home'))
                ->with('success', 'Đăng nhập bằng ' . ucfirst($provider) . ' thành công!');

        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Không thể kết nối với ' . ucfirst($provider) . '. ' . $e->getMessage());
        }
    }

    public function socialLoginPost(Request $request)
    {
        try {
            $request->validate([
                'provider' => 'required|in:google,facebook',
                'provider_id' => 'required|string',
                'email' => 'required|email',
                'name' => 'required|string|max:255',
                'avatar' => 'nullable|string',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ: ' . implode(', ', $e->validator->errors()->all())
            ], 422);
        }

        $provider = $request->input('provider');
        $providerId = $request->input('provider_id');
        $email = $request->input('email');
        $name = $request->input('name');
        $avatar = $request->input('avatar');

        // Tìm hoặc tạo người dùng
        $user = User::where('provider', $provider)
            ->where('provider_id', $providerId)
            ->first();

        if (!$user) {
            $user = User::where('email', $email)->first();

            if ($user) {
                $user->update([
                    'provider' => $provider,
                    'provider_id' => $providerId,
                    'avatar' => $user->avatar ?? $avatar,
                ]);
            } else {
                $user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'avatar' => $avatar,
                    'provider' => $provider,
                    'provider_id' => $providerId,
                    'email_verified_at' => now(),
                    'password' => Hash::make(Str::random(24)),
                ]);

                event(new Registered($user));
            }
        }

        if ($user->isBlocked()) {
            return response()->json([
                'success' => false,
                'message' => 'Tài khoản của bạn đã bị khoá.'
            ], 403);
        }

        Auth::login($user, true);
        $request->session()->regenerate();
        $this->cartService->mergeSessionCartToDb();

        return response()->json([
            'success' => true,
            'message' => 'Đăng nhập thành công!',
            'redirect' => route('home')
        ]);
    }
}

