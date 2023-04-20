<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Teacher extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'teacher';

    public  $incrementing = true;

    protected $data = ['deleted_at'];

    protected $fillable = [
        'id',
        'name',
        'last_name',
        'card',
        'dui',
        'nit',
        'phone_number',
        'mail',
    ];

    public $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public static function allDataSearched($search, $sortBy, $sort, $skip, $itemsPerpage)
    {
        return Teacher::select('teacher.*', 'teacher.id as id')

            ->where('teacher.name', 'like', $search)
            ->orWhere('teacher.last_name', 'like', $search)
            ->orWhere('teacher.card', 'like', $search)
            ->orWhere('teacher.dui', 'like', $search)
            ->orWhere('teacher.phone_number', 'like', $search)
            ->orWhere('teacher.mail', 'like', $search)

            ->skip($skip)
            ->take($itemsPerpage)
            ->orderBy("teacher.$sortBy", $sort)
            ->get();
    }

    public static function counterPagination($search)
    {
        return Teacher::select('teacher.*', 'teacher.id as id')

            ->where('teacher.name', 'like', $search)
            ->orWhere('teacher.last_name', 'like', $search)
            ->orWhere('teacher.card', 'like', $search)
            ->orWhere('teacher.dui', 'like', $search)
            ->orWhere('teacher.phone_number', 'like', $search)
            ->orWhere('teacher.mail', 'like', $search)

            ->count();
    }
}
