<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PoTenderItemTechnical extends Model
{
    public $table = 'po_item_technical_awarding';

    protected $fillable = [
        'qty',
        'description',
    ];
}
