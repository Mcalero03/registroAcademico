<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pensum_Type extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pensum_type';

    public $incrementing = true;

    protected $data = ['deleted_at'];

    protected $fillable = [
        'id',
        'pensum_type_name'
    ];

    public $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}
