<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Group extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'group';

    public  $incrementing = true;

    protected $data = ['deleted_at'];

    protected $fillable = [
        'id',
        'group_code',
        'students_quantity',
        'subject_id',
        'teacher_id',
    ];

    public $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public static function allDataSearched($search, $sortBy, $sort, $skip, $itemsPerpage)
    {
        return Group::select(DB::raw('CONCAT(teacher.name, ", ", teacher.last_name) as teacher_full_name'), 'group.*', 'group.id as id', 'subject.subject_name', 'subject.subject_code')
            ->join('teacher', 'group.teacher_id', '=', 'teacher.id')
            ->join('subject', 'group.subject_id', '=', 'subject.id')
            ->where('group.group_code', 'like', $search)
            ->orWhere('group.students_quantity', 'like', $search)

            ->skip($skip)
            ->take($itemsPerpage)
            ->orderBy("group.$sortBy", $sort)
            ->get();
    }

    public static function counterPagination($search)
    {
        return Group::select(DB::raw('CONCAT(teacher.name, ", ", teacher.last_name) as teacher_full_name'), 'group.*', 'group.id as id', 'subject.subject_name', 'subject.subject_code')
            ->join('teacher', 'group.teacher_id', '=', 'teacher.id')
            ->join('subject', 'group.subject_id', '=', 'subject.id')
            ->where('group.group_code', 'like', $search)
            ->orWhere('group.students_quantity', 'like', $search)

            ->count();
    }

    public static function Groups()
    {
        return Group::select('group.*', 'group.id as id',)
            ->get();
    }

    public static function teacherId($name, $last_name)
    {
        return Teacher::select('teacher.id')
            ->where('teacher.name', $name)
            ->where('teacher.last_name', $last_name)
            ->get('teacher.id');
    }

    public static function clean($string)
    {
        $change = str_replace('[', '', $string);
        $change = str_replace(']', '', $change);
        return $change;
    }
}
