@extends('auth.layout')
@section('title', 'Đăng nhập')

@section('content')
<h2 class="text-xl font-semibold text-gray-900 mb-1">Chào mừng trở lại</h2>
<p class="text-sm text-gray-500 mb-6">Đăng nhập vào tài khoản của bạn</p>

<form method="POST" action="{{ route('login') }}" novalidate>
    @csrf

    {{-- Email --}}
    <div class="mb-4">
        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
        <input
            id="email" name="email" type="email"
            value="{{ old('email') }}"
            autocomplete="email" autofocus
            class="input-field @error('email') border-red-400 bg-red-50 @enderror"
            placeholder="ban@example.com"
        >
        @error('email')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- Mật khẩu --}}
    <div class="mb-4">
        <div class="flex items-center justify-between mb-1">
            <label for="password" class="text-sm font-medium text-gray-700">Mật khẩu</label>
            <a href="{{ route('password.request') }}" class="text-xs text-brand-600 hover:text-brand-700">
                Quên mật khẩu?
            </a>
        </div>
        <div class="relative">
            <input
                id="password" name="password" type="password"
                autocomplete="current-password"
                class="input-field pr-10 @error('password') border-red-400 bg-red-50 @enderror"
                placeholder="••••••••"
            >
            {{-- Toggle hiện/ẩn mật khẩu --}}
            <button type="button" onclick="togglePassword('password')"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                <svg id="eye-password" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7
                             -1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
            </button>
        </div>
        @error('password')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- Nhớ tài khoản --}}
    <div class="flex items-center mb-6">
        <input
            id="remember" name="remember" type="checkbox"
            value="1"
            {{ old('remember') ? 'checked' : '' }}
            class="w-4 h-4 text-brand-600 border-gray-300 rounded focus:ring-brand-500 cursor-pointer"
        >
        <label for="remember" class="ml-2 text-sm text-gray-600 cursor-pointer">
            Nhớ tài khoản
        </label>
    </div>

    <button type="submit" class="btn-primary">Đăng nhập</button>
</form>

<p class="text-center text-sm text-gray-500 mt-6">
    Chưa có tài khoản?
    <a href="{{ route('register') }}" class="text-brand-600 hover:text-brand-700 font-medium">Đăng ký ngay</a>
</p>

<script>
function togglePassword(id) {
    const input = document.getElementById(id);
    input.type = input.type === 'password' ? 'text' : 'password';
}
</script>
@endsection