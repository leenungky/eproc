<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRefBanksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->down();
        Schema::create('ref_banks', function (Blueprint $table) {
            $table->id();
            $table->string('bank_key',32)->nullable(false);          
            $table->string('country_code',8)->nullable(false);
            $table->string('description')->nullable(true);          
            $table->string('deleteflg',1)->nullable(true);          
            $table->string('created_by')->nullable(true);
            $table->string('updated_by')->nullable(true);
            $table->timestamps();

            $table->unique(['bank_key','country_code']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ref_banks');
        DB::statement('DROP SEQUENCE IF EXISTS ref_banks_id_seq');
    }
}
