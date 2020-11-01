<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BaseModel;

class TenderCommercialApproval extends BaseModel
{
    use SoftDeletes;
    public $table = 'tender_commercial_approvals';
    public $timestamps = true;

    protected $fillable = [
        'purch_org_code',
        'approver_1',
        'approver_2',
        'approver_3',
        'approver_4',
        'approver_5',
        'approver_6',
        'approver_7',
        'approver_8',
        'created_by',
        'updated_by',
    ];
}
