<?php

namespace App\Models\Ref;

use Illuminate\Database\Eloquent\Model;

class RefBuyer extends Model
{
    //
    protected $guarded = [];
    protected $primaryKey = 'user_id';
    public $incrementing = false;
    public $timestamps = true;
    protected $appends = ['user_name','purch_org','purch_group','is_active'];
    
    public function user(){
        return $this->belongsTo('App\User', 'user_id');
    }
    public function getUserNameAttribute(){
        return $this->user ? $this->user->userid : '';
    }

    public function purchaseorg(){
        return $this->hasManyThrough(
            'App\RefPurchaseOrg', 
            'App\Models\Ref\RefBuyerPurchOrg', 
            'user_id',          //fk to refbuyer on refbuyerpurchorg
            'id',               //fk to refbuyerpurchorg on refpurchaseorg
            'user_id',          //local on refbuyer
            'purch_org_id'      //local on refbuyerpurchorg
        );
    }
    public function getPurchOrgAttribute(){
        if($this->purchaseorg){
            return $this->purchaseorg->toArray();
        }else{
            return [];
        }
    }

    public function purchasegroup(){
        return $this->hasManyThrough(
            'App\RefPurchaseGroup', 
            'App\Models\Ref\RefBuyerPurchGroup', 
            'user_id',          //fk to refbuyer on refbuyerpurchgroup
            'id',               //fk to refbuyerpurchgroup on refpurchasegroup
            'user_id',          //local on refbuyer
            'purch_group_id'    //local on refbuyerpurchgroup
        );
    }
    public function getPurchGroupAttribute(){
        if($this->purchasegroup){
            return $this->purchasegroup->toArray();
        }else{
            return [];
        }
    }
    public function getIsActiveAttribute(){
        return $this->valid_from_date <= date('Y-m-d') && $this->valid_thru_date >= date('Y-m-d');
    }
}
