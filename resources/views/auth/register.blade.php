@extends('layouts.app')
@section('title', 'Đăng ký - NovaPhone')
@section('content')

<div class="flex min-h-[70vh] items-center justify-center px-4 py-12 sm:px-6 lg:px-8">
    <div class="w-full max-w-md space-y-8 rounded-2xl border border-white/10 bg-night-soft p-8 shadow-2xl">
        <div>
            <h2 class="mt-2 text-center text-3xl font-extrabold tracking-tight text-white">
                Tạo tài khoản mới
            </h2>
            <p class="mt-2 text-center text-sm text-gray-400">
                Gia nhập NovaPhone để trải nghiệm dịch vụ mua sắm đẳng cấp
            </p>
        </div>
        
        <form class="mt-8 space-y-6" action="{{ route('register') }}" method="POST">
            @csrf
            
            <div class="space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-300">Họ và tên</label>
                    <input id="name" name="name" type="text" required value="{{ old('name') }}"
                           class="mt-1 block w-full rounded-xl border border-white/10 bg-night px-4 py-3 text-white placeholder:text-gray-500 focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500 sm:text-sm">
                    @error('name')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-300">Địa chỉ Email</label>
                    <input id="email" name="email" type="email" autocomplete="email" required value="{{ old('email') }}"
                           class="mt-1 block w-full rounded-xl border border-white/10 bg-night px-4 py-3 text-white placeholder:text-gray-500 focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500 sm:text-sm">
                    @error('email')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-300">Số điện thoại (tùy chọn)</label>
                    <input id="phone" name="phone" type="tel" value="{{ old('phone') }}"
                           class="mt-1 block w-full rounded-xl border border-white/10 bg-night px-4 py-3 text-white placeholder:text-gray-500 focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500 sm:text-sm">
                    @error('phone')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-300">Mật khẩu</label>
                    <div class="relative mt-1">
                        <input id="password" name="password" type="password" autocomplete="new-password" required
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
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-300">Xác nhận mật khẩu</label>
                    <div class="relative mt-1">
                        <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required
                               class="block w-full rounded-xl border border-white/10 bg-night px-4 py-3 pr-10 text-white placeholder:text-gray-500 focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500 sm:text-sm">
                        <button type="button" data-toggle-password class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-white focus:outline-none">
                            <svg class="icon-eye hidden size-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
                            <svg class="icon-eye-slash size-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" /></svg>
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="flex flex-col gap-1">
                <div class="flex items-start">
                    <div class="flex h-5 items-center">
                        <input id="terms" name="terms" type="checkbox" required
                               class="h-4 w-4 rounded border-white/10 bg-night text-brand-600 focus:ring-brand-500 focus:ring-offset-night-soft">
                    </div>
                    <div class="ml-3 text-sm">
                        <label for="terms" class="font-medium text-gray-300">
                            Tôi đồng ý với <a href="#" class="font-semibold text-brand-400 hover:text-brand-300">Điều khoản dịch vụ</a> và <a href="#" class="font-semibold text-brand-400 hover:text-brand-300">Chính sách bảo mật</a>
                        </label>
