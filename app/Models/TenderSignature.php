<?php

namespace App\Models;

use App\Buyer;
use App\Models\BaseModel;
use App\Models\Ref\RefBuyer;
use App\TenderParameter;
use App\Traits\PublicView;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\App;

class TenderSignature extends BaseModel
{
    use SoftDeletes;

    public $table = 'tender_signatures';

    const TYPE = [
        1 => 'proposer',
        2 => 'approver',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        "tender_number" ,
        "sign_by_id" ,
        "type" ,
        "order",
        'notes',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function parameter()
    {
        return $this->belongsTo(TenderParameter::class, 'tender_number','tender_number');
    }

    public function buyer(){
        return $this->belongsTo(RefBuyer::class, 'sign_by_id','user_id');
    }

    public function user(){
        return $this->belongsTo(User::class, 'sign_by_id','id');
    }

    public function getUpdatedAtAttribute($value)
    {
        return $value ? Carbon::parse($value)->format(static::DATETIME_FORMAT) : null;
    }
}
