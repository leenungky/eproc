<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTenderLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->down();
        Schema::create('tender_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('user_id')->nullable();
            $table->string('activity', 25);
            $table->unsignedInteger('model_id')->nullable();
            $table->string('model_type')->nullable();
            $table->string('page_type')->nullable();
            $table->string('ref_number', 32);
            $table->text('properties')->nullable();
            $table->string('host', 45)->nullable();
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
        Schema::dropIfExists('tender_logs');
        DB::statement('DROP SEQUENCE IF EXISTS tender_logs_id_seq');
    }
}
