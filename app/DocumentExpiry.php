<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentExpiry extends Model
{
    use SoftDeletes;

    //
    protected $table = 'vendor_document_expiration';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;
    protected $fillable = [
        'vendor_business_permits_id',
        'status',
        'valid_from_date',
        'valid_thru_date',
        'current_date',
        'updated_by',      
        'created_by'
    ];
}
