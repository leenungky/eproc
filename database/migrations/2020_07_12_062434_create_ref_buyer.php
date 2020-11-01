<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRefBuyer extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->down();
        Schema::create('ref_buyers', function (Blueprint $table) {
            $table->bigInteger('user_id');
            $table->string('buyer_name');
            $table->date('valid_from_date');
            $table->date('valid_thru_date')->nullable();
            $table->bigInteger('created_by')->nullable();
            $table->bigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->primary('user_id');
        });
        Schema::create('ref_buyer_purch_orgs', function (Blueprint $table) {
            $table->bigInteger('user_id');
            $table->bigInteger('purch_org_id');

            $table->primary(['user_id','purch_org_id']);
        });
        Schema::create('ref_buyer_purch_groups', function (Blueprint $table) {
            $table->bigInteger('user_id');
            $table->bigInteger('purch_group_id');

            $table->primary(['user_id','purch_group_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ref_buyers');
        Schema::dropIfExists('ref_buyer_purch_orgs');
        Schema::dropIfExists('ref_buyer_purch_groups');
    }
}
