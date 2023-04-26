<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Encrypt;

class TeacherSubjectDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'teacher_subject_detail';

    public  $incrementing = true;

    protected $data = ['deleted_at'];

    protected $fillable = [
        'id',
        'subject_id',
        'teacher_id	',
        'group_id',
    ];

    public $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }

    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    public function format()
    {
        return [
            'id' => Encrypt::encryptValue($this->id),
            'subject_name' => $this->subject->subject_name,
            'teacher_name' => $this->teacher->name,
            'group_name' => $this->group->group_name,
        ];
    }

    public static function allDataSearched($search, $sortBy, $sort, $skip, $itemsPerpage)
    {
        return TeacherSubjectDetail::select('teacher_subject_detail.*', 'teacher_subject_detail.id as id')

            ->where('teacher_subject_detail.subject_id', 'like', $search)
            ->orWhere('teacher_subject_detail.teacher_id', 'like', $search)
            ->orWhere('teacher_subject_detail.group_id', 'like', $search)

            ->skip($skip)
            ->take($itemsPerpage)
            ->orderBy("teacher_subject_detail.$sortBy", $sort)
            ->get()
            ->map(fn ($teacherSubjectDetail) => $teacherSubjectDetail->format());
    }

    public static function counterPagination($search)
    {
        return TeacherSubjectDetail::select('teacher_subject_detail.*', 'teacher_subject_detail.id as id')

            ->where('teacher_subject_detail.subject_id', 'like', $search)
            ->orWhere('teacher_subject_detail.teacher_id', 'like', $search)
            ->orWhere('teacher_subject_detail.group_id', 'like', $search)

            ->count();
    }
}
