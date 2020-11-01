<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VendorEvaluationScoreCategory extends Model
{
    use SoftDeletes;
    protected $guarded = ['id', 'deleted_at', 'created_at', 'updated_at'];

    public function scores(){
        return $this->hasMany('App\VendorEvaluationScore', 'category_id')->orderBy('lowest_score');
    }

}
