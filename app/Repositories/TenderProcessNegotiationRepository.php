<?php

namespace App\Repositories;

use App\Enums\TenderStatusEnum;
use App\Enums\TenderSubmissionEnum;
use App\Models\TenderBiddingDocumentRequirement;
use App\Models\TenderHeaderCommercial;
use App\Models\TenderHeaderTechnical;
use App\Models\TenderItemCommercial;
use App\Models\TenderItemTechnical;
use App\Models\TenderVendor;
use App\Models\TenderVendorAdditionalCost;
use App\Models\TenderVendorItemText;
use App\Models\TenderVendorSubmissionDetail;
use App\Models\TenderVendorTaxCode;
use App\Helpers\App as HelpersApp;
use App\Models\TenderHeaderCommercialAwarding;
use App\Models\TenderHeaderTechnicalAwarding;
use App\Models\TenderItemCommercialAwarding;
use App\Models\TenderItemTechnicalAwarding;
use App\Models\TenderLogs;
use App\Models\TenderReference;
use App\Models\TenderVendorAdditionalCostAwarding;
use App\Models\TenderVendorAwarding;
use App\Models\TenderVendorSubmission;
use App\Models\TenderVendorTaxCodeAwarding;
use App\Services\TenderMailService;
use App\TenderItem;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TenderProcessNegotiationRepository extends TenderProcessRepository
{

    protected $logName = 'TenderProcessNegotiationRepository';
    public static $LABLE_VERSION1 = 'Commercial';
    public static $LABLE_VERSION2 = 'Nego';

    public function __construct()
    {
    }

    /**
     * save data admin tender header
     *
     * @param object $params
     * @param object $tender
     * @param string $type
     *
     * @return \Illuminate\Database\Eloquent\Builder $builder
     */
    public function saveNegotiation($params, $tender, $pageType)
    {
        $stageType = 6;
        try {
            if (isset($params['vendor_id'])) {
                DB::beginTransaction();

                $tenderVendor = TenderVendor::where('tender_number', $tender->tender_number)
                    ->where('vendor_id', $params['vendor_id'])->first();

                if ($tenderVendor) {
                    $tenderVendor->negotiation_status = $params['action_type'];
                    $tenderVendor->save();

                    if ($params['action_type'] == TenderSubmissionEnum::FLOW_STATUS[1]) {
                        $this->copyTenderHeaderCommercial($params, $tender, true);
                        $this->copyTenderItemCommercial($params, $tender, true, true);

                        $this->copyTenderHeaderTechnical($params, $tender, true);
                        $this->copyTenderItemTechnical($params, $tender, true);

                        $this->startStage($params, $tender, $pageType, $stageType);
                    }

                    if (
                        $params['action_type'] == TenderSubmissionEnum::FLOW_STATUS[2] ||
                        $params['action_type'] == TenderSubmissionEnum::FLOW_STATUS[4]
                    ) {
                        $this->openStage($params, $tender, $stageType);
                    }
                }

                DB::commit();

                if ($params['action_type'] == TenderSubmissionEnum::FLOW_STATUS[1]) {
                    (new TenderMailService)->sendEmailOnTenderNegotiationStarted($tender, $stageType, $params);
                }

                return [
                    'data' => $tender,
                    'next' => null,
                ];
            } else {
                if (
                    $params['action_type'] == TenderSubmissionEnum::FLOW_STATUS[3]
                    || $params['action_type'] == TenderSubmissionEnum::FLOW_STATUS[6]
                ) {
                    if ($params['action_type'] == TenderSubmissionEnum::FLOW_STATUS[3]) {
                        (new TenderMailService)->sendEmailOnTenderNegotiationResubmitted($tender, $stageType);
                    } else if ($params['action_type'] == TenderSubmissionEnum::FLOW_STATUS[6]) {
                        (new TenderMailService)->sendEmailOnTenderNegotiationEvaluated($tender, $stageType);
                    }
                    return $this->saveProcessNegotiation($params, $tender, $pageType);
                } else {
                    return $this->saveProcess($params, $tender, $pageType);
                }
            }
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($this->logName . '::saveNegotiation error : ' . $e->getMessage());
            Log::error($e);
            throw $e;
        }
    }

    public function saveNegotiationVendor($params, $tender, $pageType) //extends function $this->saveProcess()
    {
        $stageType = !empty($params['stage_type'])
            ? $params['stage_type']
            : TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE[$pageType];

        $vendor = Auth::user()->vendor;
        $tenderVendor = TenderVendor::where('tender_number', $tender->tender_number)
            ->where('vendor_id', $vendor->id)
            ->first();
        if (in_array($tenderVendor->negotiation_status, [TenderSubmissionEnum::FLOW_STATUS[2], TenderSubmissionEnum::FLOW_STATUS[4], TenderSubmissionEnum::FLOW_STATUS[6]])) {
            //can't save
            if ($tenderVendor->negotiation_status == TenderSubmissionEnum::FLOW_STATUS[6]) {
                $message = "Negotiation process is already finished";
            } else {
                $message = "Negotiation is being opened";
            }
            return [
                'success' => false,
                'message' => $message,
                'data' => $tender,
                'next' => null,
            ];
        } else {
            switch ($params['action_type']) {
                case 'request-submission-detail':
                    return $this->saveNegotiationSubmissionDetail($tender, $stageType);
                default:
                    return $this->saveProcess($params, $tender, $pageType);
            }
        }
    }

    protected function saveNegotiationSubmissionDetail($tender, $stageType)
    {
        try {
            $vendor = Auth::user()->vendor;
            DB::beginTransaction();

            $data = [
                'tender_number' => $tender->tender_number,
                'vendor_id' => $vendor->id,
                'submission_date' => Carbon::now(),
                'submission_method' => $stageType,
                'status' => TenderVendorSubmission::STATUS[0],
                'action_status' => TenderStatusEnum::ACT_NEW,
            ];

            //nego technical
            // TenderVendorSubmission::updateOrCreate([
            //     'tender_number' => $tender->tender_number,
            //     'vendor_id' => $vendor->id,
            //     'submission_method' => TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE["negotiation_technical"],
            // ], $data);

            //nego commercial
            TenderVendorSubmission::updateOrCreate([
                'tender_number' => $tender->tender_number,
                'vendor_id' => $vendor->id,
                'submission_method' => $stageType,
            ], $data);

            $details = TenderVendorSubmissionDetail::where('tender_number', $tender->tender_number)
                ->where('vendor_id', $vendor->id)
                ->whereNull('deleted_at')
                ->where('submission_method', TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE["process_commercial_evaluation"])
                ->get();

            if ($details->count() > 0) {
                foreach ($details as $detail) {
                    TenderVendorSubmissionDetail::create([
                        'tender_number' => $detail->tender_number,
                        'vendor_id' => $detail->vendor_id,
                        'vendor_code' => $detail->vendor_code,
                        'bidding_document_id' => $detail->bidding_document_id,
                        'submission_method' => $stageType,
                        'attachment' => $detail->attachment,
                        'status' => TenderVendorSubmissionDetail::STATUS[1],
                        'order' => $detail->order,
                    ]);
                }
            }

            DB::commit();
            return [
                'data' => $tender,
                'next' => null,
            ];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($this->logName . '::saveNegotiationSubmissionDetail error : ' . $e->getMessage());
            Log::error($e);
            throw $e;
        }
    }

    /**
     * find data vendor submission detail
     *
     * @param string $number
     * @param int $vendorId
     * @param string $stageType
     *
     * @return \Illuminate\Database\Eloquent\Builder $builder
     */
    public function findVendorSubmissionDetail($number, $vendorId, $stageType, $defaultIsEmpty = false)
    {
        try {
            $query = TenderBiddingDocumentRequirement::select(
                'tender_bidding_document_requirements.line_id',
                'tender_bidding_document_requirements.order',
                'tender_bidding_document_requirements.tender_number',
                'tender_vendor_submission_detail.id',
                'tender_vendor_submission_detail.vendor_id',
                'tender_vendor_submission_detail.vendor_code',
                'tender_vendor_submission_detail.status',
                'tender_vendor_submission_detail.attachment',
                'tender_bidding_document_requirements.description',
                'tender_bidding_document_requirements.stage_type',
                'tender_bidding_document_requirements.submission_method',
                'tender_bidding_document_requirements.is_required'
            )->leftJoin(
                'tender_vendor_submission_detail',
                function ($join) use ($vendorId) {
                    TenderVendorSubmissionDetail::vendorStatus($join)
                        ->on('tender_vendor_submission_detail.bidding_document_id', 'tender_bidding_document_requirements.line_id')
                        ->where('tender_vendor_submission_detail.vendor_id', DB::raw($vendorId))
                        ->whereNull('tender_vendor_submission_detail.deleted_at');
                }
            )
                ->where('tender_bidding_document_requirements.tender_number', $number)
                ->where('tender_vendor_submission_detail.submission_method', 6)
                ->whereNull('tender_vendor_submission_detail.deleted_at')
                ->whereNull('tender_bidding_document_requirements.deleted_at');

            if (!$defaultIsEmpty) {
                $query = $query->where('tender_vendor_submission_detail.vendor_id', $vendorId);
            }

            // if (!empty($stageType)) {
            //     $query = $query->where('tender_bidding_document_requirements.submission_method', $stageType);
            // }

            return $query;
        } catch (Exception $e) {
            Log::error($this->logName . '::findVendorSubmissionDetail error : ' . $e->getMessage());
            throw $e;
        }
    }

    private function copyTenderHeaderCommercial($params, $tender, $initialData = false)
    {
        $tenderHeader = TenderHeaderCommercial::where('tender_number', $tender->tender_number)
            ->where('vendor_id', $params['vendor_id'])
            ->where('status', '!=', TenderSubmissionEnum::STATUS_ITEM[1])
            ->whereNull('deleted_at')
            ->where('submission_method', TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE["process_commercial_evaluation"])
            ->first();

        $countNego = TenderHeaderCommercial::where('tender_number', $tender->tender_number)
            ->where('vendor_id', $params['vendor_id'])
            ->where('submission_method', TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE["negotiation_commercial"])
            ->whereNull('deleted_at')
            ->count();

        if ($tenderHeader && $countNego <= 0) {
            $tenderHeaderNego = new TenderHeaderCommercial;
            $tenderHeaderNego->tender_number = $tenderHeader->tender_number;
            $tenderHeaderNego->vendor_id = $tenderHeader->vendor_id;
            $tenderHeaderNego->vendor_code = $tenderHeader->vendor_code;
            $tenderHeaderNego->quotation_number = $tenderHeader->quotation_number;
            $tenderHeaderNego->quotation_date = $tenderHeader->quotation_date;
            $tenderHeaderNego->quotation_note = $tenderHeader->quotation_note;
            $tenderHeaderNego->quotation_file = $tenderHeader->quotation_file;
            $tenderHeaderNego->incoterm = $tenderHeader->incoterm;
            $tenderHeaderNego->incoterm_location = $tenderHeader->incoterm_location;
            $tenderHeaderNego->bid_bond_value = $tenderHeader->bid_bond_value;
            $tenderHeaderNego->bid_bond_file = $tenderHeader->bid_bond_file;
            $tenderHeaderNego->bid_bond_end_date = $tenderHeader->bid_bond_end_date;
            $tenderHeaderNego->currency_code = $tenderHeader->currency_code;
            $tenderHeaderNego->submission_method = TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE["negotiation_commercial"];
            $tenderHeaderNego->save();
        } else {
            Log::error($this->logName . '::copyTenderHeaderCommercial error : tender header commercial not found');
            throw new Exception("tender header commercial not found.");
        }
    }

    private function copyTenderItemCommercial($params, $tender, $copytaxesAndAddCost = true, $initialData = false)
    {
        $tenderItems = TenderItemCommercial::where('tender_number', $tender->tender_number)
            ->where('vendor_id', $params['vendor_id'])
            ->where('status', '!=', TenderSubmissionEnum::STATUS_ITEM[1])
            ->whereNull('deleted_at')
            ->where('submission_method', TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE["process_commercial_evaluation"])
            ->get();

        $countNego = TenderItemCommercial::where('tender_number', $tender->tender_number)
            ->where('vendor_id', $params['vendor_id'])
            ->where('submission_method', TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE["negotiation_commercial"])
            ->whereNull('deleted_at')
            ->count();

        if ($tenderItems->count() > 0 && $countNego <= 0) {
            foreach ($tenderItems as $item) {

                if (empty($item->item_id)) {
                    throw new Exception("data tender items commercial not valid.");
                }

                $tenderItemNego = new TenderItemCommercial;
                $tenderItemNego->tender_number = $item->tender_number;
                $tenderItemNego->vendor_id = $item->vendor_id;
                $tenderItemNego->vendor_code = $item->vendor_code;
                $tenderItemNego->item_id = $item->item_id;
                $tenderItemNego->est_unit_price = $item->est_unit_price;
                $tenderItemNego->price_unit = $item->price_unit;
                $tenderItemNego->subtotal = $item->subtotal;
                $tenderItemNego->currency_code = $item->currency_code;
                $tenderItemNego->compliance = $item->compliance;
                $tenderItemNego->overall_limit = $item->overall_limit;
                $tenderItemNego->submission_method = TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE["negotiation_commercial"];
                $tenderItemNego->save();
            }

            if ($copytaxesAndAddCost) {
                $this->copyVendorTaxCodes($tender, $params, $tenderItemNego->submission_method);
                $this->copyVendorAdditionalCost($tender, $params, $tenderItemNego->submission_method);
            }
        } else {
            Log::error($this->logName . '::copyTenderItemCommercial error : tender items commercial not found');
            // throw new Exception("tender items commercial not found.");
        }
    }

    private function copyTenderHeaderTechnical($params, $tender, $initialData = false)
    {
        $tenderHeader = TenderHeaderTechnical::where('tender_number', $tender->tender_number)
            ->where('vendor_id', $params['vendor_id'])
            ->where('status', '!=', TenderSubmissionEnum::STATUS_ITEM[1])
            ->whereNull('deleted_at')
            ->where('submission_method', TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE["process_technical_evaluation"])
            ->first();

        $countNego = TenderHeaderTechnical::where('tender_number', $tender->tender_number)
            ->where('vendor_id', $params['vendor_id'])
            ->where('submission_method', TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE["negotiation_commercial"])
            ->whereNull('deleted_at')
            ->count();

        if ($tenderHeader && $countNego <= 0) {
            $tenderHeaderNego = new TenderHeaderTechnical;
            $tenderHeaderNego->tender_number = $tenderHeader->tender_number;
            $tenderHeaderNego->vendor_id = $tenderHeader->vendor_id;
            $tenderHeaderNego->vendor_code = $tenderHeader->vendor_code;
            $tenderHeaderNego->quotation_number = $tenderHeader->quotation_number;
            $tenderHeaderNego->quotation_date = $tenderHeader->quotation_date;
            $tenderHeaderNego->quotation_note = $tenderHeader->quotation_note;
            $tenderHeaderNego->tkdn_percentage = $tenderHeader->tkdn_percentage;
            $tenderHeaderNego->quotation_file = $tenderHeader->quotation_file;
            $tenderHeaderNego->tkdn_file = $tenderHeader->tkdn_file;
            $tenderHeaderNego->proposed_item_file = $tenderHeader->proposed_item_file;
            $tenderHeaderNego->submission_method = TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE["negotiation_technical"];
            $tenderHeaderNego->save();
        } else {
            Log::info($this->logName . '::copyTenderHeaderTechnical error : tender header technical not found');
            // throw new Exception("tender header technical not found.");
        }
    }

    private function copyTenderItemTechnical($params, $tender, $initialData = false)
    {
        $tenderItems = TenderItemTechnical::where('tender_number', $tender->tender_number)
            ->where('vendor_id', $params['vendor_id'])
            ->where('status', '!=', TenderSubmissionEnum::STATUS_ITEM[1])
            ->whereNull('deleted_at')
            ->where('submission_method', TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE["process_technical_evaluation"])
            ->get();

        $countNego = TenderItemTechnical::where('tender_number', $tender->tender_number)
            ->where('vendor_id', $params['vendor_id'])
            ->whereNull('deleted_at')
            ->where('submission_method', TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE["negotiation_commercial"])
            ->count();

        if ($tenderItems->count() > 0 && $countNego <= 0) {
            foreach ($tenderItems as $item) {

                if (empty($item->item_id)) {
                    throw new Exception("data tender items technical not valid.");
                }

                $tenderItemNego = new TenderItemTechnical;
                $tenderItemNego->tender_number = $item->tender_number;
                $tenderItemNego->vendor_id = $item->vendor_id;
                $tenderItemNego->vendor_code = $item->vendor_code;
                $tenderItemNego->item_id = $item->item_id;
                $tenderItemNego->description = $item->description;
                $tenderItemNego->qty = $item->qty;
                $tenderItemNego->compliance = $item->compliance;
                $tenderItemNego->submission_method = TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE["negotiation_technical"];
                $tenderItemNego->save();
            }
            $this->copyVendorItemText($tender, $params, $tenderItemNego->submission_method);
        } else {
            Log::error($this->logName . '::copyTenderItemTechnical error : tender items technical not found');
            // throw new Exception("tender items technical not found.");
        }
    }

    protected function saveTenderItems($params, $tender, $stageType)
    {
        try {
            DB::beginTransaction();

            $vendor = Auth::user()->vendor;
            $params['vendor_id'] = $vendor->id;
            $params['vendor_code'] = $vendor->vendor_code;

            if (!empty($params['items']) && count($params['items']) > 0) {
                $items = $params['items'];
                foreach ($items as $k => $it) {
                    $data = [
                        'tender_number' => $tender->tender_number,
                        'id' => $it['key_id'] ?? null,
                        'vendor_id' => $params['vendor_id'],
                        'vendor_code' => $params['vendor_code'],
                        'item_id' => $it['line_id'],
                        'description' => $it['description_vendor'],
                        'qty' => $it['qty_vendor'] ?? 0,
                        'price_unit' => $it['price_unit_vendor'],
                        'est_unit_price' => $it['est_unit_price_vendor'] ?? 0,
                        'currency_code' => $it['currency_code_vendor'] ?? 0,
                        'overall_limit' => $it['overall_limit_vendor'] ?? 0,
                        'compliance' => $it['compliance'] ?? 0,
                    ];
                    if ($it['compliance'] == 'no_quote') {
                        $data['price_unit'] = 0;
                        $data['est_unit_price'] = 0;
                    }

                    // $data['subtotal'] = $data['qty'] * $data['price_unit'];
                    // unset($data['description']);
                    // unset($data['qty']);
                    $data['submission_method'] = $stageType;

                    // TenderItemTechnical::where('tender_number', $data["tender_number"])
                    //     ->where('vendor_id', $data['vendor_id'])
                    //     ->where('item_id', $data['item_id'])
                    //     ->whereNull('deleted_at')
                    //     ->where('submission_method', TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE["negotiation_technical"])
                    //     ->update(["description" => $it['description_vendor']]);

                    $model = TenderItemCommercial::find($data['id']);
                    if (!$model) $model = new TenderItemCommercial();
                    $model->fill($data);
                    $model->save();
                }
            }

            $model = null;
            if (!empty($params['item'])) {
                $model = TenderItemCommercial::find($params['item']['key_id']);

                if ($model) {
                    $model->est_unit_price = $params['item']['est_unit_price_vendor'];
                    $model->compliance = $params['item']['compliance'];

                    if (!empty($params['item']['overall_limit_vendor'])) {
                        $model->overall_limit = $params['item']['overall_limit_vendor'];
                    }
                    if ($params['item']['compliance'] == 'no_quote') {
                        $model->est_unit_price = 0;
                    }

                    $model->save();
                }
            }

            if (!isset($params['items']) || empty($params['items'])) {
                $this->saveVendorAdditionalCost($tender, $params, $stageType, $model);
                if ($model) {
                    $this->saveVendorTaxCodes($tender, $params, $stageType, $model);
                }
            }

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
        try {
            $number = $tender->tender_number;
            $query = TenderItem::select(
                'tender_items.*',
                'v.id as vendor_id',
                'v.vendor_name',
                'nego.sequence_no',
                'nego.submission_method',
                'nego.compliance',
                DB::raw("CASE WHEN COALESCE(nego.submission_method, 4) = 4 THEN '" . self::$LABLE_VERSION1 . "' ELSE '" . self::$LABLE_VERSION2 . "' END as item_version_comm"),
                DB::raw("nego.description as description_vendor"),
                DB::raw("nego.qty as qty_vendor"),
                DB::raw("nego.est_unit_price as est_unit_price_vendor"),
                DB::raw("nego.overall_limit as overall_limit_vendor"),
                DB::raw("nego.price_unit as price_unit_vendor"),
                DB::raw("nego.currency_code as currency_code_vendor"),
                DB::raw("COALESCE(tas.additional_cost,0) as additional_cost"),
                DB::raw("ROUND(nego.overall_limit * nego.qty, 2) as total_overall_limit_vendor"),
                DB::raw("ROUND(nego.est_unit_price * nego.qty, 2) as subtotal_vendor"),
            )
                ->join(DB::raw('(' . $this->rawNegotiation() . ') nego'), function ($join) {
                    $join->on('nego.item_id', '=', 'tender_items.line_id');
                })
                ->join('tender_vendors as tv', function ($join) {
                    $join->on('tv.vendor_id', '=', 'nego.vendor_id');
                    $join->on('tv.tender_number', '=', 'nego.tender_number');
                })
                ->join('vendors as v', function ($join) {
                    $join->on('v.id', '=', 'nego.vendor_id');
                })
                ->leftJoin(DB::raw('(' . $this->rawAdditionalCostNegotiation($number, $tender->conditional_type) . ') tas'), function ($join) {
                    $join->on('tas.tender_number', '=', 'tender_items.tender_number');
                    $join->on('tas.item_id', '=', 'tender_items.line_id');
                    $join->on('tas.vendor_id', '=', 'v.id');
                    $join->on('tas.submission_method', '=', 'nego.submission_method');
                })
                ->where('tender_items.tender_number', $number)
                ->where('tv.negotiation_status', "!=", "start")
                ->whereRaw("UPPER(COALESCE(tender_items.deleteflg, '')) != 'X'")
                ->orderBy('tender_items.id', 'asc')
                ->orderBy('v.vendor_name', 'asc')
                ->orderBy('tender_items.line_id', 'asc')
                ->orderBy('nego.sequence_no', 'asc');
            return $query;
        } catch (Exception $e) {
            Log::error($this->logName . '::findTenderComparisonItems error : ' . $e->getMessage());
            throw $e;
        }
    }

    private function rawNegotiation()
    {
        $query = "select A.*, b.description, b.qty, 1 as sequence_no from tender_item_commercial a
                inner join tender_item_technical b
                    on a.tender_number = b.tender_number and a.vendor_id = b.vendor_id and a.item_id = b.item_id
                where a.deleted_at is null and b.deleted_at is null
                and a.status != '" . TenderSubmissionEnum::STATUS_ITEM[1] . "'
                and b.status != '" . TenderSubmissionEnum::STATUS_ITEM[1] . "'
                and a.submission_method = " . TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE["process_commercial_evaluation"] . "
                and b.submission_method = " . TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE["process_technical_evaluation"] . "

                union all

                select a.*, b.description, b.qty, 2 as sequence_no from tender_item_commercial a
                inner join tender_item_technical b
                    on a.tender_number = b.tender_number and a.vendor_id = b.vendor_id and a.item_id = b.item_id
                where a.deleted_at is null and b.deleted_at is null
                and a.status != '" . TenderSubmissionEnum::STATUS_ITEM[1] . "'
                and b.status != '" . TenderSubmissionEnum::STATUS_ITEM[1] . "'
                and a.submission_method = " . TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE["negotiation_commercial"] . "
                and b.submission_method = " . TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE["negotiation_technical"];

        return $query;
    }

    private function rawAdditionalCostNegotiation($number, $type, $subtotal = 0)
    {
        $WORKFLOW_MAPPING_TYPE = TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE; // ["negotiation_commercial"];
        // $commercialItems = "select tivc.tender_number,tivc.vendor_id,tivc.item_id,tivc.price_unit,tivc.est_unit_price,tivc.overall_limit,
        //         (case
        //             when tivc.compliance='no_quote' then 0
        //             when ti.item_category='0' then round((tivc.est_unit_price * tivt.qty), 2)
        //             when ti.item_category!='0' then round((tivc.overall_limit * tivt.qty), 2)
        //         end) as subtotal,
        //         tivc.compliance,
        //         tivc.submission_method
        //     from tender_item_commercial tivc
        //     join tender_item_technical tivt on tivt.item_id=tivc.item_id and tivc.tender_number=tivt.tender_number and tivt.vendor_id=tivc.vendor_id
        //         and tivt.submission_method=" . $WORKFLOW_MAPPING_TYPE['process_technical_evaluation'] . "
        //     join tender_items ti on ti.tender_number=tivc.tender_number and ti.line_id=tivc.item_id
        //     where tivc.submission_method=" . $WORKFLOW_MAPPING_TYPE['process_commercial_evaluation'] . "
        //     and tivc.deleted_at is null and tivt.deleted_at is null and ti.deleted_at is null
        //     and tivc.status != '" . TenderSubmissionEnum::STATUS_ITEM[1] . "'
        //     and tivt.status != '" . TenderSubmissionEnum::STATUS_ITEM[1] . "'";

        // $negotiationItems = "select tivc.tender_number,tivc.vendor_id,tivc.item_id,tivc.price_unit,tivc.est_unit_price,tivc.overall_limit,
        //     (case
        //         when tivc.compliance='no_quote' then 0
        //         when ti.item_category='0' then round((tivc.est_unit_price * tivt.qty), 2)
        //         when ti.item_category!='0' then round((tivc.overall_limit * tivt.qty), 2)
        //     end) as subtotal,
        //     tivc.compliance,
        //     tivc.submission_method
        // from tender_item_commercial tivc
        // join tender_item_technical tivt on tivt.item_id=tivc.item_id and tivc.tender_number=tivt.tender_number and tivt.vendor_id=tivc.vendor_id
        //     and tivt.submission_method=" . $WORKFLOW_MAPPING_TYPE['negotiation_technical'] . "
        // join tender_items ti on ti.tender_number=tivc.tender_number and ti.line_id=tivc.item_id
        // where tivc.submission_method=" . $WORKFLOW_MAPPING_TYPE['negotiation_commercial'] . "
        // and tivc.deleted_at is null and tivt.deleted_at is null and ti.deleted_at is null
        // and tivc.status != '" . TenderSubmissionEnum::STATUS_ITEM[1] . "'
        // and tivt.status != '" . TenderSubmissionEnum::STATUS_ITEM[1] . "'";

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

        $negotiationItems = "select tivc.tender_number,tivc.vendor_id,tivc.item_id,tivc.price_unit,tivc.est_unit_price,tivc.overall_limit,";
        if ($type == 'CT1') {
            $negotiationItems .= "(case
                when tivc.compliance='no_quote' then 0
                else round((tivc.est_unit_price * tivt.qty) + (tivc.overall_limit * tivt.qty / (case when tivc.price_unit > 0 then tivc.price_unit else 1 end)), 2)
            end) as subtotal,";
        } else {
            $negotiationItems .= "(case
                when tivc.compliance='no_quote' then 0
                when ti.item_category='0' then round((tivc.est_unit_price * tivt.qty / (case when tivc.price_unit > 0 then tivc.price_unit else 1 end)), 2)
                when ti.item_category!='0' then round((tivc.overall_limit * tivt.qty / (case when tivc.price_unit > 0 then tivc.price_unit else 1 end)), 2)
            end) as subtotal,";
        }
        $negotiationItems .= "tivc.compliance,tivc.submission_method from tender_item_commercial tivc
            join tender_item_technical tivt on tivt.item_id=tivc.item_id and tivc.tender_number=tivt.tender_number and tivt.vendor_id=tivc.vendor_id
                and tivt.submission_method=" . $WORKFLOW_MAPPING_TYPE['negotiation_technical'] . "
                and tivt.status !='" . TenderSubmissionEnum::STATUS_ITEM[1] . "'
            join tender_items ti on ti.tender_number=tivc.tender_number and ti.line_id=tivc.item_id
            where tivc.submission_method=" . $WORKFLOW_MAPPING_TYPE['negotiation_commercial'] . " and ti.deleted_at is null and UPPER(COALESCE(ti.deleteflg, '')) != 'X'
            and tivc.status !='" . TenderSubmissionEnum::STATUS_ITEM[1] . "'
            AND tivt.deleted_at IS null
            AND tivc.deleted_at IS null
        ";

        // header level
        if ($type == 'CT1') {
            $subQuery = "select
                a.tender_number,a.vendor_id,a.item_id,
                sum(case
                    when c.compliance = 'no_quote' then 0
                    when b.calculation_type=1 and a.calculation_pos=1 then round((a.percentage * c.subtotal / 100.00),2)
                    when b.calculation_type=1 and a.calculation_pos=2 then (0 - round((a.percentage * c.subtotal / 100.00),2))
                    else 0
                END) as additional_cost,
                c.submission_method
            from tender_vendor_additional_costs a
            inner join conditional_types b on a.conditional_code=b.\"type\"
            join (" . $commercialItems . " union " . $negotiationItems . ") c on a.tender_number=c.tender_number
            and a.vendor_id=c.vendor_id and a.submission_method = c.submission_method
            where a.deleted_at is null and a.tender_number='" . $number . "' AND a.conditional_type='" . $type . "'
            and c.compliance !='no_quote' and b.calculation_type=1
            and a.status !='" . TenderSubmissionEnum::STATUS_ITEM[1] . "'
            group by a.tender_number,a.vendor_id,a.item_id,c.submission_method
            union all
            select
                a.tender_number,a.vendor_id,a.item_id,
                sum(a.additional_cost) as additional_cost,
                a.submission_method
            from
            (
                select
                    a.tender_number,a.vendor_id,a.item_id,
                    case
                        when c.compliance = 'no_quote' then 0
                        when b.calculation_type=2 and a.calculation_pos=1 then max(a.value)
                        when b.calculation_type=2 and a.calculation_pos=2 then (0-max(a.value))
                        else 0
                    END as additional_cost,
                    a.submission_method
                from tender_vendor_additional_costs a
                inner join conditional_types b on a.conditional_code=b.\"type\"
                join (" . $commercialItems . " union " . $negotiationItems . ") c on a.tender_number=c.tender_number
                and a.vendor_id=c.vendor_id and a.submission_method = c.submission_method
                where a.deleted_at is null and a.tender_number='" . $number . "' AND a.conditional_type='" . $type . "'
                and c.compliance !='no_quote' and b.calculation_type=2
                and a.status !='" . TenderSubmissionEnum::STATUS_ITEM[1] . "'
                group by a.tender_number,a.vendor_id,a.item_id,a.conditional_code,b.calculation_type,a.calculation_pos,a.submission_method,c.compliance
            ) a group by a.tender_number,a.vendor_id,a.item_id,a.submission_method
            ";

            $query = "select
                a.tender_number
                ,a.vendor_id,a.item_id,
                sum(a.additional_cost) as additional_cost,
                a.submission_method
            from (" . $subQuery . ") a group by a.tender_number,a.vendor_id,a.item_id,a.submission_method";
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
                END) as additional_cost,
                c.submission_method
            from tender_vendor_additional_costs a
            inner join conditional_types b on a.conditional_code=b.\"type\"
            join (" . $commercialItems . " union " . $negotiationItems . ") c on a.tender_number=c.tender_number
            and a.item_id=c.item_id and a.submission_method = c.submission_method
            where a.deleted_at is null and a.tender_number='" . $number . "' AND a.conditional_type='" . $type . "'
            and a.status !='" . TenderSubmissionEnum::STATUS_ITEM[1] . "'
            group by a.tender_number,a.vendor_id,a.item_id,c.submission_method";
        }
        return $query;
    }

    /**
     * find data tender items summary
     *
     * @param string $number
     * @param int $vendorId
     * @param string $stageType
     *
     * @return \Illuminate\Database\Eloquent\Builder $builder
     */
    public function findTenderSummaryItems($tender, $stageType)
    {
        try {
            $number = $tender->tender_number;
            $query = TenderItem::leftJoin(DB::raw('(' . $this->rawNegotiation() . ') nego'), function ($join) {
                $join->on('nego.item_id', '=', 'tender_items.line_id');
            })
                ->join('tender_vendors as tv', function ($join) {
                    $join->on('tv.vendor_id', '=', 'nego.vendor_id');
                    $join->on('tv.tender_number', '=', 'nego.tender_number');
                })
                ->join('vendors as v', function ($join) {
                    $join->on('v.id', '=', 'nego.vendor_id');
                })
                ->leftJoin(DB::raw('(' . $this->rawAdditionalCostNegotiation($number, $tender->conditional_type) . ') tas'), function ($join) use ($tender) {
                    $join->on('tas.tender_number', '=', 'tender_items.tender_number');
                    $join->on('tas.vendor_id', '=', 'v.id');
                    $join->on('tas.submission_method', '=', 'nego.submission_method');
                    if ($tender->conditional_type == 'CT2') {
                        $join->on('tas.item_id', '=', 'tender_items.line_id');
                    }
                })
                ->where('tender_items.tender_number', $number)
                ->whereRaw("UPPER(COALESCE(tender_items.deleteflg, '')) != 'X'")
                ->where('nego.sequence_no', 2)
                ->where('tv.negotiation_status', "!=", "start");

            if ($tender->conditional_type == 'CT1') {
                $query = $query->select(
                    'v.vendor_code',
                    'v.vendor_name',
                    DB::raw("CASE WHEN sequence_no = 1 THEN '" . self::$LABLE_VERSION1 . "' ELSE '" . self::$LABLE_VERSION2 . "' END as item_version_comm"),
                    DB::raw("SUM(nego.est_unit_price) as est_unit_price_vendor"),
                    DB::raw("ROUND(SUM(nego.est_unit_price * nego.qty),2) as subtotal_vendor"),
                    DB::raw("ROUND(SUM(nego.overall_limit * nego.qty),2) as total_overall_limit_vendor"),
                    DB::raw("(COALESCE(tas.additional_cost,0)) as total_additional_cost"),
                    'nego.currency_code as currency_code_vendor'
                )->groupBy('v.id', 'v.vendor_name', 'nego.currency_code', 'tas.additional_cost', 'nego.sequence_no')
                    ->orderBy('v.id', 'asc')
                    ->orderBy('v.vendor_name', 'asc')
                    ->orderBy('nego.sequence_no', 'asc');
            } else {
                $query = $query->select(
                    'v.vendor_code',
                    'v.vendor_name',
                    DB::raw("CASE WHEN sequence_no = 1 THEN '" . self::$LABLE_VERSION1 . "' ELSE '" . self::$LABLE_VERSION2 . "' END as item_version_comm"),
                    DB::raw("SUM(nego.est_unit_price) as est_unit_price_vendor"),
                    DB::raw("ROUND(SUM(nego.est_unit_price * nego.qty),2) as subtotal_vendor"),
                    DB::raw("ROUND(SUM(nego.overall_limit * nego.qty),2) as total_overall_limit_vendor"),
                    DB::raw("SUM(COALESCE(tas.additional_cost,0)) as total_additional_cost"),
                    'nego.currency_code as currency_code_vendor'
                )->groupBy('v.id', 'v.vendor_name', 'nego.currency_code', 'nego.sequence_no')
                    ->orderBy('v.id', 'asc')
                    ->orderBy('v.vendor_name', 'asc')
                    ->orderBy('nego.sequence_no', 'asc');
            }

            return $query;
        } catch (Exception $e) {
            Log::error($this->logName . '::findTenderSummaryItems error : ' . $e->getMessage());
            throw $e;
        }
    }

    private function saveProcessNegotiation($params, $tender, $pageType)
    {
        try {
            DB::beginTransaction();

            $WORKFLOW_MAPPING_TYPE = TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE;
            $stageType = $WORKFLOW_MAPPING_TYPE["negotiation_commercial"];

            $tenderVendors = TenderVendor::where("tender_number", $tender->tender_number)
                ->where("tender_number", $tender->tender_number)
                ->whereNull('deleted_at')
                // ->whereNotNull("negotiation_status")
                ->get();

            foreach ($tenderVendors as $item) {
                //request resubmission negotiation
                if ($params['action_type'] == TenderSubmissionEnum::FLOW_STATUS[3]) {

                    $tenderHeader = TenderHeaderCommercial::where('tender_number', $tender->tender_number)
                        ->where('vendor_id', $item->vendor_id)
                        ->whereNull('deleted_at')
                        ->where('submission_method', $WORKFLOW_MAPPING_TYPE["negotiation_commercial"])
                        ->first();

                    if ($tenderHeader) {
                        $item->negotiation_status = $params['action_type'];
                        $item->save();
                    } else {
                        $item->negotiation_status = null;
                        $item->save();
                    }

                    //set tender workflow negotiation to not finished
                    // TenderWorkflowHelper::reopenWorkflow($tender,'negotiation');
                }

                //finish negotiation
                if ($params['action_type'] == TenderSubmissionEnum::FLOW_STATUS[6]) {
                    $item->negotiation_status = $params['action_type'];
                    $item->save();
                }
            }

            //request resubmission negotiation
            if ($params['action_type'] == TenderSubmissionEnum::FLOW_STATUS[3]) {
                $workflowValues = explode('-', $tender->workflow_values);
                $tender->workflow_values = $workflowValues[0] . '-' . $stageType . '-' . $params['action_type'];
                $tender->save();

                $vendorId = null;
                if (isset($params["vendor_id"])) {
                    $vendorId = $params["vendor_id"];
                }

                TenderReference::create([
                    'tender_number' => $tender->tender_number,
                    'ref_type' => $params['action_type'],
                    'ref_value' => '',
                    'ref_vendor_id' => $vendorId,
                    'submission_method' => $stageType,
                ]);
            }

            //finish negotiation
            if ($params['action_type'] == TenderSubmissionEnum::FLOW_STATUS[6]) {
                $params['action_type'] = TenderSubmissionEnum::FLOW_STATUS[5];
                $this->finishStage($params, $tender, $WORKFLOW_MAPPING_TYPE["negotiation_commercial"]);

                $params['action_type'] = TenderSubmissionEnum::FLOW_STATUS[6];
                $this->completeEvaluation($tender, $params, $pageType);

                $submission = TenderVendorSubmission::where('tender_number', $tender->tender_number)
                    ->where('submission_method', $WORKFLOW_MAPPING_TYPE["negotiation_commercial"])
                    ->get();

                foreach ($submission as $sub) {
                    TenderVendorSubmission::deleteDraftStatus($sub, $params['action_type']);
                }

                $this->updateDataAwarding($tender);
            }

            DB::commit();
            return [
                'data' => $tender,
                'next' => null,
            ];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($this->logName . '::openStage error : ' . $e->getMessage());
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
    private function copyVendorItemText($tender, $params, $stageType)
    {
        try {

            $itemTexts = TenderVendorItemText::where('tender_number', $tender->tender_number)
                ->where('vendor_id', $params['vendor_id'])
                ->where('status', '!=', TenderSubmissionEnum::STATUS_ITEM[1])
                ->whereNull('deleted_at')
                ->where('submission_method', TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE["process_technical_evaluation"])
                ->get();

            // insert data;
            $data = [];
            if ($itemTexts->count() > 0) {
                foreach ($itemTexts as $item) {
                    $data = [
                        'tender_number' => $tender->tender_number,
                        'vendor_id' => $item->vendor_id,
                        'vendor_code' => $item->vendor_code,
                        'item_id' => $item->item_id,
                        'PREQ_NO' => $item->PREQ_NO,
                        'PREQ_ITEM' => $item->PREQ_ITEM,
                        'TEXT_ID' => $item->TEXT_ID,
                        'TEXT_ID_DESC' => $item->TEXT_ID_DESC,
                        'TEXT_FORM' => $item->TEXT_FORM,
                        'TEXT_LINE' => $item->TEXT_LINE,
                        'submission_method' => $stageType
                    ];

                    TenderVendorItemText::create($data);
                }
            }

            return true;
        } catch (Exception $e) {
            Log::error($this->logName . '::copyVendorItemText error : ' . $e->getMessage());
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
    private function copyVendorTaxCodes($tender, $params, $stageType)
    {
        try {
            $itemTextCodes = TenderVendorTaxCode::where('tender_number', $tender->tender_number)
                ->where('vendor_id', $params['vendor_id'])
                ->where('status', '!=', TenderSubmissionEnum::STATUS_ITEM[1])
                ->whereNull('deleted_at')
                ->where('submission_method', TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE["process_commercial_evaluation"])
                ->get();

            // insert data;
            $data = [];
            if ($itemTextCodes->count() > 0) {
                foreach ($itemTextCodes as $item) {
                    $data = [
                        'tender_number' => $tender->tender_number,
                        'vendor_id' => $item->vendor_id,
                        'vendor_code' => $item->vendor_code,
                        'item_id' => $item->item_id,
                        'tax_code' => $item->tax_code,
                        'description' => $item->description,
                        'submission_method' => $stageType,
                    ];

                    TenderVendorTaxCode::create($data);
                }
            }

            return true;
        } catch (Exception $e) {
            Log::error($this->logName . '::copyVendorTaxCodes error : ' . $e->getMessage());
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
    private function copyVendorAdditionalCost($tender, $params, $stageType)
    {
        try {
            $itemAdditionalCosts = TenderVendorAdditionalCost::where('tender_number', $tender->tender_number)
                ->where('vendor_id', $params['vendor_id'])
                ->where('status', '!=', TenderSubmissionEnum::STATUS_ITEM[1])
                ->whereNull('deleted_at')
                ->where('submission_method', TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE["process_commercial_evaluation"])
                ->get();

            // insert data;
            $data = [];
            if ($itemAdditionalCosts->count() > 0) {
                foreach ($itemAdditionalCosts as $item) {
                    $data = [
                        'tender_number' => $tender->tender_number,
                        'vendor_id' => $item->vendor_id,
                        'vendor_code' => $item->vendor_code,
                        'item_id' => $item->item_id,
                        'conditional_code' => $item->conditional_code,
                        'conditional_name' => $item->conditional_name,
                        'percentage' => $item->percentage,
                        'value' => $item->value,
                        'calculation_pos' => $item->calculation_pos,
                        'conditional_type' => $item->conditional_type,
                        'submission_method' => $stageType,
                    ];

                    TenderVendorAdditionalCost::create($data);
                }
            }

            return true;
        } catch (Exception $e) {
            Log::error($this->logName . '::copyVendorAdditionalCost error : ' . $e->getMessage());
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
    public function saveVendorAdditionalCost($tender, $params, $stageType, $item = null)
    {
        try {
            // delete before insert
            $delModel = TenderVendorAdditionalCost::where('tender_number', $tender->tender_number)
                ->where('submission_method', $stageType)
                ->whereNull('deleted_at')
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
            if (!empty($params['cost'])) {
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
                        'submission_method' => $stageType
                    ];

                    TenderVendorAdditionalCost::create($data);
                }
            }

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
    public function saveVendorTaxCodes($tender, $params, $stageType, $item)
    {
        try {
            // delete before insert
            $delModel = TenderVendorTaxCode::where('tender_number', $tender->tender_number)
                ->where('vendor_id', $params['vendor_id'])
                ->where('submission_method', $stageType)
                ->whereNull('deleted_at');

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
            if (!empty($params['tax'])) {
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
            }

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
    public function saveVendorItemText($tender, $params, $stageType, $item)
    {
        try {
            // delete before insert
            $delModel = TenderVendorItemText::where('tender_number', $tender->tender_number)
                ->where('item_id', $item->item_id)
                ->where('submission_method', $stageType)
                ->whereNull('deleted_at')
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

            return true;
        } catch (Exception $e) {
            Log::error($this->logName . '::saveVendorItemText error : ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * action on vendor delete draft
     *
     * @param \App\TenderParameter $tender
     * @param string $stageType
     *
     */
    protected function resetSubmissionDetail($tender, $stageType)
    {
        try {
            $vendor = Auth::user()->vendor;
            DB::beginTransaction();

            $this->deleteDraft([
                'vendor_id' => $vendor->id,
                'vendor_code' => $vendor->vendor_code,
            ], $tender);

            // set log vendor delete draft
            $submission = TenderVendorSubmission::where('tender_number', $tender->tender_number)
                ->where('submission_method', $stageType)
                ->where('vendor_id', $vendor->id)
                ->first();
            $pageTypes = array_flip(TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE);
            TenderLogs::createNew([
                'activity' => 'delete_draft',
                'model_id' => $submission->id ?? null,
                'model_type' => 'App\Models\TenderVendorSubmission',
                'page_type' => $pageTypes[$stageType],
                'ref_number' => $tender->tender_number ?? '',
                'properties' => $submission,
            ]);

            DB::commit();
            return [
                'data' => $tender,
                'next' => null,
            ];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($this->logName . '::resetSubmissionDetail error : ' . $e->getMessage());
            throw $e;
        }
    }

    private function deleteDraft($params, $tender)
    {
        $WORKFLOW_MAPPING_TYPE = TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE;
        $tableVendors = [
            'tender_vendor_submission_detail',
            'tender_header_commercial',
            'tender_item_commercial',
            'tender_vendor_additional_cost',
            'tender_vendor_tax_code'
        ];

        if (count($tableVendors) > 0) {

            foreach ($tableVendors as $table) {
                $tableName = $table;
                $modelClass = '\\App\\Models\\' . HelpersApp::getClassName($tableName);

                // delete item draft
                DB::table((new $modelClass)->getTable())->where('tender_number', $tender->tender_number)
                    ->where('status', TenderSubmissionEnum::STATUS_ITEM[1])
                    ->where('vendor_id', $params['vendor_id'])
                    ->where('submission_method', $WORKFLOW_MAPPING_TYPE['negotiation_commercial'])
                    ->delete();

                $model = DB::table((new $modelClass)->getTable())->where('tender_number', $tender->tender_number)
                    ->where('status', TenderSubmissionEnum::STATUS_ITEM[2])
                    ->where('vendor_id', $params['vendor_id'])
                    ->where('submission_method', $WORKFLOW_MAPPING_TYPE['negotiation_commercial'])
                    ->whereNull('deleted_at');

                if ($model->count() > 0) {
                    // update status submit change to submit new
                    $model->update(['action_status' => TenderStatusEnum::ACT_NEW]);
                } else {
                    $this->reCopyDataCommercial($params, $tender, $table);
                }
            }
        }
    }

    private function reCopyDataCommercial($params, $tender, $table)
    {
        $WORKFLOW_MAPPING_TYPE = TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE;

        if ($table == "tender_header_commercial") {
            $this->copyTenderHeaderCommercial($params, $tender);
        }
        if ($table == "tender_item_commercial") {
            $this->copyTenderItemCommercial($params, $tender, false);
        }
        if ($table == "tender_vendor_tax_code") {
            $this->copyVendorTaxCodes($tender, $params, $WORKFLOW_MAPPING_TYPE['negotiation_commercial']);
        }
        if ($table == "tender_vendor_additional_cost") {
            $this->copyVendorAdditionalCost($tender, $params, $WORKFLOW_MAPPING_TYPE['negotiation_commercial']);
        }

        if ($table == "tender_vendor_submission_detail") {
            $details = TenderVendorSubmissionDetail::where('tender_number', $tender->tender_number)
                ->where('vendor_id', $params['vendor_id'])
                ->whereNull('deleted_at')
                ->where('submission_method', $WORKFLOW_MAPPING_TYPE['process_commercial_evaluation'])
                ->get();

            if ($details->count() > 0) {
                foreach ($details as $detail) {
                    TenderVendorSubmissionDetail::create([
                        'tender_number' => $detail->tender_number,
                        'vendor_id' => $detail->vendor_id,
                        'vendor_code' => $detail->vendor_code,
                        'bidding_document_id' => $detail->bidding_document_id,
                        'submission_method' => $WORKFLOW_MAPPING_TYPE['negotiation_commercial'],
                        'attachment' => $detail->attachment,
                        'status' => TenderVendorSubmissionDetail::STATUS[1],
                        'order' => $detail->order,
                    ]);
                }
            }
        }
    }

    private function updateDataAwarding($tender)
    {
        $awaringItems = TenderVendorAwarding::where("tender_number", $tender->tender_number)
            ->whereNull("deleted_at")
            ->get();

        if ($awaringItems->count() > 0) {
            foreach ($awaringItems as $awaringItem) {
                $this->updateDataHeaderCommercialAwarding($tender, $awaringItem->vendor_id);
                $this->updateDataItemCommercialAwarding($tender, $awaringItem->vendor_id);
            }
        }
    }

    private function updateDataHeaderCommercialAwarding($tender, $vendorId)
    {
        try {
            $WORKFLOW_MAPPING_TYPE = TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE;

            $dataHeaders = TenderHeaderCommercial::where('tender_number', $tender->tender_number)
                ->where('vendor_id', $vendorId)
                ->where('status', '!=', TenderSubmissionEnum::STATUS_ITEM[1])
                ->whereNull('deleted_at')
                ->where('submission_method', $WORKFLOW_MAPPING_TYPE['negotiation_commercial'])
                ->get();

            if ($dataHeaders->count() > 0) {
                foreach ($dataHeaders as $tenderHeader) {

                    // Log::info($this->logName . '::carbon test quotation date  : ' . Carbon::parse($tenderHeader->quotation_date)->setTimeZone(config('timezone'))->format('d.m.Y H:i'));
                    Log::info($this->logName . '::check get quotation date  : ' . $tenderHeader->quotation_date);

                    $quotation_date = null;
                    $bid_bond_end_date = null;

                    if ($tenderHeader->quotation_date && !empty($tenderHeader->quotation_date)) {
                        $quotation_date = Carbon::parse($tenderHeader->quotation_date)->setTimeZone(config('timezone'))->format('Y.m.d H:i');
                    }

                    if ($tenderHeader->bid_bond_end_date && !empty($tenderHeader->bid_bond_end_date)) {
                        $bid_bond_end_date = Carbon::parse($tenderHeader->quotation_date)->setTimeZone(config('timezone'))->format('Y.m.d H:i');
                    }

                    TenderHeaderCommercialAwarding::where([
                        ["tender_number", "=", $tenderHeader->tender_number],
                        ["vendor_id", "=", $tenderHeader->vendor_id]
                    ])->whereNull("deleted_at")
                        ->update([
                            'quotation_number' => $tenderHeader->quotation_number,
                            'quotation_date' => $quotation_date,
                            'quotation_note' => $tenderHeader->quotation_note,
                            'quotation_file' => $tenderHeader->quotation_file,
                            'incoterm' => $tenderHeader->incoterm,
                            'incoterm_location' => $tenderHeader->incoterm_location,
                            'bid_bond_value' => $tenderHeader->bid_bond_value,
                            'bid_bond_file' => $tenderHeader->bid_bond_file,
                            'bid_bond_end_date' => $bid_bond_end_date,
                            'currency_code' => $tenderHeader->currency_code,
                            'submission_method' => $tenderHeader->submission_method
                        ]);
                }
            }
        } catch (Exception $e) {
            Log::error($this->logName . '::updateDataHeaderCommercialAwarding error : ' . $e->getMessage());
            throw $e;
        }
    }

    private function updateDataItemCommercialAwarding($tender, $vendorId)
    {
        try {
            $WORKFLOW_MAPPING_TYPE = TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE;
            $stageType = $WORKFLOW_MAPPING_TYPE['negotiation_commercial'];

            $tenderItems = TenderItemCommercial::where('tender_number', $tender->tender_number)
                ->where('vendor_id', $vendorId)
                ->where('status', '!=', TenderSubmissionEnum::STATUS_ITEM[1])
                ->whereNull('deleted_at')
                ->where('submission_method', $stageType)
                ->get();

            if ($tenderItems->count()) {
                foreach ($tenderItems as $item) {
                    if (empty($item->item_id)) {
                        throw new Exception("data tender items commercial not valid.");
                    }

                    TenderItemCommercialAwarding::where([
                        ['tender_number', "=", $item->tender_number],
                        ['vendor_id', "=", $item->vendor_id],
                        ['item_id', "=", $item->item_id]
                    ])->whereNull('deleted_at')
                        ->update([
                            'est_unit_price' => $item->est_unit_price,
                            'price_unit' => $item->price_unit,
                            'subtotal' => $item->subtotal,
                            'currency_code' => $item->currency_code,
                            'compliance' => $item->compliance,
                            'overall_limit' => $item->overall_limit,
                            'submission_method' => $item->submission_method
                        ]);

                    $this->updateVendorTaxCodesAwarding($tender, $stageType, $item);
                    if ($tender->conditional_type == "CT2") {
                        $this->updateVendorAdditionalCostAwarding($tender, $stageType, $vendorId, $item);
                    }
                }
            }

            if ($tender->conditional_type == "CT1") {
                $this->updateVendorAdditionalCostAwarding($tender, $stageType, $vendorId);
            }
        } catch (Exception $e) {
            Log::error($this->logName . '::updateDataItemCommercialAwarding error : ' . $e->getMessage());
            throw $e;
        }
    }

    private function updateVendorTaxCodesAwarding($tender, $stageType, $item)
    {
        try {
            $itemTextCodes = TenderVendorTaxCode::where('tender_number', $tender->tender_number)
                ->where('vendor_id', $item->vendor_id)
                ->where('status', '!=', TenderSubmissionEnum::STATUS_ITEM[1])
                ->whereNull('deleted_at')
                ->where('submission_method', $stageType)
                ->get();

            $itemDel = TenderVendorTaxCodeAwarding::where('tender_number', $tender->tender_number)
                ->where('vendor_id', $item->vendor_id)
                ->whereNull('deleted_at')
                ->where('submission_method', $stageType);

            if ($item) {
                $itemTextCodes = $itemTextCodes->where('item_id', $item->item_id);
                $itemDel = $itemDel->where('item_id', $item->item_id);
            }

            $itemDel->forceDelete();

            // update data;
            $data = [];
            if ($itemTextCodes->count() > 0) {
                foreach ($itemTextCodes as $item) {
                    $data = [
                        'tender_number' => $tender->tender_number,
                        'vendor_id' => $item->vendor_id,
                        'vendor_code' => $item->vendor_code,
                        'item_id' => $item->item_id,
                        'line_id' => $item->line_id,
                        'tax_code' => $item->tax_code,
                        'description' => $item->description,
                        'submission_method' => $stageType
                    ];

                    TenderVendorTaxCodeAwarding::create($data);
                }
            }

            return true;
        } catch (Exception $e) {
            Log::error($this->logName . '::updateVendorTaxCodesAwarding error : ' . $e->getMessage());
            throw $e;
        }
    }

    private function updateVendorAdditionalCostAwarding($tender, $stageType, $vendorId, $item = null)
    {
        try {
            $itemAdditionalCosts = TenderVendorAdditionalCost::where('tender_number', $tender->tender_number)
                ->where('vendor_id', $vendorId)
                ->where('status', '!=', TenderSubmissionEnum::STATUS_ITEM[1])
                ->whereNull('deleted_at')
                ->where('submission_method', $stageType)
                ->get();

            $itemDel = TenderVendorAdditionalCostAwarding::where('tender_number', $tender->tender_number)
                ->where('vendor_id', $vendorId)
                ->whereNull('deleted_at')
                ->where('submission_method', $stageType);

            if ($item) {
                $itemAdditionalCosts = $itemAdditionalCosts->where("item_id", $item->item_id);

                $itemDel = $itemDel->where("item_id", $item->item_id);
            }

            $itemDel->forceDelete();

            // update data;
            $data = [];
            if ($itemAdditionalCosts->count() > 0) {
                foreach ($itemAdditionalCosts as $item) {
                    $data = [
                        'tender_number' => $tender->tender_number,
                        'vendor_id' => $item->vendor_id,
                        'vendor_code' => $item->vendor_code,
                        'item_id' => $item->item_id,
                        'line_id' => $item->line_id,
                        'conditional_code' => $item->conditional_code,
                        'conditional_name' => $item->conditional_name,
                        'percentage' => $item->percentage,
                        'value' => $item->value,
                        'calculation_pos' => $item->calculation_pos,
                        'conditional_type' => $item->conditional_type,
                        'submission_method' => $stageType
                    ];

                    TenderVendorAdditionalCostAwarding::create($data);
                }
            }

            return true;
        } catch (Exception $e) {
            Log::error($this->logName . '::updateVendorAdditionalCostAwarding error : ' . $e->getMessage());
            throw $e;
        }
    }
}
