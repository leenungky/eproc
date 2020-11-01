<?php

namespace App\Models;

use App\Models\BaseModel;
use App\TenderParameter;
use App\Traits\PublicView;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\App;

class TenderSchedule extends BaseModel
{
    use SoftDeletes, PublicView;

    public $table = 'tender_schedules';

    const TYPE = [
        1 => 'registration',
        2 => 'pre_qualification',
        3 => 'tender',
        4 => 'technical',
        5 => 'commercial',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'start_date',
        'end_date',
    ];

    protected $fillable = [
        "tender_number" ,
        "start_date" ,
        "end_date" ,
        'type',
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

    public function setStartDateAttribute($value)
    {
        $this->attributes['start_date'] = $value ? Carbon::createFromFormat(static::DATETIME_FORMAT, $value)->format(static::DB_DATETIME_FORMAT) : null;
    }
    public function getStartDateAttribute($value)
    {
        return $value ? Carbon::parse($value)->format(static::DATETIME_FORMAT) : null;
    }

    public function setEndDateAttribute($value)
    {
        $this->attributes['end_date'] = $value ? Carbon::createFromFormat(static::DATETIME_FORMAT, $value)->format(static::DB_DATETIME_FORMAT) : null;
    }
    public function getEndDateAttribute($value)
    {
        return $value ? Carbon::parse($value)->format(static::DATETIME_FORMAT) : null;
    }
}
