<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
        'college_id',
        'pensum_type_id	',
    ];

    public $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}
