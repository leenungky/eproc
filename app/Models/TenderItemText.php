<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class TenderItemText extends BaseModel
{
    use SoftDeletes;

    public $table = 'tender_item_text';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'tender_number',
        'item_id',
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
