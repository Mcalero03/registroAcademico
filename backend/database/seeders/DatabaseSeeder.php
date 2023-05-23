<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\StudentPensumDetail;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $this->call([
            DepartmentSeeder::class,
            MunicipalitiesSeeder::class,
            SchoolSeeder::class,
            Pensum_TypeSeeder::class,
            ClassroomSeeder::class,
            SubSchoolSeeder::class,
            PensumSeeder::class,
            SubjectSeeder::class,
            Pensum_Subject_DetailSeeder::class,
            PrerequisiteSeeder::class,
            TeacherSeeder::class,
            EvaluationSeeder::class,
            CycleSeeder::class,
            CycleSubjectDetailSeeder::class,
            GroupSeeder::class,
            ScheduleSeeder::class,
            KinshipSeeder::class,
            StudentSeeder::class,
            RelativeSeeder::class,
            InscriptionSeeder::class,
            InscriptionDetailSeeder::class,
            AttendanceSeeder::class,
            Attendance_DetailSeeder::class,
            CalificationSeeder::class,
            ScheduleClassroomGroupDetailSeeder::class,
            StudentPensumDetailSeeder::class,
        ]);
    }
}
