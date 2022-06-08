<?php

namespace App\Http\Controllers;

use App\Models\MyCourse;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    public function index($course)
    {
        $reviews = Review::where('course_id', $course)
        ->get();

        return response()->json($reviews);
    }

    // public function show(Review $review)
    // {
    //     if ($review == false) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Review not found'
    //         ]);
    //     }

    //     return response()->json([
    //         'status' => true,
    //         'data' => $review
    //     ]);
    // }

    public function store(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'rating' => 'required|numeric|between:1,5',
            'note' => 'nullable|string',
            'user_id' => ['required', 'numeric', function ($attribute, $value, $fail) {
                $data = getUser((int)$value);

                if ($data['status'] === false) {
                    $fail($data['message']);
                }
            }],
            'course_id' => ['required', 'numeric', 'exists:courses,id', function ($attribute, $value, $fail) use ($request) {
                $data = MyCourse::where('user_id', $request->input('user_id'))
                ->where('course_id', $value)
                ->first();

                if (!$data) {
                    $fail("User not take this course.");
                }
            }, function ($attribute, $value, $fail) use ($request) {
                $data = Review::where('user_id', $request->input('user_id'))
                ->where('course_id', $value)
                ->first();
                
                if ($data) {
                    $fail("User already review this course.");
                }
            }],
        ]);

        if ($validated->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validated->errors()->all()
            ], 400);
        }

        $review = new Review();
        $review->rating = $request->rating;
        $review->note = $request->note;
        $review->user_id = $request->user_id;
        $review->course_id = $request->course_id;

        $review->save();

        return response()->json([
            'status' => true,
            'data' => $review
        ]);
    }

    public function update(Request $request, $review)
    {
        $review = Review::find($review);

        if (!$review) {
            return response()->json([
                'status' => false,
                'message' => 'Review not found'
            ], 400);
        }

        if ($review->user_id != $request->query('user_id')) {
            return response()->json([
                'status' => false,
                'message' => 'You are not owner of this review'
            ], 400);
        }

        $validated = Validator::make($request->all(), [
            'rating' => 'required|numeric|between:1,5',
            'note' => 'nullable|string',
        ]);

        if ($validated->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validated->errors()->all()
            ], 400);
        }

        $review->rating = $request->rating;
        $review->note = $request->note;
        $review->save();

        return response()->json([
            'status' => true,
            'data' => $review
        ]);
    }

    public function destroy(Request $request, $review)
    {
        $review = Review::find($review);
        
        if (!$review) {
            return response()->json([
                'status' => false,
                'message' => 'Review not found'
            ], 400);
        }

        if ($review->user_id != $request->query('user_id')) {
            return response()->json([
                'status' => false,
                'message' => 'You are not owner of this review'
            ], 400);
        }

        $review->delete();

        return response()->json([
            'status' => true,
            'message' => 'Review deleted'
        ]);
    }
}
