<?php

namespace App\Repositories;

use App\RefProject;
use App\SapConnector;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class RefProjectRepository extends BaseRepository{

    /**
     * find all data
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findAll()
    {
        try {
            return RefProject::all();
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * find data by  id
     *
     * @param int $primaryKey
     *
     * @return \App\RefProject
     */
    public function findById($primaryKey, $withThrow = true)
    {
        try {
            if ($withThrow) {
                return RefProject::findOrFail($primaryKey);
            } else {
                return RefProject::find($primaryKey);
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * sync Ref Project from SAP Data
     *
     * @return array
     *
     * @throws \Exception
     */
    public function syncSAPData()
    {
        try {
            $result = (new SapConnector())->call('project_list', []);
            $sapFields = config('eproc.sap.showed_fields.project_list');
            $i = 0;
            $data = [];
            foreach ($result['T_PROJECT']['ITEM'] as $key => $val) {
                $row = [];
                foreach($sapFields as $sapField=>$eprocField){
                    if(in_array($eprocField,['start_date','finish_date'])){
                        $row[$eprocField] = $val[$sapField]=='00.00.0000' ? null : Carbon::createFromFormat('d.m.Y', $val[$sapField])->format('Y-m-d');
                    }else{
                        $row[$eprocField] = $val[$sapField];
                    }
                }
                $now = Carbon::now();
                $val['created_at'] = $now;
                array_push($data, $row);
            }

            if(count($data) > 0){
                // TODO: remove this line
                // RefProject::truncate();

                $newData = [];
                foreach($data as $k => $v){
                    $m = RefProject::where('company_code', $v['company_code'])
                        ->where('code', $v['code'])
                        ->first();
                    if(!$m){
                        $newData[] = $v;
                    }else{
                        $m->fill($v);
                        $m->save();
                    }
                }

                // insert sap data
                $this->insertBulk($newData);
            }
            return $data;
        } catch (Exception $e) {
            Log::error($e->getMessage());
            throw new Exception('Error Sync SAP Data.');
        }
    }

    /**
     * bulk insert record
     *
     * @param array $data
     *
     * @return boolean
     *
     * @throws \Exception
     */
    private function insertBulk(array $data)
    {
        try {
            DB::beginTransaction();
            $result = RefProject::insertBulk($data);
            DB::commit();
            return $result;
        } catch (Exception $e) {
            DB::rollBack();
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
            $model = RefProject::findOrFail($primaryKey);
            $result = $model->delete();
            DB::commit();
            return $result;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }


}