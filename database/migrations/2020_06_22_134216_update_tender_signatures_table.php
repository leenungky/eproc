<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTenderSignaturesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tender_signatures', function (Blueprint $table) {
            $table->dropColumn('position_id');
            $table->integer('order')->nullable(true);
            $table->string('status')->nullable(true)->default('draft');
            $table->text('notes')->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tender_signatures', function (Blueprint $table) {
            $table->integer('position_id')->nullable(true);
            $table->dropColumn('order');
            $table->dropColumn('status');
            $table->dropColumn('notes');
        });
    }
}
