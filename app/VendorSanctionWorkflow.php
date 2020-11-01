<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VendorSanctionWorkflow extends Model
{
    //
    protected $table = 'vendor_sanction_workflows';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;
    
    protected $fillable = [
        'vendor_id',
        'vendor_sanction_id',
        'activity',
        'remarks',
        'started_at',
        'finished_at',
        'created_by'
    ];
}
