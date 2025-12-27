<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
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
