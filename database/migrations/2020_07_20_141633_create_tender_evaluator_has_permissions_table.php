<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTenderEvaluatorHasPermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tender_evaluator_has_permissions', function (Blueprint $table) {
            $table->bigInteger('permission_id');
            $table->integer('evaluator_id');
        });
        Schema::table('tender_evaluators', function (Blueprint $table) {
            $table->dropColumn('buyer_type_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tender_evaluator_has_permissions');
        Schema::table('tender_evaluators', function (Blueprint $table) {
            $table->integer('buyer_type_id')->nullable(true);
        });
    }
}
