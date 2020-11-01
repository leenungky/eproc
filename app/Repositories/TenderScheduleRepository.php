<?php

namespace App\Repositories;

use App\Models\Role;
use App\Models\TenderSchedule;
use Exception;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class TenderScheduleRepository extends BaseRepository
{

    private $logName = 'TenderScheduleRepository';

    /**
     * find all data TenderParameter
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findAll()
    {
        try {
            $models = TenderSchedule::all();
            return $models;
        } catch (Exception $e) {
            Log::error($this->logName . '::findAll error : ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * find all position
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findPositionOptions()
    {
        try {
            $models = Role::pluck('name','id');
            return $models;
        } catch (Exception $e) {
            Log::error($this->logName . '::findRoleOptions error : ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * get all type by tender submission
     *
     * @param \App\TenderParameter $tender
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTypeOptions($tender)
    {
        $scheduleTypes = TenderSchedule::TYPE;
        if($tender->prequalification == 0){
            unset($scheduleTypes[2]);
        }
        if($tender->submission_method == '2S'){
            unset($scheduleTypes[3]);
        }else{
            unset($scheduleTypes[4]);
            unset($scheduleTypes[5]);
        }
        return $scheduleTypes;
    }

    /**
     * find data TenderParameter by id
     *
     * @param int $primaryKey
     *
     * @return \App\Models\TenderSchedule $data
     */
    public function findById($primaryKey, $withThrow = true)
    {
        try {
            if ($withThrow) {
                return TenderSchedule::findOrFail($primaryKey);
            } else {
                return TenderSchedule::find($primaryKey);
            }
        } catch (Exception $e) {
            Log::error($this->logName . '::findById error : ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * find data by tender number
     *
     * @param string $number
     *
     * @return \Illuminate\Database\Eloquent\Collection $data
     */
    public function findByTenderNumber($number)
    {
        try {
            $query = TenderSchedule::where('tender_number', $number)->get();
            return $query;
        } catch (Exception $e) {
            Log::error($this->logName . '::findByTenderNumber error : ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * find data by tender number and schedule type
     *
     * @param string $number
     * @param int $type
     *
     * @return \App\Models\TenderSchedule $data
     */
    public function findByType($number, $type)
    {
        try {
            $query = TenderSchedule::where('tender_number', $number)
                ->where('type', $type)->first();
            return $query;
        } catch (Exception $e) {
            Log::error($this->logName . '::findByType error : ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * save record
     *
     * @param array $params
     *
     * @return \App\Models\TenderSchedule updated data
     *
     * @throws \Exception
     */
    public function save($params)
    {
        try {
            DB::beginTransaction();
            $model = new TenderSchedule();
            if(isset($params['id'])){
                $model = TenderSchedule::find($params['id']);
            }
            $model->fill($params);
            $model->save();
            DB::commit();
            return $model;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($this->logName . '::saveSchedule error : ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * delete record
     *
     * @param int $primaryKey
     *
     * @return bool
     *
     * @throws \Exception
     */
    public function delete($primaryKey)
    {
        try {
            DB::beginTransaction();
            $model = TenderSchedule::findOrFail($primaryKey);
            $result = $model->delete();
            DB::commit();
            return $result;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($this->logName . '::delete error : ' . $e->getMessage());
            throw $e;
        }
    }
}
