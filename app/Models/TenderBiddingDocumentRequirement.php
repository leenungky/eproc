<?php

namespace App\Models;

use App\Buyer;
use App\Models\BaseModel;
use App\TenderParameter;
use App\Traits\PublicView;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\App;

class TenderBiddingDocumentRequirement extends BaseModel
{
    use SoftDeletes, PublicView;

    public $table = 'tender_bidding_document_requirements';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        "tender_number" ,
        "description" ,
        "stage_type" ,
        'submission_method',
        'is_required',
        'order',
        'public_status',
        'action_status',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function parameter()
    {
        return $this->belongsTo(TenderParameter::class, 'tender_number','tender_number');
    }
}
