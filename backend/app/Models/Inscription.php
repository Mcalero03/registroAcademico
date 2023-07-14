<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Inscription extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'inscription';

    public $incrementing = true;

    protected $data = ['deleted_at'];

    protected $fillable = [
        'id',
        'inscription_date',
        'status',
        'cycle_id',
        'student_id',
        'pensum_id'
    ];

    public $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public static function allDataSearched($search, $sortBy, $sort, $skip, $itemsPerpage)
    {
        return Inscription::select(DB::raw("CONCAT(student.name, ', ', student.last_name) as full_name, CONCAT(cycle.cycle_number, '-', cycle.year) as cycle"), 'inscription.*', 'pensum.program_name', 'pensum.id as id_pensum')
            ->join('cycle', 'inscription.cycle_id', '=', 'cycle.id')
            ->join('student', 'inscription.student_id', '=', 'student.id')
            ->leftjoin('inscription_detail', 'inscription.id', '=', 'inscription_detail.inscription_id')
            ->leftjoin('group', 'group.id', '=', 'inscription_detail.group_id')
            ->leftjoin('subject', 'group.subject_id', '=', 'subject.id')
            ->leftjoin('pensum_subject_detail', 'subject.id', '=', 'pensum_subject_detail.subject_id')
            ->leftjoin('pensum', 'pensum_subject_detail.pensum_id', '=', 'pensum.id')
            ->where('inscription.pensum_id', 'pensum.id')
            ->where('inscription.inscription_date', 'like', $search)
            ->orWhere('inscription.status', 'like', $search)
            ->orWhere('pensum.program_name', 'like', $search)
            ->orWhere('student.name', 'like', $search)
            ->orWhere('student.last_name', 'like', $search)
            ->orWhere('student.student_card', 'like', $search)
            ->orWhere('cycle.cycle_number', 'like', $search)
            ->orWhere('inscription.student_id', 'like', $search)

            ->skip($skip)
            ->take($itemsPerpage)
            ->orderBy("inscription.$sortBy", $sort)
            ->get();
    }

    public static function counterPagination($search)
    {
        return Inscription::select(DB::raw("CONCAT(student.name, ', ', student.last_name) as full_name, CONCAT(cycle.cycle_number, '-', cycle.year) as cycle"), 'inscription.*', 'pensum.program_name')
            ->join('cycle', 'inscription.cycle_id', '=', 'cycle.id')
            ->join('student', 'inscription.student_id', '=', 'student.id')
            ->leftjoin('student_pensum_detail', 'student.id', '=', 'student_pensum_detail.student_id')
            ->leftjoin('pensum', 'student_pensum_detail.pensum_id', '=', 'pensum.id')
            ->where('inscription.inscription_date', 'like', $search)
            ->orWhere('inscription.status', 'like', $search)
            ->orWhere('pensum.program_name', 'like', $search)
            ->orWhere('student.name', 'like', $search)
            ->orWhere('student.last_name', 'like', $search)
            ->orWhere('student.student_card', 'like', $search)
            ->orWhere('cycle.cycle_number', 'like', $search)
            ->orWhere('inscription.student_id', 'like', $search)

            ->count();
    }

    public static function studentId($name, $last_name)
    {
        return Student::select('student.id')
            ->where('student.name', $name)
            ->where('student.last_name', $last_name)
            ->get('student.id');
    }

    public static function cycleId($number, $year)
    {
        return Cycle::select('cycle.id')
            ->where('cycle.cycle_number', $number)
            ->where('cycle.year', $year)
            ->get('cycle.id');
    }

    public static function clean($string)
    {
        $change = str_replace('[', '', $string);
        $change = str_replace(']', '', $change);
        return $change;
    }

    public static function searchStudent($searchStudent)
    {
        return Student::select(DB::raw("CONCAT(student.name, ', ', student.last_name) as full_name"),)
            ->where('student.student_card', '=', $searchStudent)
            ->get();
    }
}
