<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Attendance_Detail;

class Attendance_DetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Attendance_Detail::insert([
            [
                "id" => 1,
                "status" => "Asistió",
                "inscription_id" => 1,
                "attendance_id" => 1,
            ],
            // [
            //     "id" => 2,
            //     "attendance_date" => "2023-01-16",
            //     "attendance_time" => "07:31:00",
            //     "status" => "Asistencia",
            //     "inscription_id" => 2,
            // ],
            // [
            //     "id" => 3,
            //     "attendance_date" => "2023-01-18",
            //     "attendance_time" => "07:15:00",
            //     "status" => "Asistencia",
            //     "inscription_id" => 3,
            // ],
            // [
            //     "id" => 4,
            //     "attendance_date" => "2023-01-18",
            //     "attendance_time" => "07:16:00",
            //     "status" => "Asistencia",
            //     "inscription_id" => 4,
            // ],
            // [
            //     "id" => 5,
            //     "attendance_date" => "2023-01-16",
            //     "attendance_time" => "09:32:00",
            //     "status" => "Asistencia",
            //     "inscription_id" => 5,
            // ],
            // [
            //     "id" => 6,
            //     "attendance_date" => "2023-01-16",
            //     "attendance_time" => "09:36:00",
            //     "status" => "Asistencia",
            //     "inscription_id" => 6,
            // ],
            // [
            //     "id" => 7,
            //     "attendance_date" => "2023-07-16",
            //     "attendance_time" => "07:30:00",
            //     "status" => "Asistencia",
            //     "inscription_id" => 7,
            // ],
            // [
            //     "id" => 8,
            //     "attendance_date" => "2023-07-16",
            //     "attendance_time" => "07:32:00",
            //     "status" => "Asistencia",
            //     "inscription_id" => 8,
            // ],
            // [
            //     "id" => 9,
            //     "attendance_date" => "2023-07-19",
            //     "attendance_time" => "09:36:00",
            //     "status" => "Asistencia",
            //     "inscription_id" => 9,
            // ],
            // [
            //     "id" => 10,
            //     "attendance_date" => "2023-07-19",
            //     "attendance_time" => "09:40:00",
            //     "status" => "Asistencia",
            //     "inscription_id" => 10,
            // ],
            // [
            //     "id" => 11,
            //     "attendance_date" => "2023-07-16",
            //     "attendance_time" => "13:10:00",
            //     "status" => "Asistencia",
            //     "inscription_id" => 11,
            // ],
            // [
            //     "id" => 12,
            //     "attendance_date" => "2023-07-16",
            //     "attendance_time" => "13:16:00",
            //     "status" => "Asistencia",
            //     "inscription_id" => 12,
            // ],
        ]);
    }
}
