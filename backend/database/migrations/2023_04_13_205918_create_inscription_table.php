<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inscription', function (Blueprint $table) {
            $table->id();
            $table->date('inscription_date');
            $table->float('subject_average', 10, 0);
            $table->integer('attendance_quantity');
            $table->string('status', 100);
            $table->foreignId('cycle_id')->constrained('cycle');
            $table->foreignId('student_id')->constrained('student');
            $table->foreignId('group_id')->constrained('group');
            $table->foreignId('subject_id')->constrained('subject');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inscription');
    }
};
