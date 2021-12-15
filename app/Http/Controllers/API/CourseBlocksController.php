<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\BlockResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Block;
use App\Models\Course;
use App\Http\Controllers\API\BlockTasksController;

class CourseBlocksController extends Controller
{
    public function index($courseId, Request $request)
    {
        $blocks = Course::find($courseId)->blocks;
        $validator = Validator::make($request->all(),[
            'student_id' => 'numeric',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors());
        }
        if(isset($request['student_id']))
        {
            $blockTasksController = new BlockTasksController();
            $res = [];
            foreach($blocks as $block)
            {
                $tasks = $blockTasksController->index($block->id, $request)->original;
                array_push($res, [
                    'id' => $block->id,
                    'course_id' => $block->course_id,
                    'parent_id' => $block->parent_id,
                    'name' => $block->name,
                    'points_max' => $block->points_max,
                    'tasks' => $tasks
                ]);
            }
            return response()->json($res);
        }
        else
        {
            $data = Course::find($courseId)->blocks;
            return response()->json(BlockResource::collection($data));
        }
    }

    public function show($courseId, $blockId)
    {
        $block = Course::find($courseId)->blocks()->where('id', $blockId)->first();
        if (is_null($block))
            return response()->json("Block with id - $blockId for course $courseId not found", 404);
        return response()->json(new BlockResource($block));
    }
}
