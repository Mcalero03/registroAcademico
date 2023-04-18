<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
}
