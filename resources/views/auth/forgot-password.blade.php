<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Quên mật khẩu NovaPhone — Nhập email để nhận link đặt lại mật khẩu nhanh chóng.">
    <title>Quên mật khẩu — NovaPhone</title>

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

        /* Pulse animation cho icon thành công */
        @keyframes success-pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.08); }
        }
        .success-pulse { animation: success-pulse 2s ease-in-out infinite; }

        /* Fade-in cho thông báo */
        @keyframes fade-slide-in {
            from { opacity: 0; transform: translateY(-8px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .fade-slide-in { animation: fade-slide-in 0.4s ease-out; }
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

                {{-- Quay lại đăng nhập --}}
                <a href="{{ route('login') }}" class="inline-flex items-center text-xs text-slate-500 hover:text-indigo-400 transition mb-6 group">
                    <svg class="w-4 h-4 mr-1.5 transition-transform group-hover:-translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Quay lại đăng nhập
                </a>

                {{-- Header --}}
                <div class="mb-6">
                    <div class="w-12 h-12 rounded-xl bg-indigo-500/15 border border-indigo-500/20 flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h1 class="text-2xl font-bold text-white mb-1">Quên mật khẩu?</h1>
                    <p class="text-sm text-slate-400">Nhập email đã đăng ký, chúng tôi sẽ gửi link đặt lại mật khẩu cho bạn.</p>
                </div>

                {{-- Thông báo thành công --}}
                @if (session('status'))
                    <div class="fade-slide-in mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/25">
                        <div class="flex items-start gap-3">
                            <div class="success-pulse flex-shrink-0 w-9 h-9 rounded-lg bg-emerald-500/20 flex items-center justify-center">
                                <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-emerald-300">{{ session('status') }}</p>
                                <p class="text-xs text-slate-400 mt-1">Vui lòng kiểm tra hộp thư email (bao gồm cả thư rác).</p>
                            </div>
                        </div>

                        {{-- Link trực tiếp cho dev/local --}}
                        @if (session('dev_link'))
                            <div class="mt-3 p-3 rounded-lg bg-amber-500/10 border border-amber-500/20">
                                <p class="text-[10px] font-bold uppercase tracking-wider text-amber-400/80 mb-1.5">🔧 Môi trường phát triển</p>
                                <a href="{{ session('dev_link') }}"
                                   class="block text-xs text-indigo-400 hover:text-indigo-300 break-all underline decoration-indigo-400/30 hover:decoration-indigo-300/60 transition">
                                    {{ session('dev_link') }}
                                </a>
                            </div>
                        @endif
                    </div>
                @endif

                {{-- Form --}}
                <form method="POST" action="{{ route('password.email') }}" novalidate class="space-y-5">
                    @csrf

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-xs font-medium text-slate-400 mb-1.5">Địa chỉ email</label>
                        <div class="relative">
                            <svg class="w-4 h-4 text-slate-500 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <input
                                id="email" name="email" type="email"
                                value="{{ old('email') }}"
                                autofocus
                                class="field @error('email') field-error @enderror"
                                placeholder="ban@example.com"
                            >
                        </div>
                        @error('email')
                            <p class="mt-1.5 text-xs text-red-400 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Nút gửi --}}
                    <button type="submit" class="btn-glow w-full py-3 rounded-xl text-white text-sm font-bold uppercase tracking-wide">
                        Gửi link đặt lại mật khẩu
                    </button>
                </form>

                {{-- Footer --}}
                <p class="text-center text-sm text-slate-500 mt-6">
                    Đã nhớ mật khẩu?
                    <a href="{{ route('login') }}" class="text-indigo-400 hover:text-indigo-300 font-medium">Đăng nhập</a>
                </p>
            </div>
        </div>

        <p class="text-center text-xs text-slate-600 mt-5">© {{ date('Y') }} NovaPhone. Mọi quyền được bảo lưu.</p>
    </div>

</body>
</html>