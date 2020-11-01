<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRefSysParamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->down();
        Schema::create('ref_sys_params', function (Blueprint $table) {
            $table->id();
            $table->string('name',64);
            $table->string('value1',64)->nullable();
            $table->string('value2',64)->nullable();
            $table->string('comment')->nullable();

            $table->unique('name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ref_sys_params');
        DB::statement('DROP SEQUENCE IF EXISTS ref_sys_params_id_seq');
    }
}
