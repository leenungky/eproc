<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VendorProfileBankAccount extends Model
{
    use SoftDeletes;

    //
    protected $table = 'vendor_profile_bank_accounts';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;
    protected $fillable = [
        'vendor_profile_id',
        'account_holder_name',
        'account_number',
        'currency',
        'bank_name',
        'bank_address',
        'bank_statement_letter',
        'parent_id',
        'is_finished',
        'is_submitted',
        'is_current_data',
        'created_by'
    ];
}
