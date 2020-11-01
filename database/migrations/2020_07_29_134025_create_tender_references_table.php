<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTenderReferencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tender_references', function (Blueprint $table) {
            $table->id();
            $table->string('tender_number', 32);
            $table->string('ref_type', 32)->nullable(true);
            $table->string('ref_value')->nullable(true);
            $table->smallInteger('submission_method')->nullable(true);

            $table->string('created_by')->nullable(true);
            $table->string('updated_by')->nullable(true);
            $table->timestamps();

            $table->index(array('tender_number'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tender_references');
    }
}
