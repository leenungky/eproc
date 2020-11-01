<?php

namespace App\Repositories;

use App\Models\TenderWeighting;
use App\Vendor;
use Exception;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class TenderWeightingRepository extends BaseRepository
{

    private $logName = 'TenderWeightingRepository';
    public $guarded = ['id','tender_number','public_status','action_status','line_id','is_commercial','created_at','created_by','updated_at','updated_by','deleted_at','deleted_by'];


    public function __construct()
    {
    }

    public function fields()
    {
        $fields = [];
        foreach(Schema::getColumnListing((new TenderWeighting())->table) as $field){
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
            $models = TenderWeighting::all();
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
     * @return \App\Models\TenderWeighting $data
     */
    public function findById($primaryKey, $withThrow = true)
    {
        try {
            if ($withThrow) {
                return TenderWeighting::findOrFail($primaryKey);
            } else {
                return TenderWeighting::find($primaryKey);
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
    public function findByTenderNumber($number, $method = 1)
    {
        try {
            $query = TenderWeighting::where('tender_number',$number)
                ->where('submission_method', $method)
                ->get();
            return $query;
        } catch (Exception $e) {
            Log::error($this->logName . '::findByTenderNumber error : ' . $e->getMessage());
            Log::error($e);
            throw $e;
        }
    }

    public function findVendorScores($number, $vendorId, $method = 1)
    {
        try {
            $vendor = Vendor::find($vendorId);
            $query = TenderWeighting::select(
                    'tender_weightings.*',
                    'tender_vendor_scores.score',
                    DB::raw($vendorId . ' as vendor_id'),
                    DB::raw("'" . $vendor->vendor_code . "' as vendor_code"),
                )
                ->leftJoin('tender_vendor_scores', function ($join) use($vendorId) {
                    $join->on('tender_vendor_scores.weight_id', '=', 'tender_weightings.line_id')
                        ->where('tender_vendor_scores.vendor_id', $vendorId)
                        ->whereNull('tender_vendor_scores.deleted_at');
                })
                ->where('tender_weightings.tender_number',$number)
                ->where('tender_weightings.submission_method', $method)
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
     * @return \App\Models\TenderWeighting updated data
     *
     * @throws \Exception
     */
    public function save($params)
    {
        try {
            DB::beginTransaction();
            $model = new TenderWeighting();
            if(isset($params['id'])){
                $model = TenderWeighting::find($params['id']);
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
    public function delete($primaryKey)
    {
        try {
            DB::beginTransaction();
            $model = TenderWeighting::findOrFail($primaryKey);
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
