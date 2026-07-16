<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists and not default or from external provider
            if ($user->avatar && !str_starts_with($user->avatar, 'http') && file_exists(public_path($user->avatar))) {
                unlink(public_path($user->avatar));
            }
            
            $imageName = time() . '_' . uniqid() . '.' . $request->file('avatar')->extension();
            $request->file('avatar')->move(public_path('images/avatars'), $imageName);
            $validated['avatar'] = 'images/avatars/' . $imageName;
        }

        $user->fill($validated);
        $user->save();

        return redirect()->route('account.show')->with('status', 'Hồ sơ cá nhân đã được cập nhật thành công.');
    }
}
