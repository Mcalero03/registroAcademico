<?php

use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DirectionController;
use App\Http\Controllers\PensumTypeController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\RelativeController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\CollegeController;
use App\Http\Controllers\CycleController;
use App\Http\Controllers\PensumController;
use App\Http\Controllers\PensumSubjectDetailController;
use App\Http\Controllers\EvaluationController;
use App\Http\Controllers\MunicipalityController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\InscriptionController;
use App\Http\Controllers\AttendanceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/status', fn () => response()->json(["message" => "Active"]));
Route::resource('/department', DepartmentController::class);
Route::resource('/direction', DirectionController::class);
Route::resource('/pensumType', PensumTypeController::class);
Route::resource('/group', GroupController::class);
Route::resource('/relative', RelativeController::class);
Route::resource('/subject', SubjectController::class);
Route::resource('/teacher', TeacherController::class);
Route::resource('/college', CollegeController::class);
Route::resource('/cycle', CycleController::class);
Route::resource('/pensum', PensumController::class);
Route::resource('/pensumSubjectDetail', PensumSubjectDetailController::class);
Route::resource('/evaluation', EvaluationController::class);
Route::resource('/municipality', MunicipalityController::class);
Route::resource('/schedule', ScheduleController::class);
Route::resource('/student', StudentController::class);
Route::resource('/inscription', InscriptionController::class);
Route::resource('/attendance', AttendanceController::class);
