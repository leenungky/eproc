<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRefProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->down();
        Schema::create('ref_projects', function (Blueprint $table) {
            $table->string('code',32)->primary();
            $table->string('name')->nullable(true);
            $table->string('company_code',16)->nullable(true);
            $table->string('plant',16)->nullable(true);
            $table->string('purchasing_org',8)->nullable(true);
            $table->date('start_date')->nullable(true);
            $table->date('finish_date')->nullable(true);
            $table->string('system_status',32)->nullable(true);
            $table->string('user_status',32)->nullable(true);
            $table->string('created_by')->nullable(true);
            $table->string('updated_by')->nullable(true);

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['company_code','code']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ref_projects');
    }
}
