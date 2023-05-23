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
        Schema::create('cycle', function (Blueprint $table) {
            $table->id();
            $table->integer('cycle_number');
            $table->integer('year');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('status', 100)->comment('Creado', 'Abierto', 'En proceso', 'Finalizado',);
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
        Schema::dropIfExists('cycle');
    }
};
