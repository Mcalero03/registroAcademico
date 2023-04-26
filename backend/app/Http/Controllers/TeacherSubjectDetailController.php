<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\TeacherSubjectDetail;
use Illuminate\Http\Request;
use App\Models\Subject;
use App\Models\Teacher;
use Encrypt;

class TeacherSubjectDetailController extends Controller
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
            $itemsPerPage =  TeacherSubjectDetail::count();
            $skip = 0;
        }

        $sortBy = (isset($request->sortBy[0]['key'])) ? $request->sortBy[0]['key'] : 'id';
        $sort = (isset($request->sortDesc[0]['order'])) ? "asc" : 'desc';

        $search = (isset($request->search)) ? "%$request->search%" : '%%';

        $teacherSubjectDetail = TeacherSubjectDetail::allDataSearched($search, $sortBy, $sort, $skip, $itemsPerPage);

        $total = TeacherSubjectDetail::counterPagination($search);

        return response()->json([
            "message" => "Registros obtenidos correctamente.",
            "data" => $teacherSubjectDetail,
            "total" => $total,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $teacherSubjectDetail = new TeacherSubjectDetail;
        $teacherSubjectDetail->subject_id = Subject::where('subject_name', $request->subject_name)->first()?->id;
        $teacherSubjectDetail->teacher_id = Teacher::where('name', $request->teacher_name)->first()?->id;
        $teacherSubjectDetail->group_id = Group::where('group_name', $request->group_name)->first()?->id;

        $teacherSubjectDetail->save();

        return response()->json([
            'message' => 'Registro creado correctamente',
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

        $teacherSubjectDetail = TeacherSubjectDetail::where('id', $data['id'])->first();
        $teacherSubjectDetail->subject_id = Subject::where('subject_name', $request->subject_name)->first()?->id;
        $teacherSubjectDetail->teacher_id = Teacher::where('name', $request->teacher_name)->first()?->id;
        $teacherSubjectDetail->group_id = Group::where('group_name', $request->group_name)->first()?->id;

        $teacherSubjectDetail->save();

        return response()->json([
            'message' => 'Registro modificado correctamente',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $id = Encrypt::decryptValue($request->id);

        TeacherSubjectDetail::where('id', $id)->delete();

        return response()->json([
            'message' => 'Registro eliminado correctamente'
        ]);
    }
}
