<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTenderProposalApprovalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tender_proposal_approvals', function (Blueprint $table) {
            $table->id();
            $table->char('purch_org_code', 4)->nullable(false)->unique();
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
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tender_proposal_approvals');
    }
}
