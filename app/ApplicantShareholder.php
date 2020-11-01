<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ApplicantShareholder extends Model {

    use SoftDeletes;

    //
    protected $table = 'applicant_shareholders';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;
    
    protected $fillable = [
        'applicant_id',
        'full_name',
        'nationality',
        'share_percentage',
        'email',
        'identity_number',
        'identity_attachment',
        'created_by'
    ];

}
