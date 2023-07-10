<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;

class Teacher extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'teacher';

    public  $incrementing = true;

    protected $data = ['deleted_at'];

    protected $fillable = [
        'id',
        'name',
        'last_name',
        'teacher_card',
        'dui',
        'nit',
        'phone_number',
        'mail',
        'school_id',
    ];

    public $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public static function allDataSearched($search, $sortBy, $sort, $skip, $itemsPerpage)
    {
        return Teacher::select(DB::raw("CONCAT(teacher.name, ', ', teacher.last_name) as full_name"), 'teacher.*', 'teacher.id as id', 'school.school_name')
            ->join('school', 'teacher.school_id', '=', 'school.id')
            ->where('teacher.name', 'like', $search)
            ->orWhere('teacher.last_name', 'like', $search)
            ->orWhere('teacher.teacher_card', 'like', $search)
            ->orWhere('teacher.dui', 'like', $search)
            ->orWhere('teacher.phone_number', 'like', $search)
            ->orWhere('teacher.mail', 'like', $search)
            ->orWhere('school.school_name', 'like', $search)

            ->skip($skip)
            ->take($itemsPerpage)
            ->orderBy("teacher.$sortBy", $sort)
            ->get();
    }

    public static function counterPagination($search)
    {
        return Teacher::select(DB::raw("CONCAT(teacher.name, ', ', teacher.last_name) as full_name"), 'teacher.*', 'teacher.id as id', 'school.school_name')
            ->join('school', 'teacher.school_id', '=', 'school.id')
            ->where('teacher.name', 'like', $search)
            ->orWhere('teacher.last_name', 'like', $search)
            ->orWhere('teacher.teacher_card', 'like', $search)
            ->orWhere('teacher.dui', 'like', $search)
            ->orWhere('teacher.phone_number', 'like', $search)
            ->orWhere('teacher.mail', 'like', $search)
            ->orWhere('school.school_name', 'like', $search)

            ->count();
    }

    public static function searchTeacher($searchTeacher)
    {
        return Teacher::select(DB::raw("CONCAT(teacher.name, ', ', teacher.last_name) as full_name"),)
            ->where('teacher.teacher_card', '=', $searchTeacher)
            ->get();
    }
}
