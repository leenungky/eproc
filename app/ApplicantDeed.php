<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ApplicantDeed extends Model {

    use SoftDeletes;

    //
    protected $table = 'applicant_deeds';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;
    protected $fillable = [
        'applicant_id',
        'deed_type',
        'deed_number',
        'deed_date',
        'notary_name',
        'sk_menkumham_number',
        'sk_menkumham_date',
        'attachment',
        'created_by'
    ];

}
