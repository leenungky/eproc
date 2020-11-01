<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserExtensionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->down();
        Schema::create('user_extensions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->nullable(true);
            $table->smallInteger('status')->nullable(true);
            $table->string('position')->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_extensions');
        DB::statement('DROP SEQUENCE IF EXISTS user_extensions_id_seq');
    }
}
