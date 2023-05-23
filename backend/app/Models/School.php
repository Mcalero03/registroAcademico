<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class School extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'school';

    public $incrementing = true;

    protected $data = ['deleted_at'];

    protected $fillable = [
        'id',
        'school_name',
    ];

    public $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public static function allDataSearched($search, $sortBy, $sort, $skip, $itemsPerpage)
    {
        return School::select('school.*', 'school.id as id')
            ->where('school.school_name', 'like', $search)

            ->skip($skip)
            ->take($itemsPerpage)
            ->orderBy("school.$sortBy", $sort)
            ->get();
    }

    public static function counterPagination($search)
    {
        return School::select('school.*', 'school.id as id')
            ->where('school.school_name', 'like', $search)
            ->count();
    }
}
