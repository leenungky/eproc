<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddLineIdToTenderVendorTable extends Migration
{
    private $tables = [
        'tender_vendor_submissions','tender_vendor_submission_detail',
        'tender_header_technical','tender_item_technical','tender_header_commercial','tender_item_commercial',
        'tender_vendor_additional_costs','tender_vendor_tax_codes','tender_vendor_item_text',
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
                $table->smallInteger('action_status')->default(1);
                // $table->string('public_status', 30)->default('draft');
            });
        }
        Schema::table('tender_vendor_additional_costs', function (Blueprint $table) {
            $table->string('status', 30)->default('draft');
        });
        Schema::table('tender_vendor_tax_codes', function (Blueprint $table) {
            $table->string('status', 30)->default('draft');
        });
        Schema::table('tender_vendor_item_text', function (Blueprint $table) {
            $table->string('status', 30)->default('draft');
        });

        foreach($this->tables as $table){
            DB::statement("UPDATE " . $table . " SET line_id=id, action_status=1, status='submitted';");
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
                $table->dropColumn('action_status');
                $table->dropColumn('public_status');
            });
        }
    }
}
