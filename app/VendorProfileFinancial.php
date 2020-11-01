<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VendorProfileFinancial extends Model
{
    use SoftDeletes;

    //
    protected $table = 'vendor_profile_financials';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;
    protected $fillable = [
        'vendor_profile_id',
        'financial_statement_date',
        'public_accountant_full_name',
        'audit',
        'financial_statement_year',
        'valid_thru_date',
        'financial_statement_attachment',
        'currency',
        'cash',
        'bank',
        'short_term_investments',
        'long_term_investments',
        'total_receivables',
        'inventories',
        'work_in_progress',
        'total_current_assets',
        'equipments_and_machineries',
        'fixed_inventories',
        'buildings',
        'lands',
        'total_fixed_assets',
        'other_assets',
        'incoming_debts',
        'taxes_payables',
        'other_payables',
        'total_short_term_debts',
        'long_term_payables',
        'total_net_worth',
        'total_assets',
        'total_liabilities',
        'total_net_worth_exclude_land_building',
        'annual_revenue',
        'business_class',
        'parent_id',
        'is_finished',
        'is_submitted',
        'is_current_data',
        'created_by'
    ];
}
