<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Inscription;
use App\Models\TeacherSubjectDetail;
use Encrypt;
use Illuminate\Support\Facades\DB; 

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $itemsPerPage = $request->itemsPerPage ?? 10;
        $skip = ($request->page - 1) * $request->itemsPerPage;

        // Getting all the records
        if (($request->itemsPerPage == -1)) {
            $itemsPerPage =  Attendance::count();
            $skip = 0;
        }

        $sortBy = (isset($request->sortBy[0]['key'])) ? $request->sortBy[0]['key'] : 'id';
        $sort = (isset($request->sortDesc[0]['order'])) ? "asc" : 'desc';

        $search = (isset($request->search)) ? "%$request->search%" : '%%';

        $attendance = Attendance::allDataSearched($search, $sortBy, $sort, $skip, $itemsPerPage);
        $attendance = Encrypt::encryptObject($attendance, "id");

        $total = Attendance::counterPagination($search);

        return response()->json([
            "message" => "Registros obtenidos correctamente.",
            "data" => $attendance,
            "total" => $total,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $attendance = new Attendance;
        $attendance->attendance_date = $request->attendance_date;
        $attendance->attendance_time = $request->attendance_time;
        $attendance->status = $request->status;
        $attendance->inscription_id = $request->inscription_id;

        $attendance->save();

        return response()->json([
            "message" => "Registro creado correctamente",
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show()
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $data = Encrypt::decryptArray($request->all(), 'id');

        $attendance = Attendance::where('id', $data['id'])->first();
        $attendance->attendance_date = $request->attendance_date;
        $attendance->attendance_time = $request->attendance_time;
        $attendance->status = $request->status;
        $attendance->inscription_id = $request->inscription_id;
        $attendance->save();

        return response()->json([
            "message" => "Registro modificado correctamente",
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $id = Encrypt::decryptValue($request->id);

        Attendance::where('id', $id)->delete();

        return response()->json([
            "message" => "Registro eliminado correctamente",
        ]);
    } 

    public function teacherSubject($name, $last_name)
    { 
        $subject = TeacherSubjectDetail::select('subject.subject_name')
        ->join('subject', 'teacher_subject_detail.subject_id', '=', 'subject.id') 
        ->join('teacher', 'teacher_subject_detail.teacher_id', '=', 'teacher.id')
        ->where('teacher.name', $name)
        ->where('teacher.last_name', $last_name)
        ->get('subject.subject_name');

        return response()->json([
            "message" => "Registro encontrado correctamente",
            "subject" => $subject
        ]);
    } 

    public function subject($name, $last_name, $subject)
    {
        $group = TeacherSubjectDetail::select('group.group_name as group')
        ->join('subject', 'teacher_subject_detail.subject_id', '=', 'subject.id') 
        ->join('teacher', 'teacher_subject_detail.teacher_id', '=', 'teacher.id')
        ->join('group', 'teacher_subject_detail.group_id', '=', 'group.id')
        ->where('teacher.name', $name)
        ->where('teacher.last_name', $last_name)
        ->where('subject.subject_name', $subject)
        ->get('group.group_name');

        return response()->json([
            "message" => "Registro encontrado correctamente",
            "group" => $group
        ]);
    } 

    public function student($group, $subject)
    {
        $student = Inscription::select(DB::raw("CONCAT(student.name, ', ', student.last_name) as full_name"),'inscription.attendance_quantity')
        ->join('subject', 'inscription.subject_id', '=', 'subject.id') 
        ->join('group', 'inscription.group_id', '=', 'group.id')
        ->join('student', 'inscription.student_id', '=', 'student.id' )
        ->where('group.group_name', $group)
        ->where('subject.subject_name', $subject)
        ->get();

        return response()->json([
            "message" => "Registro encontrado correctamente",
            "student" => $student
        ]);
    }
}
