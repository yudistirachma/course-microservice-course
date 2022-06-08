<?php

namespace App\Http\Controllers;

use App\Models\ImageCourse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ImageCourseController extends Controller
{
    public function store(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'image' => 'required|url',
            'course_id' => 'required|integer|exists:courses,id',
        ]);

        if ($validated->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validated->errors()->all()
            ]);
        }

        $image = new ImageCourse();
        $image->image = $request->image;
        $image->course_id = $request->course_id;
        $image->save();

        return response()->json([
            'status' => true,
            'data' => $image
        ]);
    }

    public function update(Request $request, $image)
    {
        $image = ImageCourse::find($image);
        
        if(!$image) {
            return response()->json([
                'status' => false,
                'message' => 'Image not found'
            ]);
        }
        
        $validated = Validator::make($request->all(), [
            'image' => 'required|url',
            'course_id' => 'required|integer|exists:courses,id',
        ]);

        if ($validated->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validated->errors()->all()
            ]);
        }
        $image->image = $request->image;
        $image->course_id = $request->course_id;
        $image->save();

        return response()->json([
            'status' => true,
            'data' => $image
        ]);
    }

    public function destroy($image)
    {
        $image = ImageCourse::find($image);
        
        if(!$image) {
            return response()->json([
                'status' => false,
                'message' => 'Image not found'
            ]);
        }
        
        $image->delete();

        return response()->json([
            'status' => true,
            'message' => 'Image deleted'
        ]);
    }
}
