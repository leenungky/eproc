<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateConditionalTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->down();
        Schema::create('conditional_types', function (Blueprint $table) {
            $table->id();

            $table->string('type', 6)->nullable(true);
            $table->string('description', 64)->nullable(true);
            $table->smallInteger('calculation_type')->nullable(true);
            $table->smallInteger('calculation_pos')->nullable(true);
            $table->integer('order')->nullable(true);
            $table->string('created_by')->nullable(true);
            $table->string('updated_by')->nullable(true);
            $table->string('deleted_by')->nullable(true);
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
        Schema::dropIfExists('conditional_types');
        DB::statement('DROP SEQUENCE IF EXISTS conditional_types_id_seq');
    }
}
