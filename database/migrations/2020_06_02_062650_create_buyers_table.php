<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBuyersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //kode(username), nama buyer, dari tanggal, hingga tanggal, purch org, purch group
        $this->down();
        Schema::create('buyers', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->string('buyer_name');
            $table->date('valid_from_date');
            $table->date('valid_thru_date');
            $table->bigInteger('purch_org_id');
            $table->bigInteger('purch_group_id');
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
        Schema::dropIfExists('buyers');
        DB::statement('DROP SEQUENCE IF EXISTS buyers_id_seq');
    }
}
