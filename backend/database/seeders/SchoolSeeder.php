<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\School;

class SchoolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        School::insert([
            [
                "id" => 1,
                "school_name" => "Coro"
            ],
            [
                "id" => 2,
                "school_name" => "Danza"
            ],

        ]);
    }
}
