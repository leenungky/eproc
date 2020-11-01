<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTenderConfigApproversTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->down();
        Schema::create('tender_config_approvers', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('purch_org_id');
            $table->integer('role_id');
            $table->integer('order');
            $table->string('created_by')->nullable(true);
            $table->string('updated_by')->nullable(true);
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
        Schema::dropIfExists('tender_config_approvers');
        DB::statement('DROP SEQUENCE IF EXISTS tender_config_approvers_id_seq');
    }
}
