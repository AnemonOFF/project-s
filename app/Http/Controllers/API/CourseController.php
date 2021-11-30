<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\Course;
use App\Http\Resources\CourseResource;

class CourseController extends Controller
{
    public function index()
    {
        $data = Course::latest()->get();
        return response()->json(CourseResource::collection($data));
    }

    public function show($id)
    {
        $course = Course::find($id);
        if (is_null($course))
            return response()->json("Course with id - $id not found", 404);
        return response()->json(new CourseResource($course));
    }

    public function store(Request $request)
    {
        if (!auth('sanctum')->check())
            return response()->json("Authentification required", 403);

        $validator = Validator::make($request->all(),[
            'name' => 'required|string|max:100|unique:courses,name',
            'platform_id' => 'required|exists:platforms,id',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors());       
        }

        $course = Course::create([
            'name' => $request->name,
            'platform_id' => $request->platform_id,
         ]);
        
        return response()->json(new CourseResource($course));
    }

    public function update(Request $request, Course $course)
    {
        if (!auth('sanctum')->check())
            return response()->json("Authentification required", 403);

        $validator = Validator::make($request->all(),[
            'name' => 'required|string|max:100|unique:courses,name',
            'platform_id' => 'required|exists:platforms,id',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors());       
        }

        $course->name = $request->name;
        $course->platform_id = $request->platform_id;
        $course->save();
        
        return response()->json(new CourseResource($course));
    }

    public function destroy(Course $course)
    {
        if (!auth('sanctum')->check())
            return response()->json("Authentification required", 403);

        $course->delete();

        return response()->json('Course deleted successfully');
    }
}
