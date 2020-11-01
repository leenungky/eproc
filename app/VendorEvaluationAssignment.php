<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VendorEvaluationAssignment extends Model
{
    protected $guarded = ['id'];
    protected $appends = ['criteria_name'];

    public function criteria(){
        return $this->belongsTo('App\VendorEvaluationCriteria', 'criteria_id')->withTrashed();
    }
    public function getCriteriaNameAttribute(){
        return $this->criteria->name ?? "";
    }
}
