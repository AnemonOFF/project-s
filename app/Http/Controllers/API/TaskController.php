<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\Task;
use App\Http\Resources\TaskResource;

class TaskController extends Controller
{
    public function index()
    {
        $data = Task::latest()->get();
        return response()->json(TaskResource::collection($data));
    }

    public function show($id)
    {
        $task = Task::find($id);
        if (is_null($task))
            return response()->json("Task with id - $id not found", 404);
        return response()->json(new TaskResource($task));
    }

    public function store(Request $request)
    {
        if (!auth('sanctum')->check())
            return response()->json("Authentification required", 403);

        $validator = Validator::make($request->all(),[
            'name' => 'required|string|max:100',
            'block_id' => 'required|exists:courses,id',
            'points_max' => 'nullable|numeric|min:0',
            'points_pass' => 'nullable|numeric|min:0|less_than_field:points_max',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors());       
        }

        $task = Task::create([
            'name' => $request->name,
            'block_id' => $request->block_id,
            'points_max' => $request->points_max,
            'points_pass' => $request->points_pass,
         ]);
        
        return response()->json(new TaskResource($task));
    }

    public function update(Request $request, Task $task)
    {
        if (!auth('sanctum')->check())
            return response()->json("Authentification required", 403);

        $validator = Validator::make($request->all(),[
            'name' => 'required|string|max:255',
            'block_id' => 'required|exists:courses,id',
            'points_max' => 'nullable|numeric|min:0',
            'points_pass' => 'nullable|numeric|min:0|less_than_field:points_max',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors());       
        }

        $task->name = $request->name;
        $task->block_id = $request->block_id;
        $task->points_max = $request->points_max;
        $task->points_pass = $request->points_pass;
        $task->save();
        
        return response()->json(new TaskResource($task));
    }

    public function destroy(Task $task)
    {
        if (!auth('sanctum')->check())
            return response()->json("Authentification required", 403);

        $task->delete();

        return response()->json('Task deleted successfully');
    }
}
