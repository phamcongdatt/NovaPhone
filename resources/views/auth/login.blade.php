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
        </div>

        @if (session('error'))
            <div class="mt-6 rounded-xl border border-red-500/20 bg-red-500/10 p-4 text-sm text-red-400">
                {{ session('error') }}
            </div>
        @endif

        {{-- Form đăng nhập chuẩn --}}
        <form method="POST" action="{{ route('login') }}" class="mt-8 space-y-5">
            @csrf
            
            {{-- Email --}}
            <div class="space-y-1.5">
                <label for="email" class="text-xs font-bold uppercase tracking-wider text-gray-400">Địa chỉ email</label>
                <div class="relative">
                    <input 
                        type="email" 
                        name="email" 
                        id="email" 
                        value="{{ old('email') }}" 
                        required 
                        placeholder="ten@novaphone.vn"
                        class="w-full rounded-xl border border-white/10 bg-white/5 py-3 pl-11 pr-4 text-sm text-white outline-none transition duration-200 focus:border-brand-500 focus:bg-white/[0.08] focus:ring-2 focus:ring-brand-500/25"
                    >
                    <svg class="absolute left-4 top-1/2 size-4 -translate-y-1/2 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25H4.5A2.25 2.25 0 0 1 2.25 17.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5H4.5a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                    </svg>
                </div>
                @error('email')
                    <span class="text-xs text-red-500">{{ $message }}</span>
                @enderror
            </div>

            {{-- Mật khẩu --}}
            <div class="space-y-1.5">
                <div class="flex items-center justify-between">
                    <label for="password" class="text-xs font-bold uppercase tracking-wider text-gray-400">Mật khẩu</label>
                    <a href="{{ route('password.request') }}" class="text-xs font-bold text-brand-400 hover:text-brand-300 transition-colors">Quên mật khẩu?</a>
                </div>
                <div class="relative">
                    <input 
                        type="password" 
                        name="password" 
                        id="password" 
                        required 
                        placeholder="••••••••"
                        class="w-full rounded-xl border border-white/10 bg-white/5 py-3 pl-11 pr-4 text-sm text-white outline-none transition duration-200 focus:border-brand-500 focus:bg-white/[0.08] focus:ring-2 focus:ring-brand-500/25"
                    >
                    <svg class="absolute left-4 top-1/2 size-4 -translate-y-1/2 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                    </svg>
                </div>
            </div>

            {{-- Remember me --}}
            <div class="flex items-center">
                <input 
                    type="checkbox" 
                    name="remember" value="1" 
                    id="remember" 
                    class="size-4 rounded border-white/10 bg-white/5 text-brand-600 focus:ring-brand-500/30"
                >
                <label for="remember" class="ml-2 text-sm text-gray-400">Ghi nhớ đăng nhập</label>
            </div>

            {{-- Nút Đăng nhập --}}
            <button 
                type="submit" 
                class="w-full rounded-xl bg-gradient-to-r from-brand-600 to-brand-500 py-3 text-sm font-bold text-white shadow-lg shadow-brand-600/25 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-brand-600/35"
            >
                Đăng nhập
            </button>
        </form>

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
            </a>
        </div>

        {{-- Đăng ký --}}
        <p class="mt-8 text-center text-sm text-gray-500">
            Chưa có tài khoản? 
            <a href="{{ route('register') }}" class="font-bold text-brand-400 hover:text-brand-300 transition-colors">Đăng ký ngay</a>
        </p>

    </div>
</div>

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
