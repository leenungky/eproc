<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RefCompanyGroup extends Model
{
    protected $table = 'ref_company_groups';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'name',
        'description',
        'last_number'
    ];
    public $incrementing = false;
    public $timestamps = true;
}
