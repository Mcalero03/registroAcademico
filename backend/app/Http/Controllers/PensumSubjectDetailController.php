<?php

namespace App\Http\Controllers;

use App\Models\PensumSubjectDetail;
use App\Models\Pensum;
use App\Models\Subject;
use Encrypt;
use Illuminate\Http\Request;

class PensumSubjectDetailController extends Controller
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
            $itemsPerPage =  PensumSubjectDetail::count();
            $skip = 0;
        }

        $sortBy = (isset($request->sortBy[0]['key'])) ? $request->sortBy[0]['key'] : 'id';
        $sort = (isset($request->sortDesc[0]['order'])) ? "asc" : 'desc';

        $search = (isset($request->search)) ? "%$request->search%" : '%%';

        $pensum_subject_detail = PensumSubjectDetail::allDataSearched($search, $sortBy, $sort, $skip, $itemsPerPage);

        $total = PensumSubjectDetail::counterPagination($search);

        return response()->json([
            "message" => "Registros obtenidos correctamente.",
            "data" => $pensum_subject_detail,
            "total" => $total,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $pensum_subject_detail = new PensumSubjectDetail;
        $pensum_subject_detail->pensum_id = Pensum::where('program_name', $request->program_name)->first()?->id;
        $pensum_subject_detail->subject_id = Subject::where('subject_name', $request->subject_name)->first()?->id;

        $pensum_subject_detail->save();

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

        $pensum_subject_detail = PensumSubjectDetail::where('id', $data['id'])->first();
        $pensum_subject_detail->pensum_id = Pensum::where('program_name', $request->program_name)->first()?->id;
        $pensum_subject_detail->subject_id = Subject::where('subject_name', $request->subject_name)->first()?->id;

        $pensum_subject_detail->save();

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

        PensumSubjectDetail::where('id', $id)->delete();

        return response()->json([
            "message" => "Registro eliminado correctamente",
        ]);
    }
}
