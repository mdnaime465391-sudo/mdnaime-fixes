<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PasswordUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function edit(): View
    {
        return view('admin.account.password', [
            'user' => auth()->user(),
        ]);
    }

    public function update(PasswordUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $password = $request->string('password')->toString();

        $user->password = Hash::make($password);
        $user->save();

        // Invalidate every other session; the current one stays signed in.
        Auth::logoutOtherDevices($password);

        return back()->with('status', 'Password updated. Other sessions were signed out.');
    }
}
