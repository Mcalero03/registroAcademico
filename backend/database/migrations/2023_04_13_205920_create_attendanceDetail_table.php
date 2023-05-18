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
        Schema::create('attendance_detail', function (Blueprint $table) {
            $table->id();
            $table->string('status', 100);
            $table->foreignId('inscription_id')->constrained('inscription');
            $table->foreignId('attendance_id')->constrained('attendance');
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
        Schema::dropIfExists('attendance_detail');
    }
};
