<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inscription;
use App\Models\Cycle;
use App\Models\Group;
use App\Models\InscriptionDetail;
use App\Models\Student;
use App\Models\Pensum;
use DB;
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
            'cycle_id' => Cycle::where('cycle_number', $data['cycle'])->first()?->id,
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
            'message' => 'Registro eliminado correctamente.',
            'schedules' => $schedules,
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
            'message' => 'Registro eliminado correctamente.',
            'careers' => $career,
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
}
