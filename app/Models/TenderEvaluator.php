<?php

namespace App\Models;

use App\Buyer;
use App\Models\BaseModel;
use App\TenderParameter;
use App\Traits\PublicView;
use App\User;
use Illuminate\Database\Eloquent\SoftDeletes;

class TenderEvaluator extends BaseModel
{
    use SoftDeletes, PublicView;

    public $table = 'tender_evaluators';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        "tender_number" ,
        "buyer_user_id" ,
        "stage_type" ,
        'submission_method',
        // 'buyer_type_id',
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

    public function buyer()
    {
        return $this->belongsTo(Buyer::class, 'buyer_user_id','user_id');
    }

    public function user(){
        return $this->belongsTo(User::class, 'buyer_user_id');
    }

    public function role()
    {
        return Role::where('model_type', 'App\User')
            ->where('model_id', $this->buyer_id)
            ->get();
    }
}
