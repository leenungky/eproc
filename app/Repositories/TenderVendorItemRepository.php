<?php

namespace App\Repositories;

use App\Enums\TenderStatusEnum;
use App\Enums\TenderSubmissionEnum;
use App\Helpers\App as HelpersApp;
use App\Models\SapPRListServices;
use App\Models\TenderAdditionalCost;
use App\Models\TenderItemCommercial;
use App\Models\TenderItemDetail;
use App\Models\TenderItemTechnical;
use App\Models\TenderItemText;
use App\Models\TenderTaxCode;
use App\Models\TenderVendorAdditionalCost;
use App\Models\TenderVendorAdditionalCostAwarding;
use App\Models\TenderVendorItemDetail;
use App\Models\TenderVendorItemText;
use App\Models\TenderVendorItemTextAwarding;
use App\Models\TenderVendorSubmission;
use App\Models\TenderVendorSubmissionDetail;
use App\Models\TenderVendorTaxCode;
use App\Models\TenderVendorTaxCodeAwarding;
use App\Scopes\VendorViewScope;
use App\TenderItem;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use App\Scopes\PublicViewScope;

class TenderVendorItemRepository extends BaseRepository
{

    private $logName = 'TenderVendorItemRepository';
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
     * find data table
     *
     * @param string $number, tender number
     * @param string $stageType, item type
     * @param string $params, request parameter
     *
     * @return \Yajra\DataTables\DataTables $dataTable
     *
     * @throws \Exception
     */
    public function findDataTable($number, $stageType, $params)
    {
        try {
            $dataType = !empty($params['data_type']) ? $params['data_type'] : null;
            switch ($dataType) {
                case 1: // item services
                    return $this->findDataTableItemServices($number, $stageType, $params);
                case 2: // item text
                    // dd($params);
                    return $this->findDataTableItemTexts($number, $stageType, $params);
                case 3: // tax code
                    return $this->findDataTableTaxCodes($number, $stageType, $params);
                case 4: // additional cost
                    return $this->findDataTableAdditionalCost($number, $stageType, $params);
                default:
                    return $this->findDataTablePrItem($number, $stageType, $params);
            }
        } catch (Exception $e) {
            Log::error($this->logName . '::findByTenderNumber error : ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * save data tender items
     *
     * @param array $params
     * @param \App\TenderParameter $tender
     * @param string $stageType
     *
     * @return array $result
     */
    public function saveTenderVendorItems($params, $tender, $stageType)
    {
        try {
            $vendor = Auth::user()->vendor;
            $dataType = !empty($params['data_type']) ? $params['data_type'] : null;
            $params['vendor_id'] = $vendor->id;
            $params['vendor_code'] = $vendor->vendor_code;
            switch ($dataType) {
                case 1: // save pr list item single
                    if ($stageType == TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE['process_technical_evaluation']) {
                        $result = $this->saveItemTechnical($tender, $params, $stageType);
                    } else if ($stageType == TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE['process_commercial_evaluation']) {
                        $result = $this->saveItemCommercial($tender, $params, $stageType);
                    }
                    return [
                        'data' => $tender,
                        'next' => null,
                    ];
                default: // save pr list item batch
                    return $this->saveBatchTenderPrItems($params, $tender, $stageType);
            }
        } catch (Exception $e) {
            Log::error($this->logName . '::saveTenderVendorItems error : ' . $e->getMessage());
            throw $e;
        }
    }

    public function saveOnChangeTender($tender)
    {
        try {
            if ($tender->workflow_status == 'tender_process') {
                $stageTech = TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE['process_technical_evaluation'];
                $stageCom = TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE['process_commercial_evaluation'];

                $regVendorTechnical = TenderItemTechnical::select('vendor_id', 'vendor_code')
                    ->where('tender_number', $tender->tender_number)
                    ->withoutGlobalScope(VendorViewScope::class)
                    ->where('submission_method', $stageTech)
                    ->groupBy('vendor_id', 'vendor_code')
                    ->get();
                $regVendorCommercial = TenderItemCommercial::select('vendor_id', 'vendor_code')
                    ->where('tender_number', $tender->tender_number)
                    ->withoutGlobalScope(VendorViewScope::class)
                    ->where('submission_method', $stageCom)
                    ->groupBy('vendor_id', 'vendor_code')
                    ->get();

                // Update Pr Item
                $prItems = TenderItem::where('tender_number', $tender->tender_number)->get();
                if ($prItems && $prItems->count() > 0) {

                    $data = [];
                    foreach ($prItems as $tenderItems) {
                        $it = $tenderItems->toArray();
                        $data = [
                            'tender_number' => $tender->tender_number,
                            'id' => $it['key_id'] ?? null,
                            'item_id' => $it['line_id'],
                            // 'status' => TenderSubmissionEnum::STATUS_ITEM[2],
                        ];

                        foreach ($regVendorTechnical as $vendor) {
                            $found = TenderItemTechnical::where('tender_number', $tender->tender_number)
                                ->where('item_id', $it['line_id'])
                                ->where('vendor_id', $vendor->vendor_id)
                                ->where('submission_method', $stageTech)
                                ->withoutGlobalScope(VendorViewScope::class)
                                ->get();
                            if ($found == null || $found->count() == 0) {
                                $newData = new TenderItemTechnical(array_merge($data, [
                                    'vendor_id' => $vendor->vendor_id,
                                    'vendor_code' => $vendor->vendor_code,
                                    'description' => $it['description'],
                                    'qty' => $it['qty'] ?? 0,
                                    'submission_method' => $stageTech,
                                    'status' => TenderSubmissionEnum::STATUS_ITEM[1],
                                ]));
                                $newData->save();
                            } else if ((!empty($it['deleteflg']) && strtolower($it['deleteflg']) == 'x')  && !empty($found)) {
                                foreach($found as $itemTC){
                                    $itemTC->compliance = 'no_quote';
                                    $itemTC->qty = 0;
                                    $itemTC->save();
                                }
                            }
                        }
                        foreach ($regVendorCommercial as $vendor) {
                            $found = TenderItemCommercial::where('tender_number', $tender->tender_number)
                                ->where('item_id', $it['line_id'])
                                ->where('vendor_id', $vendor->vendor_id)
                                ->where('submission_method', $stageCom)
                                ->withoutGlobalScope(VendorViewScope::class)
                                ->get();
                            if ($found == null || $found->count() == 0) {
                                $newData = new TenderItemCommercial(array_merge($data, [
                                    'vendor_id' => $vendor->vendor_id,
                                    'vendor_code' => $vendor->vendor_code,
                                    'price_unit' => $it['price_unit'],
                                    'est_unit_price' => $it['est_unit_price'] ?? 0,
                                    'currency_code' => $it['currency_code'] ?? '',
                                    'overall_limit' => $it['overall_limit'] ?? 0,
                                    'submission_method' => $stageCom,
                                    'status' => TenderSubmissionEnum::STATUS_ITEM[1],
                                ]));
                                $newData->save();
                            } else if ((!empty($it['deleteflg']) && strtolower($it['deleteflg']) == 'x') && !empty($found)) {
                                foreach($found as $itemCom){
                                    $itemCom->compliance = 'no_quote';
                                    $itemCom->est_unit_price = 0;
                                    $itemCom->overall_limit = 0;
                                    $itemCom->save();
                                }
                            }
                        }
                    }
                }

                // Update Item Specification
                $itemSpec = TenderItemDetail::where('tender_number', $tender->tender_number)->get();
                if ($itemSpec && $itemSpec->count() > 0) {
                    $data = [];
                    foreach ($itemSpec as $spec) {
                        $data = [
                            'tender_number' => $tender->tender_number,
                            'category_id' => $spec->category_id,
                            'item_spec_id' => $spec->line_id,
                            'description' => $spec->description,
                            'requirement' => $spec->requirement,
                            'reference' => $spec->reference,
                            'submission_method' => $stageTech,
                        ];

                        foreach ($regVendorTechnical as $vendor) {
                            $found = TenderVendorItemDetail::where('tender_number', $tender->tender_number)
                                ->where('item_spec_id', $spec->line_id)
                                ->where('vendor_id', $vendor->vendor_id)
                                ->where('submission_method', $stageTech)
                                ->withoutGlobalScope(VendorViewScope::class)
                                ->get();
                            if ($found == null || $found->count() == 0) {
                                $newData = new TenderVendorItemDetail(array_merge($data, [
                                    'vendor_id' => $vendor->vendor_id,
                                    'vendor_code' => $vendor->vendor_code,
                                    'status' => TenderSubmissionEnum::STATUS_ITEM[1],
                                    // $vendor->status == TenderSubmissionEnum::STATUS_ITEM[1]
                                    // ? TenderSubmissionEnum::STATUS_ITEM[1] : TenderSubmissionEnum::STATUS_ITEM[2],
                                ]));
                                $newData->save();
                            } else {
                                foreach($found as $itSpec){
                                    $itSpec->fill($data);
                                    $itSpec->save();
                                }
                            }
                        }
                    }
                }
            }
            return true;
        } catch (Exception $e) {
            Log::error($this->logName . '::saveOnChangeTender error : ' . $e->getMessage());
            throw $e;
        }
    }

    public function resetInitialVendorItems($params, $tender, $stageType)
    {
        try {

            $tableVendors = [];
            $tableVendorsNegoTech = [];
            if ($stageType == TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE['process_technical_evaluation']) {
                $tableVendors[] = 'tender_header_technical';
                $tableVendors[] = 'tender_item_technical';
                $tableVendors[] = 'tender_vendor_item_text';
                $tableVendors[] = 'tender_vendor_item_detail';
            } else if ($stageType == TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE['process_commercial_evaluation']) {
                $tableVendors[] = 'tender_header_commercial';
                $tableVendors[] = 'tender_item_commercial';
                $tableVendors[] = 'tender_vendor_additional_cost';
                $tableVendors[] = 'tender_vendor_tax_code';
            } else if ($stageType == TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE['negotiation_commercial']) {
                $tableVendorsNegoTech[] = 'tender_header_technical';
                $tableVendorsNegoTech[] = 'tender_item_technical';
                $tableVendorsNegoTech[] = 'tender_vendor_item_text';

                $tableVendors[] = 'tender_header_commercial';
                $tableVendors[] = 'tender_item_commercial';
                $tableVendors[] = 'tender_vendor_additional_cost';
                $tableVendors[] = 'tender_vendor_tax_code';
            }

            // delete old data
            if (count($tableVendors) > 0) {
                foreach ($tableVendors as $table) {
                    $tableName = $table;
                    $modelClass = '\\App\\Models\\' . HelpersApp::getClassName($tableName);
                    (new $modelClass)->where('tender_number', $tender->tender_number)
                        ->where('submission_method', $stageType)
                        ->where('vendor_id', $params['vendor_id'])
                        ->where('status', TenderSubmissionEnum::STATUS_ITEM[2])
                        ->whereNull('deleted_at')
                        ->withoutGlobalScope(VendorViewScope::class)
                        ->update(['action_status' => TenderStatusEnum::ACT_DELETE]);

                    (new $modelClass)->where('tender_number', $tender->tender_number)
                        ->where('submission_method', $stageType)
                        ->where('vendor_id', $params['vendor_id'])
                        ->where('status', TenderSubmissionEnum::STATUS_ITEM[1])
                        ->withoutGlobalScope(VendorViewScope::class)
                        ->delete();
                }
            }

            // delete old data for negotiation technical
            if (count($tableVendorsNegoTech) > 0) {
                foreach ($tableVendorsNegoTech as $table) {
                    $tableName = $table;
                    $modelClass = '\\App\\Models\\' . HelpersApp::getClassName($tableName);
                    (new $modelClass)->where('tender_number', $tender->tender_number)
                        ->where('submission_method', TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE['negotiation_technical'])
                        ->where('vendor_id', $params['vendor_id'])
                        ->where('status', TenderSubmissionEnum::STATUS_ITEM[2])
                        ->whereNull('deleted_at')
                        ->withoutGlobalScope(VendorViewScope::class)
                        ->update(['action_status' => TenderStatusEnum::ACT_DELETE]);

                    (new $modelClass)->where('tender_number', $tender->tender_number)
                        ->where('submission_method', TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE['negotiation_technical'])
                        ->where('vendor_id', $params['vendor_id'])
                        ->where('status', TenderSubmissionEnum::STATUS_ITEM[1])
                        ->withoutGlobalScope(VendorViewScope::class)
                        ->delete();
                }
            }

            return $this->_initialVendorItems($params, $tender, $stageType, true);
        } catch (Exception $e) {
            Log::error($this->logName . '::resetInitialVendorItems error : ' . $e->getMessage());
            throw $e;
        }
    }
    /**
     * copy data tender items
     *
     * @param array $params
     * @param \App\TenderParameter $tender
     * @param string $stageType
     *
     * @return bool
     */
    public function initialVendorItems($params, $tender, $stageType)
    {
        try {
            DB::beginTransaction();

            if ($stageType == TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE['process_commercial_evaluation']) {
                // delete old data
                TenderVendorAdditionalCost::where('tender_number', $tender->tender_number)
                    ->where('submission_method', $stageType)
                    ->where('vendor_id', $params['vendor_id'])
                    ->delete();

                // delete old data
                TenderVendorTaxCode::where('tender_number', $tender->tender_number)
                    ->where('submission_method', $stageType)
                    ->where('vendor_id', $params['vendor_id'])
                    ->delete();
            }

            $this->_initialVendorItems($params, $tender, $stageType);
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($this->logName . '::initialVendorItems error : ' . $e->getMessage());
            throw $e;
        }
    }
    private function _initialVendorItems($params, $tender, $stageType, $deleteDraft = false)
    {
        try {
            // insert data;
            $query = TenderItem::where('tender_number', $tender->tender_number);
            $prItems = $query->get();
            $data = [];
            if ($prItems && $prItems->count() > 0) {
                foreach ($prItems as $tenderItems) {
                    $it = $tenderItems->toArray();
                    $data = [
                        'tender_number' => $tender->tender_number,
                        'id' => $it['key_id'] ?? null,
                        'vendor_id' => $params['vendor_id'],
                        'vendor_code' => $params['vendor_code'],
                        'item_id' => $it['line_id'],
                        'submission_method' => $stageType,
                        'status' => TenderSubmissionEnum::STATUS_ITEM[1],
                    ];

                    if ($stageType == TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE['process_technical_evaluation']) {
                        $newData = array_merge($data, [
                            'description' => $it['description'],
                            'qty' => $it['qty'] ?? 0,
                        ]);
                        if((!empty($it['deleteflg']) && strtolower($it['deleteflg']) == 'x')){
                            $newData = array_merge($data, [
                                'description' => $it['description'],
                                'qty' => 0,
                                'compliance' => 'no_quote',
                            ]);
                        }
                        TenderItemTechnical::create($newData);
                        $this->initialVendorItemText($tender, $params, $stageType, $tenderItems);
                    } else if ($stageType == TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE['process_commercial_evaluation']) {

                        $newData = array_merge($data, [
                            'price_unit' => $it['price_unit'],
                            'est_unit_price' => $it['est_unit_price'] ?? 0,
                            'currency_code' => $it['currency_code'] ?? '',
                            'overall_limit' => $it['overall_limit'] ?? 0,
                        ]);
                        if((!empty($it['deleteflg']) && strtolower($it['deleteflg']) == 'x')){
                            $newData = array_merge($data, [
                                'price_unit' => $it['price_unit'],
                                'est_unit_price' => 0,
                                'currency_code' => 0,
                                'overall_limit' => 0,
                                'compliance' => 'no_quote',
                            ]);
                        }
                        TenderItemCommercial::create($newData);
                        // replicate tax code untuk vendor jika tax code
                        $this->initialVendorTaxCodes($tender, $params, $stageType, $tenderItems);
                        if ($tender->conditional_type == 'CT2') {
                            // replicate additional cost untuk vendor
                            $this->initialVendorAdditionalCosts($tender, $params, $stageType, $tenderItems);
                        }
                    } else if ($stageType == TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE['negotiation_commercial']) {
                        $data["submission_method"] = TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE['negotiation_technical'];

                        $newData = array_merge($data, [
                            'description' => $it['description'],
                            'qty' => $it['qty'] ?? 0,
                        ]);
                        if((!empty($it['deleteflg']) && strtolower($it['deleteflg']) == 'x')){
                            $newData = array_merge($data, [
                                'description' => $it['description'],
                                'qty' => 0,
                                'compliance' => 'no_quote',
                            ]);
                        }
                        TenderItemTechnical::create($newData);
                        // dd($it);
                        $this->initialVendorItemText($tender, $params, TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE['negotiation_technical'], $tenderItems);

                        $data["submission_method"] = TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE['negotiation_commercial'];

                        TenderItemCommercial::create(array_merge($data, [
                            'price_unit' => $it['price_unit'],
                            'est_unit_price' => $it['est_unit_price'] ?? 0,
                            'currency_code' => $it['currency_code'] ?? '',
                            'overall_limit' => $it['overall_limit'] ?? 0,
                        ]));
                        // replicate tax code untuk vendor jika tax code
                        $this->initialVendorTaxCodes($tender, $params, $stageType, $tenderItems);
                        if ($tender->conditional_type == 'CT2') {
                            // replicate additional cost untuk vendor
                            $this->initialVendorAdditionalCosts($tender, $params, $stageType, $tenderItems);
                        }
                    }
                }

                if ($tender->conditional_type == 'CT1' && $stageType == TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE['process_commercial_evaluation']) {
                    // replicate additional cost level header
                    $this->initialVendorAdditionalCosts($tender, $params, TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE['process_commercial_evaluation']);
                }

                if ($tender->conditional_type == 'CT1' && $stageType == TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE['negotiation_commercial']) {
                    // replicate additional cost level header
                    $this->initialVendorAdditionalCosts($tender, $params, TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE['negotiation_commercial']);
                }

                if ($stageType == TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE['process_technical_evaluation']) {
                    $this->initialVendorItemDetail($tender, $params, $stageType);
                }
            }
        } catch (Exception $e) {
            Log::error($this->logName . '::_initialVendorItems error : ' . $e->getMessage());
            throw $e;
        }
    }

    public function _submitTenderVendorTechnicalSkip($params, $tender)
    {
        try {
            $countTechItems = TenderItemTechnical::where([
                ['tender_number', '=', $tender->tender_number],
                ['vendor_id', '=', $params['vendor_id']],
                ['submission_method', '=', TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE['process_technical_evaluation']],
                ['status', '=', TenderSubmissionEnum::STATUS_ITEM[2]]
            ])->whereNull('deleted_at')->withoutGlobalScope(VendorViewScope::class)->count();

            if ($countTechItems <= 0) {
                // insert data;
                $query = TenderItem::where('tender_number', $tender->tender_number);
                $prItems = $query->get();
                $data = [];
                if ($prItems && $prItems->count() > 0) {
                    foreach ($prItems as $tenderItems) {
                        $it = $tenderItems->toArray();
                        $data = [
                            'tender_number' => $tender->tender_number,
                            'id' => $it['key_id'] ?? null,
                            'vendor_id' => $params['vendor_id'],
                            'vendor_code' => $params['vendor_code'],
                            'item_id' => $it['line_id'],
                            'submission_method' => TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE['process_technical_evaluation'],
                            'status' => TenderSubmissionEnum::STATUS_ITEM[2],
                        ];

                        $dataDraft = TenderItemTechnical::where([
                            ['tender_number', '=', $tender->tender_number],
                            ['vendor_id', '=', $params['vendor_id']],
                            ['item_id', '=', $it['line_id']],
                            ['submission_method', '=', TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE['process_technical_evaluation']],
                            ['status', '=', TenderSubmissionEnum::STATUS_ITEM[1]],
                            ['action_status', '=', TenderStatusEnum::ACT_NEW]
                        ])->whereNull('deleted_at')->withoutGlobalScope(VendorViewScope::class)->first();

                        if ($dataDraft) {
                            $dataDraft->description = $it['description'];
                            $dataDraft->qty = $it['qty'] ?? 0;
                            $dataDraft->status = TenderSubmissionEnum::STATUS_ITEM[2];
                            $dataDraft->action_status = TenderStatusEnum::ACT_NEW;
                            $dataDraft->parentSave();
                        } else {
                            $newItem = new TenderItemTechnical();
                            $newItem->fill(array_merge($data, [
                                'description' => $it['description'],
                                'qty' => $it['qty'] ?? 0,
                            ]));
                            $newItem->parentSave();
                        }
                    }
                }
            }

            // delete old data
            TenderItemTechnical::withoutGlobalScope(VendorViewScope::class)
                ->where([
                    ['tender_number', '=', $tender->tender_number],
                    ['vendor_id', '=', $params['vendor_id']],
                    ['submission_method', '=', TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE['process_technical_evaluation']],
                    ['action_status', '=', TenderStatusEnum::ACT_CHANGE]
                ])
                ->delete();
        } catch (Exception $e) {
            Log::error($this->logName . '::_initialVendorItemsTechnical error : ' . $e->getMessage());
            throw $e;
        }
    }

    public function rawAdditionalCost($number, $type, $subtotal = 0)
    {
        $WORKFLOW_MAPPING_TYPE = TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE; // ["negotiation_commercial"];
        $commercialItems = "select tivc.tender_number,tivc.vendor_id,tivc.item_id,tivc.price_unit,tivc.est_unit_price,tivc.overall_limit,";
        if ($type == 'CT1') {
            $commercialItems .= "(case
                when tivc.compliance='no_quote' then 0
                else round((tivc.est_unit_price * tivt.qty) + (tivc.overall_limit * tivt.qty / (case when tivc.price_unit > 0 then tivc.price_unit else 1 end)), 2)
            end) as subtotal,";
        } else {
            $commercialItems .= "(case
                when tivc.compliance='no_quote' then 0
                when ti.item_category='0' then round((tivc.est_unit_price * tivt.qty / (case when tivc.price_unit > 0 then tivc.price_unit else 1 end)), 2)
                when ti.item_category!='0' then round((tivc.overall_limit * tivt.qty / (case when tivc.price_unit > 0 then tivc.price_unit else 1 end)), 2)
            end) as subtotal,";
        }
        $commercialItems .= "tivc.compliance,tivc.submission_method from tender_item_commercial tivc
            join tender_item_technical tivt on tivt.item_id=tivc.item_id and tivc.tender_number=tivt.tender_number and tivt.vendor_id=tivc.vendor_id
                and tivt.submission_method=" . $WORKFLOW_MAPPING_TYPE['process_technical_evaluation'] . "
                and tivt.status !='" . TenderSubmissionEnum::STATUS_ITEM[1] . "'
            join tender_items ti on ti.tender_number=tivc.tender_number and ti.line_id=tivc.item_id
            where tivc.submission_method=" . $WORKFLOW_MAPPING_TYPE['process_commercial_evaluation'] . " and ti.deleted_at is null and UPPER(COALESCE(ti.deleteflg, '')) != 'X'
            and tivc.status !='" . TenderSubmissionEnum::STATUS_ITEM[1] . "'
            AND tivt.deleted_at IS null
            AND tivc.deleted_at IS null
        ";

        // header level
        if ($type == 'CT1') {
            // $query = "select
            //     a.tender_number,a.vendor_id,a.item_id,
            //     sum(case
            //         when c.compliance = 'no_quote' then 0
            //         when b.calculation_type=1 and a.calculation_pos=1 then round((a.percentage * c.subtotal / 100.00),2)
            //         when b.calculation_type=1 and a.calculation_pos=2 then (0 - round((a.percentage * c.subtotal / 100.00),2))
            //         when b.calculation_type=2 and a.calculation_pos=1 then a.value
            //         when b.calculation_type=2 and a.calculation_pos=2 then (0-a.value)
            //         else 0
            //     END) as additional_cost
            // from tender_vendor_additional_costs a
            // inner join conditional_types b on a.conditional_code=b.\"type\"
            // join (" . $commercialItems . ") c on a.tender_number=c.tender_number and a.vendor_id=c.vendor_id
            // and a.submission_method = c.submission_method
            // where a.tender_number='" . $number . "' AND a.conditional_type='" . $type . "' and c.compliance !='no_quote'
            // and a.deleted_at is null
            // group by a.tender_number,a.vendor_id,a.item_id
            // ";

            $subQuery = "select
                    a.tender_number,a.vendor_id,a.item_id,
                    sum(case
                        when c.compliance = 'no_quote' then 0
                        when a.calculation_pos=1 then round((a.percentage * c.subtotal / 100.00),2)
                        when a.calculation_pos=2 then (0 - round((a.percentage * c.subtotal / 100.00),2))
                        else 0
                    END) as additional_cost
                from tender_vendor_additional_costs a
                inner join conditional_types b on a.conditional_code=b.\"type\"
                join (" . $commercialItems . ") c on a.tender_number=c.tender_number and a.vendor_id=c.vendor_id
                and a.submission_method = c.submission_method
                where a.tender_number='" . $number . "' AND a.conditional_type='" . $type . "' and c.compliance !='no_quote'
                and a.deleted_at is null and b.calculation_type=1
                group by a.tender_number,a.vendor_id,a.item_id
                union all
                select
                    a.tender_number,a.vendor_id,a.item_id,
                    sum(a.additional_cost) as additional_cost
                from
                (
                    select
                        a.tender_number,a.vendor_id,a.item_id,
                        case
                            when c.compliance = 'no_quote' then 0
                            when a.calculation_pos=1 then max(a.value)
                            when a.calculation_pos=2 then (0-max(a.value))
                            else 0
                        END as additional_cost
                    from tender_vendor_additional_costs a
                    inner join conditional_types b on a.conditional_code=b.\"type\"
                    join (" . $commercialItems . ") c on a.tender_number=c.tender_number and a.vendor_id=c.vendor_id
                    and a.submission_method = c.submission_method
                    where a.tender_number='" . $number . "' AND a.conditional_type='" . $type . "' and c.compliance !='no_quote'
                    and a.deleted_at is null and b.calculation_type=2
                    group by a.tender_number,a.vendor_id,a.item_id,a.conditional_code,b.calculation_type,a.calculation_pos,c.compliance
                ) a group by a.tender_number,a.vendor_id,a.item_id
                ";

            $query = "select
                a.tender_number,a.vendor_id,a.item_id,
                sum(a.additional_cost) as additional_cost
            from (" . $subQuery . ") a group by a.tender_number,a.vendor_id,a.item_id";
        }
        // item level
        else {
            $query = "select
                a.tender_number,a.vendor_id,a.item_id,
                sum(case
                    when c.compliance = 'no_quote' then 0
                    when b.calculation_type=1 and a.calculation_pos=1 then round((a.percentage * c.subtotal / 100.00),2)
                    when b.calculation_type=1 and a.calculation_pos=2 then (0 - round((a.percentage * c.subtotal / 100.00),2))
                    when b.calculation_type=2 and a.calculation_pos=1 then a.value
                    when b.calculation_type=2 and a.calculation_pos=2 then (0-a.value)
                    else 0
                END) as additional_cost
            from tender_vendor_additional_costs a
            inner join conditional_types b on a.conditional_code=b.\"type\"
            join (" . $commercialItems . ") c on a.tender_number=c.tender_number and a.item_id=c.item_id
            and a.submission_method = c.submission_method
            where a.tender_number='" . $number . "' AND a.conditional_type='" . $type . "'
            and a.deleted_at is null
            group by a.tender_number,a.vendor_id,a.item_id";
        }
        return $query;
    }

    /**
     * find data tender items comparison
     *
     * @param string $number, tender number
     * @param int $vendorId
     * @param string $stageType
     *
     * @return \Illuminate\Database\Eloquent\Builder $builder
     */
    public function findTenderComparisonItems($tender, $stageType)
    {
        if ($stageType == TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE["process_technical_evaluation"]) {
            return $this->findTenderComparisonItemsTechnical($tender);
        } else if (in_array($stageType, [TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE["process_commercial_evaluation"], TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE["process_tender_evaluation"]])) {

            return $this->findTenderComparisonItemsCommercial($tender);
        }
    }

    private function findTenderComparisonItemsTechnical($tender)
    {
        try {
            $number = $tender->tender_number;
            $tableTc = 'tender_item_technical';
            $query = TenderItem::select(
                'tender_items.*',
                'v.id as vendor_id',
                'v.vendor_name',
                DB::raw("tivt.description as description_vendor"),
                DB::raw("tivt.qty as qty_vendor"),
                DB::raw("tivt.compliance")
            )
                ->join($tableTc . ' as tivt', function ($join) {
                    TenderItemTechnical::vendorStatus($join, 'tivt')
                        ->on('tivt.item_id', '=', 'tender_items.line_id')
                        ->whereNull('tivt.deleted_at');
                })
                ->join('vendors as v', function ($join) {
                    $join->on('v.id', '=', 'tivt.vendor_id');
                })
                ->where('tender_items.tender_number', $number)
                ->whereRaw("UPPER(COALESCE(tender_items.deleteflg, '')) != 'X'")
                ->where('tivt.submission_method', TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE["process_technical_evaluation"])
                ->orderBy('tender_items.id', 'asc');
            return $query;
        } catch (Exception $e) {
            Log::error($this->logName . '::findTenderComparisonItemsTechnical error : ' . $e->getMessage());
            throw $e;
        }
    }
    private function findTenderComparisonItemsCommercial($tender)
    {
        try {
            $number = $tender->tender_number;
            $tableTc = 'tender_item_technical';
            $tableCom = 'tender_item_commercial';
            $query = TenderItem::select(
                'tender_items.*',
                'v.id as vendor_id',
                'v.vendor_name',
                DB::raw("tivt.description as description_vendor"),
                DB::raw("tivt.qty as qty_vendor"),
                DB::raw("tivc.est_unit_price as est_unit_price_vendor"),
                DB::raw("tivc.overall_limit as overall_limit_vendor"),
                DB::raw("tivc.price_unit as price_unit_vendor"),
                DB::raw("tivc.currency_code as currency_code_vendor"),
                DB::raw("tivc.compliance"),
                DB::raw("COALESCE(tas.additional_cost,0) as additional_cost"),
                // DB::raw("0 as additional_cost"),
                DB::raw("ROUND(tivc.est_unit_price * tivt.qty, 2) as subtotal_vendor"),
                DB::raw("ROUND(tivc.overall_limit * tivt.qty, 2) as total_overall_limit_vendor")
            )
                ->join($tableTc . ' as tivt', function ($join) {
                    TenderItemTechnical::vendorStatus($join, 'tivt')
                        ->on('tivt.item_id', '=', 'tender_items.line_id')
                        ->whereNull('tivt.deleted_at');
                })
                ->join($tableCom . ' as tivc', function ($join) {
                    TenderItemCommercial::vendorStatus($join, 'tivc')
                        ->on('tivc.item_id', '=', 'tender_items.line_id')
                        ->whereNull('tivc.deleted_at');
                })
                ->join('vendors as v', function ($join) {
                    $join->on('v.id', '=', 'tivc.vendor_id');
                    $join->on('v.id', '=', 'tivt.vendor_id');
                })
                ->leftJoin(DB::raw('(' . $this->rawAdditionalCost($number, $tender->conditional_type) . ') tas'), function ($join) {
                    $join->on('tas.tender_number', '=', 'tender_items.tender_number');
                    $join->on('tas.item_id', '=', 'tender_items.line_id');
                    $join->on('tas.vendor_id', '=', 'v.id');
                })
                ->where('tender_items.tender_number', $number)
                ->whereRaw("UPPER(COALESCE(tender_items.deleteflg, '')) != 'X'")
                ->where('tivt.submission_method', TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE["process_technical_evaluation"])
                ->where('tivc.submission_method', TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE["process_commercial_evaluation"])
                ->orderBy('tender_items.id', 'asc');
            return $query;
        } catch (Exception $e) {
            Log::error($this->logName . '::findTenderComparisonItemsCommercial error : ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * find data tender items summary
     *
     * @param \App\TenderParameter $tender
     * @param int $vendorId
     * @param string $stageType
     *
     * @return \Illuminate\Database\Eloquent\Builder $builder
     */
    public function findTenderSummaryItems($tender, $stageType)
    {
        if ($stageType == TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE["process_technical_evaluation"]) {
            return $this->findTenderSummaryItemTechnical($tender);
        } else if (in_array($stageType, [TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE["process_commercial_evaluation"], TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE["process_tender_evaluation"]])) {
            return $this->findTenderSummaryItemCommercial($tender);
        }
    }

    private function findTenderSummaryItemTechnical($tender)
    {
        $number = $tender->tender_number;
        try {
            $tableTc = 'tender_item_technical';
            $tableCom = 'tender_item_commercial';
            $query = TenderItem::select(
                'v.vendor_code',
                'v.vendor_name',
                DB::raw("ROUND(SUM(tivt.qty),3) as total_qty_vendor")
            )
                ->join($tableTc . ' as tivt', function ($join) {
                    TenderItemTechnical::vendorStatus($join, 'tivt')
                        ->on('tivt.item_id', '=', 'tender_items.line_id')
                        ->whereNull('tivt.deleted_at');
                })
                ->join('vendors as v', function ($join) {
                    $join->on('v.id', '=', 'tivt.vendor_id');
                })
                ->where('tender_items.tender_number', $number)
                ->whereRaw("UPPER(COALESCE(tender_items.deleteflg, '')) != 'X'")
                ->where('tivt.submission_method',  TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE["process_technical_evaluation"])
                ->groupBy('v.id', 'v.vendor_code', 'v.vendor_name');

            return $query;
        } catch (Exception $e) {
            Log::error($this->logName . '::findTenderSummaryItemTechnical error : ' . $e->getMessage());
            throw $e;
        }
    }
    private function findTenderSummaryItemCommercial($tender)
    {
        $number = $tender->tender_number;
        try {
            $tableTc = 'tender_item_technical';
            $tableCom = 'tender_item_commercial';
            $query = TenderItem::join($tableTc . ' as tivt', function ($join) {
                TenderItemTechnical::vendorStatus($join, 'tivt')
                    ->on('tivt.item_id', '=', 'tender_items.line_id')
                    ->whereNull('tivt.deleted_at');
            })
                ->join($tableCom . ' as tivc', function ($join) {
                    TenderItemCommercial::vendorStatus($join, 'tivc')
                        ->on('tivc.item_id', '=', 'tender_items.line_id')
                        ->whereNull('tivc.deleted_at');
                })
                ->join('vendors as v', function ($join) {
                    $join->on('v.id', '=', 'tivc.vendor_id');
                    $join->on('v.id', '=', 'tivt.vendor_id');
                })
                ->leftJoin(DB::raw('(' . $this->rawAdditionalCost($number, $tender->conditional_type) . ') tas'), function ($join) use ($tender) {
                    $join->on('tas.tender_number', '=', 'tender_items.tender_number');
                    $join->on('tas.vendor_id', '=', 'tivc.vendor_id');
                    if ($tender->conditional_type == 'CT2') {
                        $join->on('tas.item_id', '=', 'tender_items.line_id');
                    }
                })
                ->where('tender_items.tender_number', $number)
                ->whereRaw("UPPER(COALESCE(tender_items.deleteflg, '')) != 'X'")
                ->where('tivt.submission_method',  TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE["process_technical_evaluation"])
                ->where('tivc.submission_method',  TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE["process_commercial_evaluation"]);

            if ($tender->conditional_type == 'CT1') {
                $query = $query->select(
                    'v.vendor_code',
                    'v.vendor_name',
                    DB::raw("SUM(tivc.est_unit_price) as est_unit_price_vendor"),
                    DB::raw("ROUND(SUM(tivc.est_unit_price * tivt.qty),2) as subtotal_vendor"),
                    DB::raw("ROUND(SUM(tivc.overall_limit * tivt.qty),2) as total_overall_limit_vendor"),
                    DB::raw("(COALESCE(tas.additional_cost,0)) as total_additional_cost"),
                    'tivc.currency_code as currency_code_vendor'
                )->groupBy('v.id', 'v.vendor_name', 'tivc.currency_code', 'tas.additional_cost');
            } else {
                $query = $query->select(
                    'v.vendor_code',
                    'v.vendor_name',
                    DB::raw("SUM(tivc.est_unit_price) as est_unit_price_vendor"),
                    DB::raw("ROUND(SUM(tivc.est_unit_price * tivt.qty),2) as subtotal_vendor"),
                    DB::raw("ROUND(SUM(tivc.overall_limit * tivt.qty),2) as total_overall_limit_vendor"),
                    DB::raw("SUM(COALESCE(tas.additional_cost,0)) as total_additional_cost"),
                    'tivc.currency_code as currency_code_vendor'
                )->groupBy('v.id', 'v.vendor_name', 'tivc.currency_code');
            }

            return $query;
        } catch (Exception $e) {
            Log::error($this->logName . '::findTenderSummaryItemCommercial error : ' . $e->getMessage());
            throw $e;
        }
    }



    #region Find Data
    private function findDataTablePrItem($number, $stageType, $params)
    {
        if (!empty($params['vendor_id'])) {
            $vendorId = $params['vendor_id'];
        } else {
            $vendorId = Auth::user()->vendor ? Auth::user()->vendor->id : 0;
        }
        $tenderItems = null;

        switch ($stageType) {
            case TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE['negotiation_technical']:
            case TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE['negotiation_commercial']:
                $tenderItems = $this->findTenderNegotiationItems($number, $vendorId, $stageType);
                break;
            case TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE['awarding_process']:
                if ($params['actionView'] == "awarding_result") {
                    $tenderItems = $this->findTenderItemsAwarding($number, $vendorId);
                } else {
                    $tenderItems = $this->findTenderItems($number, $vendorId, $stageType);
                }
                break;
            default:
                $tenderItems = $this->findTenderItems($number, $vendorId, $stageType);
                break;
        }

        return DataTables::eloquent($tenderItems)
            ->addColumn('status_text', function ($row) {
                return __('tender.process_status.' . $row->status);
            })
            ->addColumn('compliance_text', function ($row) {
                return !empty($row->compliance) ? __('tender.process.compliance.' . $row->compliance) : '';
            })
            ->editColumn('description_vendor', function ($row) {
                return !empty($row->item_id) ? $row->description_vendor : $row->description;
            })
            // ->editColumn('qty_vendor', function ($row) {
            //     return !empty($row->item_id) ? $row->qty_vendor : $row->qty;
            // })
            ->editColumn('est_unit_price_vendor', function ($row) {
                return !empty($row->item_id) ? $row->est_unit_price_vendor : $row->est_unit_price;
            })
            ->editColumn('price_unit_vendor', function ($row) {
                return !empty($row->item_id) ? $row->price_unit_vendor : $row->price_unit;
            })
            ->editColumn('currency_code_vendor', function ($row) {
                return !empty($row->item_id) ? $row->currency_code_vendor : $row->currency_code;
            })
            ->editColumn('subtotal_vendor', function ($row) {
                return !empty($row->item_id) ? $row->subtotal_vendor : $row->subtotal;
            })
            ->editColumn('selected', function ($row) {
                return isset($row->selected) ? ($row->selected == 1 ? true : false) : false;
            })
            ->editColumn('disabled', function ($row) {
                return isset($row->disabled) ? ($row->disabled == 1 ? true : false) : false;
            })
            ->make(true);
    }
    private function findDataTableTaxCodes($number, $stageType, $params)
    {
        $isSubmitted = $stageType == 3;
        switch ($stageType) {
            case TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE['process_technical_evaluation']:
            case TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE['process_commercial_evaluation']:
                $params['stage_type'] = TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE['process_commercial_evaluation'];
                break;
            case TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE['negotiation_technical']:
            case TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE['negotiation_commercial']:
                $params['stage_type'] = TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE['negotiation_commercial'];
                break;
            case TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE['awarding_process']:
                if ($params['actionView'] == "awarding_result") {
                    return DataTables::eloquent($this->findTenderTaxCodesAwarding($number, $params))
                        ->make(true);
                } else {
                    $params['stage_type'] = (new TenderProcessAwardingRepository)->getStageType($number, $params["vendor_id"]);
                }
                break;
        }
        return DataTables::eloquent($this->findTenderTaxCodes($number, $params, $isSubmitted))
            ->make(true);
    }
    private function findDataTableAdditionalCost($number, $stageType, $params)
    {
        $isSubmitted = $stageType == 3;
        switch ($stageType) {
            case TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE['process_technical_evaluation']:
            case TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE['process_commercial_evaluation']:
                $params['stage_type'] = TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE['process_commercial_evaluation'];
                break;
            case TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE['negotiation_technical']:
            case TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE['negotiation_commercial']:
                $params['stage_type'] = TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE['negotiation_commercial'];
                break;
            case TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE['awarding_process']:
                if ($params['actionView'] == "awarding_result") {
                    return DataTables::eloquent($this->findTenderAdditionalCostAwarding($number, $params))
                        ->make(true);
                } else {
                    $params['stage_type'] = (new TenderProcessAwardingRepository)->getStageType($number, $params["vendor_id"]);
                }
                break;
        }
        return DataTables::eloquent($this->findTenderAdditionalCost($number, $params, $isSubmitted))
            ->make(true);
    }
    private function findDataTableItemServices($tenderNumber, $stageType, $params)
    {
        return DataTables::of($this->findTenderItemService($params['number'], $params['line_number']))
            ->make(true);
    }
    private function findDataTableItemTexts($tenderNumber, $stageType, $params)
    {
        switch ($stageType) {
            case TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE['process_technical_evaluation']:
            case TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE['process_commercial_evaluation']:
                $params['stage_type'] = TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE['process_technical_evaluation'];
                break;
            case TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE['negotiation_technical']:
            case TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE['negotiation_commercial']:
                $params['stage_type'] = TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE['negotiation_technical'];
                break;
            case TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE['awarding_process']:
                if ($params['actionView'] == "awarding_result") {
                    return DataTables::eloquent($this->findTenderItemTextAwarding($tenderNumber, $params))
                        ->make(true);
                } else {
                    $params['stage_type'] = (new TenderProcessAwardingRepository)->getStageTypeTechnical($tenderNumber, $params["vendor_id"]);
                }
                break;
        }
        return DataTables::eloquent($this->findTenderItemText($tenderNumber, $params))
            ->make(true);
    }

    /**
     * find data vendor tender items
     *
     * @param string $number, tender number
     * @param int $vendorId
     * @param string $stageType
     *
     * @return \Illuminate\Database\Eloquent\Builder $builder
     */
    private function findTenderItems($number, $vendorId, $stageType)
    {
        $isAwarding = false;
        $stageTypeTech = TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE["process_technical_evaluation"];
        $stageTypeComm = TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE["process_commercial_evaluation"];

        if ($stageType == TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE['awarding_process']) {
            $stageType = (new TenderProcessAwardingRepository)->getStageType($number, $vendorId);
            $stageTypeTech = (new TenderProcessAwardingRepository)->getStageTypeTechnical($number, $vendorId);

            $stageTypeComm = $stageType;
            $isAwarding  = true;
        }

        try {
            $alias = ($stageType == 4 || $stageType == 6) ? 'tivc' : 'tivt';
            $tableTc = 'tender_item_technical';
            $tableCom = 'tender_item_commercial';
            $query = TenderItem::select(
                'tender_items.*',
                $alias . '.id as key_id',
                $alias . '.item_id',
                $alias . '.compliance',
                $alias . '.status',
                DB::raw("tivt.description as description_vendor"),
                DB::raw("tivt.qty as qty_vendor"),
                DB::raw("tivc.est_unit_price as est_unit_price_vendor"),
                DB::raw("tivc.price_unit as price_unit_vendor"),
                DB::raw("tivc.currency_code as currency_code_vendor"),
                DB::raw("tivc.overall_limit as overall_limit_vendor"),
                DB::raw("ROUND((tivc.est_unit_price * tivt.qty) + (tivc.overall_limit * tivt.qty), 2) as subtotal_vendor")
            )
            ->leftJoin($tableTc . ' as tivt', function ($join) use ($vendorId, $stageTypeTech) {
                TenderItemTechnical::vendorStatus($join, 'tivt')
                    ->on('tivt.item_id', '=', 'tender_items.line_id')
                    ->where('tivt.vendor_id', $vendorId)
                    ->whereNull('tivt.deleted_at')
                    ->where('tivt.submission_method', $stageTypeTech);
            })
            ->leftJoin($tableCom . ' as tivc', function ($join) use ($vendorId, $stageTypeComm) {
                TenderItemCommercial::vendorStatus($join, 'tivc')
                    ->on('tivc.item_id', '=', 'tender_items.line_id')
                    ->where('tivc.vendor_id', $vendorId)
                    ->whereNull('tivc.deleted_at')
                    ->where('tivc.submission_method', $stageTypeComm);
            })
            ->where('tender_items.tender_number', $number)
            ->withoutGlobalScope(PublicViewScope::class)
            ->where('tender_items.public_status', TenderStatusEnum::PUBLIC_STATUS[2]);

            if ($isAwarding) {
                $query = $this->selectItemsAwarding($query, $number, $vendorId, "tivc");
            }

            return $query->orderBy('tender_items.id', 'asc');
        } catch (Exception $e) {
            Log::error($this->logName . '::findTenderItems error : ' . $e->getMessage());
            throw $e;
        }
    }

    private function selectItemsAwarding($query, $number, $vendorId, $alias)
    {
        $itemNotIn = collect(DB::select("select item_id from tender_item_commercial_awarding where tender_number = ? and vendor_id <> ? and vendor_id is not null", [$number, $vendorId]))->pluck("item_id")->implode(',');

        if (empty($itemNotIn)) {
            $itemNotIn = "0";
        }

        $query = $query->select(
            'tender_items.*',
            $alias . '.id as key_id',
            $alias . '.item_id',
            $alias . '.compliance',
            $alias . '.status',
            DB::raw("tivt.description as description_vendor"),
            DB::raw("tivt.qty as qty_vendor"),
            DB::raw("tivc.est_unit_price as est_unit_price_vendor"),
            DB::raw("tivc.price_unit as price_unit_vendor"),
            DB::raw("tivc.currency_code as currency_code_vendor"),
            DB::raw("tivc.overall_limit as overall_limit_vendor"),
            DB::raw("ROUND((tivc.est_unit_price * tivt.qty) + (tivc.overall_limit * tivt.qty), 2) as subtotal_vendor"),
            DB::raw("case when tivca.tender_number is not null then 1 else 0 end as selected"),
            DB::raw("case when tivc.item_id in (" . $itemNotIn . ") or UPPER(COALESCE(tender_items.deleteflg, '')) = 'X' then 1 else 0 end as disabled")
        )->leftJoin('tender_item_commercial_awarding as tivca', function ($join) use ($vendorId) {
            $join->on('tivc.tender_number', '=', 'tivca.tender_number')
                ->on('tivc.vendor_id', '=', 'tivca.vendor_id')
                ->on('tivc.item_id', '=', 'tivca.item_id')
                ->on('tivc.submission_method', '=', 'tivca.submission_method')
                ->where('tivca.vendor_id', $vendorId)
                // ->where('tivca.status', "!=", TenderSubmissionEnum::FLOW_STATUS[3])
                ->whereNull('tivca.deleted_at');
        });

        return $query;
    }

    /**
     * find data vendor tender items awarding
     *
     * @param string $number, tender number
     * @param int $vendorId
     * @param string $stageType
     *
     * @return \Illuminate\Database\Eloquent\Builder $builder
     */
    private function findTenderItemsAwarding($number, $vendorId)
    {
        try {
            $alias = 'tivc';
            $tableTc = 'tender_item_technical_awarding';
            $tableCom = 'tender_item_commercial_awarding';
            $query = TenderItem::select(
                'tender_items.*',
                $alias . '.id as key_id',
                $alias . '.item_id',
                $alias . '.compliance',
                $alias . '.status',
                DB::raw("tivt.description as description_vendor"),
                DB::raw("tivt.qty as qty_vendor"),
                DB::raw("tivc.est_unit_price as est_unit_price_vendor"),
                DB::raw("tivc.price_unit as price_unit_vendor"),
                DB::raw("tivc.currency_code as currency_code_vendor"),
                DB::raw("tivc.overall_limit as overall_limit_vendor"),
                DB::raw("ROUND((tivc.est_unit_price * tivt.qty) + (tivc.overall_limit * tivt.qty), 2) as subtotal_vendor")
            )
                ->join($tableCom . ' as tivc', function ($join) use ($vendorId) {
                    $join->on('tivc.item_id', '=', 'tender_items.line_id')
                        ->on('tivc.vendor_id', DB::raw($vendorId));
                })
                ->leftJoin($tableTc . ' as tivt', function ($join) use ($vendorId) {
                    $join->on('tivt.item_id', '=', 'tender_items.line_id')
                        ->on('tivt.vendor_id', DB::raw($vendorId));
                })
                ->where('tender_items.tender_number', $number)
                // ->where('tivc.status', "!=", TenderSubmissionEnum::FLOW_STATUS[3])
                ->whereNull('tivt.deleted_at')
                ->whereNull('tivc.deleted_at')
                ->orderBy('tender_items.id', 'asc');
            return $query;
        } catch (Exception $e) {
            Log::error($this->logName . '::findTenderItemsAwarding error : ' . $e->getMessage());
            throw $e;
        }
    }

    private function findTenderNegotiationItems($number, $vendorId, $stageType)
    {
        try {
            $alias = $stageType == 6 ? 'tivc' : 'tivt';
            $tableTc = 'tender_item_technical';
            $tableCom = 'tender_item_commercial';
            $query = TenderItem::select(
                'tender_items.*',
                $alias . '.id as key_id',
                $alias . '.item_id',
                $alias . '.compliance',
                $alias . '.status',
                'tivc.submission_method',
                'tivc.overall_limit as overall_limit_vendor',
                DB::raw("tivt.description as description_vendor"),
                DB::raw("tivt.qty as qty_vendor"),
                DB::raw("tivc.est_unit_price as est_unit_price_vendor"),
                DB::raw("tivc.price_unit as price_unit_vendor"),
                DB::raw("tivc.currency_code as currency_code_vendor"),
                // DB::raw("tivc.subtotal as subtotal_vendor"),
                DB::raw("ROUND((tivc.est_unit_price * tivt.qty) + (tivc.overall_limit * tivt.qty), 2) as subtotal_vendor")
            )
                ->leftJoin($tableTc . ' as tivt', function ($join) use ($vendorId, $stageType) {
                    TenderItemTechnical::vendorStatus($join, 'tivt')
                        ->on('tivt.item_id', '=', 'tender_items.line_id')
                        ->where('tivt.vendor_id', $vendorId)
                        ->whereNull('tivt.deleted_at')
                        ->where('tivt.submission_method', TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE["negotiation_technical"]);
                })
                ->leftJoin($tableCom . ' as tivc', function ($join) use ($vendorId, $stageType) {
                    TenderItemCommercial::vendorStatus($join, 'tivc')
                        ->on('tivc.item_id', '=', 'tender_items.line_id')
                        ->where('tivc.vendor_id', $vendorId)
                        ->whereNull('tivc.deleted_at')
                        ->where('tivc.submission_method', TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE["negotiation_commercial"]);
                })
                ->where('tender_items.tender_number', $number)
                ->distinct()
                ->orderBy('tender_items.id', 'asc');

            return $query;
        } catch (Exception $e) {
            Log::error($this->logName . '::findTenderNegotiationItems error : ' . $e->getMessage());
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
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function findTenderAdditionalCost($tenderNumber, $params, $isSubmitted = false)
    {
        try {
            $query = TenderVendorAdditionalCost::select(
                'tender_vendor_additional_costs.*',
                'conditional_types.calculation_type'
            )->leftJoin('conditional_types', function ($join) {
                $join->on('tender_vendor_additional_costs.conditional_code', '=', 'conditional_types.type');
            })
                ->where('tender_vendor_additional_costs.tender_number', $tenderNumber)
                ->where('tender_vendor_additional_costs.vendor_id', $params['vendor_id'])
                ->where('tender_vendor_additional_costs.submission_method', $params['stage_type']);

            if (!empty($params['pr_id'])) {
                $query = $query->join('tender_items', 'tender_items.line_id', 'tender_vendor_additional_costs.item_id')
                    ->where('tender_items.id', $params['pr_id'])
                    ->orderBy('tender_items.line_id', 'asc');
            }
            if ($isSubmitted) {
                $query = $query->withoutGlobalScope(VendorViewScope::class)
                    ->where('tender_vendor_additional_costs.status', TenderSubmissionEnum::STATUS_ITEM[2]);
            }
            return $query;
        } catch (Exception $e) {
            Log::error($this->logName . '::findTenderAdditionalCost error : ' . $e->getMessage());
            throw $e;
        }
    }
    /**
     * find data by tender additional cost awarding
     *
     * @param string $number
     * @param array $params
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function findTenderAdditionalCostAwarding($tenderNumber, $params)
    {
        try {
            $query = TenderVendorAdditionalCostAwarding::select(
                'tender_vendor_additional_costs_awarding.*',
                'conditional_types.calculation_type'
            )->leftJoin('conditional_types', function ($join) {
                $join->on('tender_vendor_additional_costs_awarding.conditional_code', '=', 'conditional_types.type');
            })
                ->where('tender_vendor_additional_costs_awarding.tender_number', $tenderNumber)
                // ->where('tender_vendor_additional_costs_awarding.status', "!=", TenderSubmissionEnum::FLOW_STATUS[3])
                ->where('tender_vendor_additional_costs_awarding.vendor_id', $params['vendor_id']);

            if (!empty($params['pr_id'])) {
                $query = $query->join(
                    'tender_items',
                    'tender_items.line_id',
                    'tender_vendor_additional_costs_awarding.item_id'
                )
                    ->where('tender_items.id', $params['pr_id'])
                    ->orderBy('tender_items.line_id', 'asc');
            }

            return $query;
        } catch (Exception $e) {
            Log::error($this->logName . '::findTenderAdditionalCostAwarding error : ' . $e->getMessage());
            throw $e;
        }
    }
    /**
     * find data by tender tax codes
     *
     * @param string $number
     * @param array $params
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function findTenderTaxCodes($tenderNumber, $params, $isSubmitted = false)
    {
        try {
            $query = TenderVendorTaxCode::select(
                'tender_vendor_tax_codes.id',
                'tender_vendor_tax_codes.tender_number',
                'tender_vendor_tax_codes.vendor_id',
                'tender_vendor_tax_codes.vendor_code',
                'tender_vendor_tax_codes.item_id',
                'tender_vendor_tax_codes.tax_code',
                'tender_vendor_tax_codes.description'
            )
                ->join('tender_items', 'tender_items.line_id', 'tender_vendor_tax_codes.item_id')
                ->where('tender_items.tender_number', $tenderNumber)
                ->where('tender_vendor_tax_codes.vendor_id', $params['vendor_id'])
                ->where('tender_vendor_tax_codes.submission_method', $params['stage_type'])
                ->where('tender_items.id', $params['pr_id'])
                ->orderBy('tender_items.line_id', 'asc');
            if ($isSubmitted) {
                $query = $query->withoutGlobalScope(VendorViewScope::class)
                    ->where('status', TenderSubmissionEnum::STATUS_ITEM[2]);
            }
            return $query;
        } catch (Exception $e) {
            Log::error($this->logName . '::findTenderTaxCodes error : ' . $e->getMessage());
            throw $e;
        }
    }
    /**
     * find data by tender tax codes awarding
     *
     * @param string $number
     * @param array $params
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function findTenderTaxCodesAwarding($tenderNumber, $params)
    {
        try {
            $query = TenderVendorTaxCodeAwarding::select(
                'tender_vendor_tax_codes_awarding.id',
                'tender_vendor_tax_codes_awarding.tender_number',
                'tender_vendor_tax_codes_awarding.vendor_id',
                'tender_vendor_tax_codes_awarding.vendor_code',
                'tender_vendor_tax_codes_awarding.item_id',
                'tender_vendor_tax_codes_awarding.tax_code',
                'tender_vendor_tax_codes_awarding.description'
            )
                ->join('tender_items', 'tender_items.line_id', 'tender_vendor_tax_codes_awarding.item_id')
                ->where('tender_items.tender_number', $tenderNumber)
                ->where('tender_vendor_tax_codes_awarding.vendor_id', $params['vendor_id'])
                ->where('tender_items.id', $params['pr_id'])
                // ->where('tender_vendor_tax_codes_awarding.status', "!=", TenderSubmissionEnum::FLOW_STATUS[3])
                ->orderBy('tender_items.line_id', 'asc');

            return $query;
        } catch (Exception $e) {
            Log::error($this->logName . '::findTenderTaxCodesAwarding error : ' . $e->getMessage());
            throw $e;
        }
    }
    /**
     * find data by tender item services
     *
     * @param string $number
     * @param array $params
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function findTenderItemService($number, $lineNumber)
    {
        try {
            $query = SapPRListServices::where('BANFN', $number)
                ->where('BNFPO', $lineNumber);
            return $query->get();
        } catch (Exception $e) {
            Log::error($this->logName . '::findTenderItemService error : ' . $e->getMessage());
            throw $e;
        }
    }
    /**
     * find data by tender item text
     *
     * @param string $number
     * @param array $params
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function findTenderItemText($tenderNumber, $params)
    {
        try {
            $tableSelect = 'tender_vendor_item_text';
            // if(!empty($params['compliance']) && $params['compliance'] != 'deviate') $tableSelect = 'tender_items';
            $query = TenderVendorItemText::select(
                // 'tender_vendor_item_text.*',
                'tender_vendor_item_text.id',
                'tender_vendor_item_text.tender_number',
                'tender_vendor_item_text.vendor_id',
                'tender_vendor_item_text.vendor_code',
                'tender_vendor_item_text.item_id',
                $tableSelect . '.PREQ_NO',
                $tableSelect . '.PREQ_ITEM',
                $tableSelect . '.TEXT_ID',
                $tableSelect . '.TEXT_ID_DESC',
                $tableSelect . '.TEXT_FORM',
                $tableSelect . '.TEXT_LINE',
                'tender_vendor_item_text.submission_method'
            )
                ->join('tender_items', 'tender_items.line_id', 'tender_vendor_item_text.item_id')
                ->where('tender_items.tender_number', $tenderNumber)
                ->where('tender_vendor_item_text.vendor_id', $params['vendor_id'])
                ->where('tender_vendor_item_text.submission_method', $params['stage_type'])
                ->where('tender_items.id', $params['pr_id'])
                ->orderBy('tender_vendor_item_text.line_id', 'asc');
            return $query;
        } catch (Exception $e) {
            Log::error($this->logName . '::findTenderItemText error : ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * find data by tender item text awarding
     *
     * @param string $number
     * @param array $params
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function findTenderItemTextAwarding($tenderNumber, $params)
    {
        try {
            $tableSelect = 'tender_vendor_item_text_awarding';

            $query = TenderVendorItemTextAwarding::select(
                $tableSelect . '.id',
                $tableSelect . '.tender_number',
                $tableSelect . '.vendor_id',
                $tableSelect . '.vendor_code',
                $tableSelect . '.item_id',
                $tableSelect . '.PREQ_NO',
                $tableSelect . '.PREQ_ITEM',
                $tableSelect . '.TEXT_ID',
                $tableSelect . '.TEXT_ID_DESC',
                $tableSelect . '.TEXT_FORM',
                $tableSelect . '.TEXT_LINE',
                $tableSelect . '.submission_method'
            )
                ->join('tender_items', 'tender_items.line_id', $tableSelect . '.item_id')
                ->where('tender_items.tender_number', $tenderNumber)
                ->where($tableSelect . '.vendor_id', $params['vendor_id'])
                ->where('tender_items.id', $params['pr_id'])
                // ->where($tableSelect . '.status', "!=", TenderSubmissionEnum::FLOW_STATUS[3])
                ->orderBy('tender_items.line_id', 'asc');

            return $query;
        } catch (Exception $e) {
            Log::error($this->logName . '::findTenderItemTextAwarding error : ' . $e->getMessage());
            throw $e;
        }
    }
    #endregion


    #region Save Data
    public function updateCurrencyCode($tender, $vendorId, $currencyCode, $stageType)
    {
        try {
            $items = TenderItemCommercial::where('tender_number', $tender->tender_number)
                ->where('vendor_id', $vendorId)
                ->where('submission_method', $stageType)
                // ->get();
                ->update(['currency_code' => $currencyCode]);
            // if ($items != null && $items->count() > 0) {
            //     foreach ($items as $k => $it) {
            //         $it->currency_code = $currencyCode;
            //         $it->save();
            //     }
            // }
        } catch (Exception $e) {
            Log::error($this->logName . '::updateCurrencyCode error : ' . $e->getMessage());
            throw $e;
        }
    }
    protected function saveBatchTenderPrItems($params, $tender, $stageType)
    {
        try {
            DB::beginTransaction();

            $items = $params['items'];
            if (!empty($items) && count($items) > 0) {
                foreach ($items as $k => $it) {
                    $data = [
                        'tender_number' => $tender->tender_number,
                        'id' => $it['key_id'] ?? null,
                        'vendor_id' => $params['vendor_id'],
                        'vendor_code' => $params['vendor_code'],
                        'item_id' => $it['line_id'],
                        'compliance' => $it['compliance'] ?? null,
                        'submission_method' => $stageType,
                        'status' => $it['status'] ?? TenderSubmissionEnum::STATUS_ITEM[1],
                    ];

                    if (
                        $stageType == TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE['process_technical_evaluation'] ||
                        $stageType == TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE['negotiation_technical']
                    ) {

                        $data = array_merge($data, [
                            'description' => $it['description_vendor'],
                            'qty' => $it['qty_vendor'] ?? 0,
                        ]);
                        if ($it['compliance'] == 'comply') {
                            $data['description'] = $it['description'];
                            $data['qty'] = $it['qty'];
                        } else if ($it['compliance'] == 'no_quote') {
                            $data['description'] = $it['description'];
                            $data['qty'] = 0;
                        }
                        if (!empty($data['id'])) {
                            // TenderItemTechnical::where('id', $data['id'])->update($data);
                            $model = TenderItemTechnical::where('id', $data['id'])->first();
                            if ($model != null) {
                                $model->fill($data)->save();
                            }
                        } else {
                            TenderItemTechnical::create($data);
                        }
                    } else if ($stageType == TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE['process_commercial_evaluation']) {
                        $data = array_merge($data, [
                            'price_unit' => $it['price_unit_vendor'],
                            'est_unit_price' => $it['est_unit_price_vendor'] ?? 0,
                            'currency_code' => $it['currency_code_vendor'] ?? '',
                            'overall_limit' => $it['overall_limit_vendor'] ?? 0,
                        ]);
                        if ($it['compliance'] == 'no_quote') {
                            $data['est_unit_price'] = 0;
                            $data['overall_limit'] = 0;
                        }
                        if (!empty($data['id'])) {
                            // TenderItemCommercial::where('id', $data['id'])->update($data);
                            $model = TenderItemCommercial::where('id', $data['id'])->first();
                            if ($model != null) {
                                $model->fill($data)->save();
                            }
                        } else {
                            TenderItemCommercial::create($data);
                        }
                    }
                }
            }

            TenderVendorSubmission::where('tender_number', $tender->tender_number)
                ->where('vendor_id', $params['vendor_id'])->where('submission_method', $stageType)
                ->update([
                    'status' => TenderVendorSubmissionDetail::STATUS[1],
                ]);
            // set vendor draft
            TenderVendorSubmission::updateOrCreate([
                'tender_number' => $tender->tender_number,
                'vendor_id' => $params['vendor_id'],
                'submission_method' => $stageType,
            ], [
                'tender_number' => $tender->tender_number,
                'vendor_id' => $params['vendor_id'],
                'submission_date' => Carbon::now(),
                'submission_method' => $stageType,
                'status' => TenderVendorSubmissionDetail::STATUS[1],
            ]);

            DB::commit();
            return [
                'data' => $tender,
                'next' => null,
            ];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($this->logName . '::saveTenderItems error : ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * save item technical record
     *
     * @param \App\TenderParameter $tender
     * @param array $params
     * @param string $stageType
     *
     * @return boolean
     *
     * @throws \Exception
     */
    private function saveItemTechnical($tender, $params, $stageType)
    {
        try {
            DB::beginTransaction();
            $model = null;
            if (!empty($params['item'])) {
                $data = [
                    'tender_number' => $tender->tender_number,
                    'id' => $params['item']['key_id'] ?? null,
                    'vendor_id' => $params['vendor_id'],
                    'vendor_code' => $params['vendor_code'],
                    'item_id' => $params['item']['line_id'],
                    'description' => $params['item']['description_vendor'],
                    'qty' => $params['item']['qty_vendor'] ?? 0,
                    'compliance' => $params['item']['compliance'] ?? 0,
                    'submission_method' => $stageType,
                    'status' => $params['item']['status'] ?? TenderSubmissionEnum::STATUS_ITEM[1],
                ];
                if ($params['item']['compliance'] == 'no_quote') {
                    $data['description'] = $params['item']['description'];
                    $data['qty'] = 0;
                }

                $model = TenderItemTechnical::find($data['id']);
                if (!$model) {
                    $model = new TenderItemTechnical();
                }
                $model->fill($data);
                $model->save();
            }

            if (!empty($params['item_text'])) {
                $this->saveVendorItemText($tender, $params, $stageType, $model);
            }

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($this->logName . '::saveItemTechnical error : ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * save item commercial record
     *
     * @param \App\TenderParameter $tender
     * @param array $params
     * @param string $stageType
     *
     * @return boolean
     *
     * @throws \Exception
     */
    private function saveItemCommercial($tender, $params, $stageType)
    {
        try {
            DB::beginTransaction();
            $model = null;
            if (!empty($params['item'])) {
                $data = [
                    'tender_number' => $tender->tender_number,
                    'id' => $params['item']['key_id'] ?? null,
                    'item_id' => $params['item']['line_id'],
                    'vendor_id' => $params['vendor_id'],
                    'vendor_code' => $params['vendor_code'],
                    'price_unit' => $params['item']['price_unit'] ?? 0,
                    'est_unit_price' => $params['item']['est_unit_price_vendor'] ?? 0,
                    'currency_code' => $params['item']['currency_code_vendor'] ?? '',
                    'overall_limit' => $params['item']['overall_limit_vendor'] ?? 0,
                    'compliance' => $params['item']['compliance'] ?? null,
                    'submission_method' => $stageType,
                    'status' => $params['item']['status'] ?? TenderSubmissionEnum::STATUS_ITEM[1],
                ];

                if ($params['item']['compliance'] == 'no_quote') {
                    $data['est_unit_price'] = 0;
                    $data['overall_limit'] = 0;
                }

                $model = TenderItemCommercial::find($data['id']);
                if (!$model) $model = new TenderItemCommercial();
                $model->fill($data);
                $model->save();
            }

            $this->saveVendorAdditionalCost($tender, $params, $stageType, $model);
            if ($model) {
                $this->saveVendorTaxCodes($tender, $params, $stageType, $model);
            }
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($this->logName . '::saveItemTechnical error : ' . $e->getMessage());
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
    private function saveVendorAdditionalCost($tender, $params, $stageType, $item = null)
    {
        try {
            // delete before insert
            $delModel = TenderVendorAdditionalCost::where('tender_number', $tender->tender_number)
                ->where('vendor_id', $params['vendor_id']);
            if ($item) {
                $delModel = $delModel->where('item_id', $item->item_id);
            }
            $delModel = $delModel->get();
            if ($delModel != null) {
                foreach ($delModel as $del) {
                    $del->delete();
                }
            }

            // insert data;
            $data = [];
            foreach ($params['cost'] as $val) {
                $data = [
                    'tender_number' => $tender->tender_number,
                    'vendor_id' => $params['vendor_id'],
                    'vendor_code' => $params['vendor_code'],
                    'item_id' => $item ? $item->item_id : null,
                    'conditional_code' => $val['conditional_code'],
                    'conditional_name' => $val['conditional_name'],
                    'percentage' => $val['percentage'],
                    'value' => $val['value'],
                    'calculation_pos' => $val['calculation_pos'],
                    'conditional_type' => $val['conditional_type'],
                    'submission_method' => $stageType,
                ];

                TenderVendorAdditionalCost::create($data);
            }
            // $result = TenderVendorAdditionalCost::insertBulk($data);

            return true;
        } catch (Exception $e) {
            Log::error($this->logName . '::saveVendorAdditionalCost error : ' . $e->getMessage());
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
    private function saveVendorTaxCodes($tender, $params, $stageType, $item)
    {
        try {
            // delete before insert
            $delModel = TenderVendorTaxCode::where('tender_number', $tender->tender_number)
                ->where('vendor_id', $params['vendor_id'])
                ->where('item_id', $item->item_id);

            $delModel = $delModel->get();
            if ($delModel != null) {
                foreach ($delModel as $del) {
                    $del->delete();
                }
            }

            // insert data;
            $data = [];
            foreach ($params['tax'] as $val) {
                $data = [
                    'tender_number' => $tender->tender_number,
                    'vendor_id' => $params['vendor_id'],
                    'vendor_code' => $params['vendor_code'],
                    'item_id' => $item ? $item->item_id : null,
                    'tax_code' => $val['tax_code'],
                    'description' => $val['description'],
                    'submission_method' => $stageType,
                ];
                TenderVendorTaxCode::create($data);
            }
            // $result = TenderVendorTaxCode::insertBulk($data);
            return true;
        } catch (Exception $e) {
            Log::error($this->logName . '::saveVendorTaxCodes error : ' . $e->getMessage());
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
    private function saveVendorItemText($tender, $params, $stageType, $item)
    {
        try {
            // delete before insert
            $delModel = TenderVendorItemText::where('tender_number', $tender->tender_number)
                ->where('item_id', $item->item_id)
                ->where('vendor_id', $params['vendor_id']);

            $delModel = $delModel->get();
            if ($delModel != null) {
                foreach ($delModel as $del) {
                    $del->delete();
                }
            }

            $params['item_text'] = str_replace("\n", "_*_", $params['item_text']);
            $params['item_text'] = explode('_*_', $params['item_text']);

            // insert data;
            $data = [];
            $counter = 0;
            foreach ($params['item_text'] as $val) {
                $counter++;
                $data = [
                    'tender_number' => $tender->tender_number,
                    'vendor_id' => $params['vendor_id'],
                    'vendor_code' => $params['vendor_code'],
                    'item_id' => $item ? $item->item_id : null,
                    'PREQ_NO' => $params['item'] ? $params['item']['number'] : null,
                    'PREQ_ITEM' => $params['item'] ? $params['item']['line_number'] : null,
                    'TEXT_ID' => 'B01',
                    'TEXT_ID_DESC' => 'Item text',
                    'TEXT_FORM' => $counter < (count($params['item_text'])) ? '*' : '',
                    'TEXT_LINE' => $val,
                    'submission_method' => $stageType,
                ];
                TenderVendorItemText::create($data);
            }
            // $result = TenderVendorItemText::insertBulk($data);
            return true;
        } catch (Exception $e) {
            Log::error($this->logName . '::saveVendorItemText error : ' . $e->getMessage());
            throw $e;
        }
    }


    private function initialVendorAdditionalCosts($tender, $params, $stageType, $item = null)
    {
        try {
            // insert data;
            $query = TenderAdditionalCost::where('tender_number', $tender->tender_number);
            if ($item != null) {
                $query = $query->where('pr_number', $item->number)
                    ->where('pr_line_number', $item->line_number);
            }
            $models = $query->get();
            $data = [];
            if ($models && $models->count() > 0) {
                foreach ($models->toArray() as $val) {
                    $data = [
                        'tender_number' => $tender->tender_number,
                        'vendor_id' => $params['vendor_id'],
                        'vendor_code' => $params['vendor_code'],
                        'item_id' => $item ? $item->line_id : null,
                        'conditional_code' => $val['conditional_code'],
                        'conditional_name' => $val['conditional_name'],
                        'percentage' => $val['percentage'],
                        'value' => $val['value'],
                        'calculation_pos' => $val['calculation_pos'],
                        'conditional_type' => $val['conditional_type'],
                        'submission_method' => $stageType,
                    ];
                    TenderVendorAdditionalCost::create($data);
                }
                // $result = TenderVendorAdditionalCost::insertBulk($data);
                return true;
            }
            return null;
        } catch (Exception $e) {
            Log::error($this->logName . '::initialVendorAdditionalCosts error : ' . $e->getMessage());
            throw $e;
        }
    }
    private function initialVendorTaxCodes($tender, $params, $stageType, $item)
    {
        try {
            // insert data;
            $query = TenderTaxCode::where('tender_number', $tender->tender_number)
                ->where('pr_number', $item->number)
                ->where('pr_line_number', $item->line_number);
            $models = $query->get();
            // dd($models->toArray());
            $data = [];
            if ($models && $models->count() > 0) {
                foreach ($models->toArray() as $val) {
                    $data = [
                        'tender_number' => $tender->tender_number,
                        'vendor_id' => $params['vendor_id'],
                        'vendor_code' => $params['vendor_code'],
                        'item_id' => $item ? $item->line_id : null,
                        'tax_code' => $val['tax_code'],
                        'description' => $val['description'],
                        'submission_method' => $stageType,
                    ];
                    TenderVendorTaxCode::create($data);
                }
                // $result = TenderVendorTaxCode::insertBulk($data);
                return true;
            }
            return null;
        } catch (Exception $e) {
            Log::error($this->logName . '::initialVendorTaxCodes error : ' . $e->getMessage());
            throw $e;
        }
    }
    private function initialVendorItemText($tender, $params, $stageType, $item)
    {
        try {
            // insert data;
            $query = TenderItemText::where('tender_number', $tender->tender_number)
                ->where('item_id', $item->line_id)
                ->where('PREQ_NO', $item->number)
                ->where('PREQ_ITEM', $item->line_number);
            $models = $query->get();
            $data = [];

            if ($models && $models->count() > 0) {
                foreach ($models->toArray() as $val) {
                    $data = [
                        'tender_number' => $tender->tender_number,
                        'vendor_id' => $params['vendor_id'],
                        'vendor_code' => $params['vendor_code'],
                        'item_id' => $item ? $item->line_id : null,
                        'PREQ_NO' => $item ? $item->number : null,
                        'PREQ_ITEM' => $item ? $item->line_number : null,
                        'TEXT_ID' => $val['TEXT_ID'],
                        'TEXT_ID_DESC' => $val['TEXT_ID_DESC'],
                        'TEXT_FORM' => $val['TEXT_FORM'],
                        'TEXT_LINE' => $val['TEXT_LINE'],
                        'submission_method' => $stageType,
                    ];
                    TenderVendorItemText::create($data);
                }
                // $result = TenderVendorItemText::insertBulk($data);
                return true;
            }
            return null;
        } catch (Exception $e) {
            Log::error($this->logName . '::initialVendorItemText error : ' . $e->getMessage());
            throw $e;
        }
    }
    private function initialVendorItemDetail($tender, $params, $stageType)
    {
        try {
            // insert data;
            $query = TenderItemDetail::where('tender_number', $tender->tender_number);
            $models = $query->get();
            $data = [];
            if ($models && $models->count() > 0) {
                foreach ($models->toArray() as $val) {
                    $data = [
                        'tender_number' => $tender->tender_number,
                        'item_spec_id' => $val['line_id'],
                        'vendor_id' => $params['vendor_id'],
                        'vendor_code' => $params['vendor_code'],
                        'description' => $val['description'],
                        'requirement' => $val['requirement'],
                        'reference' => $val['reference'],
                        'category_id' => $val['category_id'],
                        'submission_method' => $stageType,
                    ];
                    TenderVendorItemDetail::create($data);
                }
                return true;
            }
            return null;
        } catch (Exception $e) {
            Log::error($this->logName . '::initialVendorItemDetail error : ' . $e->getMessage());
            throw $e;
        }
    }
    #endregion
}
