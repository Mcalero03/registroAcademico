<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;

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
        return Attendance::select(DB::raw("CONCAT(g.group_code, '*', g.id) as group_code"), 'attendance.*', 'sub.subject_name')
            ->join('attendance_detail as ad', 'attendance.id', '=', 'ad.attendance_id')
            ->leftjoin('inscription_detail as i', 'ad.inscription_detail_id', '=', 'i.id')
            ->leftjoin('inscription as id', 'i.inscription_id', '=', 'id.id')
            ->leftjoin('student as s', 'id.student_id', '=', 's.id')
            ->leftjoin('group as g', 'i.group_id', '=', 'g.id')
            ->leftjoin('subject as sub', 'g.subject_id', '=', 'sub.id')
            ->where('attendance.attendance_date', 'like', $search)
            ->orwhere('attendance.attendance_time', 'like', $search)
            ->orWhere('g.group_code', 'like', $search)
            ->orWhere('sub.subject_name', 'like', $search)
            ->orWhere('attendance.group_id', 'like', 'g.id')
            ->groupBy('attendance.attendance_date')
            ->groupBy('g.group_code')
            ->groupBy('sub.subject_name')


            ->skip($skip)
            ->take($itemsPerpage)
            ->orderBy("attendance.$sortBy", $sort)
            ->get();
    }

    public static function counterPagination($search)
    {
        return Attendance::select(DB::raw("CONCAT(g.group_code, '*', g.id) as group_code"), 'attendance.*', 'sub.subject_name')
            ->join('attendance_detail as ad', 'attendance.id', '=', 'ad.attendance_id')
            ->leftjoin('inscription_detail as i', 'ad.inscription_detail_id', '=', 'i.id')
            ->leftjoin('inscription as id', 'i.inscription_id', '=', 'id.id')
            ->leftjoin('student as s', 'id.student_id', '=', 's.id')
            ->leftjoin('group as g', 'i.group_id', '=', 'g.id')
            ->leftjoin('subject as sub', 'g.subject_id', '=', 'sub.id')
            ->where('attendance.attendance_date', 'like', $search)
            ->orwhere('attendance.attendance_time', 'like', $search)
            ->orWhere('g.group_code', 'like', $search)
            ->orWhere('sub.subject_name', 'like', $search)
            ->orWhere('attendance.group_id', 'like', 'g.id')
            ->groupBy('attendance.attendance_date')
            ->groupBy('g.group_code')
            ->groupBy('sub.subject_name')
            ->count();
    }
}