<<<<<<< HEAD
=======

                        {{-- Nút đăng ký --}}
                        <button type="submit" class="btn-glow w-full py-3 rounded-xl text-white text-sm font-bold uppercase tracking-wide">
                            Đăng ký ngay
                        </button>
                    </form>

                    {{-- Divider --}}
                    <div class="flex items-center gap-3 my-5">
                        <span class="h-px flex-1 bg-white/10"></span>
                        <span class="text-xs text-slate-500">hoặc đăng ký với</span>
                        <span class="h-px flex-1 bg-white/10"></span>
                    </div>

                    {{-- Social --}}
                    <div class="grid grid-cols-2 gap-3">
                        <button type="button" onclick="handleSocialLogin('google')" class="social-btn flex items-center justify-center gap-2 py-2.5 rounded-xl text-sm text-slate-200 cursor-pointer">
                            <svg class="w-4 h-4" viewBox="0 0 24 24"><path fill="#EA4335" d="M12 10.2v3.9h5.5c-.24 1.4-1.7 4.1-5.5 4.1-3.3 0-6-2.7-6-6.1s2.7-6.1 6-6.1c1.9 0 3.1.8 3.8 1.5l2.6-2.5C16.9 3.4 14.7 2.4 12 2.4 6.9 2.4 2.8 6.5 2.8 11.6S6.9 20.8 12 20.8c5.3 0 8.8-3.7 8.8-9 0-.6-.1-1.1-.2-1.6H12z"/></svg>
                            Google
                        </button>
                        <button type="button" onclick="handleSocialLogin('facebook')" class="social-btn flex items-center justify-center gap-2 py-2.5 rounded-xl text-sm text-slate-200 cursor-pointer">
                            <svg class="w-4 h-4" viewBox="0 0 24 24"><path fill="#1877F2" d="M22 12c0-5.5-4.5-10-10-10S2 6.5 2 12c0 5 3.7 9.1 8.4 9.9v-7H7.9V12h2.5V9.8c0-2.5 1.5-3.9 3.8-3.9 1.1 0 2.2.2 2.2.2v2.5h-1.3c-1.2 0-1.6.8-1.6 1.6V12h2.8l-.4 2.9h-2.3v7C18.3 21.1 22 17 22 12z"/></svg>
                            Facebook
                        </button>
                    </div>

                    <p class="text-center text-sm text-slate-400 mt-6">
                        Đã có tài khoản?
                        <a href="{{ route('login') }}" class="text-indigo-400 hover:text-indigo-300 font-medium">Đăng nhập</a>
                    </p>
                </div>

                {{-- ============ CỘT PHẢI: GIỚI THIỆU ============ --}}
                <div class="relative hidden lg:flex flex-col p-10 bg-gradient-to-r from-transparent via-indigo-950/20 to-blue-950/25">
                    <h2 class="text-3xl font-extrabold text-white leading-tight">Gia nhập NovaPhone</h2>
                    <p class="text-sm text-slate-400 mt-3 max-w-sm">
                        Khám phá thế giới smartphone đỉnh cao. Tích điểm, theo dõi đơn hàng và nhận
                        những ưu đãi độc quyền dành riêng cho thành viên.
                    </p>

                    {{-- Ảnh điện thoại với hiệu ứng phát sáng --}}
                    <div class="phone-glow relative flex-1 flex items-center justify-center my-6">
                        <img src="{{ asset('storage/phones.png') }}" alt="NovaPhone"
                             class="relative z-10 max-h-72 w-auto object-contain drop-shadow-2xl">
                    </div>

                    {{-- Tính năng --}}
                    <div class="grid grid-cols-4 gap-3 text-center">
                        @php
                            $features = [
                                ['Tích điểm',      'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1'],
                                ['Theo dõi đơn',   'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01'],
                                ['Trải nghiệm sớm','M13 10V3L4 14h7v7l9-11h-7z'],
                                ['Ưu đãi riêng',   'M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z'],
                            ];
                        @endphp
                        @foreach ($features as [$label, $icon])
                            <div class="flex flex-col items-center gap-2">
                                <div class="w-11 h-11 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center text-indigo-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}"/>
                                    </svg>
                                </div>
                                <span class="text-xs text-slate-400">{{ $label }}</span>
                            </div>
                        @endforeach
>>>>>>> vin_dev
                    </div>
                </div>
                @error('terms')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <button type="submit"
                        class="group relative flex w-full justify-center rounded-xl border border-transparent bg-brand-600 px-4 py-3 text-sm font-bold text-white transition-all duration-200 hover:bg-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 focus:ring-offset-night-soft shadow-lg shadow-brand-600/30">
                    Đăng ký
                </button>
            </div>

            <div class="relative mt-6">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-white/10"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="bg-night-soft px-2 text-gray-500">Hoặc đăng ký bằng</span>
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

        <div class="text-center text-sm text-gray-400">
            Bạn đã có tài khoản? 
            <a href="{{ route('login') }}" class="font-bold text-brand-400 transition-colors hover:text-brand-300">
                Đăng nhập
            </a>
        </div>
    </div>
</div>

