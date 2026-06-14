@extends('auth.layout')
@section('title', 'Đặt lại mật khẩu')

@section('content')
<h2 class="text-xl font-semibold text-gray-900 mb-1">Đặt lại mật khẩu</h2>
<p class="text-sm text-gray-500 mb-6">Nhập mật khẩu mới cho tài khoản của bạn.</p>

<form method="POST" action="{{ route('password.update') }}" novalidate>
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">
    <input type="hidden" name="email" value="{{ $email }}">

    {{-- Errors chung --}}
    @if ($errors->any())
        <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg">
            @foreach ($errors->all() as $error)
                <p class="text-xs text-red-600">{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <div class="mb-4">
        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Mật khẩu mới</label>
        <div class="relative">
            <input
                id="password" name="password" type="password"
                autocomplete="new-password" autofocus
                class="input-field pr-10 @error('password') border-red-400 bg-red-50 @enderror"
                placeholder="Tối thiểu 8 ký tự"
            >
            <button type="button" onclick="togglePassword('password')"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
            </button>
        </div>
    </div>

    <div class="mb-6">
        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
            Xác nhận mật khẩu mới
        </label>
        <input
            id="password_confirmation" name="password_confirmation" type="password"
            autocomplete="new-password"
            class="input-field @error('password_confirmation') border-red-400 bg-red-50 @enderror"
            placeholder="Nhập lại mật khẩu"
        >
    </div>

    <button type="submit" class="btn-primary">Cập nhật mật khẩu</button>
</form>

<script>
function togglePassword(id) {
    const input = document.getElementById(id);
    input.type = input.type === 'password' ? 'text' : 'password';
}
</script>
@endsection