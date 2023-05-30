<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Classroom;
use App\Models\School;
use Encrypt;

class ClassroomController extends Controller
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
            $itemsPerPage =  Classroom::count();
            $skip = 0;
        }

        $sortBy = (isset($request->sortBy[0]['key'])) ? $request->sortBy[0]['key'] : 'id';
        $sort = (isset($request->sortDesc[0]['order'])) ? "asc" : 'desc';

        $search = (isset($request->search)) ? "%$request->search%" : '%%';

        $classroom = Classroom::allDataSearched($search, $sortBy, $sort, $skip, $itemsPerPage);

        $classroom = Encrypt::encryptObject($classroom, "id");

        $total = Classroom::counterPagination($search);

        return response()->json([
            "message" => "Registros obtenidos correctamente.",
            "data" => $classroom,
            "total" => $total,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $classroomExists = Classroom::where('classroom_name', $request->classroom_name)
            ->where('classroom_code', $request->classroom_code)
            ->where('school_id', School::where('school_name', $request->school_name)->first()?->id)
            ->exists();

        if (!$classroomExists) {
            $classroom = new Classroom;
            $classroom->classroom_code = $request->classroom_code;
            $classroom->classroom_name = $request->classroom_name;
            $classroom->capacity = $request->capacity;
            $classroom->status = $request->status;
            $classroom->school_id = School::where('school_name', $request->school_name)->first()?->id;

            $classroom->save();

            return response()->json([
                "message" => "Registro creado correctamente",
            ]);
        } else {
            return response()->json([
                "error" => "Ya se ha registrado esta aula ",
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

        $classroom = Classroom::where('id', $data['id'])->first();
        $classroom->classroom_code = $request->classroom_code;
        $classroom->classroom_name = $request->classroom_name;
        $classroom->capacity = $request->capacity;
        $classroom->status = $request->status;
        $classroom->school_id = School::where('school_name', $request->school_name)->first()?->id;

        $classroom->save();

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

        Classroom::where('id', $id)->delete();

        return response()->json([
            "message" => "Registro eliminado correctamente",
        ]);
    }
}
