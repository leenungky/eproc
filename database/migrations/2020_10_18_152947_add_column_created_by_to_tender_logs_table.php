<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnCreatedByToTenderLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tender_logs', function (Blueprint $table) {
            $table->string('created_by')->nullable(true);
            $table->string('updated_by')->nullable(true);
        });
        DB::statement("UPDATE tender_logs SET created_by=user_id;");
        DB::statement("UPDATE tender_logs SET created_by='admin' WHERE activity IN ('start','open','open_resubmission') AND model_type='App\Models\TenderReference' AND page_type='negotiation_commercial';");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tender_logs', function (Blueprint $table) {
            $table->dropColumn('created_by');
            $table->dropColumn('updated_by');
        });
    }
}
