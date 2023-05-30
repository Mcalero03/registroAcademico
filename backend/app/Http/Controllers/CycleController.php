<?php

namespace App\Http\Controllers;

use App\Models\Cycle;
use App\Models\CycleSubjectDetail;
use Encrypt;
use Illuminate\Http\Request;
use App\Models\Pensum;
use App\Models\Subject;

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

        foreach ($cycle as $item) {
            $item->subjects = CycleSubjectDetail::select('cycle_subject_detail.*', 'subject.subject_name')
                ->join('subject', 'cycle_subject_detail.subject_id', '=', 'subject.id')
                ->where('cycle_id', $item->id)
                ->get();
            $item->subjects = Encrypt::encryptObject($item->subjects, "id");
        }

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
        $data = $request->all();

        $activeStatus = Cycle::where('status', "Activo")
            ->exists();
        if (!$activeStatus) {
            $cycle = Cycle::create([
                'cycle_number' => $data['cycle_number'],
                'year' => $data['year'],
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'status' => $data['status'],
            ]);

            $cycle->save();

            $subjects = $data['subjects'];
            $subjectsCount = count($subjects);

            for ($i = 0; $i < $subjectsCount; $i++) {
                $item = $subjects[$i];

                CycleSubjectDetail::create([
                    'cycle_id' => $cycle->id,
                    'subject_id' => Subject::where('subject_name', $item)->first()?->id,
                ]);
            }

            return response()->json([
                "message" => "Registro creado correctamente",
            ]);
        } else {
            return response()->json([
                "error" => "Solo puede haber un ciclo activo",
            ]); #
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

        Cycle::where('id', $data['id'])->update([
            'cycle_number' => $data['cycle_number'],
            'year' => $data['year'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'status' => $data['status'],
        ]);

        CycleSubjectDetail::where('cycle_id', $data['id'])->delete();

        // $subjects = $data['subjects'];
        // $subjectsCount = count($subjects);

        // for ($i = 0; $i < $subjectsCount; $i++) {
        //     $item = $subjects[$i];

        //     CycleSubjectDetail::create([
        //         'cycle_id' => $data['id'],
        //         'subject_id' => Subject::where('subject_name', $item)->first()?->id,
        //     ]);

        //     if ($data['subjects'] != $item) {
        foreach ($data['subjects'] as $item) {
            CycleSubjectDetail::create([
                'cycle_id' => $data['id'],
                'subject_id' => Subject::where('subject_name', $item['subject_name'])->first()?->id,
            ]);
        }
        return response()->json([
            "Message" => "Registro modificado correctamente.",
        ]);
        //     }
        // }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $id = Encrypt::decryptValue($request->id);

        Cycle::where('id', $id)->delete();
        CycleSubjectDetail::where('id', $id)->delete();

        return response()->json([
            "message" => "Registro eliminado correctamente",
        ]);
    }

    public function bySchool(Request $request)
    {
        $pensum = Pensum::select('pensum.program_name')
            ->join('sub_school', 'pensum.sub_school_id', '=', 'sub_school.id')
            ->join('school', 'sub_school.school_id', '=', 'school.id')
            ->where('school.school_name', $request->school)
            ->get();

        return response()->json([
            "message" => "Registro encontrado correctamente",
            "pensum" => $pensum
        ]);
    }

    public function byPensum(Request $request)
    {
        $subject = Subject::select('subject.subject_name')
            ->join('pensum_subject_detail', 'subject.id', '=', 'pensum_subject_detail.subject_id')
            ->join('pensum', 'pensum_subject_detail.pensum_id', '=', 'pensum.id')
            ->where('pensum.program_name', $request->pensum)
            ->get();

        return response()->json([
            "message" => "Registro encontrado correctamente",
            "subject" => $subject
        ]);
    }
}
