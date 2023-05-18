<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attendance extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'attendance';

    public $incrementing = true;

    protected $data = ['deleted_at'];

    protected $fillable = [
        'id',
        'attendance_time',
        'attendance_date',
        'group_id',
    ];

    public $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public static function allDataSearched($search, $sortBy, $sort, $skip, $itemsPerpage)
    {
        return Attendance::select('attendance.*', 'sub.subject_name', 'g.group_name')
            ->join('attendance_detail as ad', 'attendance.id', '=', 'ad.attendance_id')
            ->leftjoin('inscription as i', 'ad.inscription_id', '=', 'i.id')
            ->leftjoin('student as s', 'i.student_id', '=', 's.id')
            ->leftjoin('subject as sub', 'i.subject_id', '=', 'sub.id')
            ->leftjoin('group as g', 'i.group_id', '=', 'g.id')
            ->where('attendance.attendance_date', 'like', $search)
            ->orwhere('attendance.attendance_time', 'like', $search)
            ->orWhere('attendance.group_id', 'like', $search)
            ->orWhere('sub.subject_name', 'like', $search)
            ->orWhere('attendance.group_id', 'like', 'g.id')
            ->groupBy('attendance.attendance_date')
            ->groupBy('g.group_name')
            ->groupBy('sub.subject_name')


            ->skip($skip)
            ->take($itemsPerpage)
            ->orderBy("attendance.$sortBy", $sort)
            ->get();
    }

    public static function counterPagination($search)
    {
        return Attendance::select('attendance.*', 'sub.subject_name', 'g.group_name')
            ->join('attendance_detail as ad', 'attendance.id', '=', 'ad.attendance_id')
            ->leftjoin('inscription as i', 'ad.inscription_id', '=', 'i.id')
            ->leftjoin('student as s', 'i.student_id', '=', 's.id')
            ->leftjoin('subject as sub', 'i.subject_id', '=', 'sub.id')
            ->leftjoin('group as g', 'i.group_id', '=', 'g.id')
            ->where('attendance.attendance_date', 'like', $search)
            ->orwhere('attendance.attendance_time', 'like', $search)
            ->orWhere('attendance.group_id', 'like', $search)
            ->orWhere('sub.subject_name', 'like', $search)
            ->orWhere('attendance.group_id', 'like', 'g.id')
            ->groupBy('attendance.attendance_date')
            ->groupBy('g.group_name')
            ->groupBy('sub.subject_name')

            ->count();
    }
}
