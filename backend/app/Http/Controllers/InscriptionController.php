<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inscription;
use App\Models\Cycle;
use App\Models\Subject;
use App\Models\Student;
use App\Models\Group;
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
        $inscription = new Inscription;
        $inscription->inscription_date = $request->inscription_date;
        $inscription->subject_average = $request->subject_average;
        $inscription->attendance_quantity = $request->attendance_quantity;
        $inscription->status = $request->status;
        $inscription->cycle_id = Cycle::where('cycle_number', $request->cycle_number)->first()?->id;
        $inscription->student_id = Student::where('name', $request->student_name)->first()?->id;
        $inscription->group_id = Group::where('group_name', $request->group_name)->first()?->id;
        $inscription->subject_id = Subject::where('subject_name', $request->subject_name)->first()?->id;

        $inscription->save();

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

        $inscription = Inscription::where('id', $data['id'])->first();
        $inscription->inscription_date = $request->inscription_date;
        $inscription->subject_average = $request->subject_average;
        $inscription->attendance_quantity = $request->attendance_quantity;
        $inscription->status = $request->status;
        $inscription->cycle_id = Cycle::where('cycle_number', $request->cycle_number)->first()?->id;
        $inscription->student_id = Student::where('name', $request->student_name)->first()?->id;
        $inscription->group_id = Group::where('group_name', $request->group_name)->first()?->id;
        $inscription->subject_id = Subject::where('subject_name', $request->subject_name)->first()?->id;

        $inscription->save();

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

        return response()->json([
            'message' => 'Registro eliminado correctamente.',
        ]);
    }
}
