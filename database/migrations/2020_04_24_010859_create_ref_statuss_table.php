<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRefStatussTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->down();
        Schema::create('ref_statuss', function (Blueprint $table) {
            $table->increments('id');
            $table->string('status')->nullable(false)->unique();
            $table->string('description', 255)->nullable(true);
            $table->boolean('deleteflg')->default(false)->comment('Define row active');
            $table->timestamp('created_at', 0)->nullable(false)->default(now())->comment('Define time when row has been created');
            $table->string('created_by')->nullable(false)->default('initial')->comment('Define row who user created');
            $table->timestamp('updated_at', 0)->nullable(true)->comment('Define time when row has been updated');
            $table->string('updated_by')->nullable(true)->comment('Define row who user updated');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('DROP table IF EXISTS ref_statuss cascade');
        DB::statement('DROP SEQUENCE IF EXISTS ref_statuss_id_seq');
    }
}
