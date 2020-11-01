<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVendorEvaluationColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        DB::statement('ALTER table vendor_evaluation_generals add column is_finished integer default 0;');
        DB::statement('ALTER table vendor_evaluation_assignments add column is_finished integer default 0;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER table vendor_evaluation_generals drop column is_finished;');
        DB::statement('ALTER table vendor_evaluation_assignments drop column is_finished;');
    }
}
