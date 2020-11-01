<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VendorHistoryStatus extends Model
{
    use SoftDeletes;
    
    protected $table = 'vendor_history_statuses';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;
    
    protected $fillable = [
        'vendor_id',
        'status',
        'description',
        'version',
        'process',
        'remarks',
        'created_by'
    ];
}
