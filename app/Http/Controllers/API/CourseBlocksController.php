<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\BlockResource;
use App\Models\Course;

class CourseBlocksController extends Controller
{
    public function index($course_id)
    {
        $data = Course::find($course_id)->blocks;
        return response()->json(BlockResource::collection($data));
    }

    public function show($course_id, $block_id)
    {
        $block = Course::find($course_id)->blocks()->where('id', $block_id)->first();
        if (is_null($block))
            return response()->json("Block with id - $block_id for course $course_id not found", 404);
        return response()->json(new BlockResource($block));
    }
}
