<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Prerequisite extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'prerequisite';

    public $incrementing = true;

    protected $data = ['deleted_at'];

    protected $fillable = [
        'id',
        'subject_id',
        'pensum_subject_detail_id',
    ];

    public $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public static function allDataSearched($search, $sortBy, $sort, $skip, $itemsPerpage)
    {
        return Subject::select('prerequisite.*', 'subject.subject_name as prerequisite')
            ->join('subject', 'prerequisite.subject_id', '=', 'subject.id')
            ->where('prerequisite.subject_id', 'like', $search)
            ->orWhere('prerequisite.pensum_subject_detail_id', 'like', $search)

            ->skip($skip)
            ->take($itemsPerpage)
            ->orderBy("subject.$sortBy", $sort)
            ->get();
    }

    public static function counterPagination($search)
    {
        return Subject::select('prerequisite.*', 'subject.subject_name  as prerequisite')
            ->join('subject', 'prerequisite.subject_id', '=', 'subject.id')
            ->where('prerequisite.subject_id', 'like', $search)
            ->orWhere('prerequisite.pensum_subject_detail_id', 'like', $search)

            ->count();
    }
}
