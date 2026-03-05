<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function index()
{
    $reviews = Review::with('user')
        ->whereHas('user')   
        ->latest()
        ->paginate(10);

    return view('reviews.index', compact('reviews'));
}

    public function store(Request $request)
    {
        $data = $request->validate([
            'rating'  => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['required', 'string', 'max:50'],
        ]);

        Review::create([
            'user_id' => Auth::id(),
            'rating'  => $data['rating'],
            'comment' => $data['comment'],
        ]);

        return redirect()
            ->route('reviews.index')
            ->with('success', 'Thank you for your review!');
    }
}
