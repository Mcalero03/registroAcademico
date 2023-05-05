<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Relative;
use App\Models\Kinship;
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

        foreach ($student as $item) {
            $item->relatives = Relative::select('relative.*', 'relative.id as id', 'kinship.kinship')
                ->join('kinship', 'relative.kinship_id', '=', 'kinship.id')
                ->where('student_id', $item->id)
                ->get();
            $item->relatives = Encrypt::encryptObject($item->relatives, "id");
        }

        $student = Encrypt::encryptObject($student, "id");

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
        $data = $request->all();
        $info = explode(', ', $data['municipality_name']);
        $id = Student::municipalityId($info[0], $info[1])->pluck('id');
        $municipality_id = Student::clean($id);

        $student = Student::create([
            'name' => $data['name'],
            'last_name' => $data['last_name'],
            'age' => $data['age'],
            'card' => $data['card'],
            'nie' => $data['nie'],
            'phone_number' => $data['phone_number'],
            'mail' => $data['mail'],
            'admission_date' => $data['admission_date'],
            'municipalities_id' => $municipality_id,
        ]);
        $student->save();

        foreach ($data['relatives'] as $value) {
            Relative::create([
                'name' => $value['name'],
                'last_name' => $value['last_name'],
                'dui' => $value['dui'],
                'phone_number' => $value['phone_number'],
                'mail' => $value['mail'],
                'student_id' => $student->id,
                'kinship_id' => Kinship::where('kinship', $value['kinship'])->first()?->id,
            ]);
        }

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

        $info = explode(', ', $data['municipality_name']);
        $id = Student::municipalityId($info[0], $info[1])->pluck('id');
        $municipality_id = Student::clean($id);;

        Student::where('id', $data['id'])->update([
            'name' => $data['name'],
            'last_name' => $data['last_name'],
            'age' => $data['age'],
            'card' => $data['card'],
            'nie' => $data['nie'],
            'phone_number' => $data['phone_number'],
            'mail' => $data['mail'],
            'admission_date' => $data['admission_date'],
            'municipalities_id' => $municipality_id,
        ]);

        Relative::where('student_id', $data['id'])->delete();

        foreach ($data['relatives'] as $value) {
            Relative::create([
                'name' => $value['name'],
                'last_name' => $value['last_name'],
                'dui' => $value['dui'],
                'phone_number' => $value['phone_number'],
                'mail' => $value['mail'],
                'student_id' => $data['id'],
                'kinship_id' => Kinship::where('kinship', $value['kinship'])->first()?->id,
            ]);
        }

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
        Relative::where('student_id', $id)->delete();

        return response()->json([
            'message' => 'Registro eliminado correctamente.',
        ]);
    }
}
