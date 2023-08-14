<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Group;
use App\Models\ScheduleClassroomGroupDetail;
use App\Models\Subject;
use App\Models\Schedule;
use App\Models\School;
use App\Models\Cycle;
use App\Models\Classroom;
use Encrypt;

class GroupController extends Controller
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
            $itemsPerPage =  Group::count();
            $skip = 0;
        }

        $sortBy = (isset($request->sortBy[0]['key'])) ? $request->sortBy[0]['key'] : 'id';
        $sort = (isset($request->sortDesc[0]['order'])) ? "asc" : 'desc';

        $search = (isset($request->search)) ? "%$request->search%" : '%%';

        $group = Group::allDataSearched($search, $sortBy, $sort, $skip, $itemsPerPage)->unique();
        $groups = Group::Groups();

        foreach ($group as $item) {
            $item->selectedSchedule = ScheduleClassroomGroupDetail::select('schedule_classroom_group_detail.id', 'schedule.week_day', 'schedule.start_time', 'schedule.end_time', 'classroom.classroom_name', 'schedule_classroom_group_detail.cycle_id')
                ->join('group', 'schedule_classroom_group_detail.group_id', '=', 'group.id')
                ->join('schedule', 'schedule_classroom_group_detail.schedule_id', 'schedule.id')
                ->join('classroom', 'schedule_classroom_group_detail.classroom_id', 'classroom.id')
                ->where('group.id', $item->id)
                ->orderByRaw("FIELD(schedule.week_day, 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo')")

                ->get();

            $item->cycle_id = ScheduleClassroomGroupDetail::select('schedule_classroom_group_detail.cycle_id')
                ->join('group', 'schedule_classroom_group_detail.group_id', '=', 'group.id')
                ->where('group.id', $item->id)
                ->first()?->cycle_id;

            $item->active_cycle = Cycle::where('status', 'Activo')->first()?->id;

            $item->selectedSchedule = Encrypt::encryptObject($item->selectedSchedule, "id");
        }

        $group = Encrypt::encryptObject($group, "id");

        $groups = Encrypt::encryptObject($groups, "id");

        $total = Group::counterPagination($search);

        return response()->json([
            "message" => "Registros obtenidos correctamente.",
            "data" => $group,
            "groups" => $groups,
            "total" => $total,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $info = explode(', ', $data['teacher_full_name']);
        $id = Group::teacherId($info[0], $info[1])->pluck('id');
        $teacher_id = Group::clean($id);

        $subject_id = Subject::where('subject_name', $data['subject_name'])->first()?->id;

        $group = Group::create([
            'group_code' => $data['group_code'],
            'students_quantity' => $data['students_quantity'],
            'teacher_id' => $teacher_id,
            'subject_id' => $subject_id,
        ]);

        $group->save();
        return response()->json([
            "message" => "Registro creado correctamente.",
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
        $info = explode(', ', $data['teacher_full_name']);
        $id = Group::teacherId($info[0], $info[1])->pluck('id');
        $teacher_id = Group::clean($id);

        $change = intval($data['id']);

        Group::where('id', $change)->update([
            'group_code' => $data['group_code'],
            'students_quantity' => $data['students_quantity'],
            'teacher_id' => $teacher_id,
            'subject_id' => Subject::where('subject_name', $data['subject_name'])->first()?->id,
        ]);

        $selectedSchedules = ScheduleClassroomGroupDetail::select('schedule_classroom_group_detail.id', 'schedule.week_day', 'schedule.start_time', 'schedule.end_time', 'classroom.classroom_name')
            ->join('group', 'schedule_classroom_group_detail.group_id', '=', 'group.id')
            ->join('schedule', 'schedule_classroom_group_detail.schedule_id', 'schedule.id')
            ->join('classroom', 'schedule_classroom_group_detail.classroom_id', 'classroom.id')
            ->where('group.id', $change)
            ->get();

        // Obtener los IDs existentes en $selectedSchedules
        $existingIds = $selectedSchedules->pluck('id')->toArray();


        foreach ($data['selectedSchedule'] as $value) {

            // Obtener los IDs presentes en $data['selectedSchedule']
            $selectedIds = array_column($data['selectedSchedule'], 'id');

            // Desencriptar los IDs presentes en $selectedIds
            $decryptedIds = [];
            foreach ($selectedIds as $encryptedId) {
                $decryptedId = Encrypt::decryptValue($encryptedId);
                $decryptedIds[] = $decryptedId;
            }

            // Calcular los IDs que hacen falta para eliminar
            $idsToRemove = array_diff($existingIds, $decryptedIds);

            // Eliminar los registros correspondientes a los IDs que hacen falta
            ScheduleClassroomGroupDetail::whereIn('id', $idsToRemove)->delete();

            if (!isset($value['id'])) {
                ScheduleClassroomGroupDetail::create([
                    'schedule_id' => Schedule::where('week_day', $value['week_day'])
                        ->where('start_time', $value['start_time'])
                        ->where('end_time', $value['end_time'])
                        ->first()?->id,
                    'classroom_id' => Classroom::where('classroom_name', $value['classroom_name'])->first()?->id,
                    'group_id' => $change,
                    'cycle_id' => Cycle::select('id')
                        ->where('status', 'Activo')
                        ->first()?->id,
                ]);
            } else {
                ScheduleClassroomGroupDetail::where('id', $value['id'])->update([
                    'schedule_id' => Schedule::where('week_day', $value['week_day'])
                        ->where('start_time', $value['start_time'])
                        ->where('end_time', $value['end_time'])
                        ->first()?->id,
                    'classroom_id' => Classroom::where('classroom_name', $value['classroom_name'])->first()?->id,
                    'group_id' => $change,
                    'cycle_id' => Cycle::select('id')
                        ->where('status', 'Activo')
                        ->first()?->id,
                ]);
            }
        }
        return response()->json([
            "message" => "Registro modificado correctamente.",
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $id = Encrypt::decryptValue($request->id);

        Group::where('id', $id)->delete();
        ScheduleClassroomGroupDetail::where('group_id', $id)->delete();

        return response()->json([
            "message" => "Registro eliminado correctamente.",
        ]);
    }

    public function bySubject($subject)
    {
        $subjectcode = Subject::select('subject_code')
            ->where('subject_name', 'like', $subject)
            ->get();

        return response()->json([
            "message" => "Registros obtenidos correctamente.",
            "subject" => $subjectcode,
        ]);
    }

    public function classroomCapacity($school, $student_quantity)
    {
        $school_id = School::where('school_name', $school)->first()?->id;

        $classroom = classroom::select('classroom.classroom_name')
            ->where('classroom.school_id', $school_id)
            ->where('classroom.capacity', ">=", $student_quantity)
            ->where('classroom.status', "Habilitado")
            ->whereNull('classroom.deleted_at')
            ->get();

        return response()->json([
            "message" => "Registros obtenidos correctamente.",
            "classroom" => $classroom,
        ]);
    }

    public function byTeacher($teacher, $classroom)
    {
        $info = explode(', ', $teacher);
        $id = Group::teacherId($info[0], $info[1])->pluck('id');
        $teacher_id = Group::clean($id);

        $classroom_id = Classroom::where('classroom_name', $classroom)->first()?->id;
        $active_cycle = Cycle::where('status', 'Activo')->first()?->id;

        //Busca el registro anterior de horarios por salon del profesor
        $schedule_classroom = Group::select('schedule.id')
            ->join('schedule_classroom_group_detail', 'group.id', '=', 'schedule_classroom_group_detail.group_id')
            ->join('schedule', 'schedule_classroom_group_detail.schedule_id', 'schedule.id')
            ->where('group.teacher_id', $teacher_id)
            ->where('schedule_classroom_group_detail.classroom_id', $classroom_id)
            ->where('schedule_classroom_group_detail.cycle_id', $active_cycle)
            ->whereNull('schedule_classroom_group_detail.deleted_at')
            ->get();

        //Busca el registro anterior de horarios para otros salones del profesor
        $schedule = Group::select('schedule.id')
            ->join('schedule_classroom_group_detail', 'group.id', '=', 'schedule_classroom_group_detail.group_id')
            ->join('schedule', 'schedule_classroom_group_detail.schedule_id', 'schedule.id')
            ->where('group.teacher_id', $teacher_id)
            ->where('schedule_classroom_group_detail.cycle_id', $active_cycle)
            ->whereNull('schedule_classroom_group_detail.deleted_at')
            ->get();

        //Busca registros de los horarios ya ocupados por otros profesores en ese salon
        $schedule_other_teachers = Group::select('schedule.id')
            ->join('schedule_classroom_group_detail', 'group.id', '=', 'schedule_classroom_group_detail.group_id')
            ->join('schedule', 'schedule_classroom_group_detail.schedule_id', 'schedule.id')
            ->where('schedule_classroom_group_detail.cycle_id', $active_cycle)
            ->where('schedule_classroom_group_detail.classroom_id', $classroom_id)
            ->whereNull('schedule_classroom_group_detail.deleted_at')
            ->get();

        //Devuelve el listado de horarios que no estan en el registro del profesor
        $scheduleAvailable = Schedule::select('schedule.*')
            ->whereNotIn('schedule.id', $schedule)
            ->whereNotIn('schedule.id', $schedule_other_teachers)
            ->whereNotIn('schedule.id', $schedule_classroom)
            ->whereNull('schedule.deleted_at')
            ->orderByRaw("FIELD(schedule.week_day, 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo')")

            ->get();

        //Devuelve la lista de dias, correspondiente a los horarios disponibles
        $week_day = Schedule::select('schedule.week_day')
            ->whereNotIn('schedule.id', $schedule)
            ->whereNotIn('schedule.id', $schedule_other_teachers)
            ->whereNotIn('schedule.id', $schedule_classroom)
            ->whereNull('schedule.deleted_at')
            ->orderByRaw("FIELD(schedule.week_day, 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo')")
            ->distinct()
            ->get();

        return response()->json([
            "message" => "Registros obtenidos correctamente.",
            "schedule" => $scheduleAvailable,
            "classroom" => $classroom,
            "week_day" => $week_day,
        ]);
    }


    public function byDay($day, $teacher, $classroom)
    {
        $info = explode(', ', $teacher);
        $id = Group::teacherId($info[0], $info[1])->pluck('id');
        $teacher_id = Group::clean($id);
        $active_cycle = Cycle::where('status', 'Activo')->first()?->id;
        $classroom_id = Classroom::where('classroom_name', $classroom)->first()?->id;

        $schedule = Group::select('schedule.id')
            ->join('schedule_classroom_group_detail', 'group.id', '=', 'schedule_classroom_group_detail.group_id')
            ->join('schedule', 'schedule_classroom_group_detail.schedule_id', 'schedule.id')
            ->where('group.teacher_id', $teacher_id)
            ->where('schedule_classroom_group_detail.cycle_id', $active_cycle)
            ->whereNull('schedule_classroom_group_detail.deleted_at')

            ->get();

        $schedule_other_teachers = Group::select('schedule.id')
            ->join('schedule_classroom_group_detail', 'group.id', '=', 'schedule_classroom_group_detail.group_id')
            ->join('schedule', 'schedule_classroom_group_detail.schedule_id', 'schedule.id')
            ->where('schedule_classroom_group_detail.cycle_id', $active_cycle)
            ->where('schedule_classroom_group_detail.classroom_id', $classroom_id)
            ->whereNull('schedule_classroom_group_detail.deleted_at')
            ->get();

        $start_time = Schedule::select('schedule.start_time')
            ->where('schedule.week_day', $day)
            ->whereNotIn('schedule.id', $schedule)
            ->whereNotIn('schedule.id', $schedule_other_teachers)
            ->get();

        return response()->json([
            "message" => "Registros obtenidos correctamente.",
            "start_time" => $start_time,
        ]);
    }
    public function byStartTime($start_time, $week_day)
    {
        $end_time = Schedule::select('schedule.end_time')
            ->where('schedule.week_day', $week_day)
            ->where('schedule.start_time', $start_time)
            ->get();

        return response()->json([
            "message" => "Registros obtenidos correctamente.",
            "end_time" => $end_time,
        ]);
    }
}
