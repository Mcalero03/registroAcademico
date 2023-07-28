<?php

namespace App\Http\Controllers;

use App\Models\Evaluation;
use App\Models\Inscription;
use App\Models\Calification;
use App\Models\Cycle;
use App\Models\Teacher;
use App\Models\School;
use App\Models\Group;
use Encrypt;
use DB;

use Illuminate\Http\Request;

class EvaluationController extends Controller
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
            $itemsPerPage =  Evaluation::count();
            $skip = 0;
        }

        $sortBy = (isset($request->sortBy[0]['key'])) ? $request->sortBy[0]['key'] : 'id';
        $sort = (isset($request->sortDesc[0]['order'])) ? "asc" : 'desc';

        $search = (isset($request->search)) ? "%$request->search%" : '%%';

        $evaluation = Evaluation::allDataSearched($search, $sortBy, $sort, $skip, $itemsPerPage);

        foreach ($evaluation as $item) {
            $item->califications = Calification::select(DB::raw("CONCAT(student.last_name, ', ', student.name) AS full_name"), 'calification.score', 'calification.id', 'inscription_detail.group_id', 'inscription_detail.id as inscription_detail_id',)
                ->join('evaluation', 'calification.evaluation_id', '=', 'evaluation.id')
                ->leftJoin('inscription_detail', 'calification.inscription_detail_id', '=', 'inscription_detail.id')
                ->leftJoin('inscription', 'inscription_detail.inscription_id', '=', 'inscription.id')
                ->leftJoin('student', 'inscription.student_id', '=', 'student.id')
                ->where('calification.evaluation_id', $item->id)
                ->get();

            $item->available_ponder = Inscription::select(DB::raw('100-SUM(evaluation.ponder) as available_ponder'))
                ->join('inscription_detail as i', 'inscription.id', '=', 'i.inscription_id')
                ->join('calification', 'i.id', '=', 'calification.inscription_detail_id')
                ->join('evaluation', 'calification.evaluation_id', '=', 'evaluation.id')
                ->join('group', 'i.group_id', '=', 'group.id')
                ->join('subject', 'group.subject_id', '=', 'subject.id')
                ->join('student', 'inscription.student_id', '=', 'student.id')
                ->join('pensum_subject_detail', 'subject.id', '=', 'pensum_subject_detail.subject_id')
                ->join('pensum', 'pensum_subject_detail.pensum_id', '=', 'pensum.id')
                ->join('cycle', 'inscription.cycle_id', '=', 'cycle.id')
                ->where('subject.subject_name', $item->subject_name)
                ->where('evaluation.group_id', $item->group_id)
                ->where('calification.inscription_detail_id', $item->inscription_detail_id)
                ->whereNull('calification.deleted_at')
                ->where('i.status', 'not like', 'Retirado')
                ->where('cycle.status', 'Activo')
                ->groupBy('subject.subject_name')
                ->distinct()
                ->get();

            $item->califications = Encrypt::encryptObject($item->califications, 'id');
        }

        $evaluation = Encrypt::encryptObject($evaluation, "id");

        $total = Evaluation::counterPagination($search);

        return response()->json([
            "message" => "Registros obtenidos correctamente.",
            "data" => $evaluation,
            "total" => $total,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->all();

        $ponder = $data['available_ponder'] - $data["ponder"];

        if ($data['available_ponder'] == '0') {
            return response()->json([
                "error" => "Ya alcanzó el máximo porcentaje a utilizar",
            ]);
        } elseif ($data['ponder'] == '0') {
            return response()->json([
                "error" => "No puede asignar cero en el porcentaje",
            ]);
        } elseif ($ponder >= '0') {
            $evaluation = Evaluation::create([
                "evaluation_name" => $data["evaluation_name"],
                "ponder" => $data["ponder"],
                "group_id" => Group::where('group_code', $data["group_code"])->first()->id,
            ]);

            $evaluation->save();

            foreach ($data['califications'] as $value) {
                Calification::create([
                    'evaluation_id' => $evaluation->id,
                    'inscription_detail_id' => $value['inscription_detail_id'],
                    'score' => $value['score'],
                ]);
            }

            return response()->json([
                "message" => "Registro creado correctamente.",
            ]);
        } elseif ($ponder < '0') {
            return response()->json([
                "error" => "El porcentaje ingresado excede el máximo a utilizar",
            ]);
        }
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

        $available_ponder = $data['available_ponder'][0]['available_ponder'];
        $ponder = $available_ponder - $data["ponder"];

        $previous_evaluation = Evaluation::select('ponder')->where('id', $data['id'])->first()?->ponder;

        if ($previous_evaluation == $data['ponder']) {

            Evaluation::where('id', $data['id'])->update([
                "evaluation_name" => $data["evaluation_name"],
                "ponder" => $data["ponder"],
                "group_id" => Group::where('group_code', $data["group_code"])->first()?->id,

            ]);

            foreach ($data['califications'] as $value) {
                Calification::where('id', Encrypt::decryptValue($value['id']))->update([
                    'evaluation_id' => $data['id'],
                    'inscription_detail_id' => $value['inscription_detail_id'],
                    'score' => $value['score'],
                ]);
            }

            return response()->json([
                "message" => "Registro modificado correctamente",
            ]);
        } elseif ($previous_evaluation != $data['ponder']) {
            if ($available_ponder == '0') {
                return response()->json([
                    "error" => "Ya alcanzó el máximo porcentaje a utilizar",
                ]);
            } elseif ($data['ponder'] == '0') {
                return response()->json([
                    "error" => "No puede asignar cero en el porcentaje",
                ]);
            } elseif ($ponder < '0') {
                return response()->json([
                    "error" => "El porcentaje ingresado excede el máximo a utilizar",
                ]);
            } elseif ($ponder >= '0') {
                Evaluation::where('id', $data['id'])->update([
                    "evaluation_name" => $data["evaluation_name"],
                    "ponder" => $data["ponder"],
                    "group_id" => Group::where('group_code', $data["group_code"])->first()?->id,

                ]);

                foreach ($data['califications'] as $value) {
                    Calification::where('id', Encrypt::decryptValue($value['id']))->update([
                        'evaluation_id' => $data['id'],
                        'inscription_detail_id' => $value['inscription_detail_id'],
                        'score' => $value['score'],
                    ]);
                }

                return response()->json([
                    "message" => "Registro modificado correctamente",
                ]);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $id = Encrypt::decryptValue($request->id);

        Evaluation::where('id', $id)->delete();
        Calification::where('evaluation_id', $id)->delete();

        return response()->json([
            "message" => "Registro eliminado correctamente",
        ]);
    }

    public function showTeacher($school)
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

    public function showSubjects($name, $last_name)
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
            "subjects" => $subjects
        ]);
    }

    public function showGroups($subject)
    {
        $active_cycle = Cycle::where('cycle.status', 'Activo')->first()?->id;

        $groups = Group::select('group.group_code')
            ->join('schedule_classroom_group_detail', 'group.id', '=', 'schedule_classroom_group_detail.group_id')
            ->join('subject', 'group.subject_id', '=', 'subject.id')
            ->join('cycle_subject_detail', 'subject.id', '=', 'cycle_subject_detail.subject_id')
            ->join('cycle', 'cycle_subject_detail.cycle_id', '=', 'cycle.id')
            ->where('subject.subject_name', $subject)
            ->where('schedule_classroom_group_detail.cycle_id', $active_cycle)
            ->where('cycle.status', 'Activo')
            ->whereNull('cycle.deleted_at')
            ->whereNull('cycle_subject_detail.deleted_at')

            ->get('group.group_code')->unique();

        return response()->json([
            "message" => "Registro encontrado correctamente",
            "groups" => $groups
        ]);
    }

    public function showStudents($group)
    {
        $student = Inscription::select(DB::raw("CONCAT(student.last_name, ', ',student.name) as full_name"), 'i.id as inscription_detail_id')
            ->selectRaw("0 as score")
            ->leftjoin('inscription_detail as i', 'inscription.id', '=', 'i.inscription_id')
            ->join('group', 'i.group_id', '=', 'group.id')
            ->join('subject', 'group.subject_id', '=', 'subject.id')
            ->join('cycle', 'inscription.cycle_id', '=', 'cycle.id')
            ->join('student', 'inscription.student_id', '=', 'student.id')
            ->where('group.group_code', $group)
            ->where('i.status', 'not like', 'Retirado')
            ->where('cycle.status', 'Activo')
            ->orderby('student.last_name', 'asc')
            ->get();

        $subjects = Inscription::select('group.group_code', 'group.id as group_id', 'subject.subject_name', 'i.id',)
            ->join('inscription_detail as i', 'inscription.id', '=', 'i.inscription_id')
            ->join('group', 'i.group_id', '=', 'group.id')
            ->join('subject', 'group.subject_id', '=', 'subject.id')
            ->join('student', 'inscription.student_id', '=', 'student.id')
            ->join('pensum_subject_detail', 'subject.id', '=', 'pensum_subject_detail.subject_id')
            ->join('pensum', 'pensum_subject_detail.pensum_id', '=', 'pensum.id')
            ->join('cycle', 'inscription.cycle_id', '=', 'cycle.id')
            ->where('group.group_code', $group)
            ->where('i.status', 'not like', 'Retirado')
            ->where('cycle.status', 'Activo')
            ->distinct()
            ->get();

        foreach ($subjects as $item) {
            $item->califications = Inscription::select('evaluation.evaluation_name', 'evaluation.ponder', 'calification.score', 'subject.average_approval',)
                ->join('inscription_detail as i', 'inscription.id', '=', 'i.inscription_id')
                ->join('calification', 'i.id', '=', 'calification.inscription_detail_id')
                ->join('evaluation', 'calification.evaluation_id', '=', 'evaluation.id')
                ->join('group', 'i.group_id', '=', 'group.id')
                ->join('subject', 'group.subject_id', '=', 'subject.id')
                ->join('student', 'inscription.student_id', '=', 'student.id')
                ->join('pensum_subject_detail', 'subject.id', '=', 'pensum_subject_detail.subject_id')
                ->join('pensum', 'pensum_subject_detail.pensum_id', '=', 'pensum.id')
                ->join('cycle', 'inscription.cycle_id', '=', 'cycle.id')
                ->where('group.group_code', $group)
                ->where('calification.inscription_detail_id', $item->id)
                ->where('i.status', 'not like', 'Retirado')
                ->where('cycle.status', 'Activo')
                ->whereNull('calification.deleted_at')
                ->selectRaw('ROUND(AVG((calification.score * evaluation.ponder)/100), 2) as final_average')
                ->orderBy('evaluation.evaluation_name', 'asc')
                ->groupBy('evaluation.evaluation_name', 'evaluation.ponder', 'calification.score', 'subject.average_approval')
                ->get();

            $existing_califications =
                Inscription::select('evaluation.*')
                ->join('inscription_detail as i', 'inscription.id', '=', 'i.inscription_id')
                ->join('calification', 'i.id', '=', 'calification.inscription_detail_id')
                ->join('evaluation', 'calification.evaluation_id', '=', 'evaluation.id')
                ->join('group', 'i.group_id', '=', 'group.id')
                ->join('subject', 'group.subject_id', '=', 'subject.id')
                ->join('student', 'inscription.student_id', '=', 'student.id')
                ->join('pensum_subject_detail', 'subject.id', '=', 'pensum_subject_detail.subject_id')
                ->join('pensum', 'pensum_subject_detail.pensum_id', '=', 'pensum.id')
                ->join('cycle', 'inscription.cycle_id', '=', 'cycle.id')
                ->where('group.group_code', $group)
                ->where('calification.inscription_detail_id', $item->id)
                ->whereNull('calification.deleted_at')
                ->where('i.status', 'not like', 'Retirado')
                ->where('cycle.status', 'Activo')
                ->groupBy('subject.subject_name')
                ->exists();

            if ($existing_califications == true) {
                $item->available_ponder = Inscription::select(DB::raw('100-SUM(evaluation.ponder) as total_ponder'))
                    ->join('inscription_detail as i', 'inscription.id', '=', 'i.inscription_id')
                    ->join('calification', 'i.id', '=', 'calification.inscription_detail_id')
                    ->join('evaluation', 'calification.evaluation_id', '=', 'evaluation.id')
                    ->join('group', 'i.group_id', '=', 'group.id')
                    ->join('subject', 'group.subject_id', '=', 'subject.id')
                    ->join('student', 'inscription.student_id', '=', 'student.id')
                    ->join('pensum_subject_detail', 'subject.id', '=', 'pensum_subject_detail.subject_id')
                    ->join('pensum', 'pensum_subject_detail.pensum_id', '=', 'pensum.id')
                    ->join('cycle', 'inscription.cycle_id', '=', 'cycle.id')
                    ->where('subject.subject_name', $item->subject_name)
                    ->where('evaluation.group_id', $item->group_id)
                    ->where('calification.inscription_detail_id', $item->id)
                    ->whereNull('calification.deleted_at')
                    ->where('i.status', 'not like', 'Retirado')
                    ->where('cycle.status', 'Activo')
                    ->groupBy('subject.subject_name')
                    ->get();
            } else if ($existing_califications == false) {
                $item->available_ponder =
                    Inscription::select(DB::raw('100 as total_ponder'))
                    ->get();
            }
        }
        return response()->json([
            "message" => "Registro encontrado correctamente",
            "students" => $student,
            "ponder" => $subjects
        ]);
    }

    public function showPrograms($card)
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
            $subjects[$program['program_name']] = Inscription::select('group.group_code', 'subject.subject_name', 'i.id', 'i.status', 'pensum.program_name')
                ->join('inscription_detail as i', 'inscription.id', '=', 'i.inscription_id')
                ->join('group', 'i.group_id', '=', 'group.id')
                ->join('subject', 'group.subject_id', '=', 'subject.id')
                ->join('student', 'inscription.student_id', '=', 'student.id')
                ->join('pensum_subject_detail', 'subject.id', '=', 'pensum_subject_detail.subject_id')
                ->join('pensum', 'pensum_subject_detail.pensum_id', '=', 'pensum.id')
                ->join('cycle', 'inscription.cycle_id', '=', 'cycle.id')
                ->where('student.student_card', $card)
                ->where('pensum.program_name', $program['program_name'])
                ->where('cycle.status', 'Activo')
                ->distinct()
                ->get();
        }

        foreach ($subjects[$program['program_name']] as $item) {
            $item->califications = Inscription::select('evaluation.evaluation_name', 'evaluation.ponder', 'calification.score', 'subject.average_approval',)
                ->join('inscription_detail as i', 'inscription.id', '=', 'i.inscription_id')
                ->join('calification', 'i.id', '=', 'calification.inscription_detail_id')
                ->join('evaluation', 'calification.evaluation_id', '=', 'evaluation.id')
                ->join('group', 'i.group_id', '=', 'group.id')
                ->join('subject', 'group.subject_id', '=', 'subject.id')
                ->join('student', 'inscription.student_id', '=', 'student.id')
                ->join('pensum_subject_detail', 'subject.id', '=', 'pensum_subject_detail.subject_id')
                ->join('pensum', 'pensum_subject_detail.pensum_id', '=', 'pensum.id')
                ->join('cycle', 'inscription.cycle_id', '=', 'cycle.id')
                ->where('student.student_card', $card)
                ->where('pensum.program_name', $item->program_name)
                ->where('subject.subject_name', $item->subject_name)
                ->where('calification.inscription_detail_id', $item->id)
                ->where('cycle.status', 'Activo')
                ->whereNull('calification.deleted_at')
                ->selectRaw('ROUND((calification.score * evaluation.ponder)/100, 2) as final_average')
                ->orderBy('evaluation.evaluation_name', 'asc')
                ->get();

            $item->result = Inscription::select(DB::raw('ROUND(SUM((calification.score*evaluation.ponder)/100),2) as total_average, SUM(evaluation.ponder) as total_ponder'))
                ->join('inscription_detail as i', 'inscription.id', '=', 'i.inscription_id')
                ->join('calification', 'i.id', '=', 'calification.inscription_detail_id')
                ->join('evaluation', 'calification.evaluation_id', '=', 'evaluation.id')
                ->join('group', 'i.group_id', '=', 'group.id')
                ->join('subject', 'group.subject_id', '=', 'subject.id')
                ->join('student', 'inscription.student_id', '=', 'student.id')
                ->join('pensum_subject_detail', 'subject.id', '=', 'pensum_subject_detail.subject_id')
                ->join('pensum', 'pensum_subject_detail.pensum_id', '=', 'pensum.id')
                ->join('cycle', 'inscription.cycle_id', '=', 'cycle.id')
                ->where('student.student_card', $card)
                ->where('pensum.program_name', $item->program_name)
                ->where('subject.subject_name', $item->subject_name)
                ->where('calification.inscription_detail_id', $item->id)
                ->whereNull('calification.deleted_at')
                ->where('cycle.status', 'Activo')
                ->groupBy('subject.subject_name')
                ->get();
        }

        return response()->json([
            "message" => "Registro encontrado correctamente",
            "program" => $subjects,
            "programs" => $programs,
        ]);
    }
}
