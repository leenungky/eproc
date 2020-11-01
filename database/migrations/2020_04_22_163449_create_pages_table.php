<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->down();
        Schema::create('pages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 64)->nullable(false);
            $table->bigInteger('parent_id')->default(0);
            $table->string('type', 8)->nullable(false);

            $table->string('created_by')->nullable(true);
            $table->string('updated_by')->nullable(true);
            $table->string('deleted_by')->nullable(true);
            $table->timestamps();
            $table->softDeletes();
            
            $table->unique(array('name'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pages');
        DB::statement('DROP SEQUENCE IF EXISTS pages_id_seq');
    }
}
