<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Encrypt;

class Evaluation extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'evaluation';

    public  $incrementing = true;

    protected $data = ['deleted_at'];

    protected $fillable = [
        'id',
        'evaluation_name',
        'ponder',
        'subject_id',
    ];

    public $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function format()
    {
        return [
            'id' => Encrypt::encryptValue($this->id),
            'evaluation_name' => $this->evaluation_name,
            'ponder' => $this->ponder,
            'subject_name' => $this->subject->subject_name,
        ];
    }

    public static function allDataSearched($search, $sortBy, $sort, $skip, $itemsPerpage)
    {
        return Evaluation::where('evaluation.evaluation_name', 'like', $search)
            ->orWhere('evaluation.ponder', 'like', $search)
            ->orWhere('evaluation.subject_id', 'like', $search)
            ->skip($skip)
            ->take($itemsPerpage)
            ->orderBy("evaluation.$sortBy", $sort)
            ->get()
            ->map(fn ($evaluation) => $evaluation->format());
    }

    public static function counterPagination($search)
    {
        return Evaluation::select('evaluation.*', 'evaluation.id as id')

            ->where('evaluation.evaluation_name', 'like', $search)
            ->orWhere('evaluation.ponder', 'like', $search)
            ->orWhere('evaluation.subject_id', 'like', $search)

            ->count();
    }
}
