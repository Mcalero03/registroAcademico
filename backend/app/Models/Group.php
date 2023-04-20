<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Group extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'group';

    public  $incrementing = true;

    protected $data = ['deleted_at'];

    protected $fillable = [
        'id',
        'group_name',
        'students_quantity',
    ];

    public $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public static function allDataSearched($search, $sortBy, $sort, $skip, $itemsPerpage)
    {
        return Group::select('group.*', 'group.id as id')

            ->where('group.group_name', 'like', $search)
            ->orWhere('group.students_quantity', 'like', $search)

            ->skip($skip)
            ->take($itemsPerpage)
            ->orderBy("group.$sortBy", $sort)
            ->get();
    }

    public static function counterPagination($search)
    {
        return Group::select('group.*', 'group.id as id')

            ->where('group.group_name', 'like', $search)
            ->orWhere('group.students_quantity', 'like', $search)
            ->count();
    }
}