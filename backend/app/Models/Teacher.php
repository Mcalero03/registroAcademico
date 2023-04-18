<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Teacher extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'teacher';

    public  $incrementing = true;

    protected $data = ['deleted_at'];

    protected $fillable = [
        'id',
        'name',
        'last_name',
        'card',
        'dui',
        'nit',
        'phone_number',
        'mail',
    ];

    public $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}
