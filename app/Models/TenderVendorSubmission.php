<?php

namespace App\Models;

use App\Enums\TenderStatusEnum;
use App\Enums\TenderSubmissionEnum;
use App\Helpers\App;
use App\Models\BaseModel;
use App\Scopes\VendorViewScope;
use App\TenderParameter;
use App\Traits\TenderLog;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TenderVendorSubmission extends BaseModel
{
    use SoftDeletes, TenderLog;

    public $table = 'tender_vendor_submissions';
    const STATUS = [
        0 => 'draft',
        1 => 'submitted',
        2 => 'resubmitted',
        3 => 'passed',
        4 => 'not_passed',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        // 'submission_date',
    ];

    protected $fillable = [
        "tender_number",
        "vendor_id",
        "bidding_document_id",
        'submission_date',
        'submission_method',
        "status",
        'order',
        'action_status',
        'line_id',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function parameter()
    {
        return $this->belongsTo(TenderParameter::class, 'tender_number', 'tender_number');
    }

    public function getSubmissionDateAttribute($value)
    {
        return $value ? Carbon::parse($value)->format(static::DATETIME_FORMAT) : null;
    }

    protected static function logValues($activity, $model)
    {
        if ($model->status == static::STATUS[0] && $model->action_status == TenderStatusEnum::ACT_CHANGE) {
            return false;
        } else if ($model->status == static::STATUS[0]) {
            self::withoutEvents(function () use ($model) {
                $model->action_status = TenderStatusEnum::ACT_CHANGE;
                $model->save();
            });
        }
        $pageTypes = array_flip(TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE);
        $user = Auth::user();
        $act = $activity;
        if (in_array($activity, ['created', 'updated'])) { // && $model->status != static::STATUS[0]
            $act = $model->status;
        }
        return [
            'user_id' => $user->userid ?? null,
            'activity' => $act,
            'model_id' => $model->id ?? null,
            'model_type' => get_class($model) ?? null,
            'page_type' => $pageTypes[$model->submission_method], //$workflowValues[0] ?? null,
            'ref_number' => $model->tender_number,
            'properties' => $model,
            'host' => request()->ip() ?? null,
        ];
    }

    protected static function booted()
    {
        // self::addGlobalScope(new VendorViewScope);
        static::created(function (Model $model) {
            if (empty($model->line_id)) {
                self::withoutEvents(function () use ($model) {
                    $model->line_id = $model->id;
                    $model->save();
                });
            }
            // static::publishTenderStatus($model, 'created');
        });
        static::updated(function (Model $model) {
            static::publishTenderStatus($model, 'updated');
        });
    }

    protected static function publishTenderStatus($model, $action)
    {
        // dd($model);
        $tableVendors = ['tender_vendor_submission_detail'];
        $tableVendorsNegoTech = ['tender_vendor_submission_detail'];
        $WORKFLOW_MAPPING_TYPE = TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE;
        $needUpdatesStatus = true;

        if ($action == 'updated') {
            // $oldPublicStatus = $model->getOriginal('status');
            $needUpdatesStatus = in_array($model->status, [TenderVendorSubmission::STATUS[1], TenderVendorSubmission::STATUS[2]])
                && $model->action_status == TenderStatusEnum::ACT_NEW;
        }

        if ($needUpdatesStatus) {
            if (in_array($model->submission_method, [$WORKFLOW_MAPPING_TYPE['process_technical_evaluation'], $WORKFLOW_MAPPING_TYPE['negotiation_technical']])) {
                $tableVendors[] = 'tender_header_technical';
                $tableVendors[] = 'tender_item_technical';
                $tableVendors[] = 'tender_vendor_item_text';
                $tableVendors[] = 'tender_vendor_item_detail';
            }
            if (in_array($model->submission_method, [$WORKFLOW_MAPPING_TYPE['process_commercial_evaluation'], $WORKFLOW_MAPPING_TYPE['negotiation_commercial']])) {
                $tableVendors[] = 'tender_header_commercial';
                $tableVendors[] = 'tender_item_commercial';
                $tableVendors[] = 'tender_vendor_additional_cost';
                $tableVendors[] = 'tender_vendor_tax_code';

                if($model->submission_method == $WORKFLOW_MAPPING_TYPE['negotiation_commercial']){
                    $tableVendorsNegoTech[] = 'tender_header_technical';
                    $tableVendorsNegoTech[] = 'tender_item_technical';
                    $tableVendorsNegoTech[] = 'tender_vendor_item_text';
                }
            }

            if (count($tableVendors) > 0) {
                foreach ($tableVendors as $table) {
                    $tableName = $table;
                    $modelClass = '\\App\\Models\\' . App::getClassName($tableName);
                    // update status item draft to submit
                    (new $modelClass)->where('tender_number', $model->tender_number)
                        ->where('status', TenderSubmissionEnum::STATUS_ITEM[1])
                        ->where('vendor_id', $model->vendor_id)
                        ->where('submission_method', $model->submission_method)
                        ->whereNull('deleted_at')
                        ->withoutGlobalScope(VendorViewScope::class)
                        ->update([
                            'status' => TenderSubmissionEnum::STATUS_ITEM[2],
                            // 'public_status' => TenderStatusEnum::PUBLIC_STATUS[2]
                        ]);

                    // delete item old
                    (new $modelClass)->where('tender_number', $model->tender_number)
                        ->where('action_status', '!=', TenderStatusEnum::ACT_NEW)
                        ->where('vendor_id', $model->vendor_id)
                        ->where('submission_method', $model->submission_method)
                        ->withoutGlobalScope(VendorViewScope::class)
                        ->delete();
                }
            }

            //for data nego techincal
            if (count($tableVendorsNegoTech) > 0) {
                foreach ($tableVendorsNegoTech as $table) {
                    $tableName = $table;
                    $modelClass = '\\App\\Models\\' . App::getClassName($tableName);
                    // update status item draft to submit
                    (new $modelClass)->where('tender_number', $model->tender_number)
                        ->where('status', TenderSubmissionEnum::STATUS_ITEM[1])
                        ->where('vendor_id', $model->vendor_id)
                        ->where('submission_method', $WORKFLOW_MAPPING_TYPE['negotiation_technical'])
                        ->whereNull('deleted_at')
                        ->withoutGlobalScope(VendorViewScope::class)
                        ->update([
                            'status' => TenderSubmissionEnum::STATUS_ITEM[2],
                            // 'public_status' => TenderStatusEnum::PUBLIC_STATUS[2]
                        ]);

                    // delete item old
                    (new $modelClass)->where('tender_number', $model->tender_number)
                        ->where('action_status', '!=', TenderStatusEnum::ACT_NEW)
                        ->where('vendor_id', $model->vendor_id)
                        ->where('submission_method', $WORKFLOW_MAPPING_TYPE['negotiation_technical'])
                        ->withoutGlobalScope(VendorViewScope::class)
                        ->delete();
                }
            }
        }
    }

    public static function deleteDraftStatus($model, $actionType)
    {
        $tableVendors = ['tender_vendor_submission_detail'];
        $WORKFLOW_MAPPING_TYPE = TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE;

        if (in_array($model->submission_method, [$WORKFLOW_MAPPING_TYPE['process_technical_evaluation'], $WORKFLOW_MAPPING_TYPE['negotiation_technical']])) {
            $tableVendors[] = 'tender_header_technical';
            $tableVendors[] = 'tender_item_technical';
            $tableVendors[] = 'tender_vendor_item_text';
            $tableVendors[] = 'tender_vendor_item_detail';
        }
        if (in_array($model->submission_method, [$WORKFLOW_MAPPING_TYPE['process_commercial_evaluation'], $WORKFLOW_MAPPING_TYPE['negotiation_commercial']])) {
            $tableVendors[] = 'tender_header_commercial';
            $tableVendors[] = 'tender_item_commercial';
            $tableVendors[] = 'tender_vendor_additional_cost';
            $tableVendors[] = 'tender_vendor_tax_code';
        }

        if (count($tableVendors) > 0) {

            // dd($tableVendors);
            foreach ($tableVendors as $table) {
                $tableName = $table;
                $modelClass = '\\App\\Models\\' . App::getClassName($tableName);
                // update status item draft to submit
                DB::table((new $modelClass)->getTable())->where('tender_number', $model->tender_number)
                    ->where('status', '!=', TenderSubmissionEnum::STATUS_ITEM[1])
                    ->where('vendor_id', $model->vendor_id)
                    ->where('submission_method', $model->submission_method)
                    ->whereNull('deleted_at')
                    // ->withoutGlobalScope(VendorViewScope::class)
                    ->update([
                        'action_status' => TenderStatusEnum::ACT_NEW,
                    ]);

                // delete item old
                DB::table((new $modelClass)->getTable())->where('tender_number', $model->tender_number)
                    ->where('status', TenderSubmissionEnum::STATUS_ITEM[1])
                    ->where('vendor_id', $model->vendor_id)
                    ->where('submission_method', $model->submission_method)
                    // ->withoutGlobalScope(VendorViewScope::class)
                    ->delete();
            }
        }

        if ($model->status == TenderSubmissionEnum::STATUS_ITEM[1]) {
            self::withoutEvents(function () use ($model, $actionType) {
                $model->action_status = TenderStatusEnum::ACT_NEW;
                $model->status = $actionType == TenderSubmissionEnum::FLOW_STATUS[4]
                    ? static::STATUS[2] : static::STATUS[1];
                $model->save();
            });
        }
    }
}
