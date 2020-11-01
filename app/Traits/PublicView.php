<?php
namespace App\Traits;

use App\Enums\TenderStatusEnum;
use App\Scopes\PublicViewScope;
use App\TenderParameter;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static App\Traits withDraft()
 */
trait PublicView
{
    /**
     * Boot the public view trait for a model.
     *
     * @return void
     */
    public static function bootPublicView()
    {
        self::addGlobalScope(new PublicViewScope);
        static::created(function (Model $model) {
            static::createLineId($model);
        });
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
        $tender = TenderParameter::where('tender_number', $this->tender_number)
            ->first();
        if ($tender->status == 'active') {
            if(empty($this->id) || $this->public_status == TenderStatusEnum::PUBLIC_STATUS[1]){
                $newModel = $this->parentSave($options);
            }else{
                $newModel = $this->replicate();
                $newModel->action_status = TenderStatusEnum::ACT_NEW;
                $newModel->public_status = TenderStatusEnum::PUBLIC_STATUS[1];
                $newModel->parentSave();
                self::withoutEvents(function () {
                    $model = self::find($this->id);
                    $model->action_status = TenderStatusEnum::ACT_CHANGE;
                    $model->parentSave();
                });
            }
            $tender->public_status = TenderStatusEnum::PUBLIC_STATUS[5];
            $tender->action_status = TenderStatusEnum::ACT_CHANGE;
            $tender->save();
            return $newModel;
        }
        return $this->parentSave($options);
    }

    public function delete(array $options = [])
    {
        $tender = TenderParameter::where('tender_number', $this->tender_number)
            ->first();
        if ($tender->status == 'active') {
            if($this->public_status == TenderStatusEnum::PUBLIC_STATUS[1]){
                return parent::delete($options);
            }else{
                self::withoutEvents(function () {
                    $model = self::find($this->id);
                    $model->action_status = TenderStatusEnum::ACT_DELETE;
                    $model->parentSave();
                });
            }
            $tender->public_status = TenderStatusEnum::PUBLIC_STATUS[5];
            $tender->action_status = TenderStatusEnum::ACT_CHANGE;
            $tender->save();
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
