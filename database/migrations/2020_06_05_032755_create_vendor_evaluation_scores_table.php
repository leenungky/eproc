<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendorEvaluationScoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->down();
        Schema::create('vendor_evaluation_scores', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('category_id');
            $table->string('name',32);
            $table->string('lowest_score_operator',4);
            $table->integer('lowest_score');
            $table->string('highest_score_operator',4);
            $table->integer('highest_score');
            $table->timestamps();

            $table->unique(array('category_id','name'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vendor_evaluation_scores');
        DB::statement('DROP SEQUENCE IF EXISTS vendor_evaluation_scores_id_seq');
    }
}
