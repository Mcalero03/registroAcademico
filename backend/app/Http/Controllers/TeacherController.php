<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use Illuminate\Http\Request;
use Encrypt;

class TeacherController extends Controller
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
            $itemsPerPage =  Teacher::count();
            $skip = 0;
        }

        $sortBy = (isset($request->sortBy[0]['key'])) ? $request->sortBy[0]['key'] : 'id';
        $sort = (isset($request->sortDesc[0]['order'])) ? "asc" : 'desc';

        $search = (isset($request->search)) ? "%$request->search%" : '%%';

        $teacher = Teacher::allDataSearched($search, $sortBy, $sort, $skip, $itemsPerPage);
        $teacher = Encrypt::encryptObject($teacher, "id");

        $total = Teacher::counterPagination($search);

        return response()->json([
            "message" => "Registros obtenidos correctamente.",
            "data" => $teacher,
            "total" => $total,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $teacher = new Teacher;
        $teacher->name = $request->name;
        $teacher->last_name = $request->last_name;
        $teacher->card = $request->card;
        $teacher->dui = $request->dui;
        $teacher->nit = $request->nit;
        $teacher->phone_number = $request->phone_number;
        $teacher->mail = $request->mail;

        $teacher->save();

        return response()->json([
            "message" => "Registro creado correctamenre",
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

        $teacher = Teacher::where('id', $data['id'])->first();
        $teacher->name = $request->name;
        $teacher->last_name = $request->last_name;
        $teacher->card = $request->card;
        $teacher->dui = $request->dui;
        $teacher->nit = $request->nit;
        $teacher->phone_number = $request->phone_number;
        $teacher->mail = $request->mail;

        $teacher->save();

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
        Teacher::where('id', $id)->delete();

        return response()->json([
            "message" => "Registro eliminado correctamente",
        ]);
    }
}
