<?php

namespace Database\Seeders;

use App\Models\ScheduleClassroomGroupDetail;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ScheduleClassroomGroupDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ScheduleClassroomGroupDetail::insert([
            [
                "id" => 1,
                "schedule_id" => 1,
                "classroom_id" => 1,
                "group_id" => 1,
                "cycle_id" => 1,
            ],
            [
                "id" => 2,
                "schedule_id" => 2,
                "classroom_id" => 1,
                "group_id" => 1,
                "cycle_id" => 1,
            ],
        ]);
    }
}
