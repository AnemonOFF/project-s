<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\Block;
use App\Http\Resources\BlockResource;

class BlockController extends Controller
{
    public function index()
    {
        $data = Block::latest()->get();
        return response()->json(BlockResource::collection($data));
    }

    public function show($id)
    {
        $block = Block::find($id);
        if (is_null($block))
            return response()->json("Block with id - $id not found", 404);
        return response()->json(new BlockResource($block));
    }

    public function store(Request $request)
    {
        if (!Auth::check())
            return response()->json("Authentification required", 403);

        $validator = Validator::make($request->all(),[
            'name' => 'required|string|max:100',
            'course_id' => 'required|exists:courses.id',
            'parent_id' => 'nullable|exists:blocks.id',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors());       
        }

        $block = Block::create([
            'name' => $request->name,
            'course_id' => $request->course_id,
            'parent_id' => $request->parent_id,
         ]);
        
        return response()->json(new BlockResource($block));
    }

    public function update(Request $request, Block $block)
    {
        if (!Auth::check())
            return response()->json("Authentification required", 403);

        $validator = Validator::make($request->all(),[
            'name' => 'required|string|max:100',
            'course_id' => 'required|exists:courses.id',
            'parent_id' => 'nullable|exists:blocks.id',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors());       
        }

        $block->name = $request->name;
        $block->course_id = $request->course_id;
        $block->parent_id = $request->parent_id;
        $block->save();
        
        return response()->json(new BlockResource($block));
    }

    public function destroy(Block $block)
    {
        if (!Auth::check())
            return response()->json("Authentification required", 403);

        $block->delete();

        return response()->json('Block deleted successfully');
    }
}
