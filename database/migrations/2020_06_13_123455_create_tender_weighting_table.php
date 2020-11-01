<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTenderWeightingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->down();
        Schema::create('tender_weightings', function (Blueprint $table) {
            $table->id();

            $table->string('tender_number', 32)->nullable(true);
            $table->string('criteria', 50)->nullable(true);
            $table->integer('submission_method')->nullable(true);
            $table->float('weight')->nullable(true);
            $table->boolean('is_commercial')->default(false);
            $table->integer('order')->nullable(true);
            $table->string('created_by')->nullable(true);
            $table->string('updated_by')->nullable(true);
            $table->string('deleted_by')->nullable(true);
            $table->timestamps();
            $table->softDeletes();

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
        Schema::dropIfExists('tender_weightings');
        DB::statement('DROP SEQUENCE IF EXISTS tender_weightings_id_seq');
    }
}
