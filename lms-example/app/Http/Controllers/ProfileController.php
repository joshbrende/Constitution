<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

final class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user()->load('badges');
        return view('profile.edit', ['user' => $user]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $valid = $request->validate([
            'name'    => ['required', 'string', 'max:255'],
            'surname' => ['nullable', 'string', 'max:255'],
            'email'   => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
        ]);

        $user->update($valid);

        return redirect()->route('profile.edit')->with('message', 'Profile updated.');
    }

    public function updatePassword(Request $request)
    {
        $valid = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'confirmed', Password::defaults()],
        ]);

        Auth::user()->update([
            'password' => $valid['password'],
        ]);

        return redirect()->route('profile.edit')->with('message', 'Password changed.');
    }
}
