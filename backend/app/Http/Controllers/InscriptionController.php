<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inscription;
use App\Models\Cycle;
use App\Models\Subject;
// use App\Models\Student;
use App\Models\Group;
// use App\Models\Grade;
// use App\Models\Evaluation;
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

        $inscription = Inscription::allDataSearched($search, $sortBy, $sort, $skip, $itemsPerPage);

        // foreach ($inscription as $item) {
        //     $item->grades = Grade::select('grade.*', 'evaluation.evaluation_name')
        //         ->join('evaluation', 'grade.evaluation_id', '=',  'evaluation.id')
        //         ->where('inscription_id', $item->id)
        //         ->get();
        //     $item->grades = Encrypt::encryptObject($item->grades, "id");
        // }

        $inscription = Encrypt::encryptObject($inscription, "id");

        $total = Inscription::counterPagination($search);

        return response()->json([
            "message" => "Registros obtenidos correctamente.",
            "data" => $inscription,
            "total" => $total,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $info = explode(', ', $data['full_name']);
        $id = Inscription::studentId($info[0], $info[1])->pluck('id');
        $student_id = Inscription::clean($id);

        $inscription = Inscription::create([
            'inscription_date' => $data['inscription_date'],
            'subject_average' => $data['subject_average'],
            'attendance_quantity' => $data['attendance_quantity'],
            'status' => $data['status'],
            'cycle_id' => Cycle::where('cycle_number', $data['cycle_number'])->first()?->id,
            'student_id' => $student_id,
            'group_id' => Group::where('group_name', $data['group_name'])->first()?->id,
            'subject_id' => Subject::where('subject_name', $data['subject_name'])->first()?->id,
        ]);

        $inscription->save();

        // foreach ($data['grades'] as $value) {
        //     Grade::create([
        //         'score' => $value['score'],
        //         'score_date' => $value['score_date'],
        //         'status' => $value['status'],
        //         'evaluation_id' => Evaluation::where('evaluation_name',  $value['evaluation_name'])->first()?->id,
        //         'inscription_id' => $inscription->id,
        //     ]);
        // }
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
            'subject_average' => $data['subject_average'],
            'attendance_quantity' => $data['attendance_quantity'],
            'status' => $data['status'],
            'cycle_id' => Cycle::where('cycle_number', $data['cycle_number'])->first()?->id,
            'student_id' => $student_id,
            'group_id' => Group::where('group_name', $data['group_name'])->first()?->id,
            'subject_id' => Subject::where('subject_name', $data['subject_name'])->first()?->id,
        ]);

        // Grade::where('inscription_id', $data['id'])->delete();

        // foreach ($data['grades'] as $value) {
        //     Grade::create([
        //         'score' => $value['score'],
        //         'score_date ' => $value['score_date'],
        //         'status' => $value['status'],
        //         'evaluation_id' => Evaluation::where('evaluation_name',  $value['evaluation_name'])->first()?->id,
        //         'inscription_id' => $data['id'],
        //     ]);
        // }

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
        // Grade::where('inscription_id', $id)->delete();

        return response()->json([
            'message' => 'Registro eliminado correctamente.',
        ]);
    }
}
