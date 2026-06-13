@extends('auth.layout')
@section('title', 'Đăng ký')

@section('content')
<h2 class="text-xl font-semibold text-gray-900 mb-1">Tạo tài khoản</h2>
<p class="text-sm text-gray-500 mb-6">Điền thông tin để bắt đầu</p>

<form method="POST" action="{{ route('register') }}" novalidate>
    @csrf

    {{-- Họ tên --}}
    <div class="mb-4">
        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Họ và tên</label>
        <input
            id="name" name="name" type="text"
            value="{{ old('name') }}"
            autocomplete="name" autofocus
            class="input-field @error('name') border-red-400 bg-red-50 @enderror"
            placeholder="Nguyễn Văn A"
        >
        @error('name')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- Email --}}
    <div class="mb-4">
        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
        <input
            id="email" name="email" type="email"
            value="{{ old('email') }}"
            autocomplete="email"
            class="input-field @error('email') border-red-400 bg-red-50 @enderror"
            placeholder="ban@example.com"
        >
        @error('email')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- Mật khẩu --}}
    <div class="mb-4">
        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Mật khẩu</label>
        <div class="relative">
            <input
                id="password" name="password" type="password"
                autocomplete="new-password"
                class="input-field pr-10 @error('password') border-red-400 bg-red-50 @enderror"
                placeholder="Tối thiểu 8 ký tự"
                oninput="checkStrength(this.value)"
            >
            <button type="button" onclick="togglePassword('password')"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7
                             -1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
            </button>
        </div>
        {{-- Thanh độ mạnh mật khẩu --}}
        <div id="strength-bar" class="mt-1.5 h-1 rounded-full bg-gray-100 hidden">
            <div id="strength-fill" class="h-full rounded-full transition-all duration-300"></div>
        </div>
        <p id="strength-text" class="text-xs mt-0.5 hidden"></p>
        @error('password')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- Xác nhận mật khẩu --}}
    <div class="mb-6">
        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
            Xác nhận mật khẩu
        </label>
        <input
            id="password_confirmation" name="password_confirmation" type="password"
            autocomplete="new-password"
            class="input-field @error('password_confirmation') border-red-400 bg-red-50 @enderror"
            placeholder="Nhập lại mật khẩu"
        >
        @error('password_confirmation')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <button type="submit" class="btn-primary">Tạo tài khoản</button>
</form>

<p class="text-center text-sm text-gray-500 mt-6">
    Đã có tài khoản?
    <a href="{{ route('login') }}" class="text-brand-600 hover:text-brand-700 font-medium">Đăng nhập</a>
</p>

<script>
function togglePassword(id) {
    const input = document.getElementById(id);
    input.type = input.type === 'password' ? 'text' : 'password';
}

function checkStrength(value) {
    const bar = document.getElementById('strength-bar');
    const fill = document.getElementById('strength-fill');
    const text = document.getElementById('strength-text');

    if (!value) { bar.classList.add('hidden'); text.classList.add('hidden'); return; }

    bar.classList.remove('hidden'); text.classList.remove('hidden');

    let score = 0;
    if (value.length >= 8)             score++;
    if (/[A-Z]/.test(value))           score++;
    if (/[0-9]/.test(value))           score++;
    if (/[^A-Za-z0-9]/.test(value))   score++;

    const levels = [
        { width: '25%', color: 'bg-red-500',    label: 'Yếu',      textColor: 'text-red-500' },
        { width: '50%', color: 'bg-orange-400', label: 'Trung bình', textColor: 'text-orange-400' },
        { width: '75%', color: 'bg-yellow-400', label: 'Khá',       textColor: 'text-yellow-500' },
        { width: '100%', color: 'bg-green-500', label: 'Mạnh',      textColor: 'text-green-500' },
    ];
    const level = levels[Math.max(0, score - 1)];

    fill.style.width = level.width;
    fill.className   = 'h-full rounded-full transition-all duration-300 ' + level.color;
    text.textContent = 'Độ mạnh: ' + level.label;
    text.className   = 'text-xs mt-0.5 ' + level.textColor;
}
</script>
@endsection