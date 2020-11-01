<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VendorEvaluationCriteria extends Model
{
    use SoftDeletes;
    protected $guarded = ['id', 'deleted_at', 'created_at', 'updated_at'];
    protected $appends = ['criteria_group_name'];

    public function criteriaGroup(){
        return $this->belongsTo('App\VendorEvaluationCriteriaGroup', 'criteria_group_id')->withTrashed();
    }
    public function getCriteriaGroupNameAttribute(){
        return $this->criteriaGroup->name ?? "";
    }
}
