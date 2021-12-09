<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Course;

class CourseStudentsController extends Controller
{
    public function index($courseId, Request $request)
    {
        $course = Course::find($courseId);
        $validator = Validator::make($request->all(),[
            'page' => 'required|numeric',
            //'course_id' => 'required|exists:courses,id',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors());
        }
        $page = $request['page'];
        //$courseId = $request['course_id'];
        //$course = Course::find($courseId);
        $students = $course->students()->skip(intval($page) * 50)->take(50)->get();
        $data = [
            'points_max' => $course->points_max,
            'students' => []
        ];
        foreach($students as $student)
        {
            $marksSum = 0;
            $marks = $student->marks()->get();
            foreach($marks as $mark)
                $marksSum += floatval($mark->mark);
            array_push($data['students'], [
                'id' => $student->id,
                'full_name' => $student->full_name,
                'points' => $marksSum,
            ]);
        }
        return response()->json($data);
    }
}
