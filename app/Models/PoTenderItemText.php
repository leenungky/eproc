<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class PoTenderItemText extends BaseModel
{
    //
    use SoftDeletes;

    public $table = 'po_item_text';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'tender_number',
        'vendor_id',
        'vendor_code',
        'eproc_po_number',
        'item_id',
        'po_item',
        "PREQ_NO",
        "PREQ_ITEM",
        "TEXT_ID",
        "TEXT_ID_DESC",
        "TEXT_FORM",
        "TEXT_LINE",
        'action_status',
        'public_status',
        'created_by',
        'updated_by',
    ];
}
