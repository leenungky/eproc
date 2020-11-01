<?php

namespace App\Models;

use App\Enums\TenderStatusEnum;
use App\Models\BaseModel;
use App\TenderParameter;
use App\Traits\PublicView;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class TenderAanwijzings extends BaseModel
{
    use SoftDeletes; // PublicView;

    public $table = 'tender_aanwijzings';

    const STATUS = TenderStatusEnum::PUBLIC_STATUS;

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'event_start',
        'event_end',
    ];

    protected $fillable = [
        "tender_number" ,
        "event_name" ,
        "venue" ,
        'event_start',
        'event_end',
        'note',
        'public_status',
        'action_status',
        'result_attachment',
        'result_description',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function scopeOfPublic($query, $user)
    {
        if($user && $user->vendor){
            return $query->whereIn(DB::raw('LOWER(public_status)'), [static::STATUS[2],static::STATUS[3]]);
        } else {
            return $query;
        }
    }

    public function parameter()
    {
        return $this->belongsTo(TenderParameter::class, 'tender_number','tender_number');
    }

    // public function getEventStartAttribute($value)
    // {
    //     return $value ? Carbon::parse($value)->format(static::DATE_FORMAT) : null;
    // }

    public function setEventStartAttribute($value)
    {
        $this->attributes['event_start'] = $value ? Carbon::createFromFormat(static::DATETIME_FORMAT, $value)->format(static::DB_DATETIME_FORMAT) : null;

    }

    // public function getEventEndAttribute($value)
    // {
    //     return $value ? Carbon::parse($value)->format(static::DATE_FORMAT) : null;

    // }

    public function setEventEndAttribute($value)
    {
        $this->attributes['event_end'] = $value ? Carbon::createFromFormat(static::DATETIME_FORMAT, $value)->format(static::DB_DATETIME_FORMAT) : null;

    }
}
