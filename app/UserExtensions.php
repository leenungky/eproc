<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserExtensions extends Model
{
    public $timestamps = false;
    protected $guarded = ['id'];
    protected $table = 'user_extensions';
}
