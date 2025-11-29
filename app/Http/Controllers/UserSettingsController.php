<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserSettingsController extends Controller
{
    public function index()
    {
        if (! auth()->check()) {
            return redirect()->route('auth.osu.redirect');
        }

        $user = auth()->user();

        return view('settings.index', compact('user'));
    }

    public function update(Request $request)
    {
        if (! auth()->check()) {
            return redirect()->route('auth.osu.redirect');
        }

        $user = auth()->user();

        $request->validate([
            'email' => 'nullable|email|max:255',
            'discord_username' => 'nullable|string|max:255',
        ]);

        $user->update([
            'email' => $request->input('email'),
            'discord_username' => $request->input('discord_username'),
        ]);

        return back()->with('success', 'Settings updated successfully!');
    }
}
