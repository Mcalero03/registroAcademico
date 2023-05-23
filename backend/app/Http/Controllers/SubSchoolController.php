<?php

namespace App\Http\Controllers;

use App\Models\SubSchool;
use App\Models\School;
use Illuminate\Http\Request;
use Encrypt;

class SubSchoolController extends Controller
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
            $itemsPerPage =  SubSchool::count();
            $skip = 0;
        }

        $sortBy = (isset($request->sortBy[0]['key'])) ? $request->sortBy[0]['key'] : 'id';
        $sort = (isset($request->sortDesc[0]['order'])) ? "asc" : 'desc';

        $search = (isset($request->search)) ? "%$request->search%" : '%%';

        $sub_school = SubSchool::allDataSearched($search, $sortBy, $sort, $skip, $itemsPerPage);

        $total = SubSchool::counterPagination($search);

        return response()->json([
            "message" => "Registros obtenidos correctamente.",
            "data" => $sub_school,
            "total" => $total,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $sub_school = new SubSchool;
        $sub_school->sub_school_name = $request->sub_school_name;
        $sub_school->school_id = School::where('school_name', $request->school_name)->first()?->id;

        $sub_school->save();

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

        $sub_school = SubSchool::where('id', $data['id'])->first();
        $sub_school->sub_school_name = $request->sub_school_name;
        $sub_school->school_id = School::where('school_name', $request->school_name)->first()?->id;

        $sub_school->save();

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

        SubSchool::where('id', $id)->delete();

        return response()->json([
            "message" => "Registro eliminado correctamente",
        ]);
    }
}