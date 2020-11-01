<?php

namespace App;

use App\Enums\TenderStatusEnum;
use App\Enums\TenderSubmissionEnum;
use App\Models\BaseModel;
use App\Models\TenderBiddingDocumentRequirement;
use App\Models\TenderEvaluator;
use App\Models\TenderVendor;
use App\Traits\TenderLog;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TenderParameter extends Model
{
    use SoftDeletes, TenderLog;
    protected $guarded = ['id', 'deleted_at', 'created_at', 'updated_at'];

    public function scopeOfPublic($query, $user, $pageType = null, $status = 'active')
    {
        // $excludeStatusForPublic = ['process_prequalification-open','process_prequalification-open_resubmission'];
        $wfStatusForPublic = ['process_registration'];
        if($status){
            if(is_array($status)){
                $query = $query->whereIn('tender_parameters.status', $status);
            }else if(is_string($status)){
                $query = $query->where('tender_parameters.status', $status);
            }
        }
        if(!$user){
            $query =  $query->whereIn('tender_parameters.tender_method', ['LIMITED','COMPETITIVE'])
            // ->whereNotIn('tender_parameters.workflow_values', $excludeStatusForPublic);
            ->whereIn('tender_parameters.workflow_values', $wfStatusForPublic);
        }
        return $query;
    }

    public function scopeOfUser($query, $user)
    {
        if($user && $user->vendor){
            $query = $query->whereIn('tender_parameters.status', ['active','completed']);
        }
        return $query;
    }

    public function createdBy(){
        return $this->belongsTo(User::class, 'created_by','userid');
    }

    public function vendors()
    {
        return $this->hasMany(TenderVendor::class, 'tender_number', 'tender_number');
    }

    public function evaluator()
    {
        return $this->hasMany(TenderEvaluator::class, 'tender_number', 'tender_number');
    }

    public function biddingDocument(){
        return $this->hasMany(TenderBiddingDocumentRequirement::class, 'tender_number', 'tender_number');
    }

    public function getSubmissionMethodTextAttribute()
    {
        return $this->belongsTo(RefListOption::class, 'submission_method', 'key')
            ->where('type','submission_method_options')->first()->value ?? '';
    }

    public function getTenderMethodTextAttribute()
    {
        return $this->belongsTo(RefListOption::class, 'tender_method', 'key')
            ->where('type','tender_method_options')->first()->value ?? '';
    }

    public function getPurchaseOrgTextAttribute()
    {
        return $this->belongsTo(RefPurchaseOrg::class, 'purchase_org_id', 'id')->first()->description ?? '';
    }

    public function userUpdatedBy()
    {
        return  User::select(
                'users.id',
                'users.userid',
                'users.email',
                'ref_buyers.buyer_name'
            )
            ->join('ref_buyers','users.id','ref_buyers.user_id')
            ->where('userid', $this->updated_by)
            ->first() ?? null;
    }

    protected static function booted()
    {
        static::updated(function (Model $model) {
            static::publishTenderStatus($model);
        });
    }

    protected static function publishTenderStatus($model)
    {
        $oldPublicStatus = $model->getOriginal('public_status');
        if($oldPublicStatus != $model->public_status
            && $model->public_status == TenderStatusEnum::PUBLIC_STATUS[2]){

            $tableBuyers = [
                '\\App\\Models\\TenderInternalDocument',
                '\\App\\Models\\TenderGeneralDocument',
                '\\App\\TenderItem',
                '\\App\\Models\\TenderEvaluator',
                '\\App\\Models\\TenderWeighting',
                '\\App\\Models\\TenderBiddingDocumentRequirement',
                '\\App\\Models\\TenderSchedule',
                '\\App\\Models\\TenderItemDetailCategory',
                '\\App\\Models\\TenderItemDetail',
                '\\App\\Models\\TenderAdditionalCost',
            ];

            foreach($tableBuyers as $table){
                DB::table((new $table)->getTable())->where('tender_number', $model->tender_number)
                    ->where('public_status', TenderStatusEnum::PUBLIC_STATUS[1])
                    // ->withoutGlobalScope(PublicViewScope::class)
                    ->update(['public_status' => TenderStatusEnum::PUBLIC_STATUS[2]]);

                // public new data
                DB::table((new $table)->getTable())->where('tender_number', $model->tender_number)
                    ->where('action_status', '!=', TenderStatusEnum::ACT_NEW)
                    // ->withoutGlobalScope(PublicViewScope::class)
                    ->delete();
            }

            TenderVendor::where('tender_number', $model->tender_number)
                ->where('status', TenderStatusEnum::PUBLIC_STATUS[1])
                ->update(['status' => TenderVendor::STATUS[1]]);
        }
    }

    protected static function logValues($activity, $model)
    {
        $user = Auth::user();
        $act = $activity;
        $pageType = '';
        return [
            'user_id' => $user->userid ?? null,
            'activity' => $act,
            'model_id' => $model->id ?? null,
            'model_type' => get_class($model) ?? null,
            'page_type' => $pageType ?? null,
            'ref_number' => $model->tender_number ?? '',
            'properties' => $model,
            'host' => request()->ip() ?? null,
        ];
    }

    public function saveAsTenderChange()
    {
        if ($this->status == 'active') {
            $this->public_status = TenderStatusEnum::PUBLIC_STATUS[5];
            $this->action_status = TenderStatusEnum::ACT_CHANGE;
        }
        $this->save();
    }

    public function getCreatedAtAttribute($value)
    {
        return $value ? Carbon::parse($value)->format(BaseModel::DATETIME_FORMAT) : null;
    }
}
