<?php

namespace App\Repositories;

use App\RefBank;
use App\SapConnector;
use Illuminate\Support\Facades\DB;


class RefBankRepository extends BaseRepository{

    /**
     * find all data
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findAll()
    {
        try {
            return RefBank::all();
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * find data by  id
     *
     * @param int $primaryKey
     *
     * @return \App\RefBank
     */
    public function findById($primaryKey, $withThrow = true)
    {
        try {
            if ($withThrow) {
                return RefBank::findOrFail($primaryKey);
            } else {
                return RefBank::find($primaryKey);
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * sync Ref Bank from SAP Data
     *
     * @return array
     *
     * @throws \Exception
     */
    public function syncSAPData()
    {
        try {
            $result = (new SapConnector())->call('bank_list', []);
            $sapFields = config('eproc.sap.showed_fields.bank_list');
            $i = 0;
            $data = [];
            foreach ($result['T_BANK_MASTER']['ITEM'] as $key => $val) {
                $row = [];
                foreach($sapFields as $sapField=>$eprocField){
                    $row[$eprocField] = $val[$sapField];
                }
                array_push($data, $row);
            }

            if(count($data) > 0){
                // TODO: remove this line
                // RefBank::truncate();

                $newData = [];
                foreach($data as $k => $v){
                    $m = RefBank::where('bank_key', $v['bank_key'])
                        ->where('country_code', $v['country_code'])
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
            $result = RefBank::insertBulk($data);
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
            $model = RefBank::findOrFail($primaryKey);
            $result = $model->delete();
            DB::commit();
            return $result;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }


}