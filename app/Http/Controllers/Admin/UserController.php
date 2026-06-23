<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // ─── Danh sách người dùng ───────────────────────────────────

    public function index(Request $request)
    {
        $query = User::query();

        // Tìm kiếm theo tên, email hoặc số điện thoại
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Lọc theo vai trò
        if (in_array($request->input('role'), ['user', 'admin'], true)) {
            $query->where('role', $request->input('role'));
        }

        // Lọc theo trạng thái
        if (in_array($request->input('status'), ['active', 'blocked'], true)) {
            $query->where('status', $request->input('status'));
        }

        $users = $query->latest()->paginate(15)->withQueryString();

        return view('admin.users.index', [
            'users'   => $users,
            'filters' => $request->only(['search', 'role', 'status']),
        ]);
    }

    // ─── Chi tiết người dùng ────────────────────────────────────

    public function show(User $user)
    {
        $user->loadCount(['orders', 'reviews']);

        return view('admin.users.show', compact('user'));
    }

    // ─── Khóa / Mở khóa tài khoản ───────────────────────────────

    public function toggleStatus(Request $request, User $user)
    {
        // Không cho phép admin tự khóa chính mình (tránh tự khóa khỏi hệ thống)
        if ($user->id === $request->user()->id) {
            return back()->withErrors(['error' => 'Bạn không thể khóa tài khoản của chính mình.']);
        }

        $user->update([
            'status' => $user->isBlocked() ? 'active' : 'blocked',
        ]);

        return back()->with(
            'success',
            $user->isBlocked()
                ? "Đã khóa tài khoản \"{$user->name}\"."
                : "Đã mở khóa tài khoản \"{$user->name}\"."
        );
    }
}
