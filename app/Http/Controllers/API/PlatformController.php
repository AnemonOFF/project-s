<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\Platform;
use App\Http\Resources\PlatformResource;

class PlatformController extends Controller
{
    /**
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Platform::latest()->get();
        return response()->json(PlatformResource::collection($data));
    }

    /**
     * Return the specified resource
     * 
     * @param int $id - platform id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $platform = Platform::find($id);
        if (is_null($platform))
            return response()->json("Platform with id - $id not found", 404);
        return response()->json(new PlatformResource($platform));
    }

    public function store(Request $request)
    {
        if (!Auth::check())
            return response()->json("Authentification required", 403);

        $validator = Validator::make($request->all(),[
            'name' => 'required|string|max:100',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors());       
        }

        $program = Platform::create([
            'name' => $request->name,
         ]);
        
        return response()->json(new PlatformResource($program));
    }

    public function update(Request $request, Platform $platform)
    {
        if (!Auth::check())
            return response()->json("Authentification required", 403);
        
        $validator = Validator::make($request->all(),[
            'name' => 'required|string|max:100',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors());       
        }

        $platform->name = $request->name;
        $platform->save();
        
        return response()->json(new PlatformResource($platform));
    }

    public function destroy(Platform $platform)
    {
        if (!Auth::check())
            return response()->json("Authentification required", 403);
        
        $platform->delete();

        return response()->json('Platform deleted successfully');
    }
}
