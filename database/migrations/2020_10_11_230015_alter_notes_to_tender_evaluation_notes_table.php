<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterNotesToTenderEvaluationNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tender_evaluation_notes', function (Blueprint $table) {
            DB::statement('ALTER TABLE "tender_evaluation_notes" ALTER COLUMN "notes" TYPE TEXT');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tender_evaluation_notes', function (Blueprint $table) {
            DB::statement('ALTER TABLE "tender_evaluation_notes" ALTER COLUMN "notes" TYPE VARCHAR(255)');
        });
    }
}
