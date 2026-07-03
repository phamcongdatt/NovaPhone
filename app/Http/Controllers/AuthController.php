<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\Registered;

class AuthController extends Controller
{
    /**
     * Hiển thị trang đăng nhập
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route(Auth::user()->isAdmin() ? 'admin.dashboard' : 'home');
        }

        return view('auth.login');
    }

    /**
     * Xử lý đăng nhập
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required' => 'Vui lòng nhập địa chỉ email.',
            'email.email' => 'Email không hợp lệ.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
        ]);

        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            // Kiểm tra xem tài khoản có bị khóa không
            if ($request->user()->isBlocked()) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                throw ValidationException::withMessages([
                    'email' => 'Tài khoản của bạn đang bị khóa.',
                ]);
            }

            $request->session()->regenerate();
            if ($request->user()->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }

            return redirect()->intended(route('home'));
        }

        throw ValidationException::withMessages([
            'email' => 'Thông tin đăng nhập không chính xác.',
        ]);
    }

    /**
     * Hiển thị trang đăng ký
     */
    public function showRegister()
    {
        if (Auth::check()) {
            return redirect('/');
        }
        return view('auth.register');
    }

    /**
     * Xử lý đăng ký
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:15', 'unique:users,phone'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'terms' => ['accepted'],
        ], [
            'name.required' => 'Vui lòng nhập họ tên.',
            'email.required' => 'Vui lòng nhập địa chỉ email.',
            'email.unique' => 'Email này đã được sử dụng.',
            'phone.unique' => 'Số điện thoại này đã được sử dụng.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
            'terms.accepted' => 'Bạn cần đồng ý với điều khoản sử dụng.',
        ], [
            'name' => 'họ tên',
            'email' => 'email',
            'phone' => 'số điện thoại',
            'password' => 'mật khẩu',
        ]);

        $isLocal = app()->environment('local');

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => $validated['password'], // Tự động hash do User model cast hashed
            'role' => 'user',
            'status' => 'active',
            'email_verified_at' => $isLocal ? now() : null, // Kích hoạt ngay trên local
        ]);

        // Chỉ gửi email xác thực nếu không ở local
        if (!$isLocal) {
            event(new Registered($user));
        }

        // Tự động đăng nhập
        Auth::login($user);

        if ($isLocal) {
            return redirect('/')->with('success', 'Đăng ký tài khoản thành công!');
        }

        return redirect()->route('verification.notice');
    }

    /**
     * Đăng xuất
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Hiển thị trang đổi mật khẩu
     */
    public function showChangePassword()
    {
        return view('auth.passwords.change');
    }

    /**
     * Xử lý đổi mật khẩu
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed', 'different:current_password'],
        ], [
            'current_password.required' => 'Vui lòng nhập mật khẩu hiện tại.',
            'current_password.current_password' => 'Mật khẩu hiện tại không đúng.',
            'password.required' => 'Vui lòng nhập mật khẩu mới.',
            'password.min' => 'Mật khẩu mới phải có ít nhất 8 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu mới không khớp.',
            'password.different' => 'Mật khẩu mới phải khác mật khẩu hiện tại.',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->update([
            'password' => Hash::make($request->password), // Đảm bảo bcrypt MK mới
        ]);

        return back()->with('status', 'Đổi mật khẩu thành công!');
    }

    /**
     * Chuyển hướng người dùng sang Google
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Nhận dữ liệu trả về từ Google
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // Tìm user đã có google_id này hoặc email này
            $user = User::where('google_id', $googleUser->id)->orWhere('email', $googleUser->email)->first();

            if ($user) {
                // Nếu user tồn tại (đăng ký bằng mail trước đó) thì update thêm google_id
                if (!$user->google_id) {
                    $user->update(['google_id' => $googleUser->id]);
                }

                // Kiểm tra tài khoản có bị chặn không
                if ($user->isBlocked()) {
                    return redirect('/login')->withErrors(['email' => 'Tài khoản của bạn đang bị khóa.']);
                }
            } else {
                // Tạo user mới nếu chưa tồn tại
                $user = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'password' => Str::random(16), // Tự động hash do User model cast hashed
                    'avatar' => $googleUser->avatar,
                    'role' => 'user',
                    'status' => 'active',
                ]);
            }

            Auth::login($user);

            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }

            return redirect()->intended(route('home'));

        } catch (\Exception $e) {
            return redirect('/login')->withErrors(['email' => 'Đăng nhập bằng Google thất bại. Vui lòng thử lại.']);
        }
    }

    /**
     * Hiển thị giao diện quên mật khẩu
     */
    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    /**
     * Xử lý gửi email/link đặt lại mật khẩu
     */
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ], [
            'email.required' => 'Vui lòng nhập địa chỉ email.',
            'email.email' => 'Email không hợp lệ.',
            'email.exists' => 'Email này không tồn tại trong hệ thống.',
        ]);

        $email = $request->email;
        $token = Str::random(60);

        // Lưu hoặc cập nhật token vào database
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            [
                'token' => Hash::make($token),
                'created_at' => now()
            ]
        );

        // Tạo link reset password
        $resetLink = route('password.reset', ['token' => $token, 'email' => $email]);

        // Trả về kèm session status và dev_link hỗ trợ test local
        return back()->with('status', 'Chúng tôi đã tạo liên kết đặt lại mật khẩu.')
                     ->with('dev_link', $resetLink);
    }

    /**
     * Hiển thị giao diện đặt lại mật khẩu
     */
    public function showResetPassword(Request $request, $token)
    {
        $email = $request->query('email');
        return view('auth.reset-password', compact('token', 'email'));
    }

    /**
     * Xử lý đặt lại mật khẩu mới
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email', 'exists:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'password.required' => 'Vui lòng nhập mật khẩu mới.',
            'password.min' => 'Mật khẩu mới phải có ít nhất 8 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu mới không khớp.',
        ]);

        // Kiểm tra token trong database
        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$record || !Hash::check($request->token, $record->token)) {
            return back()->withErrors(['email' => 'Mã xác thực đặt lại mật khẩu không hợp lệ hoặc đã hết hạn.']);
        }

        // Hạn của token là 60 phút
        if (now()->subMinutes(60)->gt($record->created_at)) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return back()->withErrors(['email' => 'Mã xác thực đặt lại mật khẩu đã hết hạn.']);
        }

        // Cập nhật mật khẩu cho user
        $user = User::where('email', $request->email)->first();
        $user->update([
            'password' => $request->password, // Tự động hash do User model cast hashed
        ]);

        // Xóa token đã dùng
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('success', 'Đặt lại mật khẩu thành công! Vui lòng đăng nhập bằng mật khẩu mới.');
    }

    /**
     * Đăng nhập nhanh cho môi trường phát triển (demo)
     */
    public function quickLogin()
    {
        if (app()->environment('production')) {
            abort(404);
        }

        // Tìm user demo hoặc tạo mới
        $user = User::where('role', 'user')->first();
        if (!$user) {
            $user = User::create([
                'name' => 'Demo User',
                'email' => 'demo@novaphone.vn',
                'password' => 'password123',
                'role' => 'user',
                'status' => 'active',
            ]);
        }

        Auth::login($user);
        return redirect()->intended('/')->with('success', 'Đăng nhập nhanh thành công!');
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

            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard')
                    ->with('success', 'Đăng nhập bằng ' . ucfirst($provider) . ' thành công!');
            }

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

        return response()->json([
            'success' => true,
            'message' => 'Đăng nhập thành công!',
            'redirect' => $user->isAdmin() ? route('admin.dashboard') : route('home')
        ]);
    }
}
