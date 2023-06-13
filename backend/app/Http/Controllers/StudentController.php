<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Relative;
use App\Models\Kinship;
use App\Models\StudentPensumDetail;
use App\Models\Pensum;
use App\Models\School;
use Encrypt;
use Illuminate\Support\Facades\Crypt;

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

        foreach ($student as $item) {
            $item->pensums = StudentPensumDetail::select('pensum.program_name')
                ->join('pensum', 'student_pensum_detail.pensum_id', '=', 'pensum.id')
                ->where('student_id', $item->id)
                ->get();
            $item->pensums = Encrypt::encryptObject($item->pensums, "id");
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


        $dataExists = Student::where('name', $data['name'])
            ->where('last_name', $data['last_name'])
            ->exists();

        if (!$dataExists) {
            $student = Student::create([
                'name' => $data['name'],
                'last_name' => $data['last_name'],
                'age' => $data['age'],
                'student_card' => $data['student_card'],
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

            foreach ($data['pensums'] as $value) {
                StudentPensumDetail::create([
                    'pensum_id' => Pensum::where('program_name', $value['program_name'])->first()?->id,
                    'student_id' => $student->id,
                    'status' => "En curso"
                ]);
            }

            return response()->json([
                'message' => 'Registro creado correctamente.',
            ]);
        } else {
            return response()->json([
                'error' => 'El estudiante ya está registrado',
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

        $info = explode(', ', $data['municipality_name']);
        $id = Student::municipalityId($info[0], $info[1])->pluck('id');
        $municipality_id = Student::clean($id);;

        Student::where('id', $data['id'])->update([
            'name' => $data['name'],
            'last_name' => $data['last_name'],
            'age' => $data['age'],
            'student_card' => $data['student_card'],
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

        StudentPensumDetail::where('student_id', $data['id'])->delete();

        foreach ($data['pensums'] as $value) {
            StudentPensumDetail::create([
                'pensum_id' => Pensum::where('program_name', $value['program_name'])->first()?->id,
                'student_id' => $data['id'],
                'status' => "En curso"
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
        StudentPensumDetail::where('student_id', $id)->delete();

        return response()->json([
            'message' => 'Registro eliminado correctamente.',
        ]);
    }

    public function career(Request $request)
    {
        $school_id = School::where('school_name', $request->school)->first()->id;

        if ($request->id == 0) {
            $career = Pensum::select('pensum.program_name')
                ->join('sub_school', 'pensum.sub_school_id', '=', 'sub_school.id')
                ->join('school', 'sub_school.school_id', '=', 'school.id')
                ->where('sub_school.school_id', $school_id)
                ->get('pensum.program_name');

            return response()->json([
                "message" => "Registros obtenidos correctamente.",
                "career" => $career,
            ]);
        } else {
            $data = Encrypt::decryptValue($request->id);

            $dataExists = StudentPensumDetail::select('student_pensum_detail.id')
                ->join('student', 'student_pensum_detail.student_id', '=', 'student.id')
                ->where('student_id', $data)
                ->exists();

            if ($dataExists == true) {
                $careerExists = StudentPensumDetail::select('pensum.program_name')
                    ->join('pensum', 'student_pensum_detail.pensum_id', '=', 'pensum.id')
                    ->whereNull('student_pensum_detail.deleted_at')
                    ->where('student_id', $data)
                    ->get();

                $careerList = Pensum::select('pensum.program_name')
                    ->join('sub_school', 'pensum.sub_school_id', '=', 'sub_school.id')
                    ->join('school', 'sub_school.school_id', '=', 'school.id')
                    ->where('sub_school.school_id', $school_id)
                    ->whereNotIn('pensum.program_name', $careerExists)
                    ->get('pensum.program_name');

                return response()->json([
                    "message" => "Registros obtenidos correctamente.",
                    "career" => $careerList,
                ]);
            } else {
                $career = Pensum::select('pensum.program_name')
                    ->join('sub_school', 'pensum.sub_school_id', '=', 'sub_school.id')
                    ->join('school', 'sub_school.school_id', '=', 'school.id')
                    ->where('sub_school.school_id', $school_id)
                    ->get('pensum.program_name');

                return response()->json([
                    "message" => "Registros obtenidos correctamente.",
                    "career" => $career,
                ]);
            }
        }
    }
}
