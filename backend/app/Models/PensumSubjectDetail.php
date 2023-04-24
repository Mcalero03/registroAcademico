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

    public function pensum()
    {
        return $this->belongsTo(Pensum::class, 'pensum_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function format()
    {
        return [
            'id' => Encrypt::encryptValue($this->id),
            'program_name' => $this->pensum->program_name,
            'subject_name' => $this->subject->subject_name,
        ];
    }

    public static function allDataSearched($search, $sortBy, $sort, $skip, $itemsPerpage)
    {
        return PensumSubjectDetail::where('pensum_subject_detail.pensum_id', 'like', $search)
            ->orWhere('pensum_subject_detail.subject_id', 'like', $search)

            ->skip($skip)
            ->take($itemsPerpage)
            ->orderBy("pensum_subject_detail.$sortBy", $sort)
            ->get()
            ->map(fn ($pensum_subject_detail) => $pensum_subject_detail->format());
    }

    public static function counterPagination($search)
    {
        return PensumSubjectDetail::select('pensum_subject_detail.*', 'pensum_subject_detail.id as id')

            ->where('pensum_subject_detail.pensum_id', 'like', $search)
            ->orWhere('pensum_subject_detail.subject_id', 'like', $search)

            ->count();
    }
}
