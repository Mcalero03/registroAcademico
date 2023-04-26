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

    public function format()
    {
        return [
            'id' => Encrypt::encryptValue($this->id),
            'attendance_date' => $this->attendance_date,
            'attendance_time' => $this->attendance_time,
            'status' => $this->status,
            'inscription_id' => $this->inscription_id,
        ];
    }

    public static function allDataSearched($search, $sortBy, $sort, $skip, $itemsPerpage)
    {
        return Attendance::where('attendance.attendance_date', 'like', $search)
            ->orWhere('attendance.attendance_time', 'like', $search)
            ->orWhere('attendance.status', 'like', $search)
            ->orWhere('attendance.inscription_id', 'like', $search)

            ->skip($skip)
            ->take($itemsPerpage)
            ->orderBy("attendance.$sortBy", $sort)
            ->get()
            ->map(fn ($attendance) => $attendance->format());
    }

    public static function counterPagination($search)
    {
        return Attendance::select('attendance.*', 'attendance.id as id')

            ->where('attendance.attendance_date', 'like', $search)
            ->orWhere('attendance.attendance_time', 'like', $search)
            ->orWhere('attendance.status', 'like', $search)
            ->orWhere('attendance.inscription_id', 'like', $search)

            ->count();
    }
}
