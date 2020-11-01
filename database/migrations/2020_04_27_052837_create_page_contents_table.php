<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePageContentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->down();
        Schema::create('page_contents', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->bigInteger('page_id')->nullable(false);
                $table->string('language', 64)->nullable(false)->default('en');
                $table->string('title')->nullable(true);
                $table->text('content')->nullable(true);
                
                $table->string('created_by')->nullable(true);
                $table->string('updated_by')->nullable(true);
                $table->string('deleted_by')->nullable(true);
                $table->timestamps();
                $table->softDeletes();
                    
                $table->unique(array('page_id','language'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('page_contents');
        DB::statement('DROP SEQUENCE IF EXISTS page_contents_id_seq');
    }
}
