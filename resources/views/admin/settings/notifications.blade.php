@extends('admin.layout')

@section('page-title', 'Cài đặt thông báo')
@section('page-subtitle', 'Send noti Social')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h2 class="text-xl font-bold text-white">Send noti Social</h2>
        <p class="mt-1 text-sm text-slate-400">Thiết lập tự động thông báo đơn hàng qua các nền tảng mạng xã hội.</p>
    </div>
</div>

<form action="{{ route('admin.settings.notifications') }}" method="POST">
    @csrf
    
    <div class="rounded-2xl border border-white/5 bg-[#0b1523] shadow-xl overflow-hidden">
        {{-- Section Header --}}
        <div class="border-b border-white/5 bg-white/[0.02] px-6 py-4 flex items-center justify-between">
            <h3 class="text-base font-semibold text-white">Telegram</h3>
            <button type="submit" class="rounded-lg bg-blue-600 px-6 py-2 text-sm font-bold text-white shadow-lg shadow-blue-500/20 transition-all hover:bg-blue-500 hover:-translate-y-0.5">
                Update
            </button>
        </div>
        
        <div class="p-6 space-y-8 divide-y divide-white/5">
            {{-- Toggle Enable --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-0">
                <div class="md:col-span-1">
                    <label class="text-sm font-medium text-slate-300">Tự động thông báo đơn hàng (Telegram)</label>
                </div>
                <div class="md:col-span-2 flex items-center gap-3">
                    <label class="relative inline-flex cursor-pointer items-center">
                        <input type="hidden" name="telegram_notify_enabled" value="0">
                        <input type="checkbox" id="telegram-toggle" name="telegram_notify_enabled" value="1" {{ $settings['telegram_notify_enabled'] ? 'checked' : '' }} class="peer sr-only">
                        <div class="peer h-7 w-14 rounded-full bg-slate-700/50 after:absolute after:left-[4px] after:top-[4px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-blue-600 peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-500/20"></div>
                        <span class="ml-3 text-sm font-medium text-slate-400">Yes</span>
                    </label>
                    <span class="text-sm text-slate-500 italic">Gửi tin nhắn Telegram tự động mỗi khi có khách đặt hàng thành công.</span>
                </div>
            </div>

            <div id="telegram-settings-container" class="space-y-8 divide-y divide-white/5 border-t border-white/5 pt-8 {{ $settings['telegram_notify_enabled'] ? '' : 'hidden' }}">
                {{-- Instructions --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-0">
                    <div class="md:col-span-1">
                        <label class="text-sm font-medium text-slate-300">Hướng dẫn tạo Bot & lấy thông tin</label>
                    </div>
                    <div class="md:col-span-2 text-sm text-slate-400 space-y-4 leading-relaxed">
                        <p class="font-bold text-white">HƯỚNG DẪN TẠO BOT VÀ THÊM VÀO NHÓM (Để nhiều nhân viên cùng nhận thông báo)</p>
                        
                        <p><strong class="text-white">Bước 1 (Tạo Bot):</strong> Mở Telegram, tìm <code>@BotFather</code> > Gõ lệnh <code>/newbot</code> > Đặt Tên bot (VD: <em>Bot Thông Báo</em>) và Username cho bot (bắt buộc kết thúc bằng chữ "bot", VD: <em>thongbao_shop_bot</em>). BotFather sẽ cấp cho bạn một chuỗi <strong>Bot Token</strong> (nhập vào ô bên dưới).</p>
                        
                        <p><strong class="text-white">Bước 2 (Tạo Nhóm):</strong> Tạo một Group mới trên Telegram và thêm những nhân viên cần nhận thông báo vào nhóm.</p>
                        
                        <p><strong class="text-white">Bước 3 (Thêm Bot vào Nhóm):</strong> Trong nhóm vừa tạo, bấm vào Tên nhóm ở trên cùng > Chọn <strong>Add Members</strong> (Thêm thành viên) > Gõ chính xác Username của bot bạn vừa tạo ở Bước 1 vào ô tìm kiếm (VD: <em>@thongbao_shop_bot</em>) > Chọn bot và bấm Add.</p>
                        
                        <div class="space-y-1">
                            <p><strong class="text-white">Bước 4 (Lấy Chat ID của Nhóm):</strong></p>
                            <ul class="list-inside list-disc pl-2 space-y-1">
                                <li>Tiếp tục chọn <strong>Add Members</strong> trong nhóm, tìm bot tên <code>@MissRose_bot</code> và Add vào nhóm.</li>
                                <li>Trở lại khung chat của nhóm, gõ tin nhắn <code>/id</code> và gửi.</li>
                                <li>Bot MissRose sẽ trả lời một tin nhắn có chứa ID nhóm của bạn (VD: <code>-100123456789</code>).</li>
                                <li>Hãy copy dãy số đó (bao gồm cả dấu <code>-</code>) và nhập vào ô <strong>Telegram Chat ID</strong> bên dưới.</li>
                            </ul>
                            <p class="italic text-xs mt-2">(Lưu ý: Sau khi lấy xong ID, bạn có thể xóa @MissRose_bot ra khỏi nhóm).</p>
                        </div>
                    </div>
                </div>

                {{-- Token Input --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-8">
                    <div class="md:col-span-1">
                        <label class="text-sm font-medium text-slate-300">Telegram Bot Token</label>
                    </div>
                    <div class="md:col-span-2">
                        <input type="text" name="telegram_bot_token" value="{{ old('telegram_bot_token', $settings['telegram_bot_token']) }}" class="w-full rounded-xl border border-white/[0.07] bg-black/10 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:ring-2 focus:ring-blue-500/10">
                        <p class="mt-2 text-xs text-slate-500">Nhập Bot Token lấy từ BotFather trên Telegram (VD: 123456789:ABCDefghiJKLmnop...).</p>
                    </div>
                </div>

                {{-- Chat ID Input --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-8">
                    <div class="md:col-span-1">
                        <label class="text-sm font-medium text-slate-300">Telegram Chat ID</label>
                    </div>
                    <div class="md:col-span-2">
                        <input type="text" name="telegram_chat_id" value="{{ old('telegram_chat_id', $settings['telegram_chat_id']) }}" class="w-full rounded-xl border border-white/[0.07] bg-black/10 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:ring-2 focus:ring-blue-500/10">
                        <p class="mt-2 text-xs text-slate-500">Nhập ID của Group/Channel hoặc User nhận thông báo (VD: -100123456789).</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<style>
    code {
        background-color: rgba(255, 255, 255, 0.1);
        padding: 0.125rem 0.25rem;
        border-radius: 0.25rem;
        font-family: monospace;
        color: #93c5fd;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggle = document.getElementById('telegram-toggle');
        const container = document.getElementById('telegram-settings-container');

        if (toggle && container) {
            toggle.addEventListener('change', function() {
                if (this.checked) {
                    container.classList.remove('hidden');
                } else {
                    container.classList.add('hidden');
                }
            });
        }
    });
</script>
@endsection
