<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendorEvaluationScoreCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->down();
        Schema::create('vendor_evaluation_score_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name',32);
            $table->text('categories_json')->nullable(true);
            $table->integer('po_count')->default(0);
            $table->decimal('po_total',18,2)->default(0);
            $table->string('created_by',64)->nullable(true);
            $table->string('updated_by',64)->nullable(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vendor_evaluation_score_categories');
        DB::statement('DROP SEQUENCE IF EXISTS vendor_evaluation_score_categories_id_seq');
    }
}
