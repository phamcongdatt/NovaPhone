@extends('auth.layout')
@section('title', 'Đăng nhập - NovaPhone')
@section('content')
<section data-auth-popup>
    <div>
        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[#0a84ff]">Chào mừng trở lại</p>
        <h1 class="mt-2 text-3xl font-bold tracking-[-0.05em] text-[#171717]">Đăng nhập</h1>
        <p class="mt-2 text-sm leading-6 text-[#777]">Đăng nhập để tiếp tục mua sắm và theo dõi các đơn hàng của bạn.</p>
    </div>

    <form method="POST" action="{{ route('login') }}" data-auth-form class="mt-8 space-y-5">
        @csrf
        <div>
            <label for="login-email" class="mb-2 block text-xs font-semibold text-[#3d3d3d]">Email</label>
            <div class="relative">
                <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center"><span class="size-2 rounded-full bg-[#8b8b93]"></span></span>
                <input id="login-email" name="email" type="email" required autocomplete="email" value="{{ old('email') }}" aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}" class="w-full rounded-xl border bg-[#fbfaf8] py-3 pl-8 pr-4 text-sm outline-none transition focus:border-black focus:bg-white focus:ring-4 focus:ring-black/5 {{ $errors->has('email') ? 'border-red-500' : 'border-[#dfdbd5]' }}" placeholder="Nhập email">
            </div>
            @error('email') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="login-password" class="mb-2 block text-xs font-semibold text-[#3d3d3d]">Mật khẩu</label>
            <div class="relative">
                <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center"><span class="size-2 rounded-full bg-[#8b8b93]"></span></span>
                <input id="login-password" name="password" type="password" required autocomplete="current-password" aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}" class="w-full rounded-xl border bg-[#fbfaf8] py-3 pl-8 pr-4 text-sm outline-none transition focus:border-black focus:bg-white focus:ring-4 focus:ring-black/5 {{ $errors->has('password') ? 'border-red-500' : 'border-[#dfdbd5]' }}" placeholder="Nhập mật khẩu">
            </div>
            @error('password') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="flex items-center justify-between gap-4 text-xs">
            <label class="flex cursor-pointer items-center gap-2 text-[#555]">
                <input name="remember" type="checkbox" value="1" class="size-3.5 rounded border-[#cfcac3] text-black focus:ring-black" {{ old('remember') ? 'checked' : '' }}>
                Ghi nhớ đăng nhập
            </label>
            <a href="{{ route('password.request') }}" class="font-semibold text-[#0a5ec2] transition hover:text-[#064b99]">Quên mật khẩu?</a>
        </div>

        <button type="submit" class="w-full rounded-xl bg-black px-5 py-3.5 text-sm font-semibold text-white transition duration-300 hover:bg-[#242424] focus:outline-none focus:ring-4 focus:ring-black/15">Đăng nhập</button>
    </form>

    <div class="my-7 flex items-center gap-3 text-[11px] text-[#999]">
        <span class="h-px flex-1 bg-[#e6e2dc]"></span>
        <span>Hoặc tiếp tục với</span>
        <span class="h-px flex-1 bg-[#e6e2dc]"></span>
    </div>

    <div class="grid grid-cols-2 gap-3">
        <a href="{{ route('google.login') }}" class="rounded-xl border border-[#dfdbd5] bg-white px-4 py-3 text-center text-xs font-semibold text-[#303030] transition hover:border-black hover:bg-[#fbfaf8]">Google</a>
        <a href="{{ route('auth.social.redirect', ['provider' => 'facebook']) }}" class="rounded-xl border border-[#dfdbd5] bg-white px-4 py-3 text-center text-xs font-semibold text-[#303030] transition hover:border-black hover:bg-[#fbfaf8]">Facebook</a>
    </div>

    <p class="mt-7 text-center text-xs text-[#777]">Chưa có tài khoản? <a href="{{ route('register') }}" class="font-semibold text-[#0a5ec2] transition hover:text-[#064b99]">Đăng ký ngay</a></p>
</section>
@endsection
