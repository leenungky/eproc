<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApplicantDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->down();
        Schema::create('applicant_details', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('applicant_id')->nullable(false)->unsigned();
            $table->integer('status_id')->nullable(false)->unsigned();
            $table->string('remarks', 200)->nullable(true);
            $table->boolean('statusflg')->default(false)->comment('Define row status active');
            $table->timestamp('created_at', 0)->nullable(false)->default(now())->comment('Define time when row has been created');
            $table->string('created_by')->nullable(false)->default('initial')->comment('Define row who user created');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('applicant_details');
        DB::statement('DROP SEQUENCE IF EXISTS applicant_details_id_seq');
    }
}
