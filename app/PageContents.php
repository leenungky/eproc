<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PageContents extends Model
{
    //
    protected $fillable = [
        'page_id', 'language', 'title', 'content'
    ];
}
