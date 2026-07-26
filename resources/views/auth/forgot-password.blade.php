@extends('auth.layout')
@section('title', 'Quên mật khẩu - NovaPhone')
@section('content')
<section class="rounded-[28px] border border-[#ece8e2] bg-white p-6 shadow-[0_10px_35px_rgba(0,0,0,.04)]">
    <h1 class="text-2xl font-bold">Quên mật khẩu</h1>
    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <input name="email" type="email" value="{{ old('email') }}" class="mt-5 w-full rounded-2xl border border-[#e8e4de] bg-[#fbfaf8] px-4 py-3 text-sm" placeholder="Email">
        <button class="mt-3 w-full rounded-full bg-black px-5 py-3 text-sm font-semibold text-white">Gửi liên kết đặt lại</button>
    </form>
    @if (session('dev_link'))
        <a href="{{ session('dev_link') }}" class="mt-4 block break-all rounded-xl bg-[#f7f5f2] p-3 text-xs text-blue-700 underline">Mở liên kết đặt lại mật khẩu</a>
    @endif
</section>
@endsection
