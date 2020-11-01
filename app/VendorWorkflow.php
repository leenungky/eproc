<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VendorWorkflow extends Model
{
    //
    protected $table = 'vendor_workflows';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;
    
    protected $fillable = [
        'vendor_id',
        'activity',
        'remarks',
        'started_at',
        'finished_at',
        'created_by'
    ];
}
