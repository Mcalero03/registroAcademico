<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PensumType extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pensum_type';

    public $incrementing = true;

    protected $data = ['deleted_at'];

    protected $fillable = [
        'id',
        'pensum_type_name'
    ];

    public $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public static function allDataSearched($search, $sortBy, $sort, $skip, $itemsPerpage)
    {
        return PensumType::select('pensum_type.*', 'pensum_type.id as id')

            ->where('pensum_type.pensum_type_name', 'like', $search)

            ->skip($skip)
            ->take($itemsPerpage)
            ->orderBy("pensum_type.$sortBy", $sort)
            ->get();
    }

    public static function counterPagination($search)
    {
        return PensumType::select('pensum_type.*', 'pensum_type.id as id')

            ->where('pensum_type.pensum_type_name', 'like', $search)
            ->count();
    }
}
