<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subject extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'subject';

    public  $incrementing = true;

    protected $data = ['deleted_at'];

    protected $fillable = [
        'id',
        'subject_name',
        'average_approval',
        'units_value',
    ];

    public $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
    public static function allDataSearched($search, $sortBy, $sort, $skip, $itemsPerpage)
    {
        return Subject::select('subject.*', 'subject.id as id')

            ->where('subject.subject_name', 'like', $search)
            ->orWhere('subject.average_approval', 'like', $search)
            ->orWhere('subject.units_value', 'like', $search)

            ->skip($skip)
            ->take($itemsPerpage)
            ->orderBy("subject.$sortBy", $sort)
            ->get();
    }

    public static function counterPagination($search)
    {
        return Subject::select('subject.*', 'subject.id as id')

            ->where('subject.subject_name', 'like', $search)
            ->orWhere('subject.average_approval', 'like', $search)
            ->orWhere('subject.units_value', 'like', $search)

            ->count();
    }
}
