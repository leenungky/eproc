<?php

namespace App\Models;

use App\Models\BaseModel;
use App\TenderParameter;
use App\Traits\PublicView;
use Illuminate\Database\Eloquent\SoftDeletes;

class TenderGeneralDocument extends BaseModel
{
    use SoftDeletes, PublicView;

    public $table = 'tender_general_documents';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'tender_number',
        "document_name" ,
        "description" ,
        'attachment',
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
