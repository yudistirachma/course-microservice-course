<?php

namespace App\Http\Controllers;

use App\Models\{Course, MyCourse};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $courses = Course::select('name', 'thumbnail', 'type', 'status', 'level')
        ->where(function ($query) use ($request) {

            if ($request->has('search')) {
                $query->where('name', 'like', '%'.$request->input('search').'%');
            }

            if ($request->has('status')) {
                $query->where('status', '=', $request->input('status'));
            }

            if ($request->has('type')) {
                $query->where('type', '=', $request->input('type'));
            }

            if ($request->has('level')) {
                $query->where('level', '=', $request->input('level'));
            }
            
        })
        ->paginate(10);

        return response()->json([
            'status' => true,
            'data' => $courses
        ]);
    }

    public function show($course)
    {
        // $query->join('bwa_micro_user.users as users', 'reviews.user_id', '=', 'users.id')
        // ->select('reviews.*', 'users.name', 'users.avatar', 'users.profession');
        $course = Course::with(['reviews', 'chapters.lessons', 'images'])
        ->join('mentors', 'courses.mentor_id', '=', 'mentors.id')
        ->select('courses.*', 'mentors.name as mentor_name', 'mentors.profile as mentor_profile', 'mentors.profession as mentor_profession')
        ->find($course);
        
        if (!$course) {
            return response()->json([
                'status' => false,
                'message' => 'Course not found'
            ]);
        }

        $course = $course->toArray();

        if( count($course['reviews']) > 0 ) {
            $usersId = array_column($course['reviews'], 'user_id');
            $users = getUserByIds($usersId);

            if ($users['status'] === true) {
                foreach ($course['reviews'] as $key => $review) {
                    $userIndex = array_search($review['user_id'], array_column($users['data'], 'id'));
                    $course['reviews'][$key]['user'] = $users['data'][$userIndex];
                }
            }
        }
        
        $course['totalStudent'] = MyCourse::where('course_id', $course['id'])->count();

        $totalVideo = 0;
        foreach ($course['chapters'] as $chapter) {
            $totalVideo += count($chapter['lessons']);
        }

        $course['totalVideo'] = $totalVideo;
        // $course['totalVideo'] = Chapter::where('course_id', $course['id'])->withCount('lessons')->get()->toArray();
        // $course['totalVideo'] = array_sum(array_column($course['totalVideo'], 'lessons_count'));

        return response()->json([
            'status' => true,
            'data' => $course
        ]);
    }

    public function store(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'name' => 'required|string|unique:courses,name',
            'certificate' => 'required|boolean',
            'thumbnail' => 'required|url',
            'type' => 'required|in:free,premium',
            'status' => 'required|in:draft,published',
            'price' => 'required|numeric',
            'level' => 'required|in:all-level,beginner,intermediate,advanced',
            'description' => 'nullable|string',
            'mentor_id' => 'required|integer|exists:mentors,id',
        ]);

        if ($validated->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validated->errors()->all()
            ]);
        }

        $course = new Course();
        $course->name = $request->name;
        $course->certificate = $request->certificate;
        $course->thumbnail = $request->thumbnail;
        $course->type = $request->type;
        $course->status = $request->status;
        $course->price = $request->price;
        $course->level = $request->level;
        $course->description = $request->description;
        $course->mentor_id = $request->mentor_id;
        $course->save();

        return response()->json([
            'status' => true,
            'data' => $course
        ]);
    }

    public function update($course, Request $request)
    {
        $course = Course::find($course);

        if (!$course) {
            return response()->json([
                'status' => false,
                'message' => 'Course not found'
            ]);
        }

        if ($course == false) {
            return response()->json([
                'status' => false,
                'message' => 'Course not found'
            ]);
        }

        $validated = Validator::make($request->all(), [
            'name' => 'required|string|unique:courses,name,'.$course->id,
            'certificate' => 'required|boolean',
            'thumbnail' => 'required|string',
            'type' => 'required|in:free,premium',
            'status' => 'required|in:draft,published',
            'price' => 'required|numeric',
            'level' => 'required|in:all-level,beginner,intermediate,advanced',
            'description' => 'nullable|string',
            'mentor_id' => 'required|integer|exists:mentors,id',
        ]);

        if ($validated->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validated->errors()->all()
            ]);
        }

        $course->name = $request->name;
        $course->certificate = $request->certificate;
        $course->thumbnail = $request->thumbnail;
        $course->type = $request->type;
        $course->status = $request->status;
        $course->price = $request->price;
        $course->level = $request->level;
        $course->description = $request->description;
        $course->mentor_id = $request->mentor_id;
        $course->save();

        return response()->json([
            'status' => true,
            'data' => $course
        ]);
    }
}
