<?php

namespace App\Http\Controllers;

use App\Models\PensumSubjectDetail;
use App\Models\Subject;
use App\Models\Prerequisite;
use App\Models\Pensum;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Encrypt;
use DB;
use App\Models\School;

class SubjectController extends Controller
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
            $itemsPerPage =  Subject::count();
            $skip = 0;
        }

        $sortBy = (isset($request->sortBy[0]['key'])) ? $request->sortBy[0]['key'] : 'id';
        $sort = (isset($request->sortDesc[0]['order'])) ? "asc" : 'desc';

        $search = (isset($request->search)) ? "%$request->search%" : '%%';

        $subject = Subject::allDataSearched($search, $sortBy, $sort, $skip, $itemsPerPage);
        $subjectbycycle = Subject::cycleSubject();

        foreach ($subject as $item) {
            $item->prerequisites = Prerequisite::select('prerequisite.*', 'subject.subject_name as prerequisite')
                ->join('pensum_subject_detail', 'prerequisite.pensum_subject_detail_id', 'pensum_subject_detail.id')
                ->join('subject', 'prerequisite.subject_id', '=', 'subject.id')
                ->where('pensum_subject_detail_id', $item->pensum_subject_detail_id)
                ->whereNull('subject.deleted_at')

                ->get();

            $item->prerequisites = Encrypt::encryptObject($item->prerequisites, 'id');
        }

        $subject = Encrypt::encryptObject($subject, "id");

        $total = Subject::counterPagination($search);

        return response()->json([
            "message" => "Registros obtenidos correctamente.",
            "data" => $subject,
            "subjectbycycle" => $subjectbycycle,
            "total" => $total,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->all();
        if ($data['prerequisite'] == "Con prerrequisito") {
            $data['prerequisite'] = "1";
        } else {
            $data['prerequisite'] = "0";
        }


        $dataExists = Subject::where('subject_name', $data['subject_name'])
            ->exists();

        if (!$dataExists) {
            $subject = Subject::create([
                'subject_code' => $data['subject_code'],
                'subject_name' => $data['subject_name'],
                'average_approval' => $data['average_approval'],
                'units_value' => $data['units_value'],
                'status' => $data['prerequisite'],
            ]);

            $subject->save();

            $pensum = Pensum::where('program_name', $data['program_name'])->first()?->id;
            $subject = Subject::where('subject_name', $data['subject_name'])->first()?->id;

            $dataExists = PensumSubjectDetail::where('pensum_id', $pensum)
                ->where('subject_id', $subject)
                ->exists();

            if (!$dataExists) {

                $pensum_subject_detail = PensumSubjectDetail::create([
                    'pensum_id' => $pensum,
                    'subject_id' => $subject,
                ]);

                $pensum_subject_detail->save();

                return response()->json([
                    "message" => "Registro creado correctamente",
                ]);
            } else {
                return response()->json([
                    "error" => "Ya existe un registro para la información ingresada",
                ]);
            }
            return response()->json([
                "message" => "Registro creado correctamente",
            ]);
        } else {
            return response()->json([
                "error" => "Ya existe una materia con el mismo nombre",
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

        if ($data['prerequisite'] == "Con prerrequisito") {
            $data['prerequisite'] = "1";
        } else {
            $data['prerequisite'] = "0";
        }

        Subject::where('id', $data['id'])->update([
            'subject_code' => $data['subject_code'],
            'subject_name' => $data['subject_name'],
            'average_approval' => $data['average_approval'],
            'units_value' => $data['units_value'],
            'status' => $data['prerequisite'],
        ]);

        $pensum = Pensum::where('program_name', $data['program_name'])->first()?->id;
        $subject = Subject::where('subject_name', $data['subject_name'])->first()?->id;

        $pensum_subject_detail = PensumSubjectDetail::where('pensum_id', $pensum)
            ->where('subject_id', $subject)
            ->first()?->id;

        Prerequisite::where('pensum_subject_detail_id', $pensum_subject_detail)->delete();

        foreach ($data['prerequisites'] as $value) {
            Prerequisite::create([
                'subject_id' => Subject::where('subject_name', $value['prerequisite'])->first()?->id,
                'pensum_subject_detail_id' => $pensum_subject_detail,
            ]);
        }


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

        Subject::where('id', $id)->delete();

        return response()->json([
            "message" => "Registro eliminado correctamente",
        ]);
    }

    public function subjectByCycle($school)
    {
        $school_id = School::where('school_name', $school)->first()?->id;

        $subject = Subject::select('subject.subject_name')
            ->join('pensum_subject_detail', 'subject.id', '=', 'pensum_subject_detail.subject_id')
            ->join('pensum', 'pensum_subject_detail.pensum_id', '=', 'pensum.id')
            ->join('sub_school', 'pensum.sub_school_id', '=', 'sub_school.id')
            ->join('school', 'sub_school.school_id', '=', 'school.id')
            ->join('cycle_subject_detail', 'subject.id', '=', 'cycle_subject_detail.subject_id')
            ->join('cycle', 'cycle_subject_detail.cycle_id', '=', 'cycle.id')
            ->where('cycle.status', "Activo")
            ->whereNull('cycle.deleted_at')
            ->where('cycle_subject_detail.deleted_at', null)
            ->where('school.school_name', $school)
            ->get('subject.subject_name');


        $teacher = Teacher::select(DB::raw("CONCAT(teacher.name, ', ', teacher.last_name) as full_name"))
            ->join('school', 'teacher.school_id', '=', 'school.id')
            ->where('school.id', 'like', $school_id)
            ->get();

        return response()->json([
            "message" => "Registro encontrado correctamente",
            "subject" => $subject,
            "teacher" => $teacher,
        ]);
    }

    public function bySchool($school)
    {
        $school_id = School::where('school_name', $school)->first()->id;

        $program_name = Pensum::select('pensum.program_name')
            ->join('sub_school', 'pensum.sub_school_id', '=', 'sub_school.id')
            ->join('school', 'sub_school.school_id', '=', 'school.id')
            ->where('sub_school.school_id', $school_id)
            ->get();

        return response()->json([
            "message" => "Registro encontrado correctamente",
            "program_name" => $program_name
        ]);
    }
}
