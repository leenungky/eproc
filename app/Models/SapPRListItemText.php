<?php

namespace App\Models;

use App\Models\BaseModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\App;

class SapPRListItemText extends BaseModel
{
    use SoftDeletes;

    public $table = 'sap_pr_list_item_text';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        "PREQ_NO",
        "PREQ_ITEM",
        "TEXT_ID",
        "TEXT_ID_DESC",
        "TEXT_FORM",
        "TEXT_LINE",
    ];
}
