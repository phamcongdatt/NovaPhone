@extends('layouts.app')
@section('title', 'Đổi mật khẩu - NovaPhone')
@section('content')

<div class="flex min-h-[70vh] items-center justify-center px-4 py-12 sm:px-6 lg:px-8">
    <div class="w-full max-w-md space-y-8 rounded-2xl border border-white/10 bg-night-soft p-8 shadow-2xl">
        <div>
            <h2 class="mt-2 text-center text-3xl font-extrabold tracking-tight text-white">
                Đổi mật khẩu
            </h2>
            <p class="mt-2 text-center text-sm text-gray-400">
                Bảo vệ tài khoản của bạn bằng mật khẩu an toàn
            </p>
        </div>

        @if (session('status'))
            <div class="rounded-xl border border-green-500/20 bg-green-500/10 p-4 text-center text-sm font-medium text-green-400">
                {{ session('status') }}
            </div>
        @endif
        
        <form class="mt-8 space-y-6" action="{{ route('password.change') }}" method="POST">
            @csrf
            
            <div class="space-y-4">
                <div>
                    <label for="current_password" class="block text-sm font-medium text-gray-300">Mật khẩu hiện tại</label>
                    <div class="relative mt-1">
                        <input id="current_password" name="current_password" type="password" required
                               class="block w-full rounded-xl border border-white/10 bg-night px-4 py-3 pr-10 text-white placeholder:text-gray-500 focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500 sm:text-sm">
                        <button type="button" data-toggle-password class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-white focus:outline-none">
                            <svg class="icon-eye hidden size-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
                            <svg class="icon-eye-slash size-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" /></svg>
                        </button>
                    </div>
                    @error('current_password')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-300">Mật khẩu mới</label>
                    <div class="relative mt-1">
                        <input id="password" name="password" type="password" required
                               class="block w-full rounded-xl border border-white/10 bg-night px-4 py-3 pr-10 text-white placeholder:text-gray-500 focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500 sm:text-sm">
                        <button type="button" data-toggle-password class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-white focus:outline-none">
                            <svg class="icon-eye hidden size-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
                            <svg class="icon-eye-slash size-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" /></svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-300">Xác nhận mật khẩu mới</label>
                    <div class="relative mt-1">
                        <input id="password_confirmation" name="password_confirmation" type="password" required
                               class="block w-full rounded-xl border border-white/10 bg-night px-4 py-3 pr-10 text-white placeholder:text-gray-500 focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500 sm:text-sm">
                        <button type="button" data-toggle-password class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-white focus:outline-none">
                            <svg class="icon-eye hidden size-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
                            <svg class="icon-eye-slash size-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" /></svg>
                        </button>
                    </div>
                </div>
            </div>

            <div>
                <button type="submit"
                        class="group relative flex w-full justify-center rounded-xl border border-transparent bg-brand-600 px-4 py-3 text-sm font-bold text-white transition-all duration-200 hover:bg-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 focus:ring-offset-night-soft shadow-lg shadow-brand-600/30">
                    Cập nhật mật khẩu
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
