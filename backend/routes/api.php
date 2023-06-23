<?php

use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\PensumTypeController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\RelativeController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\SubSchoolController;
use App\Http\Controllers\CycleController;
use App\Http\Controllers\PensumController;
use App\Http\Controllers\PensumSubjectDetailController;
use App\Http\Controllers\EvaluationController;
use App\Http\Controllers\MunicipalityController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\InscriptionController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\KinshipController;
use App\Http\Controllers\ClassroomController;
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
//DEPARTMENT
Route::resource('/department', DepartmentController::class);

//SCHOOL
Route::resource('/school', SchoolController::class);

//PENSUM_TYPE
Route::resource('/pensumType', PensumTypeController::class);

//GROUP
Route::resource('/group', GroupController::class);
Route::get('/group/classroomCapacity/{school}/{quantity}', [GroupController::class, 'classroomCapacity']);
Route::get('/group/bySubject/{subject}', [GroupController::class, 'bySubject']);
Route::get('/group/bySchool/{school}', [GroupController::class, 'bySchool']);
Route::get('/group/byTeacher/{teacher}/{classroom}', [GroupController::class, 'byTeacher']);
Route::get('/group/byDay/{day}/{teacher}', [GroupController::class, 'byDay']);
Route::get('/group/byStartTime/{start_time}/{week_day}', [GroupController::class, 'byStartTime']);
Route::get('/group/classroombyTeacher/{teacher}', [GroupController::class, 'classroombyTeacher']);

//RELATIVE
Route::resource('/relative', RelativeController::class);

//SUBJECT
Route::resource('/subject', SubjectController::class);
Route::get('/subject/bySchool/{school}', [SubjectController::class, 'bySchool']);
Route::get('/subject/subjectByCycle/{school}', [SubjectController::class, 'subjectByCycle']);

//TEACHER
Route::resource('/teacher', TeacherController::class);

//SUB-SCHOOL
Route::resource('/subSchool', SubSchoolController::class);

//CYCLE
Route::resource('/cycle', CycleController::class);
Route::get('/cycle/bySchool/{school}', [CycleController::class, 'bySchool']);
Route::get('/cycle/byPensum/{pensum}', [CycleController::class, 'byPensum']);

//PENSUM
Route::resource('/pensum', PensumController::class);
Route::get('/pensum/showSubSchools/{school}', [PensumController::class, 'showSubSchools']);

//PENSUM SUBJECT DETAIL
Route::resource('/pensumSubjectDetail', PensumSubjectDetailController::class);
Route::get('/pensumSubjectDetail/byPensum/{pensum}/{subject}', [PensumSubjectDetailController::class, 'pensum']); //api utilizada para asignar los prerrequisitos
Route::get('/pensumSubjectDetail/pensumSubject/{pensum}', [PensumSubjectDetailController::class, 'pensumSubject']);
Route::get('/pensumSubjectDetail/showSubSchool/{school}', [PensumSubjectDetailController::class, 'showSubSchool']);
Route::get('/pensumSubjectDetail/showPensums/{sub_school}', [PensumSubjectDetailController::class, 'showPensums']);

//MUNICIPALITY
Route::resource('/municipality', MunicipalityController::class);

//SCHEDULE
Route::resource('/schedule', ScheduleController::class);

//STUDENT
Route::resource('/student', StudentController::class);
Route::get('/student/byCareer/{id}/{school}', [StudentController::class, 'career']);

//INSCRIPTION
Route::resource('/inscription', InscriptionController::class);
Route::get('/inscription/showSchedules/{inscription}', [InscriptionController::class, 'showSchedules']);
Route::get('/inscription/showCareers/{student}', [InscriptionController::class, 'showCareers']);
Route::get('/inscription/availableSubjects/{student}/{pensum}/{cycle}', [InscriptionController::class, 'availableSubjects']);

//ATTENDANCE
Route::resource('/attendance', AttendanceController::class);
Route::get('/attendance/bySchool/{school}', [AttendanceController::class, 'bySchool']);
Route::get('/attendance/byTeacher/{name}/{last_name}', [AttendanceController::class, 'teacherSubject']);
Route::get('/attendance/bySubject/{name}/{last_name}/{subject}', [AttendanceController::class, 'subject']);
Route::get('/attendance/byGroup/{group}/{subject}', [AttendanceController::class, 'student']);

//KINSHIP
Route::resource('/kinship', kinshipController::class);

//CLASSROOM
Route::resource('/classroom', ClassroomController::class);

//EVALUATION
Route::resource('/evaluation', EvaluationController::class);
Route::get('/evaluation/showTeacher/{school}', [EvaluationController::class, 'showTeacher']);
Route::get('/evaluation/showSubjects/{name}/{last_name}', [EvaluationController::class, 'showSubjects']);
Route::get('/evaluation/showGroups/{subject}', [EvaluationController::class, 'showGroups']);
Route::get('/evaluation/showStudents/{group}', [EvaluationController::class, 'showStudents']);
