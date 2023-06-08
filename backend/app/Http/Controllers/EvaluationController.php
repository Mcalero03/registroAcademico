<?php

namespace App\Http\Controllers;

use App\Models\Evaluation;
use App\Models\Subject;
use App\Models\Calification;
use App\Models\Teacher;
use App\Models\School;
use App\Models\Group;
use App\Models\InscriptionDetail;
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
            $item->califications = Calification::select(DB::raw("CONCAT(student.last_name, ', ', student.name) AS full_name"), 'calification.score', 'calification.id', 'inscription_detail.group_id', 'inscription_detail.id as inscription_detail_id')
                ->join('evaluation', 'calification.evaluation_id', '=', 'evaluation.id')
                ->leftJoin('inscription_detail', 'calification.inscription_detail_id', '=', 'inscription_detail.id')
                ->leftJoin('inscription', 'inscription_detail.inscription_id', '=', 'inscription.id')
                ->leftJoin('student', 'inscription.student_id', '=', 'student.id')
                ->where('calification.evaluation_id', $item->id)
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

        Evaluation::where('id', $data['id'])->update([
            "evaluation_name" => $data["evaluation_name"],
            "ponder" => $data["ponder"],
            "group_id" => Group::where('group_code', $data["group_code"])->first()->id,

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
        $subjects = Group::select('subject.subject_name')
            ->join('subject', 'group.subject_id', '=', 'subject.id')
            ->join('teacher', 'group.teacher_id', '=', 'teacher.id')
            ->where('teacher.name', $name)
            ->where('teacher.last_name', $last_name)
            ->get('subject.subject_name');

        return response()->json([
            "message" => "Registro encontrado correctamente",
            "subjects" => $subjects
        ]);
    }

    public function showGroups($subject)
    {
        $groups = Group::select('group.group_code')
            ->join('subject', 'group.subject_id', '=', 'subject.id')
            ->where('subject.subject_name', $subject)
            ->get('group.group_code');

        return response()->json([
            "message" => "Registro encontrado correctamente",
            "groups" => $groups
        ]);
    }

    public function showStudents($group)
    {
        $id = Group::where('group_code', $group)->first()?->id;

        $students = InscriptionDetail::select(DB::raw("CONCAT(student.last_name, ', ',student.name) as full_name"), 'inscription_detail.id as inscription_detail_id')
            ->selectRaw("0 as score")
            ->join('inscription', 'inscription_detail.id', '=', 'inscription.id')
            ->join('group', 'inscription_detail.group_id', '=', 'group.id')
            ->join('subject', 'group.subject_id', '=', 'subject.id')
            ->join('student', 'inscription.student_id', '=', 'student.id')
            ->where('group.id', $id)
            ->orderby('student.last_name', 'asc')
            ->get();

        return response()->json([
            "message" => "Registro encontrado correctamente",
            "students" => $students
        ]);
    }
}
