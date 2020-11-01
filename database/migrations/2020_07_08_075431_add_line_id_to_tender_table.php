<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddLineIdToTenderTable extends Migration
{
    private $tables = [
        'tender_aanwijzings','tender_vendors','tender_parameters','tender_weightings',
        'tender_evaluators','tender_items','tender_additional_costs','tender_bidding_document_requirements',
        'tender_schedules','tender_tax_codes','tender_internal_documents','tender_general_documents',
    ];


    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach($this->tables as $table){
            Schema::table($table, function (Blueprint $table) {
                $table->integer('line_id')->nullable(true);
            });
        }
        foreach($this->tables as $table){
            DB::statement('UPDATE ' . $table . ' SET line_id=id;');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        foreach($this->tables as $table){
            Schema::table($table, function (Blueprint $table) {
                $table->dropColumn('line_id');
            });
        }
    }
}
