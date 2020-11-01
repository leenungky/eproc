<?php

use App\RefListOption;
use App\TenderParameter;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTenderParameterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tender_parameters', function (Blueprint $table) {
            $table->string('conditional_type', 64)->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tender_parameters', function (Blueprint $table) {
            $table->dropColumn('conditional_type');
        });
        // RefListOption::where('type', 'conditional_type_option')->delete();
    }
}
