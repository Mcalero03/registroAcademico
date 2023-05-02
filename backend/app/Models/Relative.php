<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Encrypt;

class Relative extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'relative';

    public  $incrementing = true;

    protected $data = ['deleted_at'];

    protected $fillable = [
        'id',
        'relationship',
        'name',
        'last_name',
        'dui',
        'phone_number',
        'mail',
    ];

    public $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function format()
    {
        return [
            'id' => Encrypt::encryptValue($this->id),
            'full_name' => $this->name . ', ' . $this->last_name,
            'relationship' => $this->relationship,
            'name' => $this->name,
            'last_name' => $this->last_name,
            'dui' => $this->dui,
            'phone_number' => $this->phone_number,
            'mail' => $this->mail,
        ];
    }

    public static function allDataSearched($search, $sortBy, $sort, $skip, $itemsPerpage)
    {
        return Relative::select('relative.*', 'relative.id as id')

            ->where('relative.relationship', 'like', $search)
            ->orWhere('relative.name', 'like', $search)
            ->orWhere('relative.last_name', 'like', $search)
            ->orWhere('relative.dui', 'like', $search)
            ->orWhere('relative.phone_number', 'like', $search)
            ->orWhere('relative.mail', 'like', $search)

            ->skip($skip)
            ->take($itemsPerpage)
            ->orderBy("relative.$sortBy", $sort)
            ->get()
            ->map(fn ($relative) => $relative->format());
    }

    public static function counterPagination($search)
    {
        return Relative::select('relative.*', 'relative.id as id')

            ->where('relative.relationship', 'like', $search)
            ->orWhere('relative.name', 'like', $search)
            ->orWhere('relative.last_name', 'like', $search)
            ->orWhere('relative.dui', 'like', $search)
            ->orWhere('relative.phone_number', 'like', $search)
            ->orWhere('relative.mail', 'like', $search)

            ->count();
    }
}
