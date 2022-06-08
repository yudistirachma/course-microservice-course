<?php

namespace App\Http\Controllers;

use App\Models\Mentor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MentorController extends Controller
{
    public function index()
    {
        $mentors = Mentor::select('name', 'profile', 'email', 'profession')->get();

        return response()->json([
            'status' => true,
            'data' => $mentors
        ]);
    }
    
    public function show($mentor)
    {
        $mentors = Mentor::find($mentor);

        if (!$mentors) {
            return response()->json([
                'status' => false,
                'message' => 'Mentor not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $mentors
        ]);
    }

    public function store(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'name' => 'required|string',
            'profile' => 'required|url',
            'email' => 'required|string|email|unique:mentors,email',
            'profession' => 'required|string',
        ]);

        if ($validated->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validated->errors()->all()
            ]);
        }

        $mentor = new Mentor();
        $mentor->name = $request->name;
        $mentor->profile = $request->profile;
        $mentor->email = $request->email;
        $mentor->profession = $request->profession;
        $mentor->save();

        return response()->json([
            'status' => true,
            'data' => $mentor
        ]);
    }

    public function update($mentor, Request $request)
    {
        $mentor = Mentor::find($mentor);
        
        if (!$mentor) {
            return response()->json([
                'status' => false,
                'message' => 'Mentor not found'
            ]);
        }

        $validated = Validator::make($request->all(), [
            'name' => 'required|string',
            'profile' => 'required|url',
            'email' => "required|email|unique:mentors,email,$mentor->id",
            'profession' => 'required|string',
        ]);

        if ($validated->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validated->errors()->all()
            ]);
        }

        $mentor->name = $request->name;
        $mentor->profile = $request->profile;
        $mentor->email = $request->email;
        $mentor->profession = $request->profession;
        $mentor->save();

        return response()->json([
            'status' => true,
            'data' => $mentor
        ]);
    }

    public function destroy($mentor)
    {
        $mentor = Mentor::find($mentor);

        if (!$mentor) {
            return response()->json([
                'status' => false,
                'message' => 'Mentor not found'
            ], 400);
        }

        $mentor->delete();

        return response()->json([
            'status' => true,
            'message' => 'Mentor deleted'
        ]);
    } 
}
