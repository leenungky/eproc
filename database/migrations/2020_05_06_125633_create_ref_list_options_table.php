<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRefListOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->down();
        Schema::create('ref_list_options', function (Blueprint $table) {
            $table->id();
            $table->string('type', 64);
            $table->string('key', 64);
            $table->string('value', 64);
            $table->boolean('deleteflg')->default(false);

            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ref_list_options');
        DB::statement('DROP SEQUENCE IF EXISTS ref_list_options_id_seq');
    }
}
