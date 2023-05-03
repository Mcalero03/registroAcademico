<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Encrypt;

class Municipality extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'municipalities';

    public $incrementing = true;

    protected $data = ['deleted_at'];

    protected $fillable = [
        'municipality_name',
        'mun_min',
        'mun_may',
        'dm_cod',
        'cod_mun',
        'department_id',
    ];

    public $timestamps = false;

    public $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'id')->withDefault();
    }

    public function format()
    {
        return [
            'id' => Encrypt::encryptValue($this->id),
            'municipality_name' => $this->municipality_name . ', ' . $this->department->department_name,
            'mun_min' => $this->mun_min,
            'mun_may' => $this->mun_may,
            'dm_cod' => $this->dm_cod,
            'cod_mun' => $this->cod_mun,
            'municipality' => $this->municipality_name,
            'department_name' => $this->department->department_name,
        ];
    }

    public static function allDataSearched($search, $sortBy, $sort, $skip, $itemsPerpage)
    {
        return Municipality::where('municipalities.municipality_name', 'like', $search)
            ->orWhere('municipalities.mun_min', 'like', $search)
            ->orWhere('municipalities.mun_may', 'like', $search)
            ->orWhere('municipalities.dm_cod', 'like', $search)
            ->orWhere('municipalities.cod_mun', 'like', $search)
            ->orWhere('municipalities.department_id', 'like', $search)
            ->skip($skip)
            ->take($itemsPerpage)
            ->orderBy("municipalities.$sortBy", $sort)
            ->get()
            ->map(fn ($municipality) => $municipality->format());
    }

    public static function counterPagination($search)
    {
        return Municipality::select('municipalities.*', 'municipality.id as id')

            ->where('municipalities.municipality_name', 'like', $search)
            ->orWhere('municipalities.mun_min', 'like', $search)
            ->orWhere('municipalities.mun_may', 'like', $search)
            ->orWhere('municipalities.dm_cod', 'like', $search)
            ->orWhere('municipalities.cod_mun', 'like', $search)
            ->orWhere('municipalities.department_id', 'like', $search)

            ->count();
    }
}
