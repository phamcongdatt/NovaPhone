<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Đặt lại mật khẩu tài khoản NovaPhone — Nhập mật khẩu mới để bảo mật tài khoản của bạn.">
    <title>Đặt lại mật khẩu — NovaPhone</title>

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
        .glow-border { position: relative; }
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
        .field.field-error {
            border-color: rgba(239,68,68,0.6);
            background: rgba(239,68,68,0.06);
        }

        .btn-glow {
            background: linear-gradient(90deg, #4f46e5, #7c3aed, #2563eb);
            box-shadow: 0 8px 24px -6px rgba(99,102,241,0.6);
            transition: all 0.2s ease-in-out;
        }
        .btn-glow:hover { filter: brightness(1.1); box-shadow: 0 10px 28px -4px rgba(99,102,241,0.8); transform: translateY(-1px); }

        /* Thanh hiển thị độ mạnh mật khẩu */
        .strength-bar { height: 3px; border-radius: 9999px; transition: all 0.3s ease; }
    </style>
</head>
<body class="page-bg min-h-screen flex items-center justify-center p-4 sm:p-6 font-sans text-slate-200 antialiased">

    <div class="w-full max-w-md relative z-10">

        {{-- Hiệu ứng nền --}}
        <div aria-hidden="true" class="pointer-events-none absolute inset-0">
            <div class="absolute left-1/2 top-[-18rem] size-[40rem] -translate-x-1/2 rounded-full bg-brand-600/15 blur-3xl"></div>
            <div class="absolute bottom-[-16rem] right-[-10rem] size-[32rem] rounded-full bg-blue-400/10 blur-3xl"></div>
        </div>

        {{-- Logo --}}
        <a href="{{ route('home') }}" class="mx-auto mb-7 flex w-fit items-center justify-center" aria-label="NovaPhone - Trang chủ">
            <img src="{{ asset('images/brand/nova-phone-logo.webp') }}" alt="NovaPhone" class="h-16 w-auto max-w-[240px] object-contain sm:h-[72px]">
        </a>

        {{-- Khung chính với viền phát sáng --}}
        <div class="glow-border rounded-2xl">
            <div class="bg-[#0b0b12]/95 backdrop-blur-xl rounded-2xl p-7 sm:p-10">

                {{-- Header --}}
                <div class="mb-6">
                    <div class="w-12 h-12 rounded-xl bg-indigo-500/15 border border-indigo-500/20 flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                        </svg>
                    </div>
                    <h1 class="text-2xl font-bold text-white mb-1">Đặt lại mật khẩu</h1>
                    <p class="text-sm text-slate-400">Nhập mật khẩu mới để bảo mật tài khoản của bạn.</p>
                </div>

                {{-- Errors chung --}}
                @if ($errors->any())
                    <div class="mb-5 p-3.5 rounded-xl bg-red-500/10 border border-red-500/25">
                        <div class="flex items-start gap-2.5">
                            <svg class="w-4 h-4 text-red-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div>
                                @foreach ($errors->all() as $error)
                                    <p class="text-xs text-red-300">{{ $error }}</p>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Form --}}
                <form method="POST" action="{{ route('password.update') }}" novalidate class="space-y-4">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">
                    <input type="hidden" name="email" value="{{ $email }}">

                    {{-- Hiển thị email đang reset --}}
                    <div class="p-3 rounded-xl bg-white/[0.03] border border-white/[0.06]">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">Tài khoản</p>
                        <p class="text-sm text-white font-medium">{{ $email }}</p>
                    </div>

                    {{-- Mật khẩu mới --}}
                    <div>
                        <label for="password" class="block text-xs font-medium text-slate-400 mb-1.5">Mật khẩu mới</label>
                        <div class="relative">
                            <svg class="w-4 h-4 text-slate-500 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            <input
                                id="password" name="password" type="password"
                                autocomplete="new-password" autofocus
                                class="field pr-10 @error('password') field-error @enderror"
                                placeholder="Tối thiểu 8 ký tự"
                                oninput="checkStrength(this.value)"
                            >
                            <button type="button" onclick="togglePassword('password')"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-500 hover:text-slate-300 transition">
                                <svg id="password-eye" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                        </div>
                        {{-- Thanh hiển thị độ mạnh mật khẩu --}}
                        <div class="mt-2 flex gap-1">
                            <div id="str-1" class="strength-bar flex-1 bg-white/10"></div>
                            <div id="str-2" class="strength-bar flex-1 bg-white/10"></div>
                            <div id="str-3" class="strength-bar flex-1 bg-white/10"></div>
                            <div id="str-4" class="strength-bar flex-1 bg-white/10"></div>
                        </div>
                        <p id="str-label" class="text-[10px] text-slate-500 mt-1 h-3"></p>
                    </div>

                    {{-- Xác nhận mật khẩu --}}
                    <div>
                        <label for="password_confirmation" class="block text-xs font-medium text-slate-400 mb-1.5">
                            Xác nhận mật khẩu mới
                        </label>
                        <div class="relative">
                            <svg class="w-4 h-4 text-slate-500 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            <input
                                id="password_confirmation" name="password_confirmation" type="password"
                                autocomplete="new-password"
                                class="field pr-10 @error('password_confirmation') field-error @enderror"
                                placeholder="Nhập lại mật khẩu mới"
                            >
                            <button type="button" onclick="togglePassword('password_confirmation')"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-500 hover:text-slate-300 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Nút cập nhật --}}
                    <button type="submit" class="btn-glow w-full py-3 rounded-xl text-white text-sm font-bold uppercase tracking-wide mt-2">
                        Cập nhật mật khẩu
                    </button>
                </form>

                {{-- Footer --}}
                <p class="text-center text-sm text-slate-500 mt-6">
                    Quay lại
                    <a href="{{ route('login') }}" class="text-indigo-400 hover:text-indigo-300 font-medium">Đăng nhập</a>
                </p>
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
            let score = 0;
            if (value.length >= 8) score++;
            if (/[A-Z]/.test(value)) score++;
            if (/[0-9]/.test(value)) score++;
            if (/[^A-Za-z0-9]/.test(value)) score++;

            const colors = ['', '#ef4444', '#f59e0b', '#3b82f6', '#22c55e'];
            const labels = ['', 'Yếu', 'Trung bình', 'Mạnh', 'Rất mạnh'];

            for (let i = 1; i <= 4; i++) {
                const bar = document.getElementById('str-' + i);
                bar.style.background = i <= score ? colors[score] : 'rgba(255,255,255,0.1)';
            }
            document.getElementById('str-label').textContent = value.length > 0 ? labels[score] : '';
            document.getElementById('str-label').style.color = colors[score] || '#64748b';
        }
    </script>

</body>
</html>