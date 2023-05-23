<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Encrypt;

class SubSchool extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'sub_school';

    public $incrementing = true;

    protected $data = ['deleted_at'];

    protected $fillable = [
        'id',
        'sub_school_name',
        'school_id',
    ];

    public $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function school()
    {
        return $this->belongsTo(School::class, 'school_id');
    }

    public function format()
    {
        return [
            "id" => Encrypt::encryptValue($this->id),
            'sub_school_name' => $this->sub_school_name,
            'school_name' => $this->school->school_name,
        ];
    }

    public static function allDataSearched($search, $sortBy, $sort, $skip, $itemsPerpage)
    {
        return SubSchool::where('sub_school.sub_school_name', 'like', $search)
            ->orWhere('sub_school.school_id', 'like', $search)
            ->skip($skip)
            ->take($itemsPerpage)
            ->orderBy("sub_school.$sortBy", $sort)
            ->get()
            ->map(fn ($sub_school) => $sub_school->format());
    }

    public static function counterPagination($search)
    {
        return SubSchool::select('sub_school.*', 'sub_school.id as id')

            ->where('sub_school.sub_school_name', 'like', $search)
            ->orWhere('sub_school.school_id', 'like', $search)

            ->count();
    }
}
