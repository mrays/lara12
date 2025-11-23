<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function edit(Request \$request)
    {
        return view('profile.edit', [
            'user' => \$request->user(),
        ]);
    }

    public function update(Request \$request)
    {
        \$user = \$request->user();

        \$data = \$request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
        ]);

        \$user->update(\$data);

        return redirect()->route('profile.edit')->with('success', 'Profile updated.');
    }
}
