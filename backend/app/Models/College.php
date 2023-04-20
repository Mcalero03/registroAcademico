<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Encrypt;

class College extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'college';

    public $incrementing = true;

    protected $data = ['deleted_at'];

    protected $fillable = [
        'id',
        'college_name',
        'direction_id',
    ];

    public $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function direction()
    {
        return $this->belongsTo(Direction::class, 'direction_id');
    }

    public function format()
    {
        return [
            "id" => Encrypt::encryptValue($this->id),
            'college_name' => $this->college_name,
            'direction_name' => $this->direction->direction_name,
        ];
    }

    public static function allDataSearched($search, $sortBy, $sort, $skip, $itemsPerpage)
    {
        return College::where('college.college_name', 'like', $search)
            ->orWhere('college.direction_id', 'like', $search)
            ->skip($skip)
            ->take($itemsPerpage)
            ->orderBy("college.$sortBy", $sort)
            ->get()
            ->map(fn ($college) => $college->format());
    }

    public static function counterPagination($search)
    {
        return College::select('college.*', 'college.id as id')

            ->where('college.college_name', 'like', $search)
            ->orWhere('college.direction_id', 'like', $search)

            ->count();
    }
}
