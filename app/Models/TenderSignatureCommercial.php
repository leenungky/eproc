<?php

namespace App\Models;

use App\Models\TenderSignature;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class TenderSignatureCommercial extends TenderSignature
{
    use SoftDeletes;

    public $table = 'tender_signature_commercials';

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

    public function getUpdatedAtAttribute($value)
    {
        return $value ? Carbon::parse($value)->setTimeZone(config('timezone'))->format(static::DATETIME_FORMAT) : null;
    }
}
