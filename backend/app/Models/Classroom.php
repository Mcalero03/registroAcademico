<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Classroom extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'classroom';

    public $incrementing = true;

    protected $data = ['deleted_at'];

    protected $fillable = [
        'id',
        'classroom_code',
        'classroom_name',
        'capacity',
        'status',
        'school_id',
    ];

    public $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public static function allDataSearched($search, $sortBy, $sort, $skip, $itemsPerpage)
    {
        return Classroom::select('classroom.*', 'school.school_name')
            ->join('school', 'classroom.school_id', '=', 'school.id')
            ->where('classroom.classroom_code', 'like', $search)
            ->orwhere('classroom.classroom_name', 'like', $search)
            ->orwhere('classroom.capacity', 'like', $search)
            ->orwhere('classroom.status', 'like', $search)

            ->skip($skip)
            ->take($itemsPerpage)
            ->orderBy("classroom.$sortBy", $sort)
            ->get();
    }

    public static function counterPagination($search)
    {
        return Classroom::select('classroom.*', 'school.school_name')
            ->join('school', 'classroom.school_id', '=', 'school.id')
            ->where('classroom.classroom_code', 'like', $search)
            ->orwhere('classroom.classroom_name', 'like', $search)
            ->orwhere('classroom.capacity', 'like', $search)
            ->orwhere('classroom.status', 'like', $search)

            ->count();
    }
}