<<<<<<< HEAD
@endsection
=======
    <script>
        function togglePassword(id) {
            const input = document.getElementById(id);
            input.type = input.type === 'password' ? 'text' : 'password';
        }

        function checkStrength(value) {
            const bar  = document.getElementById('strength-bar');
            const fill = document.getElementById('strength-fill');
            const text = document.getElementById('strength-text');

            if (!value) { bar.classList.add('hidden'); text.classList.add('hidden'); return; }
            bar.classList.remove('hidden'); text.classList.remove('hidden');

            let score = 0;
            if (value.length >= 8)           score++;
            if (/[A-Z]/.test(value))         score++;
            if (/[0-9]/.test(value))         score++;
            if (/[^A-Za-z0-9]/.test(value))  score++;

            const levels = [
                { width: '25%',  color: 'bg-red-500',    label: 'Yếu',        textColor: 'text-red-400' },
                { width: '50%',  color: 'bg-orange-400', label: 'Trung bình', textColor: 'text-orange-400' },
                { width: '75%',  color: 'bg-yellow-400', label: 'Khá',        textColor: 'text-yellow-400' },
                { width: '100%', color: 'bg-green-500',  label: 'Mạnh',       textColor: 'text-green-400' },
            ];
            const level = levels[Math.max(0, score - 1)];

            fill.style.width = level.width;
            fill.className   = 'h-full rounded-full transition-all duration-300 ' + level.color;
            text.textContent = 'Độ mạnh: ' + level.label;
            text.className   = 'text-xs mt-0.5 ' + level.textColor;
        }
    </script>

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

    <!-- Social Quick Login Modal -->
    <div id="social-mock-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/75 backdrop-blur-md hidden transition-all duration-300 opacity-0">
        <div class="relative w-full max-w-sm overflow-hidden rounded-3xl border border-white/10 bg-[#0c0c14]/95 p-6 shadow-2xl shadow-indigo-500/10">
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
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 block">Tài khoản có sẵn:</span>
                
                <button onclick="selectMockAccount('Nguyễn Văn A', 'user@novaphone.vn')" class="w-full flex items-center gap-3 rounded-xl border border-white/5 bg-white/5 px-3 py-2 text-left text-xs transition hover:border-indigo-500/50 hover:bg-indigo-500/10 cursor-pointer">
                    <img src="https://api.dicebear.com/7.x/adventurer/svg?seed=A" class="size-8 rounded-lg bg-indigo-500/20" alt="Avatar">
                    <div>
                        <span class="block font-semibold text-white">Nguyễn Văn A</span>
                        <span class="block text-[10px] text-slate-400">user@novaphone.vn (Mặc định)</span>
                    </div>
                </button>

                <button onclick="selectMockAccount('Trần Minh Quân', 'quan.tm@gmail.com')" class="w-full flex items-center gap-3 rounded-xl border border-white/5 bg-white/5 px-3 py-2 text-left text-xs transition hover:border-indigo-500/50 hover:bg-indigo-500/10 cursor-pointer">
                    <img src="https://api.dicebear.com/7.x/adventurer/svg?seed=Quan" class="size-8 rounded-lg bg-indigo-500/20" alt="Avatar">
                    <div>
                        <span class="block font-semibold text-white">Trần Minh Quân</span>
                        <span class="block text-[10px] text-slate-400">quan.tm@gmail.com</span>
                    </div>
                </button>
                
                <button onclick="selectMockAccount('Lê Thị Mai', 'mai.lt@gmail.com')" class="w-full flex items-center gap-3 rounded-xl border border-white/5 bg-white/5 px-3 py-2 text-left text-xs transition hover:border-indigo-500/50 hover:bg-indigo-500/10 cursor-pointer">
                    <img src="https://api.dicebear.com/7.x/adventurer/svg?seed=Mai" class="size-8 rounded-lg bg-indigo-500/20" alt="Avatar">
                    <div>
                        <span class="block font-semibold text-white">Lê Thị Mai</span>
                        <span class="block text-[10px] text-slate-400">mai.lt@gmail.com</span>
                    </div>
                </button>
            </div>

            <!-- Custom Account Form -->
            <div class="space-y-3 pt-3 border-t border-white/10">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 block">Hoặc tự nhập thông tin:</span>
                <div>
                    <input type="text" id="mock-custom-name" placeholder="Họ và tên giả lập" class="w-full rounded-xl border border-white/10 bg-white/5 px-3.5 py-2.5 text-xs text-white outline-none focus:border-indigo-500">
                </div>
                <div class="flex gap-2">
                    <input type="email" id="mock-custom-email" placeholder="Email giả lập" class="flex-1 rounded-xl border border-white/10 bg-white/5 px-3.5 py-2.5 text-xs text-white outline-none focus:border-indigo-500">
                    <button onclick="submitCustomMock()" class="rounded-xl bg-gradient-to-r from-indigo-600 to-blue-600 px-4 text-xs font-bold text-white hover:brightness-110 transition cursor-pointer">Xác nhận</button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
>>>>>>> vin_dev
