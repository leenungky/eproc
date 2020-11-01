<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PoHeader extends Model
{
    use SoftDeletes;
    public $table = 'po_header';
    protected $fillable = [
        'id',
        "tender_number" ,
        'vendor_code',
        'document_type',
        'document_date',
        'location_category',
        'vendor_profile_id',
        'purchase_org_code',
        'assign_purchorg_company_code_id',
        'eproc_po_number',
        'created_at'
    ];
}
