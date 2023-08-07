<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inscription;
use App\Models\Cycle;
use App\Models\Group;
use App\Models\InscriptionDetail;
use App\Models\Student;
use App\Models\Pensum;
use App\Models\Subject;
use Illuminate\Support\Facades\DB;
use Encrypt;

class InscriptionController extends Controller
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
            $itemsPerPage =  Inscription::count();
            $skip = 0;
        }

        $sortBy = (isset($request->sortBy[0]['key'])) ? $request->sortBy[0]['key'] : 'id';
        $sort = (isset($request->sortDesc[0]['order'])) ? "asc" : 'desc';

        $search = (isset($request->search)) ? "%$request->search%" : '%%';

        $searchStudent = (isset($request->searchStudent)) ? "$request->searchStudent" : '%%';

        $student = Inscription::searchStudent($searchStudent)->unique();

        $inscription = Inscription::allDataSearched($search, $sortBy, $sort, $skip, $itemsPerPage)->unique();

        foreach ($inscription as $item) {
            $item->inscriptions = InscriptionDetail::select('inscription_detail.*', 'group.group_code', 'subject.subject_name')
                ->join('group', 'group.id', '=',  'inscription_detail.group_id')
                ->join('subject', 'group.subject_id', '=',  'subject.id')
                ->where('inscription_id', $item->id)
                ->get();

            $item->inscriptions = Encrypt::encryptObject($item->inscriptions, "id");
        }

        $inscription = Encrypt::encryptObject($inscription, "id");

        $total = Inscription::counterPagination($search);

        return response()->json([
            "message" => "Registros obtenidos correctamente.",
            "data" => $inscription,
            "student" => $student,
            "total" => $total,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $student = $data['full_name'];
        $studentClean = Inscription::clean($student);

        $info = explode(', ', $studentClean);
        $id = Inscription::studentId($info[0], $info[1])->pluck('id');
        $student_id = Inscription::clean($id);

        $info_cycle = explode('-', $data['cycle']);
        $id_cycle = Inscription::cycleId($info_cycle[0], $info_cycle[1])->pluck('id');
        $cycle_id = Inscription::clean($id_cycle);



        $inscription = Inscription::create([
            'inscription_date' => $data['inscription_date'],
            'status' => 'Inscrito',
            'cycle_id' => $cycle_id,
            'student_id' => $student_id,
            'pensum_id' => Pensum::where('program_name', $data['program_name'])->first()?->id,
        ]);

        $inscription->save();

        foreach ($data['inscriptions'] as $value) {
            InscriptionDetail::create([
                'status' => 'Inscrito',
                'inscription_id' => $inscription->id,
                'group_id' => Group::where('group_code', $value['group_code'])->first()?->id,
            ]);
        }

        return response()->json([
            'message' => 'Registro creado correctamente.',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $data = Encrypt::decryptArray($request->all(), 'id');

        $info = explode(', ', $data['full_name']);
        $id = Inscription::studentId($info[0], $info[1])->pluck('id');
        $student_id = Inscription::clean($id);

        Inscription::where('id', $data['id'])->update([
            'inscription_date' => $data['inscription_date'],
            'student_id' => $student_id,
            'pensum_id' => Pensum::where('program_name', $data['program_name'])->first()?->id,
        ]);

        foreach ($data['inscriptions'] as $value) {
            InscriptionDetail::where('id', Encrypt::decryptValue($value['id']))->update([
                'status' => $value['status'],
                'inscription_id' => $data['id'],
                'group_id' => Group::where('group_code', $value['group_code'])->first()?->id,
            ]);
        }

        return response()->json([
            'message' => 'Registro modificado correctamente.',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $id = Encrypt::decryptValue($request->id);

        Inscription::where('id', $id)->delete();
        InscriptionDetail::where('inscription_id', $id)->delete();

        return response()->json([
            'message' => 'Registro eliminado correctamente.',
        ]);
    }

    public function showSchedules($inscription)
    {

        $id = Encrypt::decryptValue($inscription);
        $schedules = InscriptionDetail::select('schedule_classroom_group_detail.id', 'schedule.*', 'classroom.classroom_name')
            ->join('group', 'group.id', '=',  'inscription_detail.group_id')
            ->join('schedule_classroom_group_detail', 'group.id', '=', 'schedule_classroom_group_detail.group_id')
            ->join('schedule', 'schedule_classroom_group_detail.schedule_id', '=', 'schedule.id')
            ->join('classroom', 'schedule_classroom_group_detail.classroom_id', '=', 'classroom.id')
            ->where('inscription_detail.id', $id)
            ->whereNull('schedule_classroom_group_detail.deleted_at')
            ->get();

        return response()->json([
            'message' => 'Registro obtenido correctamente.',
            'schedules' => $schedules,
        ]);
    }

    public function schedules($card)
    {
        $active_cycle = Cycle::where('cycle.status', 'Activo')->first()?->id;

        $days = Student::select('schedule.week_day')
            ->join('inscription', 'inscription.student_id', '=', 'student.id')
            ->join('inscription_detail', 'inscription.id', '=', 'inscription_detail.inscription_id')
            ->join('group', 'group.id', '=', 'inscription_detail.group_id')
            ->join('schedule_classroom_group_detail', 'group.id', '=', 'schedule_classroom_group_detail.group_id')
            ->join('schedule', 'schedule_classroom_group_detail.schedule_id', '=', 'schedule.id')
            ->where('student.student_card', $card)
            ->where('schedule_classroom_group_detail.cycle_id', $active_cycle)
            ->where('inscription_detail.status', 'not like', 'Retirado')
            ->whereNull('schedule_classroom_group_detail.deleted_at')
            ->whereNull('inscription_detail.deleted_at')
            ->orderByRaw("FIELD(schedule.week_day, 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo')")

            ->distinct()
            ->get();

        $schedule = [];
        foreach ($days as $day) {
            $schedule[$day['week_day']] = Student::select('schedule.*', 'classroom.classroom_name', 'group.group_code', 'subject.subject_name')
                ->join('inscription', 'inscription.student_id', '=', 'student.id')
                ->join('inscription_detail', 'inscription.id', '=', 'inscription_detail.inscription_id')
                ->join('group', 'group.id', '=', 'inscription_detail.group_id')
                ->join('subject', 'group.subject_id', '=', 'subject.id')
                ->join('schedule_classroom_group_detail', 'group.id', '=', 'schedule_classroom_group_detail.group_id')
                ->join('schedule', 'schedule_classroom_group_detail.schedule_id', '=', 'schedule.id')
                ->join('classroom', 'schedule_classroom_group_detail.classroom_id', '=', 'classroom.id')
                ->where('student.student_card', $card)
                ->where('schedule.week_day', $day['week_day'])
                ->where('schedule_classroom_group_detail.cycle_id', $active_cycle)
                ->where('inscription_detail.status', 'not like', 'Retirado')
                ->whereNull('inscription_detail.deleted_at')
                ->whereNull('schedule_classroom_group_detail.deleted_at')
                ->orderBy('schedule.start_time', 'asc')
                ->get();
        }

        return response()->json([
            'message' => 'Registro obtenido correctamente.',
            'schedules' => $schedule,
        ]);
    }

    public function showCareers($student)
    {
        $info = explode(', ', $student);
        $id = Inscription::studentId($info[0], $info[1])->pluck('id');
        $student_id = Inscription::clean($id);

        $career = Student::select('pensum.program_name')
            ->join('student_pensum_detail', 'student.id', '=', 'student_pensum_detail.student_id')
            ->join('pensum', 'student_pensum_detail.pensum_id', '=', 'pensum.id')
            ->whereNull('student_pensum_detail.deleted_at')
            ->where('student.id', $student_id)
            ->get();

        return response()->json([
            'message' => 'Registro obtenido correctamente.',
            'careers' => $career,
        ]);
    }

    public function showInscriptions($card)
    {
        $programs = Inscription::select('pensum.program_name',)
            ->join('inscription_detail as i', 'inscription.id', '=', 'i.inscription_id')
            ->join('student', 'inscription.student_id', '=', 'student.id')
            ->join('student_pensum_detail', 'student.id', '=', 'student_pensum_detail.student_id')
            ->join('pensum', 'student_pensum_detail.pensum_id', '=', 'pensum.id')
            ->join('cycle', 'inscription.cycle_id', '=', 'cycle.id')
            ->where('student.student_card', $card)
            ->where('student_pensum_detail.status', 'not like', 'Retirado')
            ->where('cycle.status', 'Activo')
            ->whereNull('student_pensum_detail.deleted_at')
            ->distinct()
            ->get();

        foreach ($programs as $program) {
            $inscription[$program['program_name']] = Inscription::select(
                DB::raw("CONCAT(cycle.cycle_number, '-', cycle.year) AS cycle"),
                'inscription.status AS inscription_status',
                'inscription.id AS inscription_id',
                'pensum.program_name'
            )
                ->join('cycle', 'inscription.cycle_id', '=', 'cycle.id')
                ->join('student', 'inscription.student_id', '=', 'student.id')
                ->leftJoin('inscription_detail', 'inscription.id', '=', 'inscription_detail.inscription_id')
                ->leftJoin('group', 'group.id', '=', 'inscription_detail.group_id')
                ->leftJoin('subject', 'group.subject_id', '=', 'subject.id')
                ->leftJoin('pensum_subject_detail', 'subject.id', '=', 'pensum_subject_detail.subject_id')
                ->leftJoin('pensum', 'pensum_subject_detail.pensum_id', '=', 'pensum.id')
                ->where('student.student_card', $card)
                ->where('pensum.program_name', $program['program_name'])
                ->whereNull('inscription.deleted_at')
                ->where('cycle.status', 'Activo')
                ->distinct()
                ->get();
        }


        foreach ($inscription[$program['program_name']] as $item) {
            $item->subjects = Inscription::select(DB::raw("CONCAT(teacher.name, ', ', teacher.last_name) as full_name"), 'inscription_detail.status as inscription_detail_status', 'subject.subject_name', 'group.group_code', 'teacher.mail', 'inscription.id')
                ->join('cycle', 'inscription.cycle_id', '=', 'cycle.id')
                ->join('student', 'inscription.student_id', '=', 'student.id')
                ->leftjoin('inscription_detail', 'inscription.id', '=', 'inscription_detail.inscription_id')
                ->leftjoin('group', 'group.id', '=', 'inscription_detail.group_id')
                ->leftjoin('teacher', 'group.teacher_id', '=', 'teacher.id')
                ->leftjoin('subject', 'group.subject_id', '=', 'subject.id')
                ->leftjoin('pensum_subject_detail', 'subject.id', '=', 'pensum_subject_detail.subject_id')
                ->leftjoin('pensum', 'pensum_subject_detail.pensum_id', '=', 'pensum.id')
                ->where('inscription_detail.inscription_id', $item->inscription_id)
                ->where('student.student_card', $card)
                ->where('pensum.program_name', $item->program_name)
                ->whereNull('inscription_detail.deleted_at')
                ->where('cycle.status', 'Activo')
                ->get();

            $item->subjects = Encrypt::encryptObject($item->subjects, "id");
        }

        return response()->json([
            'message' => 'Registro obtenido correctamente.',
            'inscription' => $inscription,
        ]);
    }

    public function availableSubjects($student, $pensum, $cycle)
    {
        $info = explode(', ', $student);
        $id = Inscription::studentId($info[0], $info[1])->pluck('id');
        $student_id = Inscription::clean($id);

        $info_cycle = explode('-', $cycle);
        $id_cycle = Inscription::cycleId($info_cycle[0], $info_cycle[1])->pluck('id');
        $cycle_id = Inscription::clean($id_cycle);

        $pensum_id = Pensum::where('program_name', $pensum)->first()?->id;

        $otherInscriptions = InscriptionDetail::select('inscription_detail.*')
            ->join('inscription', 'inscription_detail.inscription_id', '=',  'inscription.id')
            ->join('student', 'inscription.student_id', '=', 'student.id')
            ->join('cycle', 'inscription.cycle_id', '=', 'cycle.id')
            ->where('cycle.status', 'Finalizado')
            ->where('inscription.student_id', $student_id)
            ->where('inscription.pensum_id', $pensum_id)
            ->exists();

        $newInscriptions = InscriptionDetail::select('inscription_detail.*')
            ->join('inscription', 'inscription_detail.inscription_id', '=',  'inscription.id')
            ->join('student', 'inscription.student_id', '=', 'student.id')
            ->join('cycle', 'inscription.cycle_id', '=', 'cycle.id')
            ->where('cycle.status', 'Activo')
            ->where('inscription.student_id', $student_id)
            ->where('inscription.pensum_id', $pensum_id)
            ->exists();

        if ($newInscriptions == false) {
            if ($otherInscriptions == true) {
                $allInscriptionsStatus = InscriptionDetail::select('subject.subject_name', 'inscription_detail.status')
                    ->join('inscription', 'inscription_detail.inscription_id', '=',  'inscription.id')
                    ->join('student', 'inscription.student_id', '=', 'student.id')
                    ->join('cycle', 'inscription.cycle_id', '=', 'cycle.id')
                    ->join('group', 'inscription_detail.group_id', '=', 'group.id')
                    ->join('subject', 'group.subject_id', '=', 'subject.id')
                    ->where('cycle.status', 'Finalizado')
                    ->where('inscription.student_id', $student_id)
                    ->where('inscription.pensum_id', $pensum_id)
                    ->get();

                //ID DE LA MATERIA QUE YA HA CURSADO EN ESA CARRERA Y QUE TIENE EL ESTADO APROBADO
                $oldInscriptionsApproved = InscriptionDetail::select('subject.id')
                    ->join('inscription', 'inscription_detail.inscription_id', '=',  'inscription.id')
                    ->join('student', 'inscription.student_id', '=', 'student.id')
                    ->join('cycle', 'inscription.cycle_id', '=', 'cycle.id')
                    ->join('group', 'inscription_detail.group_id', '=', 'group.id')
                    ->join('subject', 'group.subject_id', '=', 'subject.id')
                    ->where('cycle.status', 'Finalizado')
                    ->where('inscription.student_id', $student_id)
                    ->where('inscription.pensum_id', $pensum_id)
                    ->where('inscription_detail.status', 'Aprobado')

                    ->pluck('subject.id')
                    ->toArray();

                $prerequisiteApprovedSubject = Cycle::select('subject.id')
                    ->join('cycle_subject_detail', 'cycle.id', '=', 'cycle_subject_detail.cycle_id')
                    ->leftJoin('subject', 'cycle_subject_detail.subject_id', '=', 'subject.id')
                    ->leftJoin('pensum_subject_detail', 'subject.id', '=', 'pensum_subject_detail.subject_id')
                    ->leftJoin('pensum', 'pensum_subject_detail.pensum_id', '=', 'pensum.id')
                    ->leftJoin('prerequisite', 'pensum_subject_detail.id', '=', 'prerequisite.pensum_subject_detail_id')
                    ->where('subject.status', 1)
                    ->where('cycle.status', 'Activo')
                    ->where('pensum.id', $pensum_id)
                    ->whereIn('prerequisite.subject_id', $oldInscriptionsApproved)
                    ->whereNull('cycle.deleted_at')
                    ->whereNull('cycle_subject_detail.deleted_at')
                    ->pluck('subject.id')
                    ->toArray();

                $prerequisiteSubjectApprovedAsPrerequisite = Cycle::select('subject.id')
                    ->join('cycle_subject_detail', 'cycle.id', '=', 'cycle_subject_detail.cycle_id')
                    ->leftJoin('subject', 'cycle_subject_detail.subject_id', '=', 'subject.id')
                    ->leftJoin('pensum_subject_detail', 'subject.id', '=', 'pensum_subject_detail.subject_id')
                    ->leftJoin('pensum', 'pensum_subject_detail.pensum_id', '=', 'pensum.id')
                    ->leftJoin('prerequisite', 'pensum_subject_detail.id', '=', 'prerequisite.pensum_subject_detail_id')
                    ->where('subject.status', 1)
                    ->where('cycle.status', 'Activo')
                    ->where('pensum.id', $pensum_id)
                    ->whereIn('prerequisite.subject_id', $prerequisiteApprovedSubject)
                    ->whereNull('cycle.deleted_at')
                    ->whereNull('cycle_subject_detail.deleted_at')
                    ->pluck('subject.id')
                    ->toArray();

                //ID DE LA MATERIA QUE YA HA CURSADO EN ESA CARRERA Y QUE TIENE EL ESTADO REPROBADO
                $oldInscriptionsFailed = InscriptionDetail::select('subject.id')
                    ->join('inscription', 'inscription_detail.inscription_id', '=',  'inscription.id')
                    ->join('student', 'inscription.student_id', '=', 'student.id')
                    ->join('cycle', 'inscription.cycle_id', '=', 'cycle.id')
                    ->join('group', 'inscription_detail.group_id', '=', 'group.id')
                    ->join('subject', 'group.subject_id', '=', 'subject.id')
                    ->where('cycle.status', 'Finalizado')
                    ->where('inscription.student_id', $student_id)
                    ->where('inscription.pensum_id', $pensum_id)
                    ->where('inscription_detail.status', 'Reprobado')
                    ->pluck('subject.id')
                    ->toArray();

                //ID DE MATERIA DONDE LA MATERIA QUE YA CURSO(REPROBO O APROBÓ) SEA PRERREQUISITO, 
                $prerequisiteFailedSubject = Cycle::select('subject.id')
                    ->join('cycle_subject_detail', 'cycle.id', '=', 'cycle_subject_detail.cycle_id')
                    ->leftJoin('subject', 'cycle_subject_detail.subject_id', '=', 'subject.id')
                    ->leftJoin('pensum_subject_detail', 'subject.id', '=', 'pensum_subject_detail.subject_id')
                    ->leftJoin('pensum', 'pensum_subject_detail.pensum_id', '=', 'pensum.id')
                    ->leftJoin('prerequisite', 'pensum_subject_detail.id', '=', 'prerequisite.pensum_subject_detail_id')
                    ->where('subject.status', 1)
                    ->where('cycle.status', 'Activo')
                    ->where('pensum.id', $pensum_id)
                    ->whereIn('prerequisite.subject_id', $oldInscriptionsFailed)
                    ->whereNull('cycle.deleted_at')
                    ->whereNull('cycle_subject_detail.deleted_at')
                    ->pluck('subject.id')
                    ->toArray();

                //ID DE MATERIA QUE ES PRERREQUISITO DE LA MATERIA QUE YA CURSO Y LA REPROBO
                $prerequisiteSubjectFailedAsPrerequisite = Cycle::select('subject.id')
                    ->join('cycle_subject_detail', 'cycle.id', '=', 'cycle_subject_detail.cycle_id')
                    ->leftJoin('subject', 'cycle_subject_detail.subject_id', '=', 'subject.id')
                    ->leftJoin('pensum_subject_detail', 'subject.id', '=', 'pensum_subject_detail.subject_id')
                    ->leftJoin('pensum', 'pensum_subject_detail.pensum_id', '=', 'pensum.id')
                    ->leftJoin('prerequisite', 'pensum_subject_detail.id', '=', 'prerequisite.pensum_subject_detail_id')
                    ->where('subject.status', 1)
                    ->where('cycle.status', 'Activo')
                    ->where('pensum.id', $pensum_id)
                    ->whereIn('prerequisite.subject_id', $prerequisiteFailedSubject)
                    ->whereNull('cycle.deleted_at')
                    ->whereNull('cycle_subject_detail.deleted_at')
                    ->pluck('subject.id')
                    ->toArray();

                // MUESTRA LISTADO DE MATERIAS QUE PUEDE CURSAR EN EL ESTADO ACTIVO, DEJANDO FUERA LA QUE YA TIENE CURSADA Y APROBADA, LA QUE ES PRERREQUISITO DE LA REPROBADA Y EN CADENA 
                $subject_id = Cycle::select('subject.id')
                    ->join('cycle_subject_detail', 'cycle.id', '=', 'cycle_subject_detail.cycle_id')
                    ->leftJoin('subject', 'cycle_subject_detail.subject_id', '=', 'subject.id')
                    ->leftJoin('pensum_subject_detail', 'subject.id', '=', 'pensum_subject_detail.subject_id')
                    ->leftJoin('pensum', 'pensum_subject_detail.pensum_id', '=', 'pensum.id')
                    ->where('cycle.status', 'Activo')
                    ->where('pensum.id', $pensum_id)
                    ->whereNotIn('subject.id', $prerequisiteFailedSubject)
                    ->whereNotIn('subject.id', $oldInscriptionsApproved)
                    ->whereNotIn('subject.id', $prerequisiteSubjectFailedAsPrerequisite)
                    ->whereNotIn('subject.id', $prerequisiteSubjectApprovedAsPrerequisite)
                    ->whereNull('cycle.deleted_at')
                    ->whereNull('cycle_subject_detail.deleted_at')
                    ->get();

                $group = Group::select('group.group_code', 'subject.subject_name', 'schedule.start_time', 'schedule.end_time', 'schedule.week_day')
                    ->join('subject', 'group.subject_id', '=', 'subject.id')
                    ->join('schedule_classroom_group_detail', 'group.id', '=', 'schedule_classroom_group_detail.group_id')
                    ->join('schedule', 'schedule_classroom_group_detail.schedule_id', 'schedule.id')
                    ->whereIn('group.subject_id', $subject_id)
                    ->where('schedule_classroom_group_detail.cycle_id', $cycle_id)
                    ->whereNull('schedule_classroom_group_detail.deleted_at')
                    ->get();

                $selectGroup = Group::select('group.group_code')
                    ->leftjoin('schedule_classroom_group_detail', 'group.id', '=', 'schedule_classroom_group_detail.group_id')
                    ->where('schedule_classroom_group_detail.cycle_id', $cycle_id)
                    ->whereNull('schedule_classroom_group_detail.deleted_at')
                    ->whereIn('group.subject_id', $subject_id)
                    ->groupBy('schedule_classroom_group_detail.group_id')
                    ->get();

                return response()->json([
                    'message' => 'Registro obtenido correctamente.',
                    'inscriptions'  => $allInscriptionsStatus,
                    'groups' => $group,
                    'selectGroup' => $selectGroup

                ]);
            } else if ($otherInscriptions == false) {

                //MUESTRA EL LISTADO DE TODAS LAS MATERIAS QUE NO TIENEN PREREQUISITO EN EL CICLO ACTIVO 
                $subject_id = DB::table('cycle')
                    ->join('cycle_subject_detail', 'cycle.id', '=', 'cycle_subject_detail.cycle_id')
                    ->leftJoin('subject', 'cycle_subject_detail.subject_id', '=', 'subject.id')
                    ->leftJoin('pensum_subject_detail', 'subject.id', '=', 'pensum_subject_detail.subject_id')
                    ->leftJoin('pensum', 'pensum_subject_detail.pensum_id', '=', 'pensum.id')
                    ->select('subject.id')
                    ->where('subject.status', 0)
                    ->where('cycle.status', 'Activo')
                    ->where('pensum.id', $pensum_id)
                    ->whereNull('cycle.deleted_at')
                    ->whereNull('cycle_subject_detail.deleted_at')
                    ->pluck('subject.id')
                    ->toArray();

                $group = Group::select('group.group_code', 'subject.subject_name', 'schedule.start_time', 'schedule.end_time', 'schedule.week_day')
                    ->join('subject', 'group.subject_id', '=', 'subject.id')
                    ->join('schedule_classroom_group_detail', 'group.id', '=', 'schedule_classroom_group_detail.group_id')
                    ->join('schedule', 'schedule_classroom_group_detail.schedule_id', 'schedule.id')
                    ->whereIn('group.subject_id', $subject_id)
                    ->where('schedule_classroom_group_detail.cycle_id', $cycle_id)
                    ->whereNull('schedule_classroom_group_detail.deleted_at')
                    ->get();

                $selectGroup = Group::select('group.group_code')
                    ->leftjoin('schedule_classroom_group_detail', 'group.id', '=', 'schedule_classroom_group_detail.group_id')
                    ->where('schedule_classroom_group_detail.cycle_id', $cycle_id)
                    ->whereNull('schedule_classroom_group_detail.deleted_at')
                    ->whereIn('group.subject_id', $subject_id)
                    ->groupBy('schedule_classroom_group_detail.group_id')
                    ->get();

                return response()->json([
                    'message' => 'Registro obtenido correctamente.',
                    'groups' => $group,
                    'selectGroup' => $selectGroup
                ]);
            }
        } else {

            return response()->json([
                'message' => 'Registro obtenido correctamente.',
                'groups' => 'Registrado',
            ]);
        }
    }

    public function showGeneralInscriptions($card)
    {
        $programs = Inscription::select('pensum.program_name',)
            ->join('inscription_detail as i', 'inscription.id', '=', 'i.inscription_id')
            ->join('student', 'inscription.student_id', '=', 'student.id')
            ->join('student_pensum_detail', 'student.id', '=', 'student_pensum_detail.student_id')
            ->join('pensum', 'student_pensum_detail.pensum_id', '=', 'pensum.id')
            ->join('cycle', 'inscription.cycle_id', '=', 'cycle.id')
            ->where('student.student_card', $card)
            ->whereNull('student_pensum_detail.deleted_at')
            ->distinct()
            ->get();

        foreach ($programs as $program) {
            $inscription[$program['program_name']] = Subject::select(
                DB::raw(
                    "CONCAT(cycle.cycle_number, '-', cycle.year)as cycle",
                ),
                'subject.subject_code',
                'subject.subject_name',
                'subject.units_value',
                'inscription_detail.status',
                'inscription_detail.id as inscription_detail_id'
            )
                ->join('group', 'group.subject_id', '=', 'subject.id')
                ->join('inscription_detail', 'inscription_detail.group_id', '=', 'group.id')
                ->join('inscription', 'inscription.id', '=', 'inscription_detail.inscription_id')
                ->join('student', 'student.id', '=', 'inscription.student_id')
                ->join('cycle', 'cycle.id', '=', 'inscription.cycle_id')
                ->join('pensum_subject_detail', 'subject.id', '=', 'pensum_subject_detail.subject_id')
                ->join('pensum', 'pensum.id', '=', 'pensum_subject_detail.pensum_id')
                ->where('student.student_card', $card)
                ->where('pensum.program_name', $program->program_name)
                ->where('cycle.status', 'Finalizado')
                ->whereNull('inscription_detail.deleted_at')
                ->orderBy('cycle.id', 'asc')
                ->get();

            //Calculo de las unidades de merito por las materias aprobadas
            $merit_unit[$program['program_name']] = Inscription::select(DB::raw("ROUND(SUM(((calification.score*evaluation.ponder)/100)*subject.units_value),2) as merit_unit"))
                ->join('inscription_detail', 'inscription_detail.inscription_id', '=', 'inscription.id')
                ->join('group', 'group.id', '=', 'inscription_detail.group_id')
                ->join('subject', 'subject.id', '=', 'group.subject_id')
                ->join('student', 'student.id', '=', 'inscription.student_id')
                ->join('pensum_subject_detail', 'subject.id', '=', 'pensum_subject_detail.subject_id')
                ->join('pensum', 'pensum.id', '=', 'pensum_subject_detail.pensum_id')
                ->join('calification', 'calification.inscription_detail_id', '=', 'inscription_detail.id')
                ->join('evaluation', 'evaluation.id', '=', 'calification.evaluation_id')
                ->where('student.student_card', $card)
                ->where('pensum.program_name', $program->program_name)
                ->where('inscription_detail.status', 'Aprobado')
                ->whereNull('pensum_subject_detail.deleted_at')
                ->whereNull('evaluation.deleted_at')
                ->whereNull('calification.deleted_at')
                ->pluck('merit_unit');

            //Suma de las unidades valorativas de las materias aprobadas 
            $unit_value[$program['program_name']] = Inscription::select(DB::raw('SUM(subject.units_value) as units_value'))
                ->join('inscription_detail', 'inscription_detail.inscription_id', '=', 'inscription.id')
                ->join('student', 'student.id', '=', 'inscription.student_id')
                ->join('group', 'group.id', '=', 'inscription_detail.group_id')
                ->join('subject', 'subject.id', '=', 'group.subject_id')
                ->join('pensum_subject_detail', 'subject.id', '=', 'pensum_subject_detail.subject_id')
                ->join('pensum', 'pensum.id', '=', 'pensum_subject_detail.pensum_id')
                ->where('student.student_card', $card)
                ->where('pensum.program_name', $program->program_name)
                ->where('inscription_detail.status', 'Aprobado')
                ->pluck('units_value');

            $approvedInscription[$program['program_name']] = Inscription::select(DB::raw('COUNT(inscription_detail.id) as approvedInscription'))
                ->join('inscription_detail', 'inscription_detail.inscription_id', '=', 'inscription.id')
                ->join('student', 'student.id', '=', 'inscription.student_id')
                ->join('group', 'group.id', '=', 'inscription_detail.group_id')
                ->join('subject', 'subject.id', '=', 'group.subject_id')
                ->join('pensum_subject_detail', 'subject.id', '=', 'pensum_subject_detail.subject_id')
                ->join('pensum', 'pensum.id', '=', 'pensum_subject_detail.pensum_id')
                ->where('inscription_detail.status', 'Aprobado')
                ->where('student.student_card', $card)
                ->where('pensum.program_name', $program->program_name)
                ->whereNull('inscription_detail.deleted_at')
                ->pluck('approvedInscription');

            $failedInscription[$program['program_name']] = Inscription::select(DB::raw('COUNT(inscription_detail.id) as approvedInscription'))
                ->join('inscription_detail', 'inscription_detail.inscription_id', '=', 'inscription.id')
                ->join('student', 'student.id', '=', 'inscription.student_id')
                ->join('group', 'group.id', '=', 'inscription_detail.group_id')
                ->join('subject', 'subject.id', '=', 'group.subject_id')
                ->join('pensum_subject_detail', 'subject.id', '=', 'pensum_subject_detail.subject_id')
                ->join('pensum', 'pensum.id', '=', 'pensum_subject_detail.pensum_id')
                ->where('inscription_detail.status', 'Reprobado')
                ->where('student.student_card', $card)
                ->where('pensum.program_name', $program->program_name)
                ->whereNull('inscription_detail.deleted_at')
                ->pluck('approvedInscription');
        }

        //Calculo de la nota final del estudiante
        foreach ($inscription[$program['program_name']] as $item) {
            $item->averageGrade = Inscription::select(DB::raw("ROUND(SUM((calification.score*evaluation.ponder)/100),2) as total_average"))
                ->join('inscription_detail', 'inscription_detail.inscription_id', '=', 'inscription.id')
                ->join('calification', 'calification.inscription_detail_id', '=', 'inscription_detail.id')
                ->join('evaluation', 'evaluation.id', '=', 'calification.evaluation_id')
                ->where('inscription_detail.id', $item->inscription_detail_id)
                ->whereNull('evaluation.deleted_at')
                ->whereNull('calification.deleted_at')
                ->get();
        }

        return response()->json([
            'message' => 'Registro obtenido correctamente.',
            'inscription' => $inscription,
            'merit_unit' => $merit_unit,
            // 'units_value' => $unit_value,
            'approvedInscription' => $approvedInscription,
            'failedInscription' => $failedInscription,
        ]);
    }
}
