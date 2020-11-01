<?php

namespace App\Repositories;

use App\Models\SapPRList;
use App\Models\SapPRListItemText;
use App\Models\SapPRListServices;
use App\SapConnector;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PRListRepository extends BaseRepository
{

    private $logName = 'PRListRepository';
    private $fields;

    public function __construct()
    {
        $fields1 = config('eproc.sap.showed_fields.prlist');
        $fields2 = config('eproc.sap.showed_fields.prlist_services');
        $fields3 = config('eproc.sap.showed_fields.prlist_item_text');
        $this->fields = [
            'prlist' => array_keys($fields1),
            'prlist_services' => array_keys($fields2),
            'prlist_item_text' => array_keys($fields3),
        ];
    }

    /**
     * find all data
     *
     * @param bool $whiteUsed, white used data tender item
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findAll($whiteUsed = false)
    {
        try {
            $query = SapPRList::select(
                "sap_pr_list.id", "sap_pr_list.BANFN", "sap_pr_list.BNFPO", "MATNR", "TXZ01", "MATKL", "WGBEZ60", "DESCTXZ01",
                DB::raw('(CASE when "qty_available" is null then "MENGE" else "qty_available" end) as "MENGE"'),
                "MEINS", "PEINH", "MSEHL", "PREIS", "PREIS2", "BADAT", "DISPO", "DSNAM", "WERKS", "NAME1", "LGORT", "LGOBE", "KNTTP", "KNTTX", "YEARS", "YEARS2", "WAERS", "LTEXT", "PSTYP", "PTEXT", "LFDAT", "EKGRP", "EKNAM", "COST_CODE", "COST_DESC", "SAKTO", "TXT50", "LOEKZ", "EBAKZ", "STATU", "DESCSTATU", "BSART", "BATXT", "ERNAM", "AFNAM", "ZRDESC", "FRGKZ", "FKZTX", "BEDNR", "BSMNG", "ZZCERT", "ZZSTAT", "SUMLIMIT", "COMMITMENT",
                'sap_pr_list.created_by', 'sap_pr_list.updated_by',
                'sap_pr_list.created_at', 'sap_pr_list.updated_at', 'sap_pr_list.deleted_at'
            )
            ->leftJoin('v_sap_pr_list_used', function($join)
            {
                $join->on('sap_pr_list.BANFN', '=', 'v_sap_pr_list_used.BANFN');
                $join->on('sap_pr_list.BNFPO', '=', 'v_sap_pr_list_used.BNFPO');
            });
            if($whiteUsed == false){
                $query = $query->where(function ($query) {
                    $query->whereNull('qty_available')
                          ->orWhere('qty_available', '>', 0);
                });
            }
            return $query->get();
        } catch (Exception $e) {
            Log::error($this->logName . '::findAll error : ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * find all data item service
     *
     * @param string $number
     * @param string $lineNumber
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findItemServices($number, $lineNumber)
    {
        try {
            $query = SapPRListServices::where('BANFN',$number)
                ->where('BNFPO',$lineNumber);
            return $query->get();
        } catch (Exception $e) {
            Log::error($this->logName . '::findItemServices error : ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * find all data item text
     *
     * @param string $number
     * @param string $lineNumber
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findItemTexts($number, $lineNumber)
    {
        try {
            $query = SapPRListItemText::where('PREQ_NO',$number)
                ->where('PREQ_ITEM',$lineNumber);
            return $query->get();

        } catch (Exception $e) {
            Log::error($this->logName . '::findItemTexts error : ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * find data by  id
     *
     * @param int $primaryKey
     *
     * @return \App\Models\SapPRList
     */
    public function findById($primaryKey, $withThrow = true)
    {
        try {
            if ($withThrow) {
                return SapPRList::findOrFail($primaryKey);
            } else {
                return SapPRList::find($primaryKey);
            }
        } catch (Exception $e) {
            Log::error($this->logName . '::findItemTexts error : ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * sync pr list from SAP Data
     *
     * @return array
     *
     * @throws \Exception
     */
    public function syncSAPData()
    {
        try {
            $result = (new SapConnector())->call('pr_list', ['IPRLIST' => 'X']);
            return $this->saveSapPr($result);
        } catch (Exception $e) {
            Log::error($this->logName . '::syncSAPData error : ' . $e->getMessage());
            throw new Exception('Error Sync SAP Data.');
        }
    }

    private function saveSapPr($result)
    {
        try {
            DB::beginTransaction();
            $now = Carbon::now();
            $data = $this->savePrItem($result['T_ITEM']['ITEM'], $now);
            $this->savePrItemService($result['T_ITEMSERVICE']['ITEM'], $now);
            $this->savePrItemText($result['T_ITEM']['ITEM'], $result['T_ITEMTEXT']['ITEM'], $now);
            DB::commit();
            return $data;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($this->logName . '::saveSapPr error : ' . $e->getMessage());
            throw $e;
        }
    }
    private function savePrItem($resultItem, $now)
    {
        $data = [];
        if(isset($resultItem)){
            foreach ($resultItem as $key => $val) {
                $val['created_at'] = $now;
                $val['updated_at'] = $now;
                array_push($data, $this->pushData('prlist',$val));
            }
        }
        if(count($data) > 0){
            $newData = [];
            foreach($data as $k => $v){
                $m = SapPRList::where('BANFN', $v['BANFN'])
                    ->where('BNFPO', $v['BNFPO'])
                    ->first();
                if(!$m){
                    $newData[] = $v;
                }else{
                    // update sap data
                    $m->fill($v);
                    $m->save();
                }
            }

            // insert sap data
            if(count($newData) > 0){
                SapPRList::insertBulk($newData);
            }
            return $data;
        }
    }

    private function savePrItemService($resultItem, $now)
    {
        return $this->updateOrCreatePrItemService($resultItem, $now);
        // return $this->deleteInsertPrItemService($resultItem, $now);
    }

    private function updateOrCreatePrItemService($resultItem, $now)
    {
        if(isset($resultItem)){
            foreach ($resultItem as $key => $val) {
                $val['created_at'] = $now;
                $val['updated_at'] = $now;
                // array_push($data, $this->pushData('prlist_services',$val));
                SapPRListServices::updateOrCreate([
                    'BANFN' => $val['BANFN'],
                    'BNFPO' => $val['BNFPO'],
                    'EXTROW' => $val['EXTROW'],
                ],
                $val);
            }
        }
    }
    private function deleteInsertPrItemService($resultItem, $now)
    {
        $data = [];
        if(isset($resultItem)){
            foreach ($resultItem as $key => $val) {
                $val['created_at'] = $now;
                $val['updated_at'] = $now;
                array_push($data, $this->pushData('prlist_services',$val));
            }
        }
        if(count($data) > 0){
            // // TODO : remove this
            SapPRListServices::truncate();

            // insert sap data
            SapPRListServices::insertBulk($data);
            return $data;
        }
    }

    private function savePrItemText($resultItem, $resultItemText, $now)
    {
        // return $this->updateOrCreatePrItemText($resultItem, $resultItemText, $now);
        return $this->deleteInsertPrItemText($resultItem, $resultItemText, $now);
    }
    private function deleteInsertPrItemText($resultItem, $resultItemText, $now)
    {
        $data = [];
        if(isset($resultItemText)){
            foreach ($resultItemText as $key => $val) {
                $val['created_at'] = $now;
                $val['updated_at'] = $now;
                array_push($data, $this->pushData('prlist_item_text',$val));
            }
        }
        if(count($data) > 0){
            // TODO : remove this
            // SapPRListItemText::truncate();

            // TODO: for production
            if(isset($resultItem)){
                foreach ($resultItem as $key => $val) {
                    SapPRListItemText::where('PREQ_NO', $val['BANFN'])
                        ->where('PREQ_ITEM', $val['BNFPO'])
                        ->forceDelete();
                }
            }

            // insert sap data
            SapPRListItemText::insertBulk($data);
            return $data;
        }
    }

    private function pushData($typeIdx, $val)
    {
        $data = [];
        foreach($this->fields[$typeIdx] as $key){
            $keySap = $key;
            $data[$key] = $val[$keySap];
        }
        return $data;
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
            $model = SapPRList::findOrFail($primaryKey);
            $result = $model->delete();
            DB::commit();
            return $result;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($this->logName . '::delete error : ' . $e->getMessage());
            throw $e;
        }
    }

    private function _syncSAPData($force = false)
    {
        if ($force) {
            Cache::forget('prlist');
        }

        $seconds = config('eproc.sap.cachetime') ?? 300;

        $data = Cache::remember('prlist', $seconds, function () {
            $fields = config('eproc.sap.showed_fields.prlist');
            $sap = new SapConnector();
            $result = $sap->call('pr_list', ['IPRLIST' => 'X']);
            $i = 0;
            $tmp = [];
            foreach ($result['TPRNO']['ITEM'] as $key => $val) {
                // $result['TPRNO']['ITEM'][$key]['id'] = $i++;
                $item = ['id' => $i++];
                foreach ($fields as $k => $v) {
                    $item[$v] = $val[$k];
                }
                array_push($tmp, $item);
            }

            // return collect($result['TPRNO']['ITEM']);
            return collect($tmp);
        });

        return $data;
    }
}
