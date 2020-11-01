<?php

namespace App\Models;

use App\Models\BaseModel;
use App\TenderParameter;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

class TenderComment extends BaseModel
{
    public $table = 'tender_comments';

    const STATUS = [
        1 => 'new',
        2 => 'read',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        "tender_number" ,
        "user_id_from" ,
        "from_name" ,
        "user_id_to",
        "to_name" ,
        'comments',
        'submission_method',
        'status',
        'created_by',
        'updated_by',
    ];

    public function parameter()
    {
        return $this->belongsTo(TenderParameter::class, 'tender_number','tender_number');
    }

    public function getUpdatedAtAttribute($value)
    {
        return $value ? Carbon::parse($value)->format(static::DATETIME_FORMAT) : null;
    }
}
