<?php

namespace App\Models;

use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class TenderLogs extends Model
{
    public $table = 'tender_logs';

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        "tender_number" ,
        "user_id" ,
        "activity" ,
        'model_id',
        'model_type',
        'page_type',
        'ref_number',
        'properties',
        'host',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id','userid');
    }

    public function getUpdatedAtAttribute($value)
    {
        return $value ? Carbon::parse($value)->format(BaseModel::DATETIME_FORMAT) : null;
    }

    /**
     * @param array $data
     */
    public static function createNew($data)
    {
        $user = Auth::user();
        return TenderLogs::create([
            'user_id' => $user->userid ?? null,
            'activity' => $data['activity'] ?? null,
            'model_id' => $data['model_id'] ?? null,
            'model_type' => $data['model_type'] ?? null,
            'page_type' => $data['page_type'] ?? null,
            'ref_number' => $data['ref_number'] ?? null,
            'properties' => $data['properties'] ?? null,
            'host' => request()->ip() ?? null,
        ]);
    }
}
