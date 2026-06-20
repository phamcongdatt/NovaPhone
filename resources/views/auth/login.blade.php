<<<<<<< HEAD
@extends('layouts.app')
@section('title', 'Đăng nhập - NovaPhone')
@section('content')

<div class="flex min-h-[70vh] items-center justify-center px-4 py-12 sm:px-6 lg:px-8">
    <div class="w-full max-w-md space-y-8 rounded-2xl border border-white/10 bg-night-soft p-8 shadow-2xl">
        <div>
            <h2 class="mt-2 text-center text-3xl font-extrabold tracking-tight text-white">
                Chào mừng trở lại
            </h2>
            <p class="mt-2 text-center text-sm text-gray-400">
                Đăng nhập để quản lý đơn hàng và nhận ưu đãi riêng
            </p>
=======
<!DOCTYPE html>
<html lang="vi" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Đăng nhập tài khoản | NovaPhone</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-night font-sans text-gray-100 antialiased min-h-screen flex flex-col">
<div class="hero-glow flex min-h-[calc(100vh-68px-340px)] items-center justify-center px-4 py-16 sm:px-6">
    <div class="w-full max-w-md overflow-hidden rounded-3xl border border-white/10 bg-night-soft/60 p-8 shadow-2xl shadow-black/50 backdrop-blur-2xl">
        
        {{-- Header Form --}}
        <div class="text-center">
            <span class="inline-flex size-12 items-center justify-center rounded-2xl bg-brand-600 text-xl font-black text-white shadow-lg shadow-brand-600/30">N</span>
            <h2 class="mt-4 text-2xl font-black tracking-tight text-white sm:text-3xl">Đăng nhập tài khoản</h2>
            <p class="mt-2 text-sm text-gray-400">Trải nghiệm mua sắm đẳng cấp tại NovaPhone</p>
>>>>>>> vin_dev
        </div>
        
        <form class="mt-8 space-y-6" action="{{ route('login') }}" method="POST">
            @csrf
            
            <div class="space-y-4">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-300">Địa chỉ Email</label>
                    <input id="email" name="email" type="email" autocomplete="email" required value="{{ old('email') }}"
                           class="mt-1 block w-full rounded-xl border border-white/10 bg-night px-4 py-3 text-white placeholder:text-gray-500 focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500 sm:text-sm">
                    @error('email')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-300">Mật khẩu</label>
                    <div class="relative mt-1">
                        <input id="password" name="password" type="password" autocomplete="current-password" required
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
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input id="remember" name="remember" type="checkbox"
                           class="size-4 rounded border-white/10 bg-night text-brand-500 focus:ring-brand-500 focus:ring-offset-night-soft">
                    <label for="remember" class="ml-2 block text-sm text-gray-300">
                        Nhớ đăng nhập
                    </label>
                </div>

                <div class="text-sm">
                    <a href="#" class="font-medium text-brand-400 hover:text-brand-300">
                        Quên mật khẩu?
                    </a>
                </div>
            </div>

            <div>
                <button type="submit"
                        class="group relative flex w-full justify-center rounded-xl border border-transparent bg-brand-600 px-4 py-3 text-sm font-bold text-white transition-all duration-200 hover:bg-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 focus:ring-offset-night-soft shadow-lg shadow-brand-600/30">
                    Đăng nhập
                </button>
            </div>

            <div class="relative mt-6">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-white/10"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="bg-night-soft px-2 text-gray-500">Hoặc tiếp tục với</span>
                </div>
            </div>

            <div>
                <a href="{{ route('google.login') }}" class="flex w-full items-center justify-center gap-3 rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm font-semibold text-white transition-all duration-200 hover:bg-white/10 hover:border-white/20">
                    <svg class="size-5" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                    </svg>
                    Google
                </a>
            </div>
        </form>

<<<<<<< HEAD
        <div class="text-center text-sm text-gray-400">
            Bạn chưa có tài khoản? 
            <a href="{{ route('register') }}" class="font-bold text-brand-400 transition-colors hover:text-brand-300">
                Đăng ký ngay
=======
        {{-- Divider mạng xã hội --}}
        <div class="relative my-6 flex items-center justify-center">
            <div class="absolute inset-x-0 h-px bg-white/10"></div>
            <span class="relative bg-night-soft px-3 text-xs uppercase tracking-wider text-gray-500">Hoặc đăng nhập bằng</span>
        </div>

        {{-- Social --}}
        <div class="grid grid-cols-2 gap-3 mb-6">
            <button type="button" onclick="handleSocialLogin('google')" class="flex items-center justify-center gap-2 py-2.5 rounded-xl border border-white/10 bg-white/5 text-sm text-slate-200 transition hover:bg-white/10 hover:border-white/20 cursor-pointer">
                <svg class="w-4 h-4" viewBox="0 0 24 24"><path fill="#EA4335" d="M12 10.2v3.9h5.5c-.24 1.4-1.7 4.1-5.5 4.1-3.3 0-6-2.7-6-6.1s2.7-6.1 6-6.1c1.9 0 3.1.8 3.8 1.5l2.6-2.5C16.9 3.4 14.7 2.4 12 2.4 6.9 2.4 2.8 6.5 2.8 11.6S6.9 20.8 12 20.8c5.3 0 8.8-3.7 8.8-9 0-.6-.1-1.1-.2-1.6H12z"/></svg>
                Google
            </button>
            <button type="button" onclick="handleSocialLogin('facebook')" class="flex items-center justify-center gap-2 py-2.5 rounded-xl border border-white/10 bg-white/5 text-sm text-slate-200 transition hover:bg-white/10 hover:border-white/20 cursor-pointer">
                <svg class="w-4 h-4" viewBox="0 0 24 24"><path fill="#1877F2" d="M22 12c0-5.5-4.5-10-10-10S2 6.5 2 12c0 5 3.7 9.1 8.4 9.9v-7H7.9V12h2.5V9.8c0-2.5 1.5-3.9 3.8-3.9 1.1 0 2.2.2 2.2.2v2.5h-1.3c-1.2 0-1.6.8-1.6 1.6V12h2.8l-.4 2.9h-2.3v7C18.3 21.1 22 17 22 12z"/></svg>
                Facebook
            </button>
        </div>

        {{-- Divider --}}
        <div class="relative my-6 flex items-center justify-center">
            <div class="absolute inset-x-0 h-px bg-white/10"></div>
            <span class="relative bg-night-soft px-3 text-xs uppercase tracking-wider text-gray-500">Hoặc thử nghiệm nhanh</span>
        </div>

        {{-- Cụm đăng nhập nhanh (Quick Login) --}}
        <div class="grid gap-3">
            <a 
                href="{{ route('quick-login', ['email' => 'user@novaphone.vn']) }}"
                class="flex items-center justify-between rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm transition hover:border-brand-500/50 hover:bg-brand-600/10"
            >
                <div class="flex items-center gap-3">
                    <span class="flex size-7 items-center justify-center rounded-lg bg-emerald-500/15 text-[11px] font-bold text-emerald-400">A</span>
                    <div class="text-left">
                        <span class="block font-semibold text-white">Khách hàng Test</span>
                        <span class="block text-xs text-gray-500">Nguyễn Văn A (Mặc định)</span>
                    </div>
                </div>
                <svg class="size-4 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                </svg>
            </a>

            <a 
                href="{{ route('quick-login', ['email' => 'admin@novaphone.vn']) }}"
                class="flex items-center justify-between rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm transition hover:border-red-500/50 hover:bg-red-600/10"
            >
                <div class="flex items-center gap-3">
                    <span class="flex size-7 items-center justify-center rounded-lg bg-red-500/15 text-[11px] font-bold text-red-400">AD</span>
                    <div class="text-left">
                        <span class="block font-semibold text-white">Quản trị viên Test</span>
                        <span class="block text-xs text-gray-500">admin@novaphone.vn</span>
                    </div>
                </div>
                <svg class="size-4 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                </svg>
