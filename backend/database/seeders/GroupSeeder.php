<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Group;

class GroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Group::insert([
            [
                "id" => 1,
                "group_name" => "COMPMU01",
                "students_quantity" => 2,
            ],
            [
                "id" => 2,
                "group_name" => "SENMO01",
                "students_quantity" => 2,
            ],
            [
                "id" => 3,
                "group_name" => "ARES01",
                "students_quantity" => 2,
            ],
            [
                "id" => 4,
                "group_name" => "DRAM02",
                "students_quantity" => 2,
            ],
            [
                "id" => 5,
                "group_name" => "TECDA02",
                "students_quantity" => 2,
            ],
            [
                "id" => 6,
                "group_name" => "COMCO02",
                "students_quantity" => 2,
            ],
        ]);
    }
}
