@extends('auth.layout')
@section('title', 'Đổi mật khẩu - NovaPhone')
@section('content')
<section class="rounded-[28px] border border-[#ece8e2] bg-white p-6 shadow-[0_10px_35px_rgba(0,0,0,.04)]">
    <h1 class="text-2xl font-bold">Đổi mật khẩu</h1>
    <form method="POST" action="{{ route('password.change') }}" class="mt-5 grid gap-3">
        @csrf
        <input name="current_password" class="w-full rounded-2xl border border-[#e8e4de] bg-[#fbfaf8] px-4 py-3 text-sm" placeholder="Mật khẩu hiện tại" type="password">
        <input name="password" class="w-full rounded-2xl border border-[#e8e4de] bg-[#fbfaf8] px-4 py-3 text-sm" placeholder="Mật khẩu mới" type="password">
        <input name="password_confirmation" class="w-full rounded-2xl border border-[#e8e4de] bg-[#fbfaf8] px-4 py-3 text-sm" placeholder="Xác nhận mật khẩu mới" type="password">
        <button class="w-full rounded-full bg-black px-5 py-3 text-sm font-semibold text-white">Lưu thay đổi</button>
    </form>
</section>
@endsection
