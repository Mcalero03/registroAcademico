<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;

class Subject extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'subject';

    public  $incrementing = true;

    protected $data = ['deleted_at'];

    protected $fillable = [
        'id',
        'subject_code',
        'subject_name',
        'average_approval',
        'units_value',
        'status'
    ];

    public $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
    public static function allDataSearched($search, $sortBy, $sort, $skip, $itemsPerpage)
    {
        return Subject::select(DB::raw('CASE WHEN subject.status=0 THEN "Sin prerrequisito" ELSE "Con prerrequisito" END as prerequisite'), 'subject.*', 'subject.id as id', 'pensum.program_name', 'pensum_subject_detail.id as pensum_subject_detail_id', 'school.school_name')
            ->join('pensum_subject_detail', 'subject.id', '=', 'pensum_subject_detail.subject_id')
            ->join('pensum', 'pensum_subject_detail.pensum_id', '=', 'pensum.id')
            ->leftjoin('sub_school', 'pensum.sub_school_id', '=', 'sub_school.id')
            ->leftjoin('school', 'sub_school.school_id', '=', 'school.id')
            ->where('subject.subject_name', 'like', $search)
            ->orwhere('subject.subject_code', 'like', $search)
            ->orWhere('subject.average_approval', 'like', $search)
            ->orWhere('subject.units_value', 'like', $search)
            ->orWhere('subject.status', 'like', $search)

            ->skip($skip)
            ->take($itemsPerpage)
            ->orderBy("subject.$sortBy", $sort)
            ->get();
    }

    public static function counterPagination($search)
    {
        return Subject::select(DB::raw('CASE WHEN subject.status=0 THEN "Sin prerrequisito" ELSE "Con prerrequisito" END as prerequisite'), 'subject.*', 'subject.id as id', 'pensum.program_name', 'pensum_subject_detail.id as pensum_subject_detail_id', 'school.school_name')
            ->join('pensum_subject_detail', 'subject.id', '=', 'pensum_subject_detail.subject_id')
            ->join('pensum', 'pensum_subject_detail.pensum_id', '=', 'pensum.id')
            ->leftjoin('sub_school', 'pensum.sub_school_id', '=', 'sub_school.id')
            ->leftjoin('school', 'sub_school.school_id', '=', 'school.id')
            ->where('subject.subject_name', 'like', $search)
            ->orwhere('subject.subject_code', 'like', $search)
            ->orWhere('subject.average_approval', 'like', $search)
            ->orWhere('subject.units_value', 'like', $search)
            ->orWhere('subject.status', 'like', $search)

            ->count();
    }

    public static function cycleSubject()
    {
        return Subject::select(DB::raw('subject.id = 0 as subject_status'), 'subject.*')
            ->get();
    }
}
