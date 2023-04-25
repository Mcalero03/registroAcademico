<?php

namespace App\Http\Controllers;

use App\Models\Municipality;
use App\Models\Relative;
use Illuminate\Http\Request;
use App\Models\Student;
use Encrypt;

class StudentController extends Controller
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
            $itemsPerPage =  Student::count();
            $skip = 0;
        }

        $sortBy = (isset($request->sortBy[0]['key'])) ? $request->sortBy[0]['key'] : 'id';
        $sort = (isset($request->sortDesc[0]['order'])) ? "asc" : 'desc';

        $search = (isset($request->search)) ? "%$request->search%" : '%%';

        $student = Student::allDataSearched($search, $sortBy, $sort, $skip, $itemsPerPage);

        $total = Student::counterPagination($search);

        return response()->json([
            "message" => "Registros obtenidos correctamente.",
            "data" => $student,
            "total" => $total,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $student = new Student;
        $student->name = $request->name;
        $student->last_name = $request->last_name;
        $student->age = $request->age;
        $student->card = $request->card;
        $student->nie = $request->nie;
        $student->phone_number = $request->phone_number;
        $student->mail = $request->mail;
        $student->admission_date = $request->admission_date;
        $student->municipalities_id = Municipality::where('municipality_name', $request->municipality_name)->first()?->id;
        $student->relative_id = Relative::where('name', $request->relative_name)->first()?->id;

        $student->save();

        return response()->json([
            'message' => 'Registro creado correctamente.',
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

        $student = Student::where('id', $data['id'])->first();
        $student->name = $request->name;
        $student->last_name = $request->last_name;
        $student->age = $request->age;
        $student->card = $request->card;
        $student->nie = $request->nie;
        $student->phone_number = $request->phone_number;
        $student->mail = $request->mail;
        $student->admission_date = $request->admission_date;
        $student->municipalities_id = Municipality::where('municipality_name', $request->municipality_name)->first()?->id;
        $student->relative_id = Relative::where('name', $request->relative_name)->first()?->id;

        $student->save();

        return response()->json([
            'message' => 'Registro modificado correctamente.',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $id = Encrypt::decryptValue($request->id);

        Student::where('id', $id)->delete();

        return response()->json([
            'message' => 'Registro eliminado correctamente.',
        ]);
    }
}
