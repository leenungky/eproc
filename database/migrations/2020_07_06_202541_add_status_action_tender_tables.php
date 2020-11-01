<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddStatusActionTenderTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE tender_aanwijzings RENAME COLUMN "status" TO public_status;');
        Schema::table('tender_aanwijzings', function (Blueprint $table) {
            $table->smallInteger('action_status')->default(1);
        });
        Schema::table('tender_vendors', function (Blueprint $table) {
            $table->smallInteger('action_status')->default(1);
            $table->string('public_status', 30)->default('draft');
        });
        Schema::table('tender_parameters', function (Blueprint $table) {
            $table->smallInteger('action_status')->default(1);
            $table->string('public_status', 30)->default('draft');
        });
        Schema::table('tender_weightings', function (Blueprint $table) {
            $table->smallInteger('action_status')->default(1);
            $table->string('public_status', 30)->default('draft');
        });
        Schema::table('tender_evaluators', function (Blueprint $table) {
            $table->smallInteger('action_status')->default(1);
            $table->string('public_status', 30)->default('draft');
        });
        Schema::table('tender_items', function (Blueprint $table) {
            $table->smallInteger('action_status')->default(1);
            $table->string('public_status', 30)->default('draft');
        });
        Schema::table('tender_additional_costs', function (Blueprint $table) {
            $table->smallInteger('action_status')->default(1);
            $table->string('public_status', 30)->default('draft');
        });
        Schema::table('tender_bidding_document_requirements', function (Blueprint $table) {
            $table->smallInteger('action_status')->default(1);
            $table->string('public_status', 30)->default('draft');
        });
        Schema::table('tender_schedules', function (Blueprint $table) {
            $table->smallInteger('action_status')->default(1);
            $table->string('public_status', 30)->default('draft');
        });
        Schema::table('tender_tax_codes', function (Blueprint $table) {
            $table->smallInteger('action_status')->default(1);
            $table->string('public_status', 30)->default('draft');
        });
        Schema::table('tender_internal_documents', function (Blueprint $table) {
            $table->smallInteger('action_status')->default(1);
            $table->string('public_status', 30)->default('draft');
        });
        Schema::table('tender_general_documents', function (Blueprint $table) {
            $table->smallInteger('action_status')->default(1);
            $table->string('public_status', 30)->default('draft');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE tender_aanwijzings RENAME COLUMN public_status TO "status";');
        Schema::table('tender_aanwijzings', function (Blueprint $table) {
            $table->dropColumn('action_status');
        });
        Schema::table('tender_vendors', function (Blueprint $table) {
            $table->dropColumn('action_status');
            $table->dropColumn('public_status');
        });
        Schema::table('tender_parameters', function (Blueprint $table) {
            $table->dropColumn('action_status');
            $table->dropColumn('public_status');
        });
        Schema::table('tender_weightings', function (Blueprint $table) {
            $table->dropColumn('action_status');
            $table->dropColumn('public_status');
        });
        Schema::table('tender_evaluators', function (Blueprint $table) {
            $table->dropColumn('action_status');
            $table->dropColumn('public_status');
        });
        Schema::table('tender_items', function (Blueprint $table) {
            $table->dropColumn('action_status');
            $table->dropColumn('public_status');
        });
        Schema::table('tender_additional_costs', function (Blueprint $table) {
            $table->dropColumn('action_status');
            $table->dropColumn('public_status');
        });
        Schema::table('tender_bidding_document_requirements', function (Blueprint $table) {
            $table->dropColumn('action_status');
            $table->dropColumn('public_status');
        });
        Schema::table('tender_schedules', function (Blueprint $table) {
            $table->dropColumn('action_status');
            $table->dropColumn('public_status');
        });
        Schema::table('tender_tax_codes', function (Blueprint $table) {
            $table->dropColumn('action_status');
            $table->dropColumn('public_status');
        });
        Schema::table('tender_internal_documents', function (Blueprint $table) {
            $table->dropColumn('action_status');
            $table->dropColumn('public_status');
        });
        Schema::table('tender_general_documents', function (Blueprint $table) {
            $table->dropColumn('action_status');
            $table->dropColumn('public_status');
        });
    }
}
