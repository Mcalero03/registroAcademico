<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\Request;
use Encrypt;

class SubjectController extends Controller
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
            $itemsPerPage =  Subject::count();
            $skip = 0;
        }

        $sortBy = (isset($request->sortBy[0]['key'])) ? $request->sortBy[0]['key'] : 'id';
        $sort = (isset($request->sortDesc[0]['order'])) ? "asc" : 'desc';

        $search = (isset($request->search)) ? "%$request->search%" : '%%';

        $subject = Subject::allDataSearched($search, $sortBy, $sort, $skip, $itemsPerPage);
        $subject = Encrypt::encryptObject($subject, "id");

        $total = Subject::counterPagination($search);

        return response()->json([
            "message" => "Registros obtenidos correctamente.",
            "data" => $subject,
            "total" => $total,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $subject = new Subject;
        $subject->subject_name = $request->subject_name;
        $subject->average_approval = $request->average_approval;
        $subject->units_value = $request->units_value;

        $subject->save();

        return response()->json([
            "message" => "Registro creado correctamente",
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

        $subject = Subject::where('id', $data['id'])->first();
        $subject->subject_name = $request->subject_name;
        $subject->average_approval = $request->average_approval;
        $subject->units_value = $request->units_value;

        $subject->save();

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

        Subject::where('id', $id)->delete();

        return response()->json([
            "message" => "Registro eliminado correctamente",
        ]);
    }
}
