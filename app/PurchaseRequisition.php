<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseRequisition extends Model
{
    //
    use SoftDeletes;

    protected $fillable = [
        'number', 'line_number', 'product_code',
        'product_group_code','description','requisitioner',
        'purch_group_code', 'purch_group_name','qty',
        'uom','est_unit_price','price_unit','currency_code',
        'subtotal','state','expected_delivery_date','transfer_date',
        'account_assignment', 'item_category', 'gl_account',
        'wbs_element', 'cost_center', 'requisitioner_desc',
        'tracking_number', 'request_date',
    ];

    /**
     * insert bulk
     *
     * @param array $data
     *
     * @return boolean
     */
    public static function insertBulk($data)
    {
        if(!empty($data)){
            return static::insert($data);
        }
        return false;
    }


    // $table->string('certification')->nullable(true);
    // $table->string('material_status')->nullable(true);
    // $table->string('plant')->nullable(true);
    // $table->string('plant_name')->nullable(true);
    // $table->string('storage_loc')->nullable(true);
    // $table->string('storage_loc_name')->nullable(true);
    // $table->string('qty_ordered')->nullable(true);
    // $table->string('cost_desc')->nullable(true);
    // $table->string('overall_limit')->nullable(true);
    // $table->string('expected_limit')->nullable(true);
    // $table->string('cost_code')->nullable(true);
    // $table->string('deleteflg')->nullable(true);
}
