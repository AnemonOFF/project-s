<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\TaskResource;
use Illuminate\Support\Facades\Validator;
use App\Models\Block;
use Illuminate\Http\Request;

class BlockTasksController extends Controller
{
    public function index($blockId, Request $request)
    {
        $block = Block::find($blockId);
        $validator = Validator::make($request->all(),[
            'student_id' => 'numeric',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors());
        }
        if(isset($request['student_id'])){
            $studentId = $request['student_id'];
            $tasks = $block->tasks;
            $res = [];
            foreach($tasks as $task)
            {
                $mark = $task->marks->firstWhere('student_id', $studentId)->mark;
                array_push($res, [
                    'id' => $task->id,
                    'block_id' => $task->block_id,
                    'name' => $task->name,
                    'points_max' => $task->points_max,
                    'points_pass' => $task->points_pass,
                    'points_student' => $mark
                ]);
            }
            return response()->json($res);
        }
        else{
            $data = Block::find($blockId)->tasks;
            return response()->json(TaskResource::collection($data));
        }
    }

    public function show($blockId, $taskId)
    {
        $task = Block::find($blockId)->tasks()->where('id', $taskId)->first();
        if (is_null($task))
            return response()->json("Task with id - $taskId for block $blockId not found", 404);
        return response()->json(new TaskResource($task));
    }
}
