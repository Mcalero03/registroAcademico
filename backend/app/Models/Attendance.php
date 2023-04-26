<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Encrypt;

class Attendance extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'attendance';

    public $incrementing = true;

    protected $data = ['deleted_at'];

    protected $fillable = [
        'id',
        'attendance_date',
        'attendance_time	',
        'status',
        'inscription_id',
    ];

    public $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];


    public static function allDataSearched($search, $sortBy, $sort, $skip, $itemsPerpage)
    {
        return Attendance::select('attendance.*', 's.card', 'sub.subject_name', 'g.group_name')
            ->join('inscription as i', 'attendance.inscription_id', '=', 'i.id')
            ->join('student as s', 'i.student_id', '=', 's.id')
            ->join('subject as sub', 'i.subject_id', '=', 'sub.id')
            ->join('group as g', 'i.group_id', '=', 'g.id')
            ->where('attendance.attendance_date', 'like', $search)
            ->orWhere('attendance.attendance_time', 'like', $search)
            ->orWhere('attendance.status', 'like', $search)
            ->orWhere('attendance.inscription_id', 'like', $search)
            ->orWhere('s.card', 'like', $search)
            ->orWhere('sub.subject_name', 'like', $search)
            ->orWhere('g.group_name', 'like', $search)

            ->skip($skip)
            ->take($itemsPerpage)
            ->orderBy("attendance.$sortBy", $sort)
            ->get();
        // ->map(fn ($attendance) => $attendance->format());
    }

    public static function counterPagination($search)
    {
        return Attendance::select('attendance.*', 's.card', 'sub.subject_name', 'g.group_name')
            ->join('inscription as i', 'attendance.inscription_id', '=', 'i.id')
            ->join('student as s', 'i.student_id', '=', 's.id')
            ->join('subject as sub', 'i.subject_id', '=', 'sub.id')
            ->join('group as g', 'i.group_id', '=', 'g.id')
            ->where('attendance.attendance_date', 'like', $search)
            ->orWhere('attendance.attendance_time', 'like', $search)
            ->orWhere('attendance.status', 'like', $search)
            ->orWhere('attendance.inscription_id', 'like', $search)
            ->orWhere('s.card', 'like', $search)
            ->orWhere('sub.subject_name', 'like', $search)
            ->orWhere('g.group_name', 'like', $search)

            ->count();
    }
}
