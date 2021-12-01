<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\MarkResource;
use App\Models\Mark;
use App\Models\Student;
use Illuminate\Http\Request;

class StudentMarksController extends Controller
{
    public function index($student_id)
    {
        $data = Student::find($student_id)->marks;
        return response()->json(MarkResource::collection($data));
    }

    public function show($student_id, $mark_id)
    {
        $mark = Student::find($student_id)->marks()->where('id', $mark_id)->first();
        if (is_null($mark))
            return response()->json("Mark with id - $mark_id for student $student_id not found", 404);
        return response()->json(new MarkResource($mark));
    }
}
