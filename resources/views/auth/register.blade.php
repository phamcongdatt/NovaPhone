<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Đăng ký — NovaPhone</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: { 400: '#818cf8', 500: '#6366f1', 600: '#4f46e5', 700: '#4338ca' }
                    },
                    fontFamily: { sans: ['Inter', 'system-ui', 'sans-serif'] }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body { background-color: #06060a; }

        /* Hiệu ứng nền radial mờ */
        .page-bg::before {
            content: '';
            position: fixed; inset: 0;
            background:
                radial-gradient(600px circle at 20% 30%, rgba(99,102,241,0.15), transparent 45%),
                radial-gradient(700px circle at 85% 70%, rgba(56,189,248,0.12), transparent 45%);
            pointer-events: none;
        }

        /* Viền phát sáng chạy quanh khung */
        .glow-border { position: relative; border-radius: 1.5rem; }
        .glow-border::before {
            content: '';
            position: absolute; inset: -2px;
            border-radius: inherit;
            padding: 2px;
            background: linear-gradient(130deg, #6366f1, #8b5cf6, #3b82f6, #06b6d4, #6366f1);
            background-size: 300% 300%;
            -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor; mask-composite: exclude;
            animation: glow-rotate 6s linear infinite;
        }
        .glow-border::after {
            content: '';
            position: absolute; inset: -2px;
            border-radius: inherit;
            background: linear-gradient(130deg, #6366f1, #8b5cf6, #3b82f6, #06b6d4, #6366f1);
            background-size: 300% 300%;
            filter: blur(22px);
            opacity: 0.45;
            z-index: -1;
            animation: glow-rotate 6s linear infinite;
        }
        @keyframes glow-rotate {
            0%   { background-position: 0% 50%; }
            50%  { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Hiệu ứng phát sáng sau ảnh điện thoại */
        .phone-glow::before {
            content: '';
            position: absolute; inset: 0;
            background: radial-gradient(circle at 55% 45%, rgba(99,102,241,0.45), rgba(59,130,246,0.18) 35%, transparent 60%);
            filter: blur(30px);
            z-index: 0;
        }

        .field {
            width: 100%;
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.08);
            color: #f1f5f9;
            border-radius: 0.75rem;
            padding: 0.75rem 0.9rem 0.75rem 2.75rem;
            font-size: 0.875rem;
            transition: all 0.2s ease-in-out;
        }
        .field::placeholder { color: #64748b; }
        .field:focus {
            outline: none;
            border-color: #6366f1;
            background: rgba(99,102,241,0.06);
            box-shadow: 0 0 0 3px rgba(99,102,241,0.18);
        }

        .btn-glow {
            background: linear-gradient(90deg, #4f46e5, #7c3aed, #2563eb);
            box-shadow: 0 8px 24px -6px rgba(99,102,241,0.6);
            transition: all 0.2s ease-in-out;
        }
        .btn-glow:hover { filter: brightness(1.1); box-shadow: 0 10px 28px -4px rgba(99,102,241,0.8); transform: translateY(-1px); }

        .social-btn {
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.08);
            transition: all 0.2s ease-in-out;
        }
        .social-btn:hover { background: rgba(255,255,255,0.08); border-color: rgba(255,255,255,0.18); }
    </style>
</head>
<body class="page-bg min-h-screen flex items-center justify-center p-4 sm:p-6 font-sans text-slate-200 antialiased">

    <div class="w-full max-w-6xl relative z-10">

        {{-- Logo --}}
        <div class="flex items-center gap-2 mb-5">
            <div class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-500 to-blue-500 shadow-lg shadow-indigo-500/30">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 21h6a2 2 0 002-2V5a2 2 0 00-2-2H9a2 2 0 00-2 2v14a2 2 0 002 2zm3-3h.01"/>
                </svg>
            </div>
            <span class="text-lg font-bold text-white tracking-tight">NovaPhone</span>
        </div>

        {{-- Khung chính với viền phát sáng --}}
        <div class="glow-border">
            <div class="rounded-3xl bg-[#0b0b12]/95 backdrop-blur-xl overflow-hidden grid grid-cols-1 lg:grid-cols-2">

                {{-- ============ CỘT TRÁI: FORM ============ --}}
                <div class="relative z-10 p-7 sm:p-10">
                    <h2 class="text-2xl font-bold text-white mb-6 text-center">Đăng ký tài khoản</h2>

                    {{-- Flash / lỗi chung --}}
                    @if (session('success'))
                        <div class="mb-4 p-3 rounded-lg bg-green-500/10 border border-green-500/30 text-sm text-green-300">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if ($errors->any() && !$errors->has('name') && !$errors->has('email') && !$errors->has('password'))
                        <div class="mb-4 p-3 rounded-lg bg-red-500/10 border border-red-500/30 text-sm text-red-300">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('register') }}" novalidate class="space-y-4">
                        @csrf

                        {{-- Họ và tên --}}
                        <div>
                            <div class="relative">
                                <svg class="w-4 h-4 text-slate-500 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                <input id="name" name="name" type="text" value="{{ old('name') }}" autocomplete="name" autofocus
                                       class="field @error('name') !border-red-500/60 @enderror" placeholder="Họ và tên">
                            </div>
                            @error('name') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                        </div>

                        {{-- Email --}}
                        <div>
                            <div class="relative">
                                <svg class="w-4 h-4 text-slate-500 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email"
                                       class="field @error('email') !border-red-500/60 @enderror" placeholder="Email">
                            </div>
                            @error('email') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                        </div>

                        {{-- Số điện thoại --}}
                        <div>
                            <div class="relative">
                                <svg class="w-4 h-4 text-slate-500 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                                <input id="phone" name="phone" type="tel" value="{{ old('phone') }}" autocomplete="tel"
                                       class="field @error('phone') !border-red-500/60 @enderror" placeholder="Số điện thoại">
                            </div>
                            @error('phone') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                        </div>

                        {{-- Mật khẩu --}}
                        <div>
                            <div class="relative">
                                <svg class="w-4 h-4 text-slate-500 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                                <input id="password" name="password" type="password" autocomplete="new-password"
                                       class="field pr-10 @error('password') !border-red-500/60 @enderror" placeholder="Mật khẩu"
                                       oninput="checkStrength(this.value)">
                                <button type="button" onclick="togglePassword('password')"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-500 hover:text-slate-300">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </button>
                            </div>
                            <div id="strength-bar" class="mt-1.5 h-1 rounded-full bg-white/10 hidden">
                                <div id="strength-fill" class="h-full rounded-full transition-all duration-300"></div>
                            </div>
                            <p id="strength-text" class="text-xs mt-0.5 hidden"></p>
                            @error('password') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                        </div>

                        {{-- Xác nhận mật khẩu --}}
                        <div>
                            <div class="relative">
                                <svg class="w-4 h-4 text-slate-500 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password"
                                       class="field" placeholder="Xác nhận mật khẩu">
                            </div>
                            @error('password_confirmation') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                        </div>

                        {{-- Điều khoản --}}
                        <label class="flex items-start gap-2 text-xs text-slate-400 cursor-pointer select-none">
                            <input type="checkbox" name="terms" required
                                   class="mt-0.5 w-4 h-4 rounded border-white/20 bg-white/5 text-indigo-500 focus:ring-indigo-500 focus:ring-offset-0">
                            <span>Tôi đồng ý với <a href="#" class="text-indigo-400 hover:text-indigo-300">Điều khoản dịch vụ</a> và <a href="#" class="text-indigo-400 hover:text-indigo-300">Chính sách bảo mật</a> của NovaPhone.</span>
                        </label>

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
                        <img src="{{ asset('images/phones.png') }}" alt="NovaPhone"
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
                    </div>
                </div>

            </div>
        </div>

        <p class="text-center text-xs text-slate-600 mt-5">© {{ date('Y') }} NovaPhone. Mọi quyền được bảo lưu.</p>
    </div>

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
            </form>

            <div class="relative my-6 flex items-center justify-center">
                <div class="absolute inset-x-0 h-px bg-white/10"></div>
                <span class="relative bg-night-soft px-3 text-xs uppercase tracking-wider text-gray-500">Hoặc đăng ký bằng</span>
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
