<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\Mark;
use App\Http\Resources\MarkResource;

class MarkController extends Controller
{
    public function index()
    {
        $data = Mark::latest()->get();
        return response()->json(MarkResource::collection($data));
    }

    public function show($id)
    {
        $mark = Mark::find($id);
        if (is_null($mark))
            return response()->json("Mark with id - $id not found", 404);
        return response()->json(new MarkResource($mark));
    }

    public function store(Request $request)
    {
        if (!Auth::check())
            return response()->json("Authentification required", 403);

        $validator = Validator::make($request->all(),[
            'student_id' => 'required|exists:students.id',
            'task_id' => 'required|exists:tasks.id',
            'mark' => 'required|numeric|min:0',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors());       
        }

        $mark = Mark::create([
            'student_id' => $request->student_id,
            'task_id' => $request->task_id,
            'mark' => $request->mark
         ]);
        
        return response()->json(new MarkResource($mark));
    }

    public function update(Request $request, Mark $mark)
    {
        if (!Auth::check())
            return response()->json("Authentification required", 403);

        $validator = Validator::make($request->all(),[
            'student_id' => 'required|exists:students.id',
            'task_id' => 'required|exists:tasks.id',
            'mark' => 'required|numeric|min:0',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors());       
        }

        $mark->student_id = $request->student_id;
        $mark->task_id = $request->task_id;
        $mark->mark = $request->mark;
        $mark->save();
        
        return response()->json(new MarkResource($mark));
    }

    public function destroy(Mark $mark)
    {
        if (!Auth::check())
            return response()->json("Authentification required", 403);

        $mark->delete();

        return response()->json('Mark deleted successfully');
    }
}
