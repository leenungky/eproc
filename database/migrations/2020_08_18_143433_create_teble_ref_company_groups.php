<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTebleRefCompanyGroups extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ref_company_groups', function (Blueprint $table) {
            $table->string('id', 20); // Auto increament
            $table->string('name',64); 
            $table->string('description')->nullable(); // FK
            $table->string('last_number',64);
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
        Schema::dropIfExists('ref_company_groups');
    }
}
