<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VendorEvaluationWorkflow extends Model
{
    //
    protected $table = 'vendor_evaluation_workflows';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;
    
    protected $fillable = [
        'vendor_id',
        'vendor_evaluation_id',
        'activity',
        'remarks',
        'started_at',
        'finished_at',
        'created_by'
    ];
}
