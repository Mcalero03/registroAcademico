<?php

namespace App\Http\Controllers;

use App\Models\Municipality;
use App\Models\Department;
use Illuminate\Http\Request;
use Encrypt;

class MunicipalityController extends Controller
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
            $itemsPerPage =  Municipality::count();
            $skip = 0;
        }

        $sortBy = (isset($request->sortBy[0]['key'])) ? $request->sortBy[0]['key'] : 'id';
        $sort = (isset($request->sortDesc[0]['order'])) ? "asc" : 'desc';

        $search = (isset($request->search)) ? "%$request->search%" : '%%';

        $municipality = Municipality::allDataSearched($search, $sortBy, $sort, $skip, $itemsPerPage);

        $total = Municipality::counterPagination($search);

        return response()->json([
            "message" => "Registros obtenidos correctamente.",
            "data" => $municipality,
            "total" => $total,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $municipality = new Municipality;
        $municipality->municipality_name = $request->municipality;
        $municipality->mun_min = $request->mun_min;
        $municipality->mun_may = $request->mun_may;
        $municipality->dm_cod = $request->dm_cod;
        $municipality->cod_mun = $request->cod_mun;
        $municipality->department_id = Department::where('department_name', $request->department_name)->first()?->id;

        $municipality->save();

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

        $municipality = Municipality::where('id', $data['id'])->first();
        $municipality->municipality_name = $request->municipality;
        $municipality->mun_min = $request->mun_min;
        $municipality->mun_may = $request->mun_may;
        $municipality->dm_cod = $request->dm_cod;
        $municipality->cod_mun = $request->cod_mun;
        $municipality->department_id = Department::where('department_name', $request->department_name)->first()?->id;

        $municipality->save();

        return response()->json([
            "message" => "Registro modificado correctamente.",
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $id = Encrypt::decryptValue($request->id);

        Municipality::where('id', $id)->delete();

        return response()->json([
            "message" => "Registo eliminado correctamente.",
        ]);
    }
}
