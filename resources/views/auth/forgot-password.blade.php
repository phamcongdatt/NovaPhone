@extends('auth.layout')

@section('title', 'Quên mật khẩu')

@section('content')
    <a href="{{ route('login') }}" class="mb-7 inline-flex items-center gap-2 text-xs text-slate-500 transition hover:text-slate-300">
        <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="m15 18-6-6 6-6"/>
        </svg>
        Quay lại đăng nhập
    </a>

    <div class="mb-6">
        <span class="mb-4 flex size-12 items-center justify-center rounded-xl border border-indigo-400/20 bg-indigo-500/10 text-indigo-300">
            <svg class="size-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 7.5 10.65 13a2.25 2.25 0 0 0 2.7 0L21 7.5M5.25 19.5h13.5A2.25 2.25 0 0 0 21 17.25V6.75a2.25 2.25 0 0 0-2.25-2.25H5.25A2.25 2.25 0 0 0 3 6.75v10.5a2.25 2.25 0 0 0 2.25 2.25Z"/>
            </svg>
        </span>
        <h1 class="text-2xl font-bold tracking-tight text-white">Quên mật khẩu?</h1>
        <p class="mt-2 text-sm leading-relaxed text-slate-400">Nhập email đã đăng ký, chúng tôi sẽ gửi link đặt lại mật khẩu cho bạn.</p>
    </div>

    @if (session('status'))
        <div class="mb-6 max-w-full overflow-hidden rounded-xl border border-emerald-400/25 bg-emerald-500/10 p-4">
            <div class="flex gap-3">
                <span class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-emerald-400/10 text-emerald-300">✓</span>
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-emerald-300">{{ session('status') }}</p>
                    <p class="mt-1 text-xs text-slate-400">Vui lòng kiểm tra hộp thư email (bao gồm cả thư rác).</p>
                </div>
            </div>

            @if (session('dev_link'))
                <div class="mt-4 min-w-0 max-w-full overflow-hidden rounded-lg border border-amber-400/20 bg-amber-400/10 p-3 text-xs">
                    <p class="mb-1 font-semibold uppercase tracking-wide text-amber-300">Môi trường phát triển</p>
                    <a href="{{ session('dev_link') }}" class="block max-w-full whitespace-normal text-indigo-300 underline [overflow-wrap:anywhere] [word-break:break-all] hover:text-indigo-200">{{ session('dev_link') }}</a>
                </div>
            @endif
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" novalidate>
        @csrf

        <label for="email" class="mb-2 block text-xs font-medium text-slate-300">Địa chỉ email</label>
        <div class="relative">
            <svg class="pointer-events-none absolute left-4 top-1/2 size-4 -translate-y-1/2 text-slate-500" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 7.5 10.65 13a2.25 2.25 0 0 0 2.7 0L21 7.5M5.25 19.5h13.5A2.25 2.25 0 0 0 21 17.25V6.75a2.25 2.25 0 0 0-2.25-2.25H5.25A2.25 2.25 0 0 0 3 6.75v10.5a2.25 2.25 0 0 0 2.25 2.25Z"/>
            </svg>
            <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" autofocus
                   class="w-full rounded-xl border bg-white/[0.035] py-3.5 pl-11 pr-4 text-sm text-white outline-none transition placeholder:text-slate-600 focus:ring-2 {{ $errors->has('email') ? 'border-red-400/70 focus:border-red-400 focus:ring-red-500/20' : 'border-white/10 focus:border-indigo-400 focus:ring-indigo-500/20' }}"
                   placeholder="ban@example.com">
        </div>
        @error('email')
            <p class="mt-2 text-xs text-red-400">{{ $message }}</p>
        @enderror

        <button type="submit" class="mt-5 w-full rounded-xl bg-gradient-to-r from-indigo-600 via-violet-600 to-blue-600 px-4 py-3.5 text-sm font-bold uppercase tracking-wide text-white shadow-lg shadow-indigo-600/25 transition hover:brightness-110 active:scale-[.99]">
            Gửi link đặt lại mật khẩu
        </button>
    </form>

    <p class="mt-6 text-center text-xs text-slate-500">
        Đã nhớ mật khẩu?
        <a href="{{ route('login') }}" class="font-semibold text-indigo-400 hover:text-indigo-300">Đăng nhập</a>
    </p>
@endsection
