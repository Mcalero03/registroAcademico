<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use Illuminate\Http\Request;
use Encrypt;
use App\Models\School;
use App\Models\Cycle;
use App\Models\Schedule;

class TeacherController extends Controller
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
            $itemsPerPage =  Teacher::count();
            $skip = 0;
        }

        $sortBy = (isset($request->sortBy[0]['key'])) ? $request->sortBy[0]['key'] : 'id';
        $sort = (isset($request->sortDesc[0]['order'])) ? "asc" : 'desc';

        $search = (isset($request->search)) ? "%$request->search%" : '%%';

        $searchTeacher = (isset($request->searchTeacher)) ? "$request->searchTeacher" : '%%';

        $teacher_searched = Teacher::searchTeacher($searchTeacher)->unique();

        $teacher = Teacher::allDataSearched($search, $sortBy, $sort, $skip, $itemsPerPage);
        $teacher = Encrypt::encryptObject($teacher, "id");

        $total = Teacher::counterPagination($search);

        return response()->json([
            "message" => "Registros obtenidos correctamente.",
            "data" => $teacher,
            "total" => $total,
            "teacher" => $teacher_searched,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $teacher = new Teacher;
        $teacher->name = $request->name;
        $teacher->last_name = $request->last_name;
        $teacher->teacher_card = $request->teacher_card;
        $teacher->dui = $request->dui;
        $teacher->nit = $request->nit;
        $teacher->phone_number = $request->phone_number;
        $teacher->mail = $request->mail;
        $teacher->school_id = School::where("school_name", $request->school_name)->first()?->id;

        $teacher->save();

        return response()->json([
            "message" => "Registro creado correctamenre",
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $data = Encrypt::decryptArray($request->all(), 'id');

        $teacher = Teacher::where('id', $data['id'])->first();
        $teacher->name = $request->name;
        $teacher->last_name = $request->last_name;
        $teacher->teacher_card = $request->teacher_card;
        $teacher->dui = $request->dui;
        $teacher->nit = $request->nit;
        $teacher->phone_number = $request->phone_number;
        $teacher->mail = $request->mail;
        $teacher->school_id = School::where("school_name", $request->school_name)->first()?->id;

        $teacher->save();

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
        Teacher::where('id', $id)->delete();

        return response()->json([
            "message" => "Registro eliminado correctamente",
        ]);
    }

    public function showSchedules($card)
    {

        $active_cycle = Cycle::where('cycle.status', 'Activo')->first()?->id;
        $schedule = [
            'Lunes' => Teacher::select('schedule.*', 'classroom.classroom_name', 'group.group_code', 'subject.subject_name')
                ->join('group', 'group.teacher_id', '=', 'teacher.id')
                ->join('subject', 'group.subject_id', '=', 'subject.id')
                ->join('schedule_classroom_group_detail', 'group.id', '=', 'schedule_classroom_group_detail.group_id')
                ->join('schedule', 'schedule_classroom_group_detail.schedule_id', '=', 'schedule.id')
                ->join('classroom', 'schedule_classroom_group_detail.classroom_id', '=', 'classroom.id')
                ->where('teacher.teacher_card', $card)
                ->where('schedule.week_day', 'Lunes')
                ->where('schedule_classroom_group_detail.cycle_id', $active_cycle)
                ->whereNull('schedule_classroom_group_detail.deleted_at')
                ->orderBy('schedule.start_time', 'asc')
                ->get(),
            'Martes' => Teacher::select('schedule.*', 'classroom.classroom_name', 'group.group_code', 'subject.subject_name')
                ->join('group', 'group.teacher_id', '=', 'teacher.id')
                ->join('subject', 'group.subject_id', '=', 'subject.id')
                ->join('schedule_classroom_group_detail', 'group.id', '=', 'schedule_classroom_group_detail.group_id')
                ->join('schedule', 'schedule_classroom_group_detail.schedule_id', '=', 'schedule.id')
                ->join('classroom', 'schedule_classroom_group_detail.classroom_id', '=', 'classroom.id')
                ->where('teacher.teacher_card', $card)
                ->where('schedule.week_day', 'Martes')
                ->where('schedule_classroom_group_detail.cycle_id', $active_cycle)
                ->whereNull('schedule_classroom_group_detail.deleted_at')
                ->orderBy('schedule.start_time', 'asc')
                ->get(),
            'Miércoles' => Teacher::select('schedule.*', 'classroom.classroom_name', 'group.group_code', 'subject.subject_name')
                ->join('group', 'group.teacher_id', '=', 'teacher.id')
                ->join('subject', 'group.subject_id', '=', 'subject.id')
                ->join('schedule_classroom_group_detail', 'group.id', '=', 'schedule_classroom_group_detail.group_id')
                ->join('schedule', 'schedule_classroom_group_detail.schedule_id', '=', 'schedule.id')
                ->join('classroom', 'schedule_classroom_group_detail.classroom_id', '=', 'classroom.id')
                ->where('teacher.teacher_card', $card)
                ->where('schedule.week_day', 'Miércoles')
                ->where('schedule_classroom_group_detail.cycle_id', $active_cycle)
                ->whereNull('schedule_classroom_group_detail.deleted_at')
                ->orderBy('schedule.start_time', 'asc')
                ->get(),
            'Jueves' => Teacher::select('schedule.*', 'classroom.classroom_name', 'group.group_code', 'subject.subject_name')
                ->join('group', 'group.teacher_id', '=', 'teacher.id')
                ->join('subject', 'group.subject_id', '=', 'subject.id')
                ->join('schedule_classroom_group_detail', 'group.id', '=', 'schedule_classroom_group_detail.group_id')
                ->join('schedule', 'schedule_classroom_group_detail.schedule_id', '=', 'schedule.id')
                ->join('classroom', 'schedule_classroom_group_detail.classroom_id', '=', 'classroom.id')
                ->where('teacher.teacher_card', $card)
                ->where('schedule.week_day', 'Jueves')
                ->where('schedule_classroom_group_detail.cycle_id', $active_cycle)
                ->whereNull('schedule_classroom_group_detail.deleted_at')
                ->orderBy('schedule.start_time', 'asc')
                ->get(),
            'Viernes' => Teacher::select('schedule.*', 'classroom.classroom_name', 'group.group_code', 'subject.subject_name')
                ->join('group', 'group.teacher_id', '=', 'teacher.id')
                ->join('subject', 'group.subject_id', '=', 'subject.id')
                ->join('schedule_classroom_group_detail', 'group.id', '=', 'schedule_classroom_group_detail.group_id')
                ->join('schedule', 'schedule_classroom_group_detail.schedule_id', '=', 'schedule.id')
                ->join('classroom', 'schedule_classroom_group_detail.classroom_id', '=', 'classroom.id')
                ->where('teacher.teacher_card', $card)
                ->where('schedule.week_day', 'Viernes')
                ->where('schedule_classroom_group_detail.cycle_id', $active_cycle)
                ->whereNull('schedule_classroom_group_detail.deleted_at')
                ->orderBy('schedule.start_time', 'asc')
                ->get(),
            'Sábado' => Teacher::select('schedule.*', 'classroom.classroom_name', 'group.group_code', 'subject.subject_name')
                ->join('group', 'group.teacher_id', '=', 'teacher.id')
                ->join('subject', 'group.subject_id', '=', 'subject.id')
                ->join('schedule_classroom_group_detail', 'group.id', '=', 'schedule_classroom_group_detail.group_id')
                ->join('schedule', 'schedule_classroom_group_detail.schedule_id', '=', 'schedule.id')
                ->join('classroom', 'schedule_classroom_group_detail.classroom_id', '=', 'classroom.id')
                ->where('teacher.teacher_card', $card)
                ->where('schedule.week_day', 'Sábado')
                ->where('schedule_classroom_group_detail.cycle_id', $active_cycle)
                ->whereNull('schedule_classroom_group_detail.deleted_at')
                ->orderBy('schedule.start_time', 'asc')
                ->get(),
            'Domingo' => Teacher::select('schedule.*', 'classroom.classroom_name', 'group.group_code', 'subject.subject_name')
                ->join('group', 'group.teacher_id', '=', 'teacher.id')
                ->join('subject', 'group.subject_id', '=', 'subject.id')
                ->join('schedule_classroom_group_detail', 'group.id', '=', 'schedule_classroom_group_detail.group_id')
                ->join('schedule', 'schedule_classroom_group_detail.schedule_id', '=', 'schedule.id')
                ->join('classroom', 'schedule_classroom_group_detail.classroom_id', '=', 'classroom.id')
                ->where('teacher.teacher_card', $card)
                ->where('schedule.week_day', 'Domingo')
                ->where('schedule_classroom_group_detail.cycle_id', $active_cycle)
                ->whereNull('schedule_classroom_group_detail.deleted_at')
                ->orderBy('schedule.start_time', 'asc')
                ->get()
        ];

        $time = Schedule::select('schedule.start_time', 'schedule.end_time')
            ->distinct()
            ->get();

        return response()->json([
            'message' => 'Registro eliminado correctamente.',
            'schedule' => $schedule,
            'time' => $time,
        ]);
    }
}
