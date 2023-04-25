<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Encrypt;

class Student extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'student';

    public  $incrementing = true;

    protected $data = ['deleted_at'];

    protected $fillable = [
        'id',
        'name',
        'last_name',
        'age',
        'card',
        'nie',
        'phone_number',
        'mail',
        'admission_date',
        'municipalities_id',
        'relative_id',
    ];

    public $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function municipality()
    {
        return $this->belongsTo(Municipality::class, 'municipalities_id');
    }

    public function relative()
    {
        return $this->belongsTo(Relative::class, 'relative_id');
    }

    public function format()
    {
        return [
            'id' => Encrypt::encryptValue($this->id),
            'name' => $this->name,
            'last_name' => $this->last_name,
            'age' => $this->age,
            'card' => $this->card,
            'nie' => $this->nie,
            'phone_number' => $this->phone_number,
            'mail' => $this->mail,
            'admission_date' => $this->admission_date,
            'municipality_name' => $this->municipality->municipality_name,
            'relative_name' => $this->relative->name,
        ];
    }

    public static function allDataSearched($search, $sortBy, $sort, $skip, $itemsPerpage)
    {
        return Student::select('student.*', 'student.id as id')

            ->where('student.name', 'like', $search)
            ->orWhere('student.last_name', 'like', $search)
            ->orWhere('student.age', 'like', $search)
            ->orWhere('student.card', 'like', $search)
            ->orWhere('student.nie', 'like', $search)
            ->orWhere('student.phone_number', 'like', $search)
            ->orWhere('student.mail', 'like', $search)
            ->orWhere('student.admission_date', 'like', $search)
            ->orWhere('student.municipalities_id', 'like', $search)
            ->orWhere('student.relative_id', 'like', $search)

            ->skip($skip)
            ->take($itemsPerpage)
            ->orderBy("student.$sortBy", $sort)
            ->get()
            ->map(fn ($student) => $student->format());
    }

    public static function counterPagination($search)
    {
        return Student::select('student.*', 'student.id as id')

            ->where('student.name', 'like', $search)
            ->orWhere('student.last_name', 'like', $search)
            ->orWhere('student.age', 'like', $search)
            ->orWhere('student.card', 'like', $search)
            ->orWhere('student.nie', 'like', $search)
            ->orWhere('student.phone_number', 'like', $search)
            ->orWhere('student.mail', 'like', $search)
            ->orWhere('student.admission_date', 'like', $search)
            ->orWhere('student.municipalities_id', 'like', $search)
            ->orWhere('student.relative_id', 'like', $search)

            ->count();
    }
}
