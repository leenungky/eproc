<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRefProvincesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->down();
        Schema::create('ref_provinces', function (Blueprint $table) {
            // main columns
            $table->string('country_code')->nullable(false);       
            $table->string('region_code')->nullable(false);
            $table->string('region_description')->nullable(false);          
            
            // end main
            $table->string('created_by')->nullable(false)->default('initial')->comment('Define row who user created');
            $table->timestamps(); // created_at, updated_at
            $table->softDeletes(); // deleted_at

            $table->unique(['region_code','country_code']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ref_provinces');
        DB::statement('DROP SEQUENCE IF EXISTS ref_provinces_id_seq');
    }
}
