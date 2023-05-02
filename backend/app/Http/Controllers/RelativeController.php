<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Relative;
use Encrypt;

class RelativeController extends Controller
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
            $itemsPerPage =  Relative::count();
            $skip = 0;
        }

        $sortBy = (isset($request->sortBy[0]['key'])) ? $request->sortBy[0]['key'] : 'id';
        $sort = (isset($request->sortDesc[0]['order'])) ? "asc" : 'desc';

        $search = (isset($request->search)) ? "%$request->search%" : '%%';

        $relative = Relative::allDataSearched($search, $sortBy, $sort, $skip, $itemsPerPage);

        $total = Relative::counterPagination($search);

        return response()->json([
            "message" => "Registros obtenidos correctamente.",
            "data" => $relative,
            "total" => $total,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $relative = new Relative;
        $relative->relationship = $request->relationship;
        $relative->name = $request->name;
        $relative->last_name = $request->last_name;
        $relative->dui = $request->dui;
        $relative->phone_number = $request->phone_number;
        $relative->mail = $request->mail;

        $relative->save();

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

        $relative = Relative::where('id', $data['id'])->first();
        $relative->relationship = $request->relationship;
        $relative->name = $request->name;
        $relative->last_name = $request->last_name;
        $relative->dui = $request->dui;
        $relative->phone_number = $request->phone_number;
        $relative->mail = $request->mail;

        $relative->save();

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

        Relative::where('id', $id)->delete();

        return response()->json([
            "message" => "Registro eliminado correctamente.",
        ]);
    }
}
