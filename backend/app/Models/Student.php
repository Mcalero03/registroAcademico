<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
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
    ];

    public $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function municipality()
    {
        return $this->belongsTo(Municipality::class, 'municipalities_id')->withDefault();
    }

    public function format()
    {
        return [
            // 'id' => Encrypt::encryptValue($this->id),
            'id' => $this->id,
            'name' => $this->name,
            'last_name' => $this->last_name,
            'age' => $this->age,
            'card' => $this->card,
            'nie' => $this->nie,
            'phone_number' => $this->phone_number,
            'mail' => $this->mail,
            'admission_date' => $this->admission_date,
            'municipality_name' => $this->municipality->municipality_name . ', ' . $this->municipality->department->department_name,
        ];
    }

    public static function allDataSearched($search, $sortBy, $sort, $skip, $itemsPerpage)
    {
        return Student::select(DB::raw("CONCAT(municipalities.municipality_name, ', ', department.department_name) as municipality_name"), 'student.*')
            ->join('municipalities', 'student.municipalities_id', '=', 'municipalities.id')
            ->join('department', 'municipalities.department_id', '=', 'department.id')
            ->where('student.name', 'like', $search)
            ->orWhere('student.last_name', 'like', $search)
            ->orWhere('student.age', 'like', $search)
            ->orWhere('student.card', 'like', $search)
            ->orWhere('student.nie', 'like', $search)
            ->orWhere('student.phone_number', 'like', $search)
            ->orWhere('student.mail', 'like', $search)
            ->orWhere('student.admission_date', 'like', $search)
            ->orWhere('municipalities.municipality_name', 'like', $search)


            ->skip($skip)
            ->take($itemsPerpage)
            ->orderBy("student.$sortBy", $sort)
            ->get();
        // ->map(fn ($student) => $student->format());
    }

    public static function counterPagination($search)
    {
        return Student::select(DB::raw("CONCAT(municipalities.municipality_name, ', ', department.department_name) as municipality_name"), 'student.*', 'municipalities.municipality_name')
            ->join('municipalities', 'student.municipalities_id', '=', 'municipalities.id')
            ->join('department', 'municipalities.department_id', '=', 'department.id')
            ->where('student.name', 'like', $search)
            ->orWhere('student.last_name', 'like', $search)
            ->orWhere('student.age', 'like', $search)
            ->orWhere('student.card', 'like', $search)
            ->orWhere('student.nie', 'like', $search)
            ->orWhere('student.phone_number', 'like', $search)
            ->orWhere('student.mail', 'like', $search)
            ->orWhere('student.admission_date', 'like', $search)
            ->orWhere('municipalities.municipality_name', 'like', $search)

            ->count();
    }

    public static function municipalityId($municipality, $department)
    {
        return Municipality::select('municipalities.id')
            ->join('department', 'municipalities.department_id', '=', 'department.id')
            ->where('municipalities.municipality_name', $municipality)
            ->where('department.department_name', $department)
            ->get('municipalities.id');
    }

    public static function clean($string)
    {
        $change = str_replace('[', '', $string);
        $change = str_replace(']', '', $change);
        return $change;
    }
}
