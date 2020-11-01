<?php

namespace App\Repositories;

use App\Buyer;
use App\Models\Role;
use App\Models\TenderEvaluator;
use App\Models\TenderPermission;
use Exception;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TenderEvaluatorRepository extends BaseRepository
{

    private $logName = 'TenderEvaluatorRepository';
    public $guarded = ['id','created_at','created_by','updated_at','updated_by','deleted_at','deleted_by'];
    private $_fields = ['buyer_name','stage_type','buyer_type_name',];

    public function fields()
    {
        return $this->_fields;
    }

    /**
     * find all data buyer
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findBuyerTypeOptions()
    {
        try {
            $models = TenderPermission::pluck('name','id');
            return $models;
        } catch (Exception $e) {
            Log::error($this->logName . '::findBuyerTypeOptions error : ' . $e->getMessage());
            Log::error($e);
            throw $e;
        }
    }

    /**
     * find all data TenderEvaluator
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function findAll($number)
    {
        try {
            // $models = TenderEvaluator::all();
            $query = TenderEvaluator::select(
                'tender_evaluators.*',
                DB::raw("ref_buyers.buyer_name"),
                'users.email',
                'tep.permission_ids',
                'tep.permission_names as buyer_type_name',
                'user_extensions.position'
            )
            ->join('ref_buyers','tender_evaluators.buyer_user_id','ref_buyers.user_id')
            ->join('users','tender_evaluators.buyer_user_id','users.id')
            ->leftJoin('user_extensions','user_extensions.user_id','=','users.id')
            ->leftJoin(DB::raw("(select tep.evaluator_id,STRING_AGG(CAST(tep.permission_id as varchar), ',') as permission_ids,
                STRING_AGG(CAST(tp.name as varchar), ',') as permission_names
                from tender_evaluator_has_permissions tep
                join tender_permissions tp on tp.id=tep.permission_id
                group by evaluator_id) tep"), function ($join) {
                $join->on('tep.evaluator_id', '=', 'tender_evaluators.line_id');
            });

            if($number != null){
                $query = $query->where('tender_number',$number);
            }

            return $query;
        } catch (Exception $e) {
            Log::error($this->logName . '::findAll error : ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * find data TenderParameter by id
     *
     * @param int $primaryKey
     *
     * @return \App\Models\TenderEvaluator $data
     */
    public function findById($primaryKey, $withThrow = true)
    {
        try {
            if ($withThrow) {
                return TenderEvaluator::findOrFail($primaryKey);
            } else {
                return TenderEvaluator::find($primaryKey);
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
    public function findByTenderNumber($number, $userId = null)
    {
        try {
            $query = TenderEvaluator::select(
                    'tender_evaluators.*',
                    DB::raw("ref_buyers.buyer_name"),
                    'users.email',
                    'tep.permission_ids',
                    'tep.permission_names as buyer_type_name'
                )
                ->join('ref_buyers','tender_evaluators.buyer_user_id','ref_buyers.user_id')
                ->join('users','tender_evaluators.buyer_user_id','users.id')
                // ->join('roles','tender_evaluators.buyer_type_id','roles.id')
                ->leftJoin(DB::raw("(select tep.evaluator_id,STRING_AGG(CAST(tep.permission_id as varchar), ',') as permission_ids,
                    STRING_AGG(CAST(tp.name as varchar), ',') as permission_names
                    from tender_evaluator_has_permissions tep
                    join tender_permissions tp on tp.id=tep.permission_id
                    group by evaluator_id) tep"), function ($join) {
                    $join->on('tep.evaluator_id', '=', 'tender_evaluators.line_id');
                })
                ->where('tender_number',$number);

            if(!empty($userId)){
                $query->where('buyer_user_id', $userId);
                return $query->first();
            }else{
                return $query->get();
            }
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
     * @return \App\Models\TenderEvaluator updated data
     *
     * @throws \Exception
     */
    public function save($params)
    {
        try {
            DB::beginTransaction();
            $model = new TenderEvaluator();
            if(isset($params['id'])){
                $model = TenderEvaluator::find($params['id']);
            }
            $model->fill($params);
            $model->save();
            $this->savePermission($model->line_id, $params['buyer_type_ids']);
            DB::commit();
            return $model;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($this->logName . '::save error : ' . $e->getMessage());
            Log::error($e);
            throw $e;
        }
    }

    private function savePermission($evaluatorId,$permissions)
    {
        // delete old permissions
        DB::table('tender_evaluator_has_permissions')
        ->where('evaluator_id', $evaluatorId)->delete();
        // insert new permissions
        $data = [];
        if(!empty($permissions) && count($permissions) > 0) {
            foreach($permissions as $p){
                if($p != null){
                    $data[] = [
                        'permission_id' => $p,
                        'evaluator_id' => $evaluatorId,
                    ];
                }
            }
        }
        if(count($data) > 0){
            DB::table('tender_evaluator_has_permissions')->insert($data);
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
            $model = TenderEvaluator::findOrFail($primaryKey);
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
