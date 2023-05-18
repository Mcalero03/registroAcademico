<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attendance_Detail extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'attendance_detail';

    public $incrementing = true;

    protected $data = ['deleted_at'];

    protected $fillable = [
        'id',
        'status',
        'inscription_id',
        'attendance_id',
    ];

    public $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public static function allDataSearched($search, $sortBy, $sort, $skip, $itemsPerpage)
    {
        return Attendance_Detail::select('attendance_detail.*', 'sub.subject_name', 'g.group_name')
            ->leftjoin('inscription as i', 'attendance.inscription_id', '=', 'i.id')
            ->leftjoin('student as s', 'i.student_id', '=', 's.id')
            ->leftjoin('subject as sub', 'i.subject_id', '=', 'sub.id')
            ->leftjoin('group as g', 'i.group_id', '=', 'g.id')
            ->where('attendance_detail.status', 'like', $search)
            ->orWhere('attendance_detail.inscription_id', 'like', $search)
            ->orWhere('attendance_detail.attendance_id', 'like', $search)
            ->orWhere('sub.subject_name', 'like', $search)
            ->orWhere('g.group_name', 'like', $search)

            ->skip($skip)
            ->take($itemsPerpage)
            ->orderBy("attendance_detail.$sortBy", $sort)
            ->get();
    }

    public static function counterPagination($search)
    {
        return Attendance_Detail::select('attendance_detail.*', 'sub.subject_name', 'g.group_name')
            ->leftjoin('inscription as i', 'attendance.inscription_id', '=', 'i.id')
            ->leftjoin('student as s', 'i.student_id', '=', 's.id')
            ->leftjoin('subject as sub', 'i.subject_id', '=', 'sub.id')
            ->leftjoin('group as g', 'i.group_id', '=', 'g.id')
            ->where('attendance_detail.status', 'like', $search)
            ->orWhere('attendance_detail.inscription_id', 'like', $search)
            ->orWhere('attendance_detail.attendance_id', 'like', $search)
            ->orWhere('sub.subject_name', 'like', $search)
            ->orWhere('g.group_name', 'like', $search)

            ->count();
    }
}
