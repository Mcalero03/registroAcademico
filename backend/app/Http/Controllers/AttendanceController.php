<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Attendance_Detail;
use App\Models\Group;
use App\Models\Inscription;
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
            // $item->attendance_date = date("d/m/y");

            $item->attendances = Attendance_Detail::select(DB::raw("CONCAT(student.last_name, ', ',student.name) as full_name"), 'inscription.id', 'attendance_detail.status as attendance_status')
                ->join('attendance', 'attendance_detail.attendance_id', '=', 'attendance.id')
                ->leftjoin('inscription_detail as i', 'attendance_detail.inscription_detail_id', '=', 'i.id')
                ->leftjoin('inscription', 'i.inscription_id', '=', 'inscription.id')
                ->leftjoin('group', 'i.group_id', '=', 'group.id')
                ->leftjoin('subject', 'group.subject_id', '=', 'subject.id')
                ->leftjoin('student', 'inscription.student_id', '=', 'student.id')
                ->where('attendance_detail.attendance_id', $item->id)

                ->get()->unique();

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

        if ($data['attendances'] != null) {
            $date = $data['attendance_date'];
            $group_id = Group::where('group_code', $data['group'])->first()?->id;

            $dataExists = Attendance::where('attendance_date', $date)
                ->where('group_id', $group_id)
                ->exists();

            if (!$dataExists) {
                $attendance = Attendance::create([
                    'attendance_date' => $data['attendance_date'],
                    'attendance_time' => $data['attendance_time'],
                    'group_id' =>  $group_id,
                ]);

                foreach ($data['attendances'] as $value) {
                    Attendance_Detail::create([
                        'status' => $value['attendance_status'],
                        'inscription_detail_id' => Encrypt::decryptValue($value['inscription_id']),
                        'attendance_id' => $attendance->id,
                    ]);
                }

                $attendance->save();

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

    public function teacherSubject($name, $last_name)
    {
        $subject = Group::select('subject.subject_name')
            ->join('subject', 'group.subject_id', '=', 'subject.id')
            ->join('teacher', 'group.teacher_id', '=', 'teacher.id')
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
        $group = Group::select('group.group_code as group', 'group.id')
            ->join('subject', 'group.subject_id', '=', 'subject.id')
            ->join('teacher', 'group.teacher_id', '=', 'teacher.id')
            ->where('teacher.name', $name)
            ->where('teacher.last_name', $last_name)
            ->where('subject.subject_name', $subject)
            ->get('group.group_code');

        $group = Encrypt::encryptObject($group, 'id');

        return response()->json([
            "message" => "Registro encontrado correctamente",
            "group" => $group
        ]);
    }


    public function student($group, $subject)
    {
        $student = Inscription::select(DB::raw("CONCAT(student.last_name, ', ',student.name) as full_name"), 'i.id as inscription_id',)
            ->selectRaw("0 as attendance_status")
            ->leftjoin('inscription_detail as i', 'inscription.id', '=', 'i.inscription_id')
            ->join('group', 'i.group_id', '=', 'group.id')
            ->join('subject', 'group.subject_id', '=', 'subject.id')
            ->join('student', 'inscription.student_id', '=', 'student.id')
            ->where('group.group_code', $group)
            ->where('subject.subject_name', $subject)
            ->where('i.status', 'not like', 'Retirado')
            ->orderby('student.last_name', 'asc')
            ->get();

        $student = Encrypt::encryptObject($student, 'inscription_id');

        return response()->json([
            "message" => "Registro encontrado correctamente",
            "student" => $student
        ]);
    }
}
