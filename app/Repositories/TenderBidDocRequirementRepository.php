<?php

namespace App\Repositories;

use App\Models\Role;
use App\Models\TenderBiddingDocumentRequirement;
use Exception;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class TenderBidDocRequirementRepository extends BaseRepository
{

    private $logName = 'TenderEvaluatorRepository';
    public $guarded = ['id','tender_number','public_status','action_status','line_id','created_at','created_by','updated_at','updated_by','deleted_at','deleted_by'];

    public function fields()
    {
        $fields = [];
        foreach(Schema::getColumnListing((new TenderBiddingDocumentRequirement())->table) as $field){
            if(!in_array($field,$this->guarded)) $fields[] = $field;
        }
        return $fields;
    }

    /**
     * find all data TenderParameter
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findAll()
    {
        try {
            $models = TenderBiddingDocumentRequirement::all();
            return $models;
        } catch (Exception $e) {
            Log::error($this->logName . '::findAll error : ' . $e->getMessage());
            Log::error($e);
            throw $e;
        }
    }

    /**
     * find data TenderParameter by id
     *
     * @param int $primaryKey
     *
     * @return \App\Models\TenderBiddingDocumentRequirement $data
     */
    public function findById($primaryKey, $withThrow = true)
    {
        try {
            if ($withThrow) {
                return TenderBiddingDocumentRequirement::findOrFail($primaryKey);
            } else {
                return TenderBiddingDocumentRequirement::find($primaryKey);
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
            return TenderBiddingDocumentRequirement::where('tender_number',$number)
                ->get();
        } catch (Exception $e) {
            Log::error($this->logName . '::findByTenderNumber error : ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * save record
     *
     * @param array $params
     *
     * @return \App\Models\TenderBiddingDocumentRequirement updated data
     *
     * @throws \Exception
     */
    public function save($params)
    {
        try {
            DB::beginTransaction();
            $model = new TenderBiddingDocumentRequirement();
            if(isset($params['id'])){
                $model = TenderBiddingDocumentRequirement::find($params['id']);
            }
            $model->fill($params);
            $model->save();
            DB::commit();
            return $model;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($this->logName . '::save error : ' . $e->getMessage());
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
            $model = TenderBiddingDocumentRequirement::findOrFail($primaryKey);
            $result = $model->delete();
            DB::commit();
            return $result;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($this->logName . '::delete error : ' . $e->getMessage());
            Log::error($e);
            throw $e;
        }
    }
}
