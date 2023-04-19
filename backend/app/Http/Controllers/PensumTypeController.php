<?php

namespace App\Http\Controllers;

use App\Models\PensumType;
use Illuminate\Http\Request;
use Encrypt;

class PensumTypeController extends Controller
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
            $itemsPerPage =  PensumType::count();
            $skip = 0;
        }

        $sortBy = (isset($request->sortBy[0]['key'])) ? $request->sortBy[0]['key'] : 'id';
        $sort = (isset($request->sortDesc[0]['order'])) ? "asc" : 'desc';

        $search = (isset($request->search)) ? "%$request->search%" : '%%';

        $pensumType = PensumType::allDataSearched($search, $sortBy, $sort, $skip, $itemsPerPage);
        $pensumType = Encrypt::encryptObject($pensumType, "id");

        $total = PensumType::counterPagination($search);

        // dd($pensumType);

        return response()->json([
            "message" => "Registros obtenidos correctamente.",
            "data" => $pensumType,
            "total" => $total,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $pensumType = new PensumType;
        $pensumType->pensum_type_name = $request->pensum_type_name;

        $pensumType->save();

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
    public function update(Request $request, string $id)
    {
        $data = Encrypt::decryptArray($request->all(), 'id');

        $pensumType = PensumType::where('id', $data['id'])->first();
        $pensumType->pensum_type_name = $request->pensum_type_name;

        $pensumType->save();

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

        PensumType::where('id', $id)->delete();

        return response()->json([
            "message" => "Registro eliminado correctamente.",
        ]);
    }
}
