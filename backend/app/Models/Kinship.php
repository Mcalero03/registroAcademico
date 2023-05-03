<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Kinship extends Model
{
    use HasFactory, softDeletes;

    protected $table = 'kinship';

    public $incrementing = true;

    protected $data = ['deleted_at'];

    protected $fillable = [
        'id',
        'kinship',
    ];

    public $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public static function allDataSearched($search, $sortBy, $sort, $skip, $itemsPerpage)
    {
        return Kinship::select('kinship.*', 'kinship.id as id')

            ->where('kinship.kinship', 'like', $search)

            ->skip($skip)
            ->take($itemsPerpage)
            ->orderBy("kinship.$sortBy", $sort)
            ->get();
    }

    public static function counterPagination($search)
    {
        return Kinship::select('kinship.*', 'kinship.id as id')

            ->where('kinship.kinship', 'like', $search)

            ->count();
    }
}
