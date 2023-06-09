<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Schedule;
use App\Models\Group;
use Encrypt;

class ScheduleController extends Controller
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
            $itemsPerPage =  Schedule::count();
            $skip = 0;
        }

        $sortBy = (isset($request->sortBy[0]['key'])) ? $request->sortBy[0]['key'] : 'id';
        $sort = (isset($request->sortDesc[0]['order'])) ? "asc" : 'desc';

        $search = (isset($request->search)) ? "%$request->search%" : '%%';

        $schedule = Schedule::allDataSearched($search, $sortBy, $sort, $skip, $itemsPerPage);
        $total = Schedule::counterPagination($search);

        return response()->json([
            "message" => "Registros obtenidos correctamente.",
            "data" => $schedule,
            "total" => $total,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $dataExists = Schedule::where('week_day', $request->week_day)
            ->where('start_time', $request->start_time)
            ->where('end_time', $request->end_time)
            ->exists();

        if (!$dataExists) {
            $schedule = new Schedule;
            $schedule->week_day = $request->week_day;
            $schedule->start_time = $request->start_time;
            $schedule->end_time = $request->end_time;

            $schedule->save();

            return response()->json([
                "message" => "Registro creado correctamente",
            ]);
        } else {
            return response()->json([
                "error" => "Ya existe un horario con esos datos",
            ]);
        }
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

        $schedule = Schedule::where('id', $data['id'])->first();
        $schedule->week_day = $request->week_day;
        $schedule->start_time = $request->start_time;
        $schedule->end_time = $request->end_time;

        $schedule->save();

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

        Schedule::where('id', $id)->delete();

        return response()->json([
            "message" => "Registro eliminado correctamente",
        ]);
    }
}
