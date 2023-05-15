<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Encrypt;

class Inscription extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'inscription';

    public $incrementing = true;

    protected $data = ['deleted_at'];

    protected $fillable = [
        'id',
        'inscription_date',
        'subject_average',
        'attendance_quantity',
        'status',
        'cycle_id',
        'student_id',
        'group_id',
        'subject_id',
    ];

    public $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public static function allDataSearched($search, $sortBy, $sort, $skip, $itemsPerpage)
    {
        return Inscription::select(DB::raw("CONCAT(student.name, ', ', student.last_name) as full_name"), 'inscription.*', 'cycle.cycle_number', 'subject.subject_name', 'group.group_name')
            ->join('cycle', 'inscription.cycle_id', '=', 'cycle.id')
            ->join('subject', 'inscription.subject_id', '=', 'subject.id')
            ->join('student', 'inscription.student_id', '=', 'student.id')
            ->join('group', 'inscription.group_id', '=', 'group.id')
            ->where('inscription.inscription_date', 'like', $search)
            ->orWhere('inscription.subject_average', 'like', $search)
            ->orWhere('inscription.attendance_quantity', 'like', $search)
            ->orWhere('inscription.status', 'like', $search)
            ->orWhere('cycle.cycle_number', 'like', $search)
            ->orWhere('inscription.student_id', 'like', $search) //order by student
            ->orWhere('group.group_name', 'like', $search)
            ->orWhere('subject.subject_name', 'like', $search)

            ->skip($skip)
            ->take($itemsPerpage)
            ->orderBy("inscription.$sortBy", $sort)
            ->get();
    }

    public static function counterPagination($search)
    {
        return Inscription::select(DB::raw("CONCAT(student.name, ', ', student.last_name) as full_name"), 'inscription.*', 'cycle.cycle_number', 'subject.subject_name', 'group.group_name')
            ->join('cycle', 'inscription.cycle_id', '=', 'cycle.id')
            ->join('subject', 'inscription.subject_id', '=', 'subject.id')
            ->join('student', 'inscription.student_id', '=', 'student.id')
            ->join('group', 'inscription.group_id', '=', 'group.id')
            ->where('inscription.inscription_date', 'like', $search)
            ->orWhere('inscription.subject_average', 'like', $search)
            ->orWhere('inscription.attendance_quantity', 'like', $search)
            ->orWhere('inscription.status', 'like', $search)
            ->orWhere('cycle.cycle_number', 'like', $search)
            ->orWhere('inscription.student_id', 'like', $search) //order by student
            ->orWhere('group.group_name', 'like', $search)
            ->orWhere('subject.subject_name', 'like', $search)

            ->count();
    }

    public static function studentId($name, $last_name)
    {
        return Student::select('student.id')
            ->where('student.name', $name)
            ->where('student.last_name', $last_name)
            ->get('student.id');
    }

    public static function clean($string)
    {
        $change = str_replace('[', '', $string);
        $change = str_replace(']', '', $change);
        return $change;
    }
}
