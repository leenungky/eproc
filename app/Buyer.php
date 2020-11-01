<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Buyer extends Model
{
    protected $guarded = [
        'id'
    ];

    protected $appends = ['user_name','purch_org','purch_group'];
    public function user(){
        return $this->belongsTo('App\User', 'user_id');
    }
    public function getUserNameAttribute(){
        return $this->user ? $this->user->userid : '';
    }
    public function purchaseorg(){
        return $this->belongsTo('App\RefPurchaseOrg', 'purch_org_id');
    }
    public function getPurchOrgAttribute(){
        return $this->purchaseorg ? $this->purchaseorg->description : '';
    }
    public function purchasegroup(){
        return $this->belongsTo('App\RefPurchaseGroup', 'purch_group_id');
    }
    public function getPurchGroupAttribute(){
        return $this->purchasegroup ? $this->purchasegroup->description : '';
    }
}
