<?php

namespace App\Http\Controllers;

use App\Models\PensumSubjectDetail;
use App\Models\Pensum;
use App\Models\Prerequisite;
use App\Models\Subject;
use App\Models\SubSchool;
use App\Models\School;
use Encrypt;

use Illuminate\Http\Request;

class PensumSubjectDetailController extends Controller
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
            $itemsPerPage =  PensumSubjectDetail::count();
            $skip = 0;
        }

        $sortBy = (isset($request->sortBy[0]['key'])) ? $request->sortBy[0]['key'] : 'id';
        $sort = (isset($request->sortDesc[0]['order'])) ? "asc" : 'desc';

        $search = (isset($request->search)) ? "%$request->search%" : '%%';

        // $pensum_subject_detail = PensumSubjectDetail::allDataSearched($search, $sortBy, $sort, $skip, $itemsPerPage)->unique();

        $pensums = PensumSubjectDetail::pensum();

        // foreach ($pensum_subject_detail as $item) {
        //     $item->prerequisites = Prerequisite::select('prerequisite.*', 'subject.subject_name as prerequisite')
        //         ->join('subject', 'prerequisite.subject_id', '=', 'subject.id')
        //         ->where('pensum_subject_detail_id', $item->id)
        //         ->get();

        //     $item->prerequisites = Encrypt::encryptObject($item->prerequisites, 'id');
        // }

        // $pensum_subject_detail = Encrypt::encryptObject($pensum_subject_detail, 'id');
        // $pensums = Encrypt::encryptObject($pensums, 'id');


        $total = PensumSubjectDetail::counterPagination($search);

        return response()->json([
            "message" => "Registros obtenidos correctamente.",
            // "data" => $pensum_subject_detail,
            "total" => $total,
            'programs' => $pensums,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->all();
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

            foreach ($data['prerequisites'] as $value) {
                Prerequisite::create([
                    'subject_id' => Subject::where('subject_name', $value['prerequisite'])->first()?->id,
                    'pensum_subject_detail_id' => $pensum_subject_detail->id,
                ]);
            }
            return response()->json([
                "message" => "Registro creado correctamente",
            ]);
        } else {
            return response()->json([
                "error" => "Ya existe un registro para la información ingresada",
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $data = Encrypt::decryptArray($request->all(), 'id');

        PensumSubjectDetail::where('id', $data['id'])->update([
            'pensum_id' => Pensum::where('program_name', $data['program_name'])->first()?->id,
            'subject_id' => Subject::where('subject_name', $data['subject_name'])->first()?->id,
        ]);

        Prerequisite::where('pensum_subject_detail_id', $data['id'])->delete();

        foreach ($data['prerequisites'] as $value) {
            Prerequisite::create([
                'subject_id' => Subject::where('subject_name', $value['prerequisite'])->first()?->id,
                'pensum_subject_detail_id' => $data['id'],
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

        PensumSubjectDetail::where('id', $id)->delete();
        Prerequisite::where('id', $id)->delete();

        return response()->json([
            "message" => "Registro eliminado correctamente",
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function pensum(Request $request)
    {
        $dataExists = PensumSubjectDetail::select('pensum_subject_detail.id')
            ->join('pensum', 'pensum_subject_detail.pensum_id', '=', 'pensum.id')
            ->join('subject', 'pensum_subject_detail.subject_id', '=', 'subject.id')
            ->where('pensum.program_name', $request->pensum)
            ->where('subject.subject_name', $request->subject)
            ->exists();

        if ($dataExists == true) {
            $prerequisiteExists = PensumSubjectDetail::select('prerequisite.subject_id')
                ->join('prerequisite', 'pensum_subject_detail.id', 'prerequisite.pensum_subject_detail_id')
                ->join('pensum', 'pensum_subject_detail.pensum_id', '=', 'pensum.id')
                ->join('subject', 'pensum_subject_detail.subject_id', '=', 'subject.id')
                ->whereNull('prerequisite.deleted_at')
                ->where('pensum.program_name', $request->pensum)
                ->where('subject.subject_name', $request->subject)
                ->get('prerequisite.subject_id');

            $subjectAsPrerequisite = PensumSubjectDetail::select('pensum_subject_detail.subject_id')
                ->join('prerequisite', 'pensum_subject_detail.id', 'prerequisite.pensum_subject_detail_id')
                ->join('pensum', 'pensum_subject_detail.pensum_id', '=', 'pensum.id')
                ->join('subject', 'pensum_subject_detail.subject_id', '=', 'subject.id')
                ->whereNull('prerequisite.deleted_at')
                ->where('pensum.program_name', $request->pensum)
                ->where('prerequisite.subject_id', Subject::where('subject_name', $request->subject)->first()?->id)
                ->get('pensum_subject_detail.subject_id');

            $subject = PensumSubjectDetail::select('subject.subject_name')
                ->join('pensum', 'pensum_subject_detail.pensum_id', '=', 'pensum.id')
                ->join('subject', 'pensum_subject_detail.subject_id', '=', 'subject.id')
                ->where('pensum.program_name', $request->pensum)
                ->where('subject.subject_name', 'not like', $request->subject)
                ->whereNotIn('pensum_subject_detail.subject_id', $subjectAsPrerequisite)
                ->whereNotIn('pensum_subject_detail.subject_id', $prerequisiteExists)
                ->get();

            return response()->json([
                "message" => "Registros obtenidos correctamente.",
                "subject" => $subject,
            ]);
        } else {
            $subject = PensumSubjectDetail::select('pensum_subject_detail.*', 'subject.subject_name', 'pensum.program_name')
                ->join('pensum', 'pensum_subject_detail.pensum_id', '=', 'pensum.id')
                ->join('subject', 'pensum_subject_detail.subject_id', '=', 'subject.id')
                ->where('pensum.program_name', 'like', $request->pensum)
                ->where('subject.subject_name', 'not like', $request->subject)
                ->get();

            return response()->json([
                "message" => "Registros obtenidos correctamente.",
                "subject" => $subject,
            ]);
        }
    }

    public function pensumSubject($pensum)
    {
        $program_id = Pensum::where('pensum.program_name', $pensum)->first()?->id;
        $subject = Subject::select('subject.status', 'subject.subject_name', 'subject.units_value', 'subject.id as subject_id', 'subject.subject_code', 'pensum.program_name', 'school.school_name',)
            ->join('pensum_subject_detail', 'subject.id', '=', 'pensum_subject_detail.subject_id')
            ->join('pensum', 'pensum_subject_detail.pensum_id', '=', 'pensum.id')
            ->leftjoin('sub_school', 'pensum.sub_school_id', '=', 'sub_school.id')
            ->leftjoin('school', 'sub_school.school_id', '=', 'school.id')
            ->where('pensum.id', $program_id)
            ->get();

        for ($i = 1; $i <= count($subject); $i++) {
            foreach ($subject as $item) {
                $item->index = $i++;
            }
        }

        foreach ($subject as $item) {
            $item->prerequisites = Prerequisite::select('prerequisite.*', 'subject.subject_name as prerequisite')
                ->join('subject', 'prerequisite.subject_id', '=', 'subject.id')
                ->where('pensum_subject_detail_id', $item->subject_id)
                ->whereNull('subject.deleted_at')
                ->get();

            $item->count = count($item->prerequisites);
            $item->prerequisites = Encrypt::encryptObject($item->prerequisites, 'id');

            // foreach ($item->prerequisites as $i) {
            //     $i->index = 1;
            // }
            // array_push($item->prerequisites,  1);
        }



        return response()->json([
            "message" => "Registros obtenidos correctamente.",
            "subject" => $subject,
        ]);
    }

    public function showSubSchool($school)
    {
        $school_id = School::where('school.school_name', $school)->first()?->id;

        $sub_school = SubSchool::select('sub_school.sub_school_name', 'school.school_name',)
            ->join('school', 'sub_school.school_id', '=', 'school.id')
            ->where('sub_school.school_id', $school_id)
            ->get();

        return response()->json([
            "message" => "Registros obtenidos correctamente.",
            "sub_school" => $sub_school,
        ]);
    }

    public function showPensums($sub_schools)
    {
        $sub_school_id = SubSchool::where('sub_school.sub_school_name', $sub_schools)->first()?->id;

        $pensum = Pensum::select('pensum.id', 'pensum.program_name', 'sub_school.sub_school_name',)
            ->join('sub_school', 'pensum.sub_school_id', '=', 'sub_school.id')
            ->where('pensum.sub_school_id', $sub_school_id)
            ->get();

        return response()->json([
            "message" => "Registros obtenidos correctamente.",
            "pensum" => $pensum,
        ]);
    }
}