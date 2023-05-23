<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Group;
use App\Models\Subject;
use Encrypt;

class GroupController extends Controller
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
            $itemsPerPage =  Group::count();
            $skip = 0;
        }

        $sortBy = (isset($request->sortBy[0]['key'])) ? $request->sortBy[0]['key'] : 'id';
        $sort = (isset($request->sortDesc[0]['order'])) ? "asc" : 'desc';

        $search = (isset($request->search)) ? "%$request->search%" : '%%';

        $group = Group::allDataSearched($search, $sortBy, $sort, $skip, $itemsPerPage)->unique();
        $groups = Group::Groups();

        $group = Encrypt::encryptObject($group, "id");
        $groups = Encrypt::encryptObject($groups, "id");

        $total = Group::counterPagination($search);

        return response()->json([
            "message" => "Registros obtenidos correctamente.",
            "data" => $group,
            "groups" => $groups,
            "total" => $total,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $info = explode(', ', $data['teacher_full_name']);
        $id = Group::teacherId($info[0], $info[1])->pluck('id');
        $teacher_id = Group::clean($id);

        $subject_id = Subject::where('subject_name', $data['subject_name'])->first()?->id;

        // $dataExists = Group::where('teacher_id', $teacher_id)
        //     ->where('subject_id', $subject_id)
        //     ->exists();

        // if (!$dataExists) {
            $group = Group::create([
                'group_code' => $data['group_code'],
                'students_quantity' => $data['students_quantity'],
                'teacher_id' => $teacher_id,
                'subject_id' => $subject_id,
            ]);

            $group->save();

            return response()->json([
                "message" => "Registro creado correctamente.",
            ]);
        // } else {

        //     return response()->json([
        //         "message" => "Ya existe un registro igual para este grupo.",
        //     ]);
        // }
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
        $info = explode(', ', $data['teacher_full_name']);
        $id = Group::teacherId($info[0], $info[1])->pluck('id');
        $teacher_id = Group::clean($id);

        Group::where('id', $data['id'])->update([
            'group_code' => $data['group_code'],
            'students_quantity' => $data['students_quantity'],
            'teacher_id' => $teacher_id,
            'subject_id' => Subject::where('subject_name', $data['subject_name'])->first()?->id,
        ]);

        return response()->json([
            "message" => "Registro modificado correctamente.",
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $id = Encrypt::decryptValue($request->id);

        Group::where('id', $id)->delete();

        return response()->json([
            "message" => "Registro eliminado correctamente.",
        ]);
    }

    public function bySubject($subject)
    {
        $subjectcode = Subject::select('subject_code')
            ->where('subject_name', 'like', $subject)
            ->get();

        return response()->json([
            "message" => "Registros obtenidos correctamente.",
            "subject" => $subjectcode,
        ]);
    }
}