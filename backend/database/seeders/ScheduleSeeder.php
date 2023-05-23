<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Schedule;

class ScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schedule::insert([
            [
                "id" => 1,
                "week_day" => "Lunes",
                "start_time" => "07:00:00",
                "end_time" => "09:00:00",
            ],
            [
                "id" => 2,
                "week_day" => "Miércoles",
                "start_time" => "07:00:00",
                "end_time" => "09:00:00",
            ],
            [
                "id" => 3,
                "week_day" => "Martes",
                "start_time" => "07:00:00",
                "end_time" => "09:00:00",
            ],
            [
                "id" => 4,
                "week_day" => "Miércoles",
                "start_time" => "09:30:00",
                "end_time" => "11:30:00",
            ],
            [
                "id" => 5,
                "week_day" => "Lunes",
                "start_time" => "09:30:00",
                "end_time" => "11:30:00",
            ],
            [
                "id" => 6,
                "week_day" => "Martes",
                "start_time" => "09:30:00",
                "end_time" => "11:30:00",
            ],
            [
                "id" => 7,
                "week_day" => "Jueves",
                "start_time" => "07:00:00",
                "end_time" => "09:00:00",
            ],
            [
                "id" => 8,
                "week_day" => "Viernes",
                "start_time" => "09:30:00",
                "end_time" => "11:30:00",
            ],
            [
                "id" => 9,
                "week_day" => "Jueves",
                "start_time" => "09:30:00",
                "end_time" => "11:30:00",
            ],
            [
                "id" => 10,
                "week_day" => "Viernes",
                "start_time" => "07:00:00",
                "end_time" => "09:00:00",
            ],
            [
                "id" => 11,
                "week_day" => "Lunes",
                "start_time" => "13:00:00",
                "end_time" => "15:00:00",
            ],
            [
                "id" => 12,
                "week_day" => "Viernes",
                "start_time" => "13:00:00",
                "end_time" => "15:00:00",
            ],

        ]);
    }
}