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
        Schema::create('relative', function (Blueprint $table) {
            $table->id();
            $table->string('relationship', 45);
            $table->string('name');
            $table->string('last_name');
            $table->integer('dui');
            $table->integer('phone_number');
            $table->string('mail');
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
        Schema::dropIfExists('relative');
    }
};
