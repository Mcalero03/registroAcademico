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
        Schema::create('teacher_subject_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained('subject');
            $table->foreignId('teacher_id')->constrained('teacher');
            $table->foreignId('group_id')->constrained('group');
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
        Schema::dropIfExists('teacher_subject_detail');
    }
};
