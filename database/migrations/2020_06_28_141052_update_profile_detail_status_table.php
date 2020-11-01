<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateProfileDetailStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tableFields = [
            'general',
            'deed',
            'shareholder',
            'bodboc',
            'businesspermit',
            'pic',
            'equipment',
            'expert',
            'certification',
            'scopeofsupply',
            'experience',
            'bankaccount',
            'financial',
            'tax'
        ];
        DB::statement("ALTER TABLE vendor_profile_detail_statuses DROP CONSTRAINT IF EXISTS vendor_profile_detail_statuses_tool_status_check");
        DB::statement("ALTER TABLE vendor_profile_detail_statuses DROP CONSTRAINT IF EXISTS vendor_profile_detail_statuses_competency_status_check");
        foreach($tableFields as $field){
            DB::statement("ALTER TABLE vendor_profile_detail_statuses DROP CONSTRAINT IF EXISTS vendor_profile_detail_statuses_" . $field . "_status_check");
            DB::statement("
                ALTER TABLE vendor_profile_detail_statuses
                    ADD CONSTRAINT vendor_profile_detail_statuses_" . $field . "_status_check CHECK (" . $field . "_status::text = ANY (ARRAY['none'::character varying, 'warning'::character varying, 'not-finish'::character varying, 'finish'::character varying]::text[]))"
            );
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
