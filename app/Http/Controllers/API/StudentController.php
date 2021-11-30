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
        if (!Auth::check())
            return response()->json("Authentification required", 403);

        $validator = Validator::make($request->all(),[
            'name' => 'required|string|max:100',
            'surname' => 'required|string|max:100',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors());       
        }

        $student = Student::create([
            'name' => $request->name,
            'surname' => $request->surname,
         ]);
        
        return response()->json(new StudentResource($student));
    }

    public function update(Request $request, Student $student)
    {
        if (!Auth::check())
            return response()->json("Authentification required", 403);

        $validator = Validator::make($request->all(),[
            'name' => 'required|string|max:100',
            'surname' => 'required|string|max:100',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors());       
        }

        $student->name = $request->name;
        $student->surname = $request->surname;
        $student->save();
        
        return response()->json(new StudentResource($student));
    }

    public function destroy(Student $student)
    {
        if (!Auth::check())
            return response()->json("Authentification required", 403);

        $student->delete();

        return response()->json('Student deleted successfully');
    }
}
