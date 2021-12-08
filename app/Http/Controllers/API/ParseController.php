<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Spreedsheet;
use App\Jobs\ProcessSpreedsheetParse;

class ParseController extends Controller
{
    public function parse(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'file' => ['required','mimes:csv,xls,xlsx','max:4096','file'],
            'course_id' => 'required|exists:courses,id',
            'type' => 'in:default,ulearn',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors());
        }

        $spreedsheet = Spreedsheet::create([
            'name' => $request->file('file')->getClientOriginalName(),
            'path' => $request->file('file')->store('public/spreedsheets'),
            'type' => $request->type,
            'course_id' => $request->course_id,
        ]);

        ProcessSpreedsheetParse::dispatch($spreedsheet);

        return response()->json("File saved successfully, parse process will continue in background. You can close this tab.");
    }
}
