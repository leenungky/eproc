<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTenderCommercialApprovalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->down();
        Schema::create('tender_commercial_approvals', function (Blueprint $table) {
            $table->id();
            $table->char('purch_org_code', 4)->nullable(false);
            $table->integer('item_category')->nullable(false)->comment('0-material, 9-service');
            $table->string('description')->nullable(true);
            $table->string('approver_1')->nullable(true)->comment('fill with users.userid');
            $table->string('approver_2')->nullable(true)->comment('fill with users.userid');
            $table->string('approver_3')->nullable(true)->comment('fill with users.userid');
            $table->string('approver_4')->nullable(true)->comment('fill with users.userid');
            $table->string('approver_5')->nullable(true)->comment('fill with users.userid');
            $table->string('approver_6')->nullable(true)->comment('fill with users.userid');
            $table->string('approver_7')->nullable(true)->comment('fill with users.userid');
            $table->string('approver_8')->nullable(true)->comment('fill with users.userid');
            $table->string('created_by')->nullable(true);
            $table->string('updated_by')->nullable(true);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['purch_org_code','item_category']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tender_commercial_approvals');
        DB::statement('DROP SEQUENCE IF EXISTS tender_commercial_approvals_id_seq');
    }
}