>>>>>>> vin_dev
            </a>
        </div>
    </div>
</div>

<<<<<<< HEAD
@endsection
=======
<!-- Social Quick Login Modal -->
<div id="social-mock-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/75 backdrop-blur-md hidden transition-all duration-300 opacity-0">
    <div class="relative w-full max-w-sm overflow-hidden rounded-3xl border border-white/10 bg-[#0c0c14]/95 p-6 shadow-2xl shadow-indigo-500/10 text-slate-200">
        <!-- Close Button -->
        <button onclick="closeSocialMockModal()" class="absolute right-4 top-4 text-slate-400 hover:text-white transition cursor-pointer">
            <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>

        <div class="text-center mb-6">
            <span id="modal-provider-icon" class="inline-flex size-12 items-center justify-center rounded-2xl bg-white/5 mb-3 text-slate-200">
                <!-- SVG Icon will be inserted here dynamically -->
            </span>
            <h3 class="text-lg font-bold text-white">Giả lập Đăng nhập <span id="modal-provider-name">Google</span></h3>
            <p class="text-xs text-slate-400 mt-1">Hệ thống đang chạy ở local. Hãy chọn hoặc nhập tài khoản giả lập để đăng nhập nhanh.</p>
        </div>

        <!-- Predefined Accounts -->
        <div class="space-y-2.5 mb-5">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 block text-left">Tài khoản có sẵn:</span>
            
            <button onclick="selectMockAccount('Nguyễn Văn A', 'user@novaphone.vn')" class="w-full flex items-center gap-3 rounded-xl border border-white/5 bg-white/5 px-3 py-2 text-left text-xs transition hover:border-brand-500/50 hover:bg-brand-600/10 cursor-pointer">
                <img src="https://api.dicebear.com/7.x/adventurer/svg?seed=A" class="size-8 rounded-lg bg-brand-500/20" alt="Avatar">
                <div class="text-left">
                    <span class="block font-semibold text-white">Nguyễn Văn A</span>
                    <span class="block text-[10px] text-slate-400">user@novaphone.vn (Mặc định)</span>
                </div>
            </button>

            <button onclick="selectMockAccount('Trần Minh Quân', 'quan.tm@gmail.com')" class="w-full flex items-center gap-3 rounded-xl border border-white/5 bg-white/5 px-3 py-2 text-left text-xs transition hover:border-brand-500/50 hover:bg-brand-600/10 cursor-pointer">
                <img src="https://api.dicebear.com/7.x/adventurer/svg?seed=Quan" class="size-8 rounded-lg bg-brand-500/20" alt="Avatar">
                <div class="text-left">
                    <span class="block font-semibold text-white">Trần Minh Quân</span>
                    <span class="block text-[10px] text-slate-400">quan.tm@gmail.com</span>
                </div>
            </button>
            
            <button onclick="selectMockAccount('Lê Thị Mai', 'mai.lt@gmail.com')" class="w-full flex items-center gap-3 rounded-xl border border-white/5 bg-white/5 px-3 py-2 text-left text-xs transition hover:border-brand-500/50 hover:bg-brand-600/10 cursor-pointer">
                <img src="https://api.dicebear.com/7.x/adventurer/svg?seed=Mai" class="size-8 rounded-lg bg-brand-500/20" alt="Avatar">
                <div class="text-left">
                    <span class="block font-semibold text-white">Lê Thị Mai</span>
                    <span class="block text-[10px] text-slate-400">mai.lt@gmail.com</span>
                </div>
            </button>
        </div>

        <!-- Custom Account Form -->
        <div class="space-y-3 pt-3 border-t border-white/10">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 block text-left">Hoặc tự nhập thông tin:</span>
            <div>
                <input type="text" id="mock-custom-name" placeholder="Họ và tên giả lập" class="w-full rounded-xl border border-white/10 bg-white/5 px-3.5 py-2.5 text-xs text-white outline-none focus:border-brand-500">
            </div>
            <div class="flex gap-2">
                <input type="email" id="mock-custom-email" placeholder="Email giả lập" class="flex-1 rounded-xl border border-white/10 bg-white/5 px-3.5 py-2.5 text-xs text-white outline-none focus:border-brand-500">
                <button onclick="submitCustomMock()" class="rounded-xl bg-gradient-to-r from-brand-600 to-brand-500 px-4 text-xs font-bold text-white hover:brightness-110 transition cursor-pointer">Xác nhận</button>
            </div>
        </div>
    </div>
