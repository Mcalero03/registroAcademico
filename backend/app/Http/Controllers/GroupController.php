<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Group;
use App\Models\Schedule;
use Encrypt;

class GroupController extends Controller
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
            $itemsPerPage =  Group::count();
            $skip = 0;
        }

        $sortBy = (isset($request->sortBy[0]['key'])) ? $request->sortBy[0]['key'] : 'id';
        $sort = (isset($request->sortDesc[0]['order'])) ? "asc" : 'desc';

        $search = (isset($request->search)) ? "%$request->search%" : '%%';

        $group = Group::allDataSearched($search, $sortBy, $sort, $skip, $itemsPerPage)->unique();
        
        foreach ($group as $item) {
            $item->schedules = Schedule::select('schedule.*')
            ->where('group_id', $item->id)
            ->get();

            $item->schedules = Encrypt::encryptObject($item->schedules, "id");
        }
        
        $group = Encrypt::encryptObject($group, "id");

        $total = Group::counterPagination($search);

        return response()->json([
            "message" => "Registros obtenidos correctamente.",
            "data" => $group,
            "total" => $total,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->all();

        $group = Group::create([
            'group_name' =>$data['group_name'],
            'students_quantity' => $data['students_quantity'],
        ]);

        $group->save(); 

        foreach ($data['schedules'] as $item) {
            Schedule::create([
                'week_day' => $item['week_day'],
                'start_time'=> $item['start_time'],
                'end_time'=> $item['end_time'],
                'group_id'=> $group->id,
            ]);
        }

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

        Group::where('id', $data['id'])->update([
            'group_name' =>$data['group_name'],
            'students_quantity' => $data['students_quantity'],
        ]);

        Schedule::where('group_id', $data['id'])->delete();

        foreach ($data['schedules'] as $item) {
            Schedule::create([
                'week_day' => $item['week_day'],
                'start_time'=> $item['start_time'],
                'end_time'=> $item['end_time'],
                'group_id'=> $data['id'],
            ]);
        }

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

        Group::where('id', $id)->delete();
        Schedule::where('group_id', $id)->delete();

        return response()->json([
            "message" => "Registro eliminado correctamente.",
        ]);
    }
}
