<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Attendance_Detail;
use App\Models\TeacherSubjectDetail;
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

            $item->attendances = Attendance_Detail::select(DB::raw("CONCAT(student.last_name, ', ',student.name) as full_name"), 'inscription.id', 'attendance_detail.status as attendance_status')
                ->join('attendance', 'attendance_detail.attendance_id', '=', 'attendance.id')
                ->leftjoin('inscription', 'attendance_detail.inscription_id', '=', 'inscription.id')
                ->leftjoin('subject', 'inscription.subject_id', '=', 'subject.id')
                ->leftjoin('group', 'inscription.group_id', '=', 'group.id')
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

            // $fecha = '2023-05-17';
            // $group = '1';
            $date = $data['attendance_date'];
            $group = Encrypt::decryptValue($data['group']);

            $dataExists = Attendance::where('attendance_date', $date)
                ->where('group_id', $group)
                ->exists();

            if (!$dataExists) {
                $attendance = Attendance::create([
                    'attendance_date' => $data['attendance_date'],
                    'attendance_time' => $data['attendance_time'],
                    'group_id' =>  $group,
                ]);

                foreach ($data['attendances'] as $value) {
                    Attendance_Detail::create([
                        'status' => $value['attendance_status'],
                        'inscription_id' => Encrypt::decryptValue($value['inscription_id']),
                        'attendance_id' => $attendance->id,
                    ]);
                }

                $attendance->save();

                return response()->json([
                    "message" => "Registro creado correctamente",
                ]);
            } else {
                return response()->json([
                    "error" => "No se puede crear otro registro",
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
        // $data = Encrypt::decryptArray($request->all(), 'id');

        // Attendance::where('id', $data['id'])->update([
        //     'attendance_date' => $data['attendance_date'],
        //     'group_id' => $data['group_id'],
        // ]);

        // foreach ($data['attendances'] as $value) {
        //     Attendance_Detail::create([
        //         'status' => $value['status'],
        //         'inscription_id' => $value['inscription_id'],
        //         'attendance_id' => $data->id,
        //     ]);
        // }


        // return response()->json([
        //     "message" => "Registro modificado correctamente",
        // ]);
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
        $group = TeacherSubjectDetail::select('group.group_name as group', 'group.id')
            ->join('subject', 'teacher_subject_detail.subject_id', '=', 'subject.id')
            ->join('teacher', 'teacher_subject_detail.teacher_id', '=', 'teacher.id')
            ->join('group', 'teacher_subject_detail.group_id', '=', 'group.id')
            ->where('teacher.name', $name)
            ->where('teacher.last_name', $last_name)
            ->where('subject.subject_name', $subject)
            ->get('group.group_name');

        $group = Encrypt::encryptObject($group, 'id');

        return response()->json([
            "message" => "Registro encontrado correctamente",
            "group" => $group
        ]);
    }


    public function student($group, $subject)
    {
        $student = Inscription::select(DB::raw("CONCAT(student.last_name, ', ',student.name) as full_name"), 'inscription.id as inscription_id',)
            ->selectRaw("0 as attendance_status")
            ->join('subject', 'inscription.subject_id', '=', 'subject.id')
            ->join('group', 'inscription.group_id', '=', 'group.id')
            ->join('student', 'inscription.student_id', '=', 'student.id')
            ->where('group.id', Encrypt::decryptValue($group))
            ->where('subject.subject_name', $subject)
            ->orderby('student.last_name', 'asc')
            ->get();

        $student = Encrypt::encryptObject($student, 'inscription_id');

        return response()->json([
            "message" => "Registro encontrado correctamente",
            "student" => $student
        ]);
    }
}
