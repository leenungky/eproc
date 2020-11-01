<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PoVendorProfile extends Model
{
    public $table = 'po_vendor_profile';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;
}
