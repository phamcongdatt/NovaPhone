<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            
            // Merge session cart to Database
            $this->cartService->mergeSessionCartToDb();

            return redirect()->intended(route('home'))
                ->with('success', 'Đăng nhập thành công! Chào mừng bạn quay trở lại.');
        }

        return back()->withErrors([
            'email' => 'Thông tin đăng nhập không chính xác.',
        ])->onlyInput('email');
    }

    public function quickLogin(Request $request)
    {
        $email = $request->input('email', 'user@novaphone.vn');
        $user = User::where('email', $email)->first();

        if ($user) {
            Auth::login($user, true);
            $request->session()->regenerate();

            // Merge session cart to Database
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
}
