<?php

use App\Http\Controllers\CoursesController;
use App\Http\Controllers\PlatformsController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('king');
});

Route::get('/platforms', [PlatformsController::class, 'getPlatforms'])->name('platforms');

Route::get('/courses', [CoursesController::class, 'getCourses'])->name('courses');

Route::get('/login', function(Request $request){
    if (!auth('sanctum')->check())
        return response()->json('You are not logged in!');
    return response()->json('You are logged in!');
})->name('login');
