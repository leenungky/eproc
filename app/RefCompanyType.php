<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RefCompanyType extends Model
{
    use SoftDeletes;
    //
    protected $table = 'ref_company_types';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;
}
