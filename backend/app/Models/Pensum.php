<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Encrypt;

class Pensum extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pensum';

    public $incrementing = true;

    protected $data = ['deleted_at'];

    protected $fillable = [
        'id',
        'program_name',
        'uv_total',
        'required_subject',
        'optional_subject',
        'cycle_quantity',
        'study_plan_year',
        'sub_school_id',
        'pensum_type_id	',
    ];

    public $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function subschool()
    {
        return $this->belongsTo(SubSchool::class, 'sub_school_id');
    }

    public function pensum_type()
    {
        return $this->belongsTo(PensumType::class, 'pensum_type_id');
    }

    public function format()
    {
        return [
            'id' => Encrypt::encryptValue($this->id),
            'program_name' => $this->program_name,
            'uv_total' => $this->uv_total,
            'required_subject' => $this->required_subject,
            'optional_subject' => $this->optional_subject,
            'cycle_quantity' => $this->cycle_quantity,
            'study_plan_year' => $this->study_plan_year,
            'sub_school_name' => $this->subschool->sub_school_name,
            'pensum_type_name' => $this->pensum_type->pensum_type_name,
        ];
    }

    public static function allDataSearched($search, $sortBy, $sort, $skip, $itemsPerpage)
    {
        return Pensum::where('pensum.program_name', 'like', $search)
            ->orWhere('pensum.uv_total', 'like', $search)
            ->orWhere('pensum.required_subject', 'like', $search)
            ->orWhere('pensum.optional_subject', 'like', $search)
            ->orWhere('pensum.cycle_quantity', 'like', $search)
            ->orWhere('pensum.study_plan_year', 'like', $search)
            ->orWhere('pensum.sub_school_id', 'like', $search)
            ->orWhere('pensum.pensum_type_id', 'like', $search)
            ->skip($skip)
            ->take($itemsPerpage)
            ->orderBy("pensum.$sortBy", $sort)
            ->get()
            ->map(fn ($pensum) => $pensum->format());
    }

    public static function counterPagination($search)
    {
        return Pensum::select('pensum.*', 'pensum.id as id')

            ->where('pensum.program_name', 'like', $search)
            ->orWhere('pensum.uv_total', 'like', $search)
            ->orWhere('pensum.required_subject', 'like', $search)
            ->orWhere('pensum.optional_subject', 'like', $search)
            ->orWhere('pensum.cycle_quantity', 'like', $search)
            ->orWhere('pensum.study_plan_year', 'like', $search)
            ->orWhere('pensum.sub_school_id', 'like', $search)
            ->orWhere('pensum.pensum_type_id', 'like', $search)

            ->count();
    }
}
