<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InscriptionDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'inscription_detail';

    public $incrementing = true;

    protected $data = ['deleted_at'];

    protected $fillable = [
        'id',
        'status',
        'inscription_id',
        'group_id',
    ];

    public $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}
