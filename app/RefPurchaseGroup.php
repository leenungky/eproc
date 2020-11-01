<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RefPurchaseGroup extends Model
{
    //
    protected $table = 'ref_purchase_groups';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;
}
