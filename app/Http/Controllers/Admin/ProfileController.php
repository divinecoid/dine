<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('admin.profile.edit', [
            'user' => auth()->user()
        ]);
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'appearance' => 'required|in:light,dark',
        ]);

        $user->update($validated);

        return back()->with('success', 'Profile updated successfully');
    }

    public function updateAppearance(Request $request)
    {
        $request->validate([
            'appearance' => 'required|in:light,dark',
        ]);

        $user = auth()->user();
        $user->update([
            'appearance' => $request->appearance
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Appearance updated successfully'
        ]);
    }
}
