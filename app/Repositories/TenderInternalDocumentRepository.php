<?php

namespace App\Repositories;

use App\Models\TenderInternalDocument;
use Exception;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class TenderInternalDocumentRepository extends BaseRepository
{

    private $logName = 'TenderGeneralDocumentRepository';
    public $guarded = ['id','tender_number','action_status','public_status','line_id','created_at','created_by','updated_at','updated_by','deleted_at','deleted_by'];


    public function __construct()
    {
    }

    public function fields()
    {
        $fields = [];
        foreach(Schema::getColumnListing((new TenderInternalDocument())->table) as $field){
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
            $models = TenderInternalDocument::all();
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
     * @return \App\Models\TenderInternalDocument $data
     */
    public function findById($primaryKey, $withThrow = true)
    {
        try {
            if ($withThrow) {
                return TenderInternalDocument::findOrFail($primaryKey);
            } else {
                return TenderInternalDocument::find($primaryKey);
            }
        } catch (Exception $e) {
            Log::error($this->logName . '::findById error : ' . $e->getMessage());
            Log::error($e);
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
            $query = TenderInternalDocument::where('tender_number',$number)
                    ->get();
            return $query;
        } catch (Exception $e) {
            Log::error($this->logName . '::findByTenderNumber error : ' . $e->getMessage());
            Log::error($e);
            throw $e;
        }
    }

    /**
     * save record
     *
     * @param array $params
     *
     * @return \App\Models\TenderInternalDocument updated data
     *
     * @throws \Exception
     */
    public function save($params)
    {
        try {
            DB::beginTransaction();
            $model = new TenderInternalDocument();
            if(isset($params['id'])){
                $model = TenderInternalDocument::find($params['id']);
            }
            $model->fill($params);
            $model->save();
            DB::commit();
            return $model;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($this->logName . '::save error : ' . $e->getMessage());
            Log::error($e);
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
    public function delete($primaryKey, $path = null)
    {
        try {
            DB::beginTransaction();
            $model = TenderInternalDocument::findOrFail($primaryKey);
            $result = $model->delete();
            if($path){
                $this->removeStorage($path . '/' . $model->attachment);
            }
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
