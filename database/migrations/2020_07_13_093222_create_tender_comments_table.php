<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTenderCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tender_comments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('tender_number', 32)->nullable(true);
            $table->string('user_id_from')->nullable();
            $table->string('user_id_to')->nullable();
            $table->string('from_name')->nullable();
            $table->string('to_name')->nullable();
            $table->string('comments');
            $table->smallInteger('submission_method')->nullable(true);
            $table->smallInteger('status')->nullable();
            $table->string('created_by')->nullable(true);
            $table->string('updated_by')->nullable(true);
            $table->string('deleted_by')->nullable(true);
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
        Schema::dropIfExists('tender_comments');
    }
}
