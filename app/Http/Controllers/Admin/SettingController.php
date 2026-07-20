<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function notifications()
    {
        $settings = [
            'telegram_notify_enabled' => Setting::get('telegram_notify_enabled', '0'),
            'telegram_bot_token' => Setting::get('telegram_bot_token', ''),
            'telegram_chat_id' => Setting::get('telegram_chat_id', ''),
        ];

        return view('admin.settings.notifications', compact('settings'));
    }

    public function updateNotifications(Request $request)
    {
        $request->validate([
            'telegram_notify_enabled' => 'nullable|in:0,1',
            'telegram_bot_token' => 'nullable|string|max:255',
            'telegram_chat_id' => 'nullable|string|max:255',
        ]);

        Setting::set('telegram_notify_enabled', $request->input('telegram_notify_enabled', '0'));
        Setting::set('telegram_bot_token', $request->input('telegram_bot_token', ''));
        Setting::set('telegram_chat_id', $request->input('telegram_chat_id', ''));

        return redirect()->back()->with('success', 'Cập nhật cài đặt thông báo thành công!');
    }
}
