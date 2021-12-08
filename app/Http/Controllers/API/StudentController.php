<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;
use App\Http\Resources\StudentResource;

class StudentController extends Controller
{
    public function index()
    {
        $data = Student::latest()->get();
        return response()->json(StudentResource::collection($data));
    }

    public function show($id)
    {
        $student = Student::find($id);
        if (is_null($student))
            return response()->json("Student with id - $id not found", 404);
        return response()->json(new StudentResource($student));
    }

    public function store(Request $request)
    {
        if (!auth('sanctum')->check())
            return response()->json("Authentification required", 403);

        $validator = Validator::make($request->all(),[
            'full_name' => 'required|string|max:250',
            'email' => 'required|email|max:250',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors());       
        }

        $student = Student::create([
            'full_name' => $request->full_name,
            'email' => $request->email,
         ]);
        
        return response()->json(new StudentResource($student));
    }

    public function update(Request $request, Student $student)
    {
        if (!auth('sanctum')->check())
            return response()->json("Authentification required", 403);

        $validator = Validator::make($request->all(),[
            'full_name' => 'required|string|max:250',
            'email' => 'required|email|max:250',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors());       
        }

        $student->full_name = $request->full_name;
        $student->email = $request->email;
        $student->save();
        
        return response()->json(new StudentResource($student));
    }

    public function destroy(Student $student)
    {
        if (!auth('sanctum')->check())
            return response()->json("Authentification required", 403);

        $student->delete();

        return response()->json('Student deleted successfully');
    }
}
