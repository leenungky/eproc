<?php
namespace App\Traits;

use App\Enums\TenderStatusEnum;
use App\Models\TenderLogs;
use App\Scopes\PublicViewScope;
use App\TenderParameter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * @method static App\Traits withDraft()
 */
trait TenderLog
{
    /**
     * Boot the public view trait for a model.
     *
     * @return void
     */
    public static function bootTenderLog()
    {
        static::created(function (Model $model) {
            self::audit('created', $model);
        });

        static::updated(function (Model $model) {
            self::audit('updated', $model);
        });

        static::deleted(function (Model $model) {
            self::audit('deleted', $model);
        });
    }

    protected static function audit($activity, $model)
    {
        $data = static::logValues($activity, $model);
        if($data != false && !empty($data)){
            TenderLogs::create($data);
        }
    }

    protected static function logValues($activity, $model)
    {
        $user = Auth::user();
        $tender = TenderParameter::where('tender_number', $model->tender_number)
            ->first();
        $workflowValues = explode('-', $tender->workflow_values);
        return [
            'user_id' => $user->userid ?? null,
            'activity' => $activity,
            'model_id' => $model->id ?? null,
            'model_type' => get_class($model) ?? null,
            'page_type' => $workflowValues[0] ?? null,
            'ref_number' => $model->tender_number,
            'properties' => $model,
            'host' => request()->ip() ?? null,
        ];
    }
}