</div>
    <script>
        let currentProvider = '';

        const GOOGLE_SVG = `<svg class="w-6 h-6" viewBox="0 0 24 24"><path fill="#EA4335" d="M12 10.2v3.9h5.5c-.24 1.4-1.7 4.1-5.5 4.1-3.3 0-6-2.7-6-6.1s2.7-6.1 6-6.1c1.9 0 3.1.8 3.8 1.5l2.6-2.5C16.9 3.4 14.7 2.4 12 2.4 6.9 2.4 2.8 6.5 2.8 11.6S6.9 20.8 12 20.8c5.3 0 8.8-3.7 8.8-9 0-.6-.1-1.1-.2-1.6H12z"/></svg>`;
        const FACEBOOK_SVG = `<svg class="w-6 h-6" viewBox="0 0 24 24"><path fill="#1877F2" d="M22 12c0-5.5-4.5-10-10-10S2 6.5 2 12c0 5 3.7 9.1 8.4 9.9v-7H7.9V12h2.5V9.8c0-2.5 1.5-3.9 3.8-3.9 1.1 0 2.2.2 2.2.2v2.5h-1.3c-1.2 0-1.6.8-1.6 1.6V12h2.8l-.4 2.9h-2.3v7C18.3 21.1 22 17 22 12z"/></svg>`;

        function handleSocialLogin(provider) {
            const isConfigured = {
                google: {{ config('services.google.client_id') ? 'true' : 'false' }},
                facebook: {{ config('services.facebook.client_id') ? 'true' : 'false' }}
            };

            if (isConfigured[provider]) {
                window.location.href = `/auth/${provider}/redirect`;
            } else {
                currentProvider = provider;
                document.getElementById('modal-provider-name').textContent = provider === 'google' ? 'Google' : 'Facebook';
                document.getElementById('modal-provider-icon').innerHTML = provider === 'google' ? GOOGLE_SVG : FACEBOOK_SVG;
                
                const modal = document.getElementById('social-mock-modal');
                modal.classList.remove('hidden');
                setTimeout(() => {
                    modal.classList.remove('opacity-0');
                }, 50);
            }
        }

        function closeSocialMockModal() {
            const modal = document.getElementById('social-mock-modal');
            modal.classList.add('opacity-0');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }

        function selectMockAccount(name, email) {
            submitMockLogin(name, email);
        }

        function submitCustomMock() {
            const name = document.getElementById('mock-custom-name').value.trim();
            const email = document.getElementById('mock-custom-email').value.trim();
            if (!name || !email) {
                alert('Vui lòng điền họ tên và email giả lập.');
                return;
            }
            submitMockLogin(name, email);
        }

        function submitMockLogin(name, email) {
            const csrfToken = '{{ csrf_token() }}';
            
            fetch('/auth/login', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    provider: currentProvider,
                    provider_id: 'mock_' + currentProvider + '_' + Math.random().toString(36).substr(2, 9),
                    email: email,
                    name: name,
                    avatar: 'https://api.dicebear.com/7.x/adventurer/svg?seed=' + encodeURIComponent(name)
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    window.location.href = data.redirect;
                } else {
                    alert(data.message || 'Đăng nhập thất bại.');
                }
            })
            .catch(err => {
                console.error(err);
                alert('Có lỗi mạng xảy ra khi đăng nhập.');
            });
        }
    </script>
</body>
</html>
>>>>>>> vin_dev
