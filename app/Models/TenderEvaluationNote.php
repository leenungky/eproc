<?php

namespace App\Models;

use App\Models\BaseModel;
use App\TenderParameter;
use App\Traits\TenderLog;
use Illuminate\Database\Eloquent\SoftDeletes;

class TenderEvaluationNote extends BaseModel
{
    use TenderLog;

    public $table = 'tender_evaluation_notes';

    const TYPE = [
        1 => 'evaluation_note',
        2 => 'finish_note',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        "tender_number" ,
        "notes" ,
        "submission_method",
        'note_type',
        'created_by',
        'updated_by',
    ];

    public function parameter()
    {
        return $this->belongsTo(TenderParameter::class, 'tender_number','tender_number');
    }
}
