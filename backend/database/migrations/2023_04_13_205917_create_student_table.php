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
        Schema::create('student', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('last_name');
            $table->integer('age');
            $table->string('card', 45);
            $table->integer('nie');
            $table->integer('phone_number');
            $table->string('mail');
            $table->date('admission_date');
            $table->foreignId('municipalities_id')->constrained('municipalities');
            $table->foreignId('relative_id')->constrained('relative');
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
        Schema::dropIfExists('student');
    }
};
