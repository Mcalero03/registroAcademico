<?php

namespace App\Http\Controllers;

use App\Models\Cycle;
use Encrypt;
use Illuminate\Http\Request;

class CycleController extends Controller
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
            $itemsPerPage =  Cycle::count();
            $skip = 0;
        }

        $sortBy = (isset($request->sortBy[0]['key'])) ? $request->sortBy[0]['key'] : 'id';
        $sort = (isset($request->sortDesc[0]['order'])) ? "asc" : 'desc';

        $search = (isset($request->search)) ? "%$request->search%" : '%%';

        $cycle = Cycle::allDataSearched($search, $sortBy, $sort, $skip, $itemsPerPage);
        $cycle = Encrypt::encryptObject($cycle, "id");

        $cycles = Cycle::cycle();
        $total = Cycle::counterPagination($search);

        return response()->json([
            "message" => "Registros obtenidos correctamente.",
            "data" => $cycle,
            "cycles" => $cycles,
            "total" => $total,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $cycle = new Cycle;
        $cycle->cycle_number = $request->cycle_number;
        $cycle->year = $request->year;
        $cycle->start_date = $request->start_date;
        $cycle->end_date = $request->end_date;
        $cycle->status = $request->status;

        $cycle->save();

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

        $cycle = Cycle::where('id', $data['id'])->first();
        $cycle->cycle_number = $request->cycle_number;
        $cycle->year = $request->year;
        $cycle->start_date = $request->start_date;
        $cycle->end_date = $request->end_date;
        $cycle->status = $request->status;

        $cycle->save();

        return response()->json([
            "Message" => "Registro modificado correctamente.",
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $id = Encrypt::decryptValue($request->id);

        Cycle::where('id', $id)->delete();

        return response()->json([
            "message" => "Registro eliminado correctamente",
        ]);
    }
}
