<?php

namespace App\Repositories;

use App\Buyer;
use App\Enums\TenderStatusEnum;
use App\Models\ConditionalType;
use App\Models\PoReplicationStatus;
use App\Models\TaxCode;
use App\Models\PoTenderAdditionalCost;
use App\Models\PoTenderItemText;
use App\Models\PoTenderTaxCode;
use App\PoTenderItem;
use App\TenderItem;
use App\Repositories\PoRepository;
use Exception;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class PoTenderItemsRepository extends BaseRepository
{

    private $logName = 'TenderItemsRepository';
    public $guarded = ['id', 'created_at', 'created_by', 'updated_at', 'updated_by', 'deleted_at', 'deleted_by'];
    private $fields;

    public function __construct()
    {
        $fields1 = config('eproc.sap.showed_fields.prlist');
        $fields2 = [
            'EXTROW' => 'extrow',
            'KTEXT1' => 'ktext1',
            'MENGE' => 'qty',
            'MEINS' => 'uom',
            'BRTWR' => 'est_unit_price',
            'WAERS' => 'currency_code',
            'COST_CODE' => 'cost_code',
            'COST_DESC' => 'cost_desc',
        ];
        $fields3 = config('eproc.sap.showed_fields.prlist_item_text');
        $this->fields = [
            'prlist' => array_values($fields1),
            'prlist_services' => array_values($fields2),
            'prlist_item_text' => array_values($fields3),
        ];
    }

    public function fields($type = 'prlist')
    {
        return $this->fields[$type];
    }

    /**
     * find all data TenderParameter
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findAll()
    {
        try {
            $models = PoTenderItem::all();
            return $models;
        } catch (Exception $e) {
            Log::error($this->logName . '::findAll error : ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * find conditional type
     *
     * @return \Illuminate\Database\Eloquent\Collection $data
     */
    public function findConditionalType()
    {
        return ConditionalType::all();
    }

    /**
     * find tax codes
     *
     * @return \Illuminate\Database\Eloquent\Collection $data
     */
    public function findTaxCodes()
    {
        return TaxCode::all();
    }

    /**
     * find data TenderParameter by id
     *
     * @param int $primaryKey
     *
     * @return \App\Models\TenderItem $data
     */
    public function findById($primaryKey, $withThrow = true)
    {
        try {
            if ($withThrow) {
                return PoTenderItem::findOrFail($primaryKey);
            } else {
                return PoTenderItem::find($primaryKey);
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
     * @param array $params
     *
     * @return \Illuminate\Database\Eloquent\Collection $data
     */
    public function findByTenderNumber($number, $vcode, $params)
    {
        try {
            $dataType = !empty($params['data_type']) ? $params['data_type'] : null;
            switch ($dataType) {
                case 1: // item services
                    // return (new PRListRepository)->findItemServices($params['number'], $params['line_number']);
                    return $this->findItemServices($number, $vcode, $params);
                case 2: // item text
                    // return (new PRListRepository)->findItemTexts($params['number'], $params['line_number']);
                    return $this->findItemTexts($number, $params['item_id']);
                case 3: // tax code
                    return $this->findTenderTaxCodes($number, $vcode, $params);
                case 4: // additional cost
                    return $this->findTenderAdditionalCost($number, $vcode, $params);
                default:
                    return $this->findTenderItemByTenderNumber($number, $params);
            }
        } catch (Exception $e) {
            Log::error($this->logName . '::findByTenderNumber error : ' . $e->getMessage());
            throw $e;
        }
    }


    public function findItemServices($number, $vcode, $params){
        try{
            $query = DB::table('po_item_detail_services')
                ->where('tender_number', $number)
                ->where('eproc_po_number', $params['eproc_po_number'])
                ->where('BANFN', $params['number'])
                ->where('BNFPO', $params['line_number'])
                ->orderBy('EXTROW')
                ->get();
            return $query;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * find all data item text
     *
     * @param string $number, tender_number
     * @param string $lineId, line_id
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findItemTexts($number, $itemId)
    {
        try {
            $query = DB::table("po_item_text")->where('tender_number', $number)
                ->where('item_id', $itemId)
                ->where('item_id', $itemId)
                ->get();            
            return $query;
        } catch (Exception $e) {
            throw $e;
        }
    }

    private function findTenderItemByTenderNumber($number, $params)
    {
        $user = Auth::user();
        if ($user && !$user->isVendor()) {
            $viewName = 'v_sap_pr_list_used';
        } else {
            $viewName = 'v_sap_pr_list_used_vendor';
        }

        try {
            $table_tender_items = "po_items";
            $query = DB::table($table_tender_items)->select(
                $table_tender_items . '.*',
                $viewName . '.qty_available',
                DB::raw('po_item_technical_awarding.qty as qty'),
                DB::raw('po_item_technical_awarding.description as description'),
                DB::raw('po_item_commercial_awarding.est_unit_price as est_unit_price'),
                DB::raw('po_item_commercial_awarding.price_unit as price_unit'),
                DB::raw('COALESCE(po_item_commercial_awarding.overall_limit, 0) as overall_limit'),
                DB::raw('po_item_commercial_awarding.subtotal as subtotal'),

            )->where($table_tender_items . '.tender_number', $number)
                ->where($table_tender_items . '.item_id', $params["item_id"])
                ->join($viewName, function ($join) use ($viewName, $table_tender_items) {
                    $join->on($table_tender_items . '.number', '=', $viewName . '.BANFN');
                    $join->on($table_tender_items . '.line_number', '=', $viewName . '.BNFPO');
                })
                ->join("po_item_commercial_awarding", function ($join) {
                    $join->on("po_item_commercial_awarding.tender_number", "po_items.tender_number")
                        ->on("po_item_commercial_awarding.eproc_po_number", "po_items.eproc_po_number")
                        ->on("po_item_commercial_awarding.item_id", "po_items.item_id");
                })
                ->join("po_item_technical_awarding", function ($join) {
                    $join->on("po_item_technical_awarding.tender_number", "po_items.tender_number")
                        ->on("po_item_technical_awarding.eproc_po_number", "po_items.eproc_po_number")
                        ->on("po_item_technical_awarding.item_id", "po_items.item_id");
                });
            $info = $query->get();
            //dd($info);
            return $info;
        } catch (Exception $e) {
            Log::error($this->logName . '::findByTenderNumber error : ' . $e->getMessage());
            Log::error($e);
            throw $e;
        }
    }

    /**
     * find data by tender additional cost
     *
     * @param string $number
     * @param array $params
     *
     * @return \Illuminate\Database\Eloquent\Collection $data
     */
    private function findTenderAdditionalCost($number, $vcode, $params)
    {
        try {
            $costType = !empty($params['cost_type']) ? $params['cost_type'] : '';
            $prId = !empty($params['pr_id']) ? $params['pr_id'] : null;
            $table = "po_additional_costs";
            $query = DB::table($table)->where('tender_number', $number)
                ->where('conditional_type', $costType)
                ->where('eproc_po_number', $params['eproc_po_number'])
                ->where('vendor_code', $vcode)
                ->where("item_id", $prId);

            $info = $query->get();
            return $info;
        } catch (Exception $e) {
            Log::error($this->logName . '::findTenderAdditionalCost error : ' . $e->getMessage());
            throw $e;
        }
    }
    /**
     * find data by tender tax codes
     *
     * @param string $number
     * @param array $params
     *
     * @return \Illuminate\Database\Eloquent\Collection $data
     */
    private function findTenderTaxCodes($number, $vcode, $params)
    {
        try {
                $query = PoTenderTaxCode::where('tender_number', $number)
                    ->where('item_id', $params['pr_id'])
                    ->where('eproc_po_number', $params["eproc_po_number"])
                    ->where('tender_number', $number)
                    ->where('vendor_code', $vcode)
                    ->get();
                
                return $query;

        } catch (Exception $e) {
            Log::error($this->logName . '::findTenderTaxCodes error : ' . $e->getMessage());
            throw $e;
        }
    }


    /**
     * save record
     *
     * @param \App\TenderParameter $tender
     * @param array $params
     *
     * @return boolean
     *
     * @throws \Exception
     */
    public function save($tender, $vcode, $params)
    {
        $eproc_po_number = $params["eproc_po_number"];
        try {
            DB::beginTransaction();
            $model = null;
            if (!empty($params['item'])) {
                $item = $params['item'];
                $item_id =$item['id'];
               
                $item["qty"] = $this->formatNumber($item["qty"]);
                
                unset($item["id"]);
                PoTenderItem::where("id", $item_id)->update($item);

                // $arr_data_tech = array(
                //     "description" => $item['description'],
                //     "qty" => $item["qty"]);
                // $arr_data_com = array("overall_limit" => $item['overall_limit'], 
                //     "est_unit_price" => $item["est_unit_price"]);
                // DB::table("po_item_technical_awarding")->where("item_id", $item_id)->update($arr_data_tech);
                // DB::table("po_item_commercial_awarding")->where("item_id", $item_id)->update($arr_data_com);
                $poRepo = new PoRepository();

                $arr_data = array(
                    "qty" => $item["qty"],
                    "description" => $item["description"]
                );
                $poRepo->getQryTechnical("po_item_technical_awarding", $eproc_po_number, $tender->tender_number, $vcode)->update($arr_data);
                $arr_data = array(
                    "est_unit_price" => $item["est_unit_price"],
                    "overall_limit" => $item["overall_limit"]
                );
                $poRepo->getQryCommercial("po_item_commercial_awarding", $eproc_po_number, $tender->tender_number, $vcode)->update($arr_data);
                // $com->est_unit_price = $params['item']["est_unit_price"];
                // $com->overall_limit = $params['item']["overall_limit"];
                // $com->save();
            }
            //dd($params['cost']);
            //if (!empty($params['cost'])) {
            $model = PoTenderItem::where("id", $item_id)->first();
            $cost_type = $params["cost_type"];           
            if ($cost_type=="CT2"){
                $this->saveAdditionalCost($tender, $vcode, $params['cost'],  $cost_type, $model);
            }else{
                $eproc_po_number = $params["eproc_po_number"];
                $this->saveHeaderAdditionalCost($tender, $vcode, $params,  $eproc_po_number);
            }
            if (!empty($params['tax'])) {                
                $eproc_po_number = $params["eproc_po_number"];                
                $this->saveTaxCodes($tender, $vcode,  $eproc_po_number, $params['tax'], $model);
            }
            if (!empty($params['item_text'])) {
                $this->saveItemText($tender, $params['item_text'], $model);
            }

            $this->savePoItemReplication($tender);
            DB::commit();
            return true; // $model;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($this->logName . '::save error : ' . $e->getMessage());
            throw $e;
        }
    }
    /**
     * save record additional cost
     *
     * @param \App\TenderParameter $tender
     * @param array $params
     * @param \App\Models\TenderItem $item
     *
     * @return boolean
     *
     * @throws \Exception
     */
    public function saveAdditionalCost($tender, $vcode, $params, $cost_type, $item = null)
    {
        try {
            // delete before insert
            $delModel = PoTenderAdditionalCost::where('tender_number', $tender->tender_number)
                ->where('conditional_type', $cost_type)
                ->where('vendor_code', $vcode)
                ->where('item_id', $item->item_id)
                ->delete();

            // insert data;
            $data = [];
            foreach ($params as $val) {
                $data[] = [
                    'tender_number' => $tender->tender_number,
                    'pr_number' => $item ? $item->number : null,
                    'pr_line_number' => $item ? $item->line_number : null,
                    'conditional_code' => $val['conditional_code'],
                    'conditional_name' => $val['conditional_name'],
                    'percentage' => $this->formatNumber($val['percentage']),
                    'value' => $val['value'],
                    'calculation_pos' => $val['calculation_pos'],
                    'conditional_type' => $cost_type,
                    'eproc_po_number' => $item->eproc_po_number,
                    'vendor_code' => $vcode,
                    'item_id' => $item->item_id
                ];
            }
            $result = PoTenderAdditionalCost::insertBulk($data);
            return $result;
        } catch (Exception $e) {
            Log::error($this->logName . '::saveAdditionalCost error : ' . $e->getMessage());
            throw $e;
        }
    }

    public function saveHeaderAdditionalCost($tender, $vcode, $params, $eproc_po_number) {
        try {
            // delete before insert
            $delModel = PoTenderAdditionalCost::where('tender_number', $tender->tender_number)
                ->where('conditional_type', "CT1")
                ->where('vendor_code', $vcode)
                ->where('eproc_po_number', $eproc_po_number)
                ->delete();
            // insert data;
            $data = [];
            foreach ($params["cost"] as $val) {
                $data[] = [
                    'tender_number' => $tender->tender_number,
                    'conditional_code' => $val['conditional_code'],
                    'conditional_name' => $val['conditional_name'],
                    'percentage' => $this->formatNumber($val['percentage']),
                    'value' => $val['value'],
                    'calculation_pos' => $val['calculation_pos'],
                    'conditional_type' => "CT1",
                    'eproc_po_number' => $eproc_po_number,
                    'vendor_code' => $vcode,
                ];
            }
            $result = PoTenderAdditionalCost::insertBulk($data);
            return $result;
        } catch (Exception $e) {
            Log::error($this->logName . '::saveAdditionalCost error : ' . $e->getMessage());
            throw $e;
        }
    }
    /**
     * save record tax codes
     *
     * @param \App\TenderParameter $tender
     * @param array $params
     * @param \App\Models\TenderItem $item
     *
     * @return boolean
     *
     * @throws \Exception
     */
    public function saveTaxCodes($tender,  $vcode,  $eproc_po_number, $params, $item = null)
    {
        try {
            // delete before insert
            $delModel = PoTenderTaxCode::where('tender_number', $tender->tender_number)
            ->where("eproc_po_number", $eproc_po_number)
            ->where("vendor_code",$vcode)
            ->whereNull("deleted_at")
            ->where("item_id", $item->item_id)
            ->delete();
            $data = [];
            foreach ($params as $val) {
                $data[] = [
                    'tender_number' => $tender->tender_number,
                    'pr_number' => $item ? $item->number : null,
                    'pr_line_number' => $item ? $item->line_number : null,
                    'tax_code' => $val['tax_code'],
                    'description' => $val['description'],
                    'eproc_po_number' => $eproc_po_number,
                    'vendor_code' => $vcode, 
                    'item_id' => $item->item_id
                ];
            }
            $result = PoTenderTaxCode::insertBulk($data);
            return $result;
        } catch (Exception $e) {
            Log::error($this->logName . '::saveTaxCodes error : ' . $e->getMessage());
            throw $e;
        }
    }
    /**
     * save record item text
     *
     * @param \App\TenderParameter $tender
     * @param array $params
     * @param \App\Models\TenderItem $item
     *
     * @return boolean
     *
     * @throws \Exception
     */
    private function savePoItemReplication($tender)
    {

        $status = PoReplicationStatus::where("tender_number", $tender->tender_number)->first();
        if (empty($status)) {
            $data = [
                'tender_number' => $tender->tender_number,
                'action_status' => 1
            ];
            PoReplicationStatus::insert($data);
        } else {
            $status->action_status = 1;
            $status->save();
        }
    }

    private function saveItemText($tender, $itemText, $item)
    {
        try {
            // delete before insert
            //dd('a');
            $delModel = PoTenderItemText::where('tender_number', $tender->tender_number)
                ->where('item_id', $item->line_id)
                ->delete();

            $itemText = str_replace("\n", "_*_", $itemText);
            $itemText = explode('_*_', $itemText);
            // insert data;
            $data = [];
            $counter = 0;
            foreach ($itemText as $val) {
                $counter++;
                $data[] = [
                    'tender_number' => $tender->tender_number,
                    'item_id' => $item ? $item->item_id : null,
                    'PREQ_NO' => $item->number ?? null,
                    'PREQ_ITEM' => $item->line_number ?? null,
                    'TEXT_ID' => 'B01',
                    'TEXT_ID_DESC' => 'Item text',
                    'TEXT_FORM' => $counter < (count($itemText)) ? '*' : '',
                    'TEXT_LINE' => $val,
                ];
                // TenderItemText::create($data);
            }
            $result = PoTenderItemText::insertBulk($data);
            return true;
        } catch (Exception $e) {
            Log::error($this->logName . '::saveItemText error : ' . $e->getMessage());
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
            $model = PoTenderItem::findOrFail($primaryKey);
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
    public function formatNumber($val)
    {
        return (float)number_format((float)str_replace(",", ".", $val), 4, ".", "");
    }
}
