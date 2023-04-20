<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Relative extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'relative';

    public  $incrementing = true;

    protected $data = ['deleted_at'];

    protected $fillable = [
        'id',
        'relationship',
        'name',
        'last_name',
        'dui',
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
        return Relative::select('relative.*', 'relative.id as id')

            ->where('relative.relationship', 'like', $search)
            ->orWhere('relative.name', 'like', $search)
            ->orWhere('relative.last_name', 'like', $search)
            ->orWhere('relative.dui', 'like', $search)
            ->orWhere('relative.phone_number', 'like', $search)
            ->orWhere('relative.mail', 'like', $search)

            ->skip($skip)
            ->take($itemsPerpage)
            ->orderBy("relative.$sortBy", $sort)
            ->get();
    }

    public static function counterPagination($search)
    {
        return Relative::select('relative.*', 'relative.id as id')

            ->where('relative.relationship', 'like', $search)
            ->orWhere('relative.name', 'like', $search)
            ->orWhere('relative.last_name', 'like', $search)
            ->orWhere('relative.dui', 'like', $search)
            ->orWhere('relative.phone_number', 'like', $search)
            ->orWhere('relative.mail', 'like', $search)

            ->count();
    }
}
