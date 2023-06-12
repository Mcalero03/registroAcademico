<?php

namespace App\Http\Controllers;

use App\Models\Pensum;
use App\Models\PensumType;
use App\Models\SubSchool;
use Encrypt;
use Illuminate\Http\Request;

class PensumController extends Controller
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
            $itemsPerPage =  Pensum::count();
            $skip = 0;
        }

        $sortBy = (isset($request->sortBy[0]['key'])) ? $request->sortBy[0]['key'] : 'id';
        $sort = (isset($request->sortDesc[0]['order'])) ? "asc" : 'desc';

        $search = (isset($request->search)) ? "%$request->search%" : '%%';

        $pensum = Pensum::allDataSearched($search, $sortBy, $sort, $skip, $itemsPerPage);

        $pensum = Encrypt::encryptObject($pensum, 'id');

        $total = Pensum::counterPagination($search);

        return response()->json([
            "message" => "Registros obtenidos correctamente.",
            "data" => $pensum,
            "total" => $total,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $pensum = new Pensum;
        $pensum->program_name = $request->program_name;
        $pensum->uv_total = $request->uv_total;
        $pensum->required_subject = $request->required_subject;
        $pensum->optional_subject = $request->optional_subject;
        $pensum->cycle_quantity = $request->cycle_quantity;
        $pensum->study_plan_year = $request->study_plan_year;
        $pensum->sub_school_id = SubSchool::where('sub_school_name', $request->sub_school_name)->first()?->id;
        $pensum->pensum_type_id = PensumType::where('pensum_type_name', $request->pensum_type_name)->first()?->id;

        $pensum->save();

        return response()->json([
            "message" => "Registro creado correctamente",
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
        $pensum = Pensum::where('id', $data['id'])->first();
        $pensum->program_name = $request->program_name;
        $pensum->uv_total = $request->uv_total;
        $pensum->required_subject = $request->required_subject;
        $pensum->optional_subject = $request->optional_subject;
        $pensum->cycle_quantity = $request->cycle_quantity;
        $pensum->study_plan_year = $request->study_plan_year;
        $pensum->sub_school_id = SubSchool::where('sub_school_name', $request->sub_school_name)->first()?->id;
        $pensum->pensum_type_id = PensumType::where('pensum_type_name', $request->pensum_type_name)->first()?->id;

        $pensum->save();

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
        Pensum::where('id', $id)->delete();

        return response()->json([
            "message" => "Registo eliminado correctamente",
        ]);
    }

    public function showSubSchools($school)
    {
        $sub_schools = SubSchool::select('sub_school.sub_school_name')
            ->join('school', 'sub_school.school_id', '=', 'school.id')
            ->where('school_name', $school)
            ->get();

        return response()->json([
            "message" => "Registro encontrado correctamente",
            "sub_schools" => $sub_schools,
        ]);
    }
}
