<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cycle extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'cycle';

    public $incrementing = true;

    protected $data = ['deleted_at'];

    protected $fillable = [
        'id',
        'cycle_number',
        'year',
        'start_date',
        'end_date',
        'status',
    ];

    public $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public static function allDataSearched($search, $sortBy, $sort, $skip, $itemsPerpage)
    {
        return Cycle::select('cycle.*', 'cycle.id as id')

            ->where('cycle.cycle_number', 'like', $search)
            ->orWhere('cycle.year', 'like', $search)
            ->orWhere('cycle.start_date', 'like', $search)
            ->orWhere('cycle.end_date', 'like', $search)
            ->orWhere('cycle.status', 'like', $search)

            ->skip($skip)
            ->take($itemsPerpage)
            ->orderBy("cycle.$sortBy", $sort)
            ->get();
    }

    public static function counterPagination($search)
    {
        return Cycle::select('cycle.*', 'cycle.id as id')

            ->where('cycle.cycle_number', 'like', $search)
            ->orWhere('cycle.year', 'like', $search)
            ->orWhere('cycle.start_date', 'like', $search)
            ->orWhere('cycle.end_date', 'like', $search)
            ->orWhere('cycle.status', 'like', $search)

            ->count();
    }
}
