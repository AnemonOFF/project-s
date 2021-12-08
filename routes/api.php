<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//API route for login user
Route::post('/login', [App\Http\Controllers\API\AuthController::class, 'login']);

//API routes for platforms, courses, blocks, tasks, marks and students
Route::resource('platforms', App\Http\Controllers\API\PlatformController::class);
Route::resource('platforms.courses', App\Http\Controllers\API\PlatformCoursesController::class);
Route::resource('courses', App\Http\Controllers\API\CourseController::class);
Route::resource('courses.blocks', App\Http\Controllers\API\CourseBlocksController::class);
Route::resource('blocks', App\Http\Controllers\API\BlockController::class);
Route::resource('blocks.tasks', App\Http\Controllers\API\BlockTasksController::class);
Route::resource('tasks', App\Http\Controllers\API\TaskController::class);
Route::resource('tasks.marks', App\Http\Controllers\API\TaskMarksController::class);
Route::resource('marks', App\Http\Controllers\API\MarkController::class);
Route::resource('students', App\Http\Controllers\API\StudentController::class);
Route::resource('students.marks', App\Http\Controllers\API\StudentMarksController::class);
Route::resource('courses.students', App\Http\Controllers\API\CourseStudentsController::class);

//Protecting routes
Route::group(['middleware' => ['auth:sanctum']], function () {
    //API route to get current user info
    Route::get('/profile', function(Request $request) {
        return auth()->user();
    });

    //API route for parse spreedsheet
    Route::post('/parse', [App\Http\Controllers\API\ParseController::class, 'parse']);

    //API route for register new user
    Route::post('/register', [App\Http\Controllers\API\AuthController::class, 'register']);

    //API route for logout user
    Route::post('/logout', [App\Http\Controllers\API\AuthController::class, 'logout']);
});
