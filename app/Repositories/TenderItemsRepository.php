<?php

namespace App\Repositories;

use App\Models\ConditionalType;
use App\Models\TaxCode;
use App\Models\TenderAdditionalCost;
use App\Models\TenderItemText;
use App\Models\TenderTaxCode;
use App\TenderItem;
use App\TenderParameter;
use Exception;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class TenderItemsRepository extends BaseRepository
{

    private $logName = 'TenderItemsRepository';
    public $guarded = ['id', 'created_at', 'created_by', 'updated_at', 'updated_by', 'deleted_at', 'deleted_by'];

    /** @var array */
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

    /**
     *
     * @return string
     */
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
            $models = TenderItem::all();
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
                return TenderItem::findOrFail($primaryKey);
            } else {
                return TenderItem::find($primaryKey);
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
    public function findByTenderNumber($number, $params)
    {
        try {
            $dataType = !empty($params['data_type']) ? $params['data_type'] : null;
            switch ($dataType) {
                case 1: // item services
                    return (new PRListRepository)->findItemServices($params['number'], $params['line_number']);
                case 2: // item text
                    // return (new PRListRepository)->findItemTexts($params['number'], $params['line_number']);
                    return $this->findItemTexts($number, $params['item_id']);
                case 3: // tax code
                    return $this->findTenderTaxCodes($number, $params);
                case 4: // additional cost
                    return $this->findTenderAdditionalCost($number, $params);
                default:
                    return $this->findTenderItemByTenderNumber($number);
            }
        } catch (Exception $e) {
            Log::error($this->logName . '::findByTenderNumber error : ' . $e->getMessage());
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
            $query = TenderItemText::where('tender_number',$number)
                ->where('item_id',$itemId);
            return $query->get();

        } catch (Exception $e) {
            throw $e;
        }
    }

    private function findTenderItemByTenderNumber($number)
    {
        $user = Auth::user();
        if($user && !$user->isVendor()){
            $viewName = 'v_sap_pr_list_used';
        } else {
            $viewName = 'v_sap_pr_list_used_vendor';
        }
        try {
            $query = TenderItem::select(
                'tender_items.*',
                $viewName.'.qty_available'

            )->where('tender_number', $number)
            ->join($viewName, function($join) use($viewName)
            {
                $join->on('tender_items.number', '=', $viewName.'.BANFN');
                $join->on('tender_items.line_number', '=', $viewName.'.BNFPO');
            });
            return $query->get();
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
    private function findTenderAdditionalCost($number, $params)
    {
        try {
            $costType = !empty($params['cost_type']) ? $params['cost_type'] : '';
            $prId = !empty($params['pr_id']) ? $params['pr_id'] : null;
            $query = TenderAdditionalCost::where('tender_number', $number)
                ->where('conditional_type', $costType);
            if ($prId != null) {
                $item = TenderItem::findOrFail($prId);
                $query = $query->where('pr_number', $item->number)
                    ->where('pr_line_number', $item->line_number);
            }
            return $query->get();
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
    private function findTenderTaxCodes($number, $params)
    {
        try {
            $prId = !empty($params['pr_id']) ? $params['pr_id'] : null;
            $item = TenderItem::findOrFail($prId);
            $query = TenderTaxCode::where('tender_number', $number)
                ->where('pr_number', $item->number)
                ->where('pr_line_number', $item->line_number);
            return $query->get();
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
    public function save($tender, $params)
    {
        try {
            DB::beginTransaction();
            $model = null;
            if (!empty($params['item'])) {
                $model = TenderItem::find($params['item']['id']);
                $model->fill($params['item']);
                $model->save();
            }
            if (!empty($params['cost'])) {
                $this->saveAdditionalCost($tender, $params['cost'], $model);
            }
            if (!empty($params['tax'])) {
                $this->saveTaxCodes($tender, $params['tax'], $model);
            }
            if (!empty($params['item_text'])) {
                $this->saveItemText($tender, $params['item_text'], $model);
            }

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
    public function saveAdditionalCost($tender, $params, $item = null)
    {
        try {
            $OldModel = TenderAdditionalCost::where('tender_number', $tender->tender_number);
            if ($item) {
                $OldModel = $OldModel->where('pr_number', $item->number)
                    ->where('pr_line_number', $item->line_number);
            }
            $OldModel = $OldModel->get();
            foreach($OldModel as $m){
                $found = null;
                foreach ($params as $val) {
                    if($m->conditional_code == $val['conditional_code']){
                        $found = $val;
                        break;
                    }
                }
                if($found != null){
                    $m->percentage = $val['percentage'];
                    $m->value = $val['value'];
                    $m->calculation_pos = $val['calculation_pos'];
                    $m->conditional_type = $val['conditional_type'];
                    $m->save();
                }else{
                    $m->delete();
                }
            }
            foreach ($params as $val) {
                $found = null;
                foreach($OldModel as $m){
                    if($m->conditional_code == $val['conditional_code']){
                        $found = $val;
                        break;
                    }
                }
                if($found == null){
                    $data = [
                        'tender_number' => $tender->tender_number,
                        'pr_number' => $item ? $item->number : null,
                        'pr_line_number' => $item ? $item->line_number : null,
                        'conditional_code' => $val['conditional_code'],
                        'conditional_name' => $val['conditional_name'],
                        'percentage' => $val['percentage'],
                        'value' => $val['value'],
                        'calculation_pos' => $val['calculation_pos'],
                        'conditional_type' => $val['conditional_type'],
                    ];
                    TenderAdditionalCost::create($data);
                }
            }
            return true;
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
    public function saveTaxCodes($tender, $params, $item = null)
    {
        try {
            // delete before insert
            $delModel = TenderTaxCode::where('tender_number', $tender->tender_number);
            if ($item) {
                $delModel = $delModel->where('pr_number', $item->number)
                    ->where('pr_line_number', $item->line_number);
            }
            $delModel->forceDelete();

            // insert data;
            $data = [];
            foreach ($params as $val) {
                $data[] = [
                    'tender_number' => $tender->tender_number,
                    'pr_number' => $item ? $item->number : null,
                    'pr_line_number' => $item ? $item->line_number : null,
                    'tax_code' => $val['tax_code'],
                    'description' => $val['description'],
                ];
            }
            $result = TenderTaxCode::insertBulk($data);
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
    private function saveItemText($tender, $itemText, $item)
    {
        try {
            // delete before insert
            $delModel = TenderItemText::where('tender_number', $tender->tender_number)
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
                    'item_id' => $item ? $item->line_id : null,
                    'PREQ_NO' => $item->number ?? null,
                    'PREQ_ITEM' => $item->line_number ?? null,
                    'TEXT_ID' => 'B01',
                    'TEXT_ID_DESC' => 'Item text',
                    'TEXT_FORM' => $counter < (count($itemText)) ? '*' : '',
                    'TEXT_LINE' => $val,
                ];
                // TenderItemText::create($data);
            }
            $result = TenderItemText::insertBulk($data);
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
            $model = TenderItem::findOrFail($primaryKey);
            // $model->deleteflg = 'x';
            // $result = $model->save();
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
