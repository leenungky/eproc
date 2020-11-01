<?php

namespace App\Models\Ref;

use Illuminate\Database\Eloquent\Model;

class RefBuyerPurchGroup extends Model
{
    //
    protected $guarded = [];
    protected $primaryKey = ['user_id','purch_group_id'];
    public $incrementing = false;
    public $timestamps = false;

    protected $appends = ['user_name','valid_from_date','valid_thru_date'];
    public function buyer(){
        return $this->belongsTo('App\Models\Ref\RefBuyer', 'user_id');
    }
    public function getUserNameAttribute(){
        return $this->buyer ? $this->buyer->buyer_name : '';
    }
    public function getValidFromDateAttribute(){
        return $this->buyer ? $this->buyer->valid_from_date : date('Y-m-d');
    }
    public function getValidThruDateAttribute(){
        return $this->buyer ? $this->buyer->valid_thru_date : date('Y-m-d');
    }
}
