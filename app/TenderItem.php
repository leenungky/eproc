<?php

namespace App;

use App\Models\BaseModel;
use App\Traits\PublicView;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TenderItem extends BaseModel
{
    //
    use SoftDeletes, PublicView;

    public $table = 'tender_items';
    protected $guarded = ['id', 'deleted_at', 'created_at', 'updated_at'];
}
