<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VendorEvaluationGeneral extends Model
{
    protected $guarded = ['id'];
    protected $appends = ['category_name','category_type','po_count','po_total'];

    public function category(){
        return $this->belongsTo('App\VendorEvaluationScoreCategory', 'category_id')->withTrashed();
    }
    public function getCategoryNameAttribute(){
        return $this->category->name;
    }
    public function getCategoryTypeAttribute(){
        return $this->category->categories_json;
    }
    public function getPoCountAttribute(){
        return $this->category->po_count;
    }
    public function getPoTotalAttribute(){
        return $this->category->po_total;
    }
}
