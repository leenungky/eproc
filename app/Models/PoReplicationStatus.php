<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PoReplicationStatus extends Model
{
    public $table = 'po_replication_status';
    protected $fillable = [
        "tender_number" ,
        'action_status',
        'created_at',
        'updated_at'
    ];
}
