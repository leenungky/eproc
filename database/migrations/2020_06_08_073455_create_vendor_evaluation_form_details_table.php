<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendorEvaluationFormDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->down();
        Schema::create('vendor_evaluation_form_details', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('vendor_evaluation_id'); // FK
            $table->bigInteger('criteria_id'); // FK
            $table->bigInteger('vendor_id'); // FK
            $table->integer('score');
            $table->timestamps();

            $table->unique(['vendor_evaluation_id','vendor_id','criteria_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vendor_evaluation_form_details');
        DB::statement('DROP SEQUENCE IF EXISTS vendor_evaluation_form_details_id_seq');
    }
}
