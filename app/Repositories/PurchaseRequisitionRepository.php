<?php

namespace App\Repositories;

use App\PurchaseRequisition;
use Exception;
use Illuminate\Support\Facades\DB;

class PurchaseRequisitionRepository extends BaseRepository
{

    // private $logName = 'PurchaseReuisitionRepository';

    public function __construct()
    {
    }

    /**
     * find all data
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findAll()
    {
        try {
            $listRepo = new PRListRepository();
            $result = $listRepo->findAll();
            // if($result->count() <= 0){
            //     $listRepo->syncSAPData();
            //     $result = $listRepo->findAll();
            // }

            $i = 0;
            $fields = config('eproc.sap.showed_fields.prlist');
            $data=[];
            foreach ($result as $key => $val) {
                $item = ['id' => ++$i];
                foreach ($fields as $k => $v) {
                    $item[$v] = $val[$k];
                }
                array_push($data, $item);
            }

            return $data;

            // return PurchaseRequisition::all();
        } catch (Exception $e) {
            throw $e;
        }
    }

}
