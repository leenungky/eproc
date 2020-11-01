<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VendorSanctionHistory extends Model
{
    //
    protected $appends = ['sanction_detail'];
    public function sanction(){
        return $this->belongsTo('App\VendorSanction', 'vendor_sanction_id')->withTrashed();
    }
    public function getSanctionDetailAttribute(){
        return $this->sanction->attachment;
    }
}
