<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Encrypt;

class Schedule extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'schedule';

    public  $incrementing = true;

    protected $data = ['deleted_at'];

    protected $fillable = [
        'id',
        'week_day',
        'start_time',
        'end_time',
        'group_id',
    ];

    public $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    public function format()
    {
        return [
            'id' => Encrypt::encryptValue($this->id),
            'week_day' => $this->week_day,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'group_name' => $this->group->group_name,
        ];
    }

    public static function allDataSearched($search, $sortBy, $sort, $skip, $itemsPerpage)
    {
        return Schedule::select('schedule.*', 'schedule.id as id')

            ->where('schedule.week_day', 'like', $search)
            ->orWhere('schedule.start_time', 'like', $search)
            ->orWhere('schedule.end_time', 'like', $search)
            ->orWhere('schedule.group_id', 'like', $search)

            ->skip($skip)
            ->take($itemsPerpage)
            ->orderBy("schedule.$sortBy", $sort)
            ->get()
            ->map(fn ($schedule) => $schedule->format());
    }

    public static function counterPagination($search)
    {
        return Schedule::select('schedule.*', 'schedule.id as id')

            ->where('schedule.week_day', 'like', $search)
            ->orWhere('schedule.start_time', 'like', $search)
            ->orWhere('schedule.end_time', 'like', $search)
            ->orWhere('schedule.group_id', 'like', $search)

            ->count();
    }
}
