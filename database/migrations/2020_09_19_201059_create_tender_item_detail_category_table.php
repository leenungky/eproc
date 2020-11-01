<?php

use App\RefListOption;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTenderItemDetailCategoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tender_item_detail_category', function (Blueprint $table) {
            $table->id();
            $table->string('tender_number', 32)->nullable(true);
            $table->string('key', 32)->nullable(true);
            $table->string('category_name', 32)->nullable(true);
            $table->smallInteger('order')->nullable(true);
            $table->smallInteger('template_id')->nullable(true);
            $table->smallInteger('action_status')->default(1);
            $table->string('public_status', 30)->default('draft');
            $table->integer('line_id')->nullable(true);
            $table->string('created_by')->nullable(true);
            $table->string('updated_by')->nullable(true);
            $table->timestamps();
            $table->softDeletes();
        });

        $data = [
            [
                'type'=>'item_specification_category',
                'key'=>'cat1',
                'value'=>'Equipment',
                'deleteflg' => false,
            ],[
                'type'=>'item_specification_category',
                'key'=>'cat2',
                'value'=>'Tools',
                'deleteflg' => false,
            ],[
                'type'=>'item_specification_category',
                'key'=>'cat3',
                'value'=>'Resources',
                'deleteflg' => false,
            ],
            [
                'type'=>'item_specification_category',
                'key'=>'cat4',
                'value'=>'Other',
                'deleteflg' => true,
            ],
            //tender method
            [
                'type'=>'item_specification_category',
                'key'=>'cat5',
                'value'=>'Comment',
                'deleteflg' => false,
            ]
        ];
        RefListOption::insert($data);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tender_item_detail_category');
        RefListOption::where('type', 'item_specification_category')
            ->delete();
    }
}
