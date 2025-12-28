<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\v1\Feedback;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    /**
     * Display a listing of feedback.
     */
    public function index()
    {
        $user = auth()->user();

        // If brand owner/admin, see all feedback
        // If standard user, see only their own feedback
        if ($user->isBrandOwner() || $user->hasRole('admin')) {
            $feedbacks = Feedback::with('user')->latest()->get();
        } else {
            $feedbacks = Feedback::where('user_id', $user->id)->latest()->get();
        }

        return response()->json(['data' => $feedbacks]);
    }

    /**
     * Store a newly created feedback.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category' => 'required|string|in:general,bug,suggestion',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $feedback = Feedback::create([
            'user_id' => auth()->id(),
            'category' => $validated['category'],
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'status' => 'pending',
        ]);

        return response()->json(['data' => $feedback, 'message' => 'Feedback submitted successfully.'], 201);
    }

    /**
     * Display the specified feedback.
     */
    public function show($id)
    {
        $user = auth()->user();
        $feedback = Feedback::with('user')->findOrFail($id);

        if (!$user->isBrandOwner() && !$user->hasRole('admin') && $feedback->user_id !== $user->id) {
            abort(403);
        }

        return response()->json(['data' => $feedback]);
    }
}
