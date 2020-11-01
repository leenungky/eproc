<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VendorEvaluationScore extends Model
{
    protected $guarded = ['id'];
    protected $appends = ['category_name','category_type'];

    public function category(){
        return $this->belongsTo('App\VendorEvaluationScoreCategory', 'category_id')->withTrashed();
    }
    public function getCategoryNameAttribute(){
        return $this->category->name;
    }
    public function getCategoryTypeAttribute(){
        return $this->category->categories_json;
    }
}
