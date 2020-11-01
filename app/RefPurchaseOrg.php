<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RefPurchaseOrg extends Model
{
    //
    use SoftDeletes;
    
    protected $table = 'ref_purchase_orgs';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;
}
