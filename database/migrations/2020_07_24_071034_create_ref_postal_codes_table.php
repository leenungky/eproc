<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRefPostalCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ref_postal_codes', function (Blueprint $table) {
            $table->string('country_code',5);
            $table->integer('length')->default(0);
            $table->boolean('required')->default(false);
            $table->string('check_rule',5)->nullable(true);

            $table->primary('country_code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ref_postal_codes');
    }
}
