<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Attendance_Detail;
use App\Models\Group;
use App\Models\Inscription;
use App\Models\Cycle;
use App\Models\School;
use App\Models\Teacher;
use DB;
use Encrypt;

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

        $attendance = Attendance::allDataSearched($search, $sortBy, $sort, $skip, $itemsPerPage)->unique();

        foreach ($attendance as $item) {

            $item->attendances = Attendance_Detail::select(
                DB::raw("CONCAT(student.last_name, ', ',student.name) as full_name"),
                'inscription.id',
                'attendance_detail.status as attendance_status'
            )
                ->join('attendance', 'attendance_detail.attendance_id', '=', 'attendance.id')
                ->leftjoin('inscription_detail as i', 'attendance_detail.inscription_detail_id', '=', 'i.id')
                ->leftjoin(
                    'inscription',
                    'i.inscription_id',
                    '=',
                    'inscription.id'
                )
                ->leftjoin('group', 'i.group_id', '=', 'group.id')
                ->leftjoin('subject', 'group.subject_id', '=', 'subject.id')
                ->leftjoin('student', 'inscription.student_id', '=', 'student.id')
                ->where('attendance_detail.attendance_id', $item->id)

                ->get();

            $item->attendance_count = Attendance_Detail::select(
                DB::raw("COUNT(CASE WHEN attendance_detail.status = '1' THEN attendance_detail.id END) as attendance_count"),
                DB::raw("COUNT(CASE WHEN attendance_detail.status = '0' THEN attendance_detail.id END) as no_attendance_count"),
            )
                ->join('attendance', 'attendance_detail.attendance_id', '=', 'attendance.id')
                ->where('attendance_detail.attendance_id', $item->id)

                ->get();

            $item->attendances = Encrypt::encryptObject($item->attendances, "id");
        }

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
        $data = $request->all();
        function getTime()
        {
            $datetime = date("H:i:s");
            return $datetime;
        }

        // Llamada a la función para obtener la hora actual
        $currentTime = getTime();

        $group = explode('*', $data['group']);
        $id_group = $group[1];

        if ($data['attendances'] != null) {
            $date = $data['attendance_date'];
            // $group_id = Group::where('id', $id_group)->first()?->id;

            $dataExists = Attendance::where('attendance_date', $date)
                ->where('group_id', $id_group)
                ->exists();

            if (!$dataExists) {
                $attendance = Attendance::create([
                    'attendance_date' => $data['attendance_date'],
                    'attendance_time' => $currentTime,
                    'group_id' =>  $id_group,
                ]);

                foreach ($data['attendances'] as $value) {
                    Attendance_Detail::create([
                        'status' => $value['attendance_status'],
                        'inscription_detail_id' => Encrypt::decryptValue($value['inscription_detail_id']),
                        'attendance_id' => $attendance->id,
                    ]);
                }

                return response()->json([
                    "message" => "Registro creado correctamente",
                ]);
            } else {
                return response()->json([
                    "error" => "Ya se tomó la asistencia del grupo",
                ]);
            }
        } else {
            return response()->json([
                "error" => "No se especificaron estudiantes",
            ]);
        }
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
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
    }

    public function bySchool($school)
    {
        $id = School::where('school_name', $school)->first()?->id;

        $teachers = Teacher::select(DB::raw("CONCAT(teacher.name, ', ', teacher.last_name) as full_name"),)
            ->join('school', 'teacher.school_id', '=', 'school.id')
            ->where('teacher.school_id', $id)
            ->get();

        return response()->json([
            "message" => "Registro encontrado correctamente",
            "teachers" => $teachers
        ]);
    }

    public function teacherSubject($name, $last_name)
    {
        $active_cycle = Cycle::where('cycle.status', 'Activo')->first()?->id;

        $subjects = Group::select('subject.subject_name', 'subject.id')
            ->join('subject', 'group.subject_id', '=', 'subject.id')
            ->join('cycle_subject_detail', 'subject.id', '=', 'cycle_subject_detail.subject_id')
            ->join('cycle', 'cycle_subject_detail.cycle_id', '=', 'cycle.id')
            ->join('schedule_classroom_group_detail', 'group.id', '=', 'schedule_classroom_group_detail.group_id')
            ->join('teacher', 'group.teacher_id', '=', 'teacher.id')
            ->where('teacher.name', $name)
            ->where('teacher.last_name', $last_name)
            ->where('schedule_classroom_group_detail.cycle_id', $active_cycle)
            ->where('cycle.status', 'Activo')
            ->whereNull('cycle.deleted_at')
            ->whereNull('cycle_subject_detail.deleted_at')

            ->get('subject.subject_name', 'subject.id')->unique();

        return response()->json([
            "message" => "Registro encontrado correctamente",
            "subject" => $subjects
        ]);
    }

    public function bySubject($name, $last_name, $subject)
    {
        $active_cycle = Cycle::where('cycle.status', 'Activo')->first()?->id;

        $groups = Group::select(DB::raw("CONCAT(group.group_code, '*', group.id) as group_code"))
            ->join('schedule_classroom_group_detail', 'group.id', '=', 'schedule_classroom_group_detail.group_id')
            ->join('subject', 'group.subject_id', '=', 'subject.id')
            ->join('teacher', 'group.teacher_id', '=', 'teacher.id')
            ->join('cycle_subject_detail', 'subject.id', '=', 'cycle_subject_detail.subject_id')
            ->join('cycle', 'cycle_subject_detail.cycle_id', '=', 'cycle.id')
            ->where('subject.subject_name', $subject)
            ->where('teacher.name', $name)
            ->where('teacher.last_name', $last_name)
            ->where('schedule_classroom_group_detail.cycle_id', $active_cycle)
            ->where('cycle.status', 'Activo')
            ->whereNull('cycle.deleted_at')
            ->whereNull('cycle_subject_detail.deleted_at')
            ->whereNull('group.deleted_at')
            ->get("group.group_code");

        $groups = Encrypt::encryptObject($groups, 'id');

        return response()->json([
            "message" => "Registro encontrado correctamente",
            "group" => $groups
        ]);
    }


    public function byGroup($group, $subject)
    {

        $groups = explode('*', $group);
        $id_group = $groups[1];

        $student = Inscription::select(DB::raw("CONCAT(student.last_name, ', ',student.name) as full_name"), 'i.id as inscription_detail_id')
            ->selectRaw("0 as attendance_status")
            ->leftjoin('inscription_detail as i', 'inscription.id', '=', 'i.inscription_id')
            ->join('group', 'i.group_id', '=', 'group.id')
            ->join('subject', 'group.subject_id', '=', 'subject.id')
            ->join('cycle', 'inscription.cycle_id', '=', 'cycle.id')
            ->join('student', 'inscription.student_id', '=', 'student.id')
            ->where('group.id', $id_group)
            ->where('subject.subject_name', $subject)
            ->where('i.status', 'not like', 'Retirado')
            ->where('cycle.status', 'Activo')
            ->orderby('student.last_name', 'asc')
            ->get();

        $student = Encrypt::encryptObject($student, 'inscription_detail_id');

        return response()->json([
            "message" => "Registro encontrado correctamente",
            "student" => $student
        ]);
    }
}
