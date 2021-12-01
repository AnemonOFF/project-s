<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\MarkResource;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskMarksController extends Controller
{
    public function index($task_id)
    {
        $data = Task::find($task_id)->marks;
        return response()->json(MarkResource::collection($data));
    }

    public function show($task_id, $mark_id)
    {
        $mark = Task::find($task_id)->marks()->where('id', $mark_id)->first();
        if (is_null($mark))
            return response()->json("Mark with id - $mark_id for task $task_id not found", 404);
        return response()->json(new MarkResource($mark));
    }
}
