<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Encrypt;

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
        'subject_id',
    ];

    public $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public static function allDataSearched($search, $sortBy, $sort, $skip, $itemsPerpage)
    {
        return Evaluation::where('evaluation.evaluation_name', 'like', $search)
            ->join('subject', 'evaluation.subject_id', '=', 'subject.id')
            ->join('group', 'subject.id', '=', 'group.subject_id')
            ->join('schedule_classroom_group_detail', 'group.id', '=', 'schedule_classroom_group_detail.group_id')
            ->join('teacher', 'group.teacher_id', '=', 'teacher.id')
            ->orWhere('evaluation.ponder', 'like', $search)
            ->orWhere('evaluation.subject_id', 'like', $search)

            ->skip($skip)
            ->take($itemsPerpage)
            ->orderBy("evaluation.$sortBy", $sort)
            ->get();
    }

    public static function counterPagination($search)
    {
        return Evaluation::where('evaluation.evaluation_name', 'like', $search)
            ->join('subject', 'evaluation.subject_id', '=', 'subject.id')
            ->join('group', 'subject.id', '=', 'group.subject_id')
            ->join('schedule_classroom_group_detail', 'group.id', '=', 'schedule_classroom_group_detail.group_id')
            ->join('teacher', 'group.teacher_id', '=', 'teacher.id')
            ->orWhere('evaluation.ponder', 'like', $search)
            ->orWhere('evaluation.subject_id', 'like', $search)

            ->where('evaluation.evaluation_name', 'like', $search)
            ->orWhere('evaluation.ponder', 'like', $search)
            ->orWhere('evaluation.subject_id', 'like', $search)

            ->count();
    }
}
