@extends('auth.layout')
@section('title', 'Đăng ký - NovaPhone')
@section('content')
<section data-auth-popup>
    <div>
        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[#0a84ff]">Tạo tài khoản</p>
        <h1 class="mt-2 text-3xl font-bold tracking-[-0.05em] text-[#171717]">Đăng ký</h1>
        <p class="mt-2 text-sm leading-6 text-[#777]">Tạo tài khoản NovaPhone để lưu ưu đãi, theo dõi đơn hàng và mua sắm nhanh hơn.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" data-auth-form class="mt-7 grid gap-4 sm:grid-cols-2">
        @csrf
        <div>
            <label for="register-name" class="mb-2 block text-xs font-semibold text-[#3d3d3d]">Họ và tên</label>
            <div class="relative">
                <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center"><span class="size-2 rounded-full bg-[#8b8b93]"></span></span>
                <input id="register-name" name="name" type="text" required maxlength="255" autocomplete="name" value="{{ old('name') }}" aria-invalid="{{ $errors->has('name') ? 'true' : 'false' }}" class="w-full rounded-xl border bg-[#fbfaf8] py-3 pl-8 pr-4 text-sm outline-none transition focus:border-black focus:bg-white focus:ring-4 focus:ring-black/5 {{ $errors->has('name') ? 'border-red-500' : 'border-[#dfdbd5]' }}" placeholder="Nhập họ và tên">
            </div>
            @error('name') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="register-email" class="mb-2 block text-xs font-semibold text-[#3d3d3d]">Email</label>
            <div class="relative">
                <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center"><span class="size-2 rounded-full bg-[#8b8b93]"></span></span>
                <input id="register-email" name="email" type="email" required maxlength="255" autocomplete="email" value="{{ old('email') }}" aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}" class="w-full rounded-xl border bg-[#fbfaf8] py-3 pl-8 pr-4 text-sm outline-none transition focus:border-black focus:bg-white focus:ring-4 focus:ring-black/5 {{ $errors->has('email') ? 'border-red-500' : 'border-[#dfdbd5]' }}" placeholder="Nhập email">
            </div>
            @error('email') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
        <div class="sm:col-span-2">
            <label for="register-phone" class="mb-2 block text-xs font-semibold text-[#3d3d3d]">Số điện thoại <span class="font-normal text-[#999]">(tùy chọn)</span></label>
            <div class="relative">
                <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center"><span class="size-2 rounded-full bg-[#8b8b93]"></span></span>
                <input id="register-phone" name="phone" type="tel" maxlength="15" autocomplete="tel" value="{{ old('phone') }}" aria-invalid="{{ $errors->has('phone') ? 'true' : 'false' }}" class="w-full rounded-xl border bg-[#fbfaf8] py-3 pl-8 pr-4 text-sm outline-none transition focus:border-black focus:bg-white focus:ring-4 focus:ring-black/5 {{ $errors->has('phone') ? 'border-red-500' : 'border-[#dfdbd5]' }}" placeholder="Nhập số điện thoại">
            </div>
            @error('phone') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="register-password" class="mb-2 block text-xs font-semibold text-[#3d3d3d]">Mật khẩu</label>
            <div class="relative">
                <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center"><span class="size-2 rounded-full bg-[#8b8b93]"></span></span>
                <input id="register-password" name="password" type="password" required minlength="8" autocomplete="new-password" data-password-strength-input aria-describedby="password-strength-help" aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}" class="w-full rounded-xl border bg-[#fbfaf8] py-3 pl-8 pr-4 text-sm outline-none transition focus:border-black focus:bg-white focus:ring-4 focus:ring-black/5 {{ $errors->has('password') ? 'border-red-500' : 'border-[#dfdbd5]' }}" placeholder="Tối thiểu 8 ký tự">
            </div>
            @error('password') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="register-password-confirmation" class="mb-2 block text-xs font-semibold text-[#3d3d3d]">Xác nhận mật khẩu</label>
            <div class="relative">
                <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center"><span class="size-2 rounded-full bg-[#8b8b93]"></span></span>
                <input id="register-password-confirmation" name="password_confirmation" type="password" required minlength="8" autocomplete="new-password" data-password-confirmation aria-describedby="password-confirmation-status" class="w-full rounded-xl border border-[#dfdbd5] bg-[#fbfaf8] py-3 pl-8 pr-4 text-sm outline-none transition focus:border-black focus:bg-white focus:ring-4 focus:ring-black/5" placeholder="Nhập lại mật khẩu">
            </div>
            <p id="password-confirmation-status" data-password-confirmation-status class="mt-1.5 min-h-4 text-xs" aria-live="polite"></p>
            @error('password_confirmation') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div data-password-strength class="sm:col-span-2 -mt-1" id="password-strength-help">
            <div class="flex items-center justify-between gap-3 text-[11px]">
                <span class="font-medium text-[#777]">Độ mạnh mật khẩu</span>
                <span data-password-strength-label class="font-semibold text-[#8b8b93]" aria-live="polite">Chưa nhập</span>
            </div>
            <div class="mt-2 grid grid-cols-4 gap-1.5" aria-hidden="true">
                <span data-password-strength-segment class="h-1.5 rounded-full bg-[#e6e2dc]"></span>
                <span data-password-strength-segment class="h-1.5 rounded-full bg-[#e6e2dc]"></span>
                <span data-password-strength-segment class="h-1.5 rounded-full bg-[#e6e2dc]"></span>
                <span data-password-strength-segment class="h-1.5 rounded-full bg-[#e6e2dc]"></span>
            </div>
            <p class="mt-2 text-[11px] leading-5 text-[#8b8b93]">Dùng ít nhất 8 ký tự; thêm chữ hoa, số và ký tự đặc biệt để tăng độ mạnh.</p>
        </div>

        <div class="sm:col-span-2">
            <label class="flex cursor-pointer items-start gap-2.5 text-xs leading-5 text-[#5f5f5f]">
                <input name="terms" type="checkbox" id="terms" value="on" required class="mt-0.5 size-3.5 rounded border-[#cfcac3] text-black focus:ring-black {{ $errors->has('terms') ? 'border-red-500' : '' }}" {{ old('terms') ? 'checked' : '' }}>
                <span>Tôi đồng ý với Điều khoản sử dụng và Chính sách bảo mật của NovaPhone.</span>
            </label>
            @error('terms') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="sm:col-span-2">
            <button type="submit" class="w-full rounded-xl bg-black px-5 py-3.5 text-sm font-semibold text-white transition duration-300 hover:bg-[#242424] focus:outline-none focus:ring-4 focus:ring-black/15">Đăng ký</button>
        </div>
    </form>

    <div class="my-6 flex items-center gap-3 text-[11px] text-[#999]">
        <span class="h-px flex-1 bg-[#e6e2dc]"></span>
        <span>Hoặc đăng ký với</span>
        <span class="h-px flex-1 bg-[#e6e2dc]"></span>
    </div>

    <div class="grid grid-cols-2 gap-3">
        <a href="{{ route('google.login') }}" class="rounded-xl border border-[#dfdbd5] bg-white px-4 py-3 text-center text-xs font-semibold text-[#303030] transition hover:border-black hover:bg-[#fbfaf8]">Google</a>
        <a href="{{ route('auth.social.redirect', ['provider' => 'facebook']) }}" class="rounded-xl border border-[#dfdbd5] bg-white px-4 py-3 text-center text-xs font-semibold text-[#303030] transition hover:border-black hover:bg-[#fbfaf8]">Facebook</a>
    </div>

    <p class="mt-6 text-center text-xs text-[#777]">Đã có tài khoản? <a href="{{ route('login') }}" class="font-semibold text-[#0a5ec2] transition hover:text-[#064b99]">Đăng nhập ngay</a></p>
</section>
@endsection
