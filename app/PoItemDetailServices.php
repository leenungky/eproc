<?php

namespace App;

use App\Models\BaseModel;
use App\Traits\PublicView;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PoItemDetailServices extends BaseModel
{
    use SoftDeletes;

    public $table = 'po_item_detail_services';
    protected $guarded = ['id', 'deleted_at', 'created_at', 'updated_at'];
}
