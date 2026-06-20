@extends('auth.layout')
@section('title', 'Quên mật khẩu')

@section('content')
<a href="{{ route('login') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 mb-4">
    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
    </svg>
    Quay lại đăng nhập
</a>

<div class="mb-6">
    <div class="w-12 h-12 bg-brand-50 rounded-xl flex items-center justify-center mb-4">
        <svg class="w-6 h-6 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
        </svg>
    </div>
    <h2 class="text-xl font-semibold text-gray-900 mb-1">Quên mật khẩu?</h2>
    <p class="text-sm text-gray-500">Nhập email của bạn, chúng tôi sẽ gửi link đặt lại mật khẩu.</p>
</div>

<form method="POST" action="{{ route('password.email') }}" novalidate>
    @csrf

    <div class="mb-5">
        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
        <input
            id="email" name="email" type="email"
            value="{{ old('email') }}"
            autofocus
            class="input-field @error('email') border-red-400 bg-red-50 @enderror"
            placeholder="ban@example.com"
        >
        @error('email')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <button type="submit" class="btn-primary">Gửi link đặt lại mật khẩu</button>
</form>
@endsection