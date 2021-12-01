<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Platform;
use App\Http\Resources\CourseResource;

class PlatformCoursesController extends Controller
{
    public function index($platform_id)
    {
        $data = Platform::find($platform_id)->courses;
        return response()->json(CourseResource::collection($data));
    }

    public function show($platform_id, $course_id)
    {
        $course = Platform::find($platform_id)->courses()->where('id', $course_id)->first();
        if (is_null($course))
            return response()->json("Course with id - $course_id for platform $platform_id not found", 404);
        return response()->json(new CourseResource($course));
    }
}
