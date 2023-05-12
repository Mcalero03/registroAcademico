<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Encrypt;

class PensumSubjectDetail extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'pensum_subject_detail';

    public $incrementing = true;

    protected $data = ['deleted_at'];

    protected $fillable = [
        'id',
        'pensum_id',
        'subject_id',
    ];

    public $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public static function allDataSearched($search, $sortBy, $sort, $skip, $itemsPerpage)
    {
        return PensumSubjectDetail::select('pensum_subject_detail.*', 'subject.subject_name', 'pensum.program_name', 's.subject_name as prerequisite')
            ->join('pensum', 'pensum_subject_detail.pensum_id', '=', 'pensum.id')
            ->join('subject', 'pensum_subject_detail.subject_id', '=', 'subject.id')
            ->leftJoin('prerequisite', 'pensum_subject_detail.id', '=', 'prerequisite.pensum_subject_detail_id')
            ->leftjoin('subject as s', 'prerequisite.subject_id', '=', 's.id')
            ->whereNull('prerequisite.pensum_subject_detail_id')
            ->whereNull('prerequisite.deleted_at')
            ->orWhere('pensum_subject_detail.subject_id', 'like', $search)

            ->skip($skip)
            ->take($itemsPerpage)
            ->orderBy("pensum_subject_detail.$sortBy", $sort)
            ->get();
    }

    public static function counterPagination($search)
    {
        return PensumSubjectDetail::select('pensum_subject_detail.*', 'subject.subject_name', 'pensum.program_name')
            ->join('pensum', 'pensum_subject_detail.pensum_id', '=', 'pensum.id')
            ->join('subject', 'pensum_subject_detail.subject_id', '=', 'subject.id')
            ->orWhere('pensum_subject_detail.subject_id', 'like', $search)
            ->count();
    }
}
