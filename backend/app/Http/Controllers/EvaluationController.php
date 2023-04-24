<?php

namespace App\Http\Controllers;

use App\Models\Evaluation;
use App\Models\Subject;
use Encrypt;

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
        $evaluation = new Evaluation;
        $evaluation->evaluation_name = $request->evaluation_name;
        $evaluation->ponder = $request->ponder;
        $evaluation->subject_id = Subject::where('subject_name', $request->subject_name)->first()->id;

        $evaluation->save();

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
        $evaluation = Evaluation::where('id', $data['id'])->first();
        $evaluation->evaluation_name = $request->evaluation_name;
        $evaluation->ponder = $request->ponder;
        $evaluation->subject_id = Subject::where('subject_name', $request->subject_name)->first()?->id;

        $evaluation->save();

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

        return response()->json([
            "message" => "Registro eliminado correctamente",
        ]);
    }
}
