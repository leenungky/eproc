<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VendorProfileTax extends Model
{
    use SoftDeletes;

    //
    protected $table = 'vendor_profile_taxes';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;
    protected $fillable = [
        'vendor_profile_id',
        'tax_document_type',
        'tax_document_number',
        'issued_date',
        'tax_document_attachment',
        'parent_id',
        'is_finished',
        'is_submitted',
        'is_current_data',
        'created_by'
    ];
}
