<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\MyCourse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MyCourseController extends Controller
{
    public function index(Request $request)
    {
        $myCourse = MyCourse::query()
        ->join('courses', 'my_courses.course_id', '=', 'courses.id')
        ->select('courses.*');

        $myCourse->when($request->has('user_id'), function ($query) use ($request) {
            $query->where('user_id', $request->input('user_id'));
        });

        return response()->json([
            'status' => true,
            'data' => $myCourse->get(),
        ]);
    }

    public function registeredCourse(Request $request, $course)
    {
        $registeredCourse = MyCourse::join('courses', 'my_courses.course_id', '=', 'courses.id')
        ->select('courses.*')
        ->where('course_id', $course)
        ->where('user_id', $request->input('user_id'))
        ->first();

        if (!$registeredCourse) {
            return response()->json([
                'status' => false,
                'message' => 'you are not registered this course',
            ], 400);
        }

        return response()->json([
            'status' => true,
            'data' => $registeredCourse,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'course_id' => 'required|integer',
            'user_id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()
            ], 400);
        }

        $courseId = $request->input('course_id');
        $course = Course::find($courseId);

        if (!$course) {
            return response()->json([
                'status' => 'error',
                'message' => 'course not found'
            ], 404);
        }

        $userId = $request->input('user_id');
        $user = getUser($userId);

        if ($user['status'] === 'error') {
            return response()->json([
                'status' => $user['status'],
                'message' => $user['message']
            ], $user['http_code']);
        }

        $isExistMyCourse = MyCourse::where('course_id', '=', $courseId)
            ->where('user_id', '=', $userId)
            ->exists();
        
        if ($isExistMyCourse) {
            return response()->json([
                'status' => 'error',
                'message' => 'user already take this course'
            ], 409);
        }

        if ($course->type === 'premium') {
            if ($course->price === 0) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Price can\'t be 0'
                ], 405);
            }
            
            $order = postOrder([
                'user' => $user['data'],
                'course' => $course->toArray()
            ]);

            if ($order['status'] === false) {
                return response()->json([
                    'status' => $order['status'],
                    'message' => $order['message']
                ], $order['http_code']);
            }

            return response()->json([
                'status' => $order['status'],
                'data' => $order['data']
            ]);
        } else {
            $myCourse = MyCourse::create($data);

            return response()->json([
                'status' => true,
                'data' => $myCourse
            ]);
        }
    }

    public function createPremiumAccess(Request $request)
    {
        $data = $request->all();
        $myCourse = MyCourse::create($data);

        return response()->json([
            'status' => 'success',
            'data' => $myCourse
        ]);
    }

    // public function update(Request $request, $myCourse)
    // {
    //     $myCourse = MyCourse::find($myCourse);

    //     if (!$myCourse) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'My course not found'
    //         ]);
    //     }

    //     $validated = Validator::make($request->all(), [
    //         "course_id" => "required|integer|exists:courses,id",
    //         "user_id" => ['required', 'integer', function ($attribute, $value, $fail) use ($myCourse, $request) {
    //             $data = MyCourse::where('user_id', $request->input('user_id'))
    //             ->where('course_id', $request->input('course_id'))
    //             ->first();

    //             if ($data) {
    //                 if ($myCourse->id != $data->id) {
    //                     return $fail("User already take this course.");
    //                 }
    //             }

    //             $data = getUser($value);
                
    //             if ($data['status'] === false) {
    //                 return $fail($data['message']);
    //             }
    //         }],
    //     ]);
        
    //     if ($validated->fails()) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => $validated->errors()->all()
    //         ], 400);
    //     }

    //     $myCourse->course_id = $request->input('course_id');
    //     $myCourse->user_id = $request->input('user_id');
    //     $myCourse->save();

    //     return response()->json([
    //         'status' => true,
    //         'message' => 'Successfully updated my course',
    //         'data' => $myCourse
    //     ]);
    // }

    // public function createPremiumAccess(Request $request)
    // {
    //     $data = $request->all();
    //     $myCourse = MyCourse::create($data);

    //     return response()->json([
    //         'status' => 'success',
    //         'data' => $myCourse
    //     ]);
    // }
}
