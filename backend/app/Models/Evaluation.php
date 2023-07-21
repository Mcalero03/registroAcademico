<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;

class Evaluation extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'evaluation';

    public  $incrementing = true;

    protected $data = ['deleted_at'];

    protected $fillable = [
        'id',
        'evaluation_name',
        'ponder',
        'group_id',
    ];

    public $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public static function allDataSearched($search, $sortBy, $sort, $skip, $itemsPerpage)
    {
        return Evaluation::select(DB::raw("CONCAT(teacher.name, ', ', teacher.last_name) as teacher_name"), 'evaluation.evaluation_name', 'evaluation.ponder', 'subject.subject_name', 'group.group_code', 'group.id as group_id', 'evaluation.id', 'school.school_name', 'inscription_detail.id as inscription_detail_id')
            ->join('calification', 'evaluation.id', '=', 'calification.evaluation_id')
            ->leftjoin('inscription_detail', 'calification.inscription_detail_id', '=', 'inscription_detail.id')
            ->leftjoin('group', 'inscription_detail.group_id', '=', 'group.id')
            ->leftjoin('subject', 'group.subject_id', '=', 'subject.id')
            ->leftjoin('schedule_classroom_group_detail', 'group.id', '=', 'schedule_classroom_group_detail.group_id')
            ->leftjoin('teacher', 'group.teacher_id', '=', 'teacher.id')
            ->leftjoin('school', 'teacher.school_id', '=', 'school.id')
            ->whereNull('calification.deleted_at')
            ->where('subject.subject_name', 'like', $search)
            ->orWhere('evaluation.ponder', 'like', $search)
            ->orWhere('group.group_code', 'like', $search)
            ->orWhere('evaluation.evaluation_name', 'like', $search)
            ->groupBy('evaluation.evaluation_name')
            ->groupBy('subject.subject_name')
            ->groupBy('group.group_code')

            ->skip($skip)
            ->take($itemsPerpage)
            ->orderBy("evaluation.$sortBy", $sort)
            ->get();
    }

    public static function counterPagination($search)
    {
        return Evaluation::select(DB::raw("CONCAT(teacher.name, ', ', teacher.last_name) as full_name_teacher"), 'evaluation.evaluation_name', 'evaluation.ponder', 'subject.subject_name', 'group.group_code', 'evaluation.id', 'school.school_name')
            ->join('calification', 'evaluation.id', '=', 'calification.evaluation_id')
            ->leftjoin('inscription_detail', 'calification.inscription_detail_id', '=', 'inscription_detail.id')
            ->leftjoin('group', 'inscription_detail.group_id', '=', 'group.id')
            ->leftjoin('subject', 'group.subject_id', '=', 'subject.id')
            ->leftjoin('schedule_classroom_group_detail', 'group.id', '=', 'schedule_classroom_group_detail.group_id')
            ->leftjoin('teacher', 'group.teacher_id', '=', 'teacher.id')
            ->leftjoin('school', 'teacher.school_id', '=', 'school.id')
            ->where('subject.subject_name', 'like', $search)
            ->orWhere('evaluation.ponder', 'like', $search)
            ->orWhere('group.group_code', 'like', $search)
            ->orWhere('evaluation.evaluation_name', 'like', $search)
            ->groupBy('evaluation.evaluation_name')
            ->groupBy('subject.subject_name')
            ->groupBy('group.group_code')


            ->count();
    }
}
