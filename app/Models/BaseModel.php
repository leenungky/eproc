<?php

namespace App\Models;

use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class BaseModel extends Model
{
    const DATE_FORMAT = 'd.m.Y';
    const DATETIME_FORMAT = 'd.m.Y H:i';
    const DB_DATETIME_FORMAT = 'Y-m-d H:i:s';
    const DB_DATE_FORMAT = 'Y-m-d';
    const DB_DATETIME = 'Y-m-d H:i';
    // const DATE_DISPLAY_FORMAT = 'd.m.Y H:i';
    const CREATED_BY = 'created_by';
    const UPDATED_BY = 'updated_by';

    public static function boot()
    {
        parent::boot();
        static::deleting(function ($model) {
            if($model->hasAttribute('deleted_by')){
                $model->deleted_by = Auth::user()->userid ?? 0;
                $model->save();
            }
        });
    }

    public function hasAttribute($attr)
    {
        return array_key_exists($attr, $this->attributes);
    }

    public function setUpdatedAt($value)
    {
        try {
            $this->{static::UPDATED_BY} = Auth::user()->userid ?? 0; // Auth::id(); <- error kalau dipanggil dari cron
        } catch (Exception $e) {
            Log::error($e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine());
        }
        parent::setUpdatedAt($value);
    }

    public function setCreatedAt($value)
    {
        try {
            $this->{static::CREATED_BY} = Auth::user()->userid ?? 0; // Auth::id();
            $this->{static::UPDATED_BY} = Auth::user()->userid ?? 0; // Auth::id();
        } catch (Exception $e) {
            Log::error($e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine());
        }
        parent::setCreatedAt($value);
    }

    /**
     * insert bulk
     *
     * @param array $data
     *
     * @return boolean
     */
    public static function insertBulk($data,  $userId = null)
    {
        try {
            $model = (new static);
            if ($model->usesTimestamps()) {
                foreach ($data as $k => $v) {
                    $data[$k]['created_at'] = Carbon::now();
                    $data[$k]['created_by'] = $userId ? $userId : Auth::user()->userid ?? 'system';
                    $data[$k]['updated_at'] = Carbon::now();
                    $data[$k]['updated_by'] = $userId ? $userId : Auth::user()->userid ?? 'system';
                }
            }
            return $model->insert($data);
        } catch (Exception $e) {
            // Log::error($e);
            throw $e;
        }
        // return static::insert($data);
        return false;
    }
}
