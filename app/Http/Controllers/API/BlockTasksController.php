<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\TaskResource;
use App\Models\Block;
use Illuminate\Http\Request;

class BlockTasksController extends Controller
{
    public function index($block_id)
    {
        $data = Block::find($block_id)->tasks;
        return response()->json(TaskResource::collection($data));
    }

    public function show($block_id, $task_id)
    {
        $task = Block::find($block_id)->tasks()->where('id', $task_id)->first();
        if (is_null($task))
            return response()->json("Task with id - $task_id for block $block_id not found", 404);
        return response()->json(new TaskResource($task));
    }
}
