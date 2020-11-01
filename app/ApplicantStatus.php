<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ApplicantStatus extends Model {

    //
    protected $table = 'applicant_statuss';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = false;
    
    protected $fillable = [
        'applicant_id',
        'status_id',
        'remarks'
    ];

}
