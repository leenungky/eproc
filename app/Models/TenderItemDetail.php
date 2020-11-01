<?php

namespace App\Models;

use App\Models\BaseModel;
use App\TenderParameter;
use App\Traits\PublicView;
use Illuminate\Database\Eloquent\SoftDeletes;

class TenderItemDetail extends BaseModel
{
    use SoftDeletes, PublicView;

    public $table = 'tender_item_detail';
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'tender_number',
        'description',
        'requirement',
        'reference',
        'data',
        'respond',
        'category_id',
        'action_status',
        'public_status',
        'line_id',
        'created_by',
        'updated_by',
    ];

    public function parameter()
    {
        return $this->belongsTo(TenderParameter::class, 'tender_number','tender_number');
    }
}
