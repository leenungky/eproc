<?php
namespace App\Traits;

use App\Enums\TenderStatusEnum;
use App\Enums\TenderSubmissionEnum;
use App\Models\TenderVendorSubmission;
use App\Scopes\VendorViewScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * @method static App\Traits withDraft()
 */
trait VendorView
{
    /**
     * Boot the public view trait for a model.
     *
     * @return void
     */
    public static function bootVendorView()
    {
        self::addGlobalScope(new VendorViewScope);
        static::created(function (Model $model) {
            static::createLineId($model);
        });
    }

    public static function vendorStatus($query, $aliasTable = '')
    {
        $user = Auth::user();

        if($aliasTable == null || empty($aliasTable)) {
            $aliasTable = (new static)->getTable();
        }
        if($user && $user->isVendor()){
            // tampilkan item dengan status new (submitted | draft)
            $query->where($aliasTable.'.action_status', TenderStatusEnum::ACT_NEW);
        } else {
            // tampilkan item yang sudah disubmit
            $query->where($aliasTable.'.status', '!=', TenderSubmissionEnum::STATUS_ITEM[1]);
        }
        return $query;
    }

    public static function createLineId($model)
    {
        if(empty($model->line_id)){
            $model->line_id = $model->id;
            $model->parentSave();
        }
    }

    public function save(array $options = [])
    {
        $user = Auth::user();
        if($user && $user->isVendor()){
            // $submission = TenderVendorSubmission::where('tender_number', $this->tender_number)
            // ->where('submission_method', $this->submission_method)
            // ->where('vendor_id', $this->vendor_id)
            // ->first();
            if(empty($this->id) || $this->status == TenderSubmissionEnum::STATUS_ITEM[1]){
                $newModel = $this->parentSave($options);
            }else{
                $newModel = $this->replicate();
                $newModel->action_status = TenderStatusEnum::ACT_NEW;
                $newModel->status = TenderSubmissionEnum::STATUS_ITEM[1];
                $newModel->parentSave();
                self::withoutEvents(function () {
                    $model = self::find($this->id);
                    $model->action_status = TenderStatusEnum::ACT_CHANGE;
                    $model->parentSave();
                });
            }
            // if (!empty($submission) && $submission->action_status == TenderStatusEnum::ACT_NEW) {
            //     $submission->action_status = TenderStatusEnum::ACT_CHANGE;
            //     $submission->save();
            // }
            return $newModel;
        }
        return $this->parentSave($options);
    }

    public function delete(array $options = [])
    {
        $user = Auth::user();
        if($user && $user->isVendor()){
            if($this->status == TenderSubmissionEnum::STATUS_ITEM[1]){
                return parent::delete($options);
            }else{
                self::withoutEvents(function () {
                    $model = self::find($this->id);
                    $model->action_status = TenderStatusEnum::ACT_DELETE;
                    $model->parentSave();
                });
            }
            return true;
        }
        return parent::delete($options);
    }

    public function parentSave(array $options = [])
    {
        return parent::save($options);
    }
    public function parentDelete(array $options = [])
    {
        return parent::save($options);
    }
}
