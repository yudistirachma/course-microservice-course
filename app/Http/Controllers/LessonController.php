<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LessonController extends Controller
{
    public function index(Request $request)
    {
        $lessons = Lesson::query();

        if ($request->input('chapter')) {
            $lessons->where('chapter_id', $request->input('chapter'));
        }

        return response()->json([
            'status' => true,
            'data' => $lessons->get()
        ]);
    }

    public function show($lesson)
    {
        $lesson = Lesson::find($lesson);
        
        if (!$lesson) {
            return response()->json([
                'status' => false,
                'message' => 'Lesson not found'
            ]);
        }

        return response()->json([
            'status' => true,
            'data' => $lesson
        ]);
    }

    public function store(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'name' => 'required|string',
            'video' => 'required|url',
            'chapter_id' => 'required|integer|exists:chapters,id',
        ]);

        if ($validated->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validated->errors()->all()
            ]);
        }

        $lesson = new Lesson();
        $lesson->name = $request->name;
        $lesson->video = $request->video;
        $lesson->chapter_id = $request->chapter_id;

        $lesson->save();

        return response()->json([
            'status' => true,
            'data' => $lesson
        ]);
    }

    public function update(Request $request, $lesson)
    {
        $lesson = Lesson::find($lesson);

        if (!$lesson) {
            return response()->json([
                'status' => false,
                'message' => 'Lesson not found'
            ]);
        }

        $validated = Validator::make($request->all(), [
            'name' => 'nullable|string',
            'video' => 'nullable|url',
            // 'chapter_id' => 'required|integer|exists:chapters,id',
        ]);

        if ($validated->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validated->errors()->all()
            ]);
        }

        if ($request->name) {
            $lesson->name = $request->name;
        }

        if ($request->video) {
            $lesson->video = $request->video;
        }

        $lesson->save();

        return response()->json([
            'status' => true,
            'data' => $lesson
        ]);
    }

    public function destroy($lesson)
    {
        $lesson = Lesson::find($lesson);

        if (!$lesson) {
            return response()->json([
                'status' => false,
                'message' => 'Lesson not found'
            ]);
        }

        $lesson->delete();

        return response()->json([
            'status' => true,
            'message' => 'Lesson deleted'
        ]);
    }
}
