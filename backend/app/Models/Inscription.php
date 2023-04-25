<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Encrypt;

class Inscription extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'inscription';

    public $incrementing = true;

    protected $data = ['deleted_at'];

    protected $fillable = [
        'id',
        'inscription_date',
        'subject_average',
        'attendance_quantity',
        'status',
        'cycle_id',
        'student_id',
        'group_id',
        'subject_id',
    ];

    public $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function cycle()
    {
        return $this->belongsTo(Cycle::class, 'cycle_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function format()
    {

        return [
            'id' => Encrypt::encryptValue($this->id),
            'inscription_date' => $this->inscription_date,
            'subject_average' => $this->subject_average,
            'attendance_quantity' => $this->attendance_quantity,
            'status' => $this->status,
            'cycle_number' => $this->cycle->cycle_number,
            'student_name' => $this->student->name,
            'group_name' => $this->group->group_name,
            'subject_name' => $this->subject->subject_name,
        ];
    }

    public static function allDataSearched($search, $sortBy, $sort, $skip, $itemsPerpage)
    {
        return Inscription::select('inscription.*', 'inscription.id as id')

            ->where('inscription.inscription_date', 'like', $search)
            ->orWhere('inscription.subject_average', 'like', $search)
            ->orWhere('inscription.attendance_quantity', 'like', $search)
            ->orWhere('inscription.status', 'like', $search)
            ->orWhere('inscription.cycle_id', 'like', $search)
            ->orWhere('inscription.student_id', 'like', $search)
            ->orWhere('inscription.group_id', 'like', $search)
            ->orWhere('inscription.subject_id', 'like', $search)

            ->skip($skip)
            ->take($itemsPerpage)
            ->orderBy("inscription.$sortBy", $sort)
            ->get()
            ->map(fn ($inscription) => $inscription->format());
    }

    public static function counterPagination($search)
    {
        return Inscription::select('inscription.*', 'inscription.id as id')

            ->where('inscription.inscription_date', 'like', $search)
            ->orWhere('inscription.subject_average', 'like', $search)
            ->orWhere('inscription.attendance_quantity', 'like', $search)
            ->orWhere('inscription.status', 'like', $search)
            ->orWhere('inscription.cycle_id', 'like', $search)
            ->orWhere('inscription.student_id', 'like', $search)
            ->orWhere('inscription.group_id', 'like', $search)
            ->orWhere('inscription.subject_id', 'like', $search)

            ->count();
    }
}
