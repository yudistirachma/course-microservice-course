<?php

namespace App\Http\Controllers;

use App\Models\Chapter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ChapterController extends Controller
{
    public function index(Request $request)
    {
        $chapters = Chapter::query();

        if ($request->has('course')) {  
            $chapters->where('course_id', $request->course);
        }

        return response()->json([
            'status' => true,
            'data' => $chapters->paginate(10)
        ]);
    }

    public function show($chapter)
    {
        $chapter = Chapter::find($chapter);
        
        if ($chapter == false) {
            return response()->json([
                'status' => false,
                'message' => 'Chapter not found'
            ]);
        }

        return response()->json([
            'status' => true,
            'data' => $chapter
        ]);
    }

    public function store(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'name' => 'required|string',
            'course_id' => 'required|integer|exists:courses,id',
        ]);

        if ($validated->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validated->errors()->all()
            ]);
        }

        $chapter = new Chapter();
        $chapter->name = $request->name;
        $chapter->course_id = $request->course_id;
        $chapter->save();

        return response()->json([
            'status' => true,
            'data' => $chapter
        ]);
    }

    public function update(Request $request, $chapter)
    {
        $chapter = Chapter::find($chapter);

        if ($chapter == false) {
            return response()->json([
                'status' => false,
                'message' => 'Chapter not found'
            ]);
        }

        $validated = Validator::make($request->all(), [
            'name' => 'required|string',
            'course_id' => 'required|numeric|exists:courses,id',
        ]);

        if ($validated->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validated->errors()->all()
            ]);
        }

        $chapter->name = $request->name;
        $chapter->course_id = $request->course_id;
        $chapter->save();

        return response()->json([
            'status' => true,
            'data' => $chapter
        ]);
    }
}