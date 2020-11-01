<?php

namespace App\Repositories;

use App\Enums\TenderSubmissionEnum;
use App\Models\PoTenderItemText;
use App\Models\TenderAwardingAttachment;
use App\Models\TenderHeaderCommercial;
use App\Models\TenderHeaderCommercialAwarding;
use App\Models\TenderHeaderTechnical;
use App\Models\TenderHeaderTechnicalAwarding;
use App\Models\TenderItemCommercial;
use App\Models\TenderItemCommercialAwarding;
use App\Models\TenderItemTechnical;
use App\Models\TenderItemTechnicalAwarding;
use App\Models\TenderVendorAdditionalCost;
use App\Models\TenderVendorAdditionalCostAwarding;
use App\Models\TenderVendorAwarding;
use App\Models\TenderVendorItemText;
use App\Models\TenderVendorItemTextAwarding;
use App\Models\TenderVendorSubmission;
use App\Models\TenderVendorTaxCode;
use App\Models\TenderVendorTaxCodeAwarding;
use App\Models\SapPRListServices;
use App\Models\TenderReference;
use App\PoItemDetailServices;
use App\PoTenderItem;
use App\PurchaseOrder;
use App\RefListOption;
use App\RefPurchaseOrg;
use App\Services\TenderMailService;
use App\TenderItem;
use App\TenderWorkflow;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class TenderProcessAwardingRepository extends TenderProcessRepository
{

    protected $logName = 'TenderProcessAwardingRepository';

    public function __construct()
    {
    }

    /**
     * find data
     *
     * @param App\TenderParameter $tender, tender number
     * @param string $params, request parameter
     *
     * @return \Yajra\DataTables\DataTables $dataTable
     *
     * @throws \Exception
     */
    public function findItemAwarding($tender, $params = null)
    {
        $submissionScore = $this->findVendorSubmissionScorePassed($tender->tender_number, 3, 4);
        $dataScore = $submissionScore->toArray();
        $scoreTech = $submissionScore->pluck('score_tc', 'vendor_id')->toArray();
        $scoreComm = $submissionScore->pluck('score_com', 'vendor_id')->toArray();
        $dataNego = $this->findVendorSubmissionScorePassed($tender->tender_number, 4, 6);
        $dataNegoDate = $dataNego->pluck('submission_date', 'vendor_id')->toArray();
        $submissionMethod = RefListOption::where('type', 'submission_method_options')->where('deleteflg', false)->pluck('value', 'key');
        $dataStatusAwarding = $this->findVendorAwarding($tender->tender_number)
            ->pluck('awarding_status', 'vendor_id')->toArray();
        $dataVendorStatusAwarding = $this->findVendorAwarding($tender->tender_number)
            ->pluck('status', 'vendor_id')->toArray();
        $dataVendorPO = $this->findVendorAwarding($tender->tender_number, true, "only_has_po")
            ->pluck('po_number', 'vendor_id')->toArray();
        $dataVendorPOSAP = $this->findVendorAwarding($tender->tender_number, true, "only_has_po")
            ->pluck('sap_po_number', 'vendor_id')->toArray();

        $dataAwarding = null;
        if (isset($params['actionView']) && $params['actionView'] == 'awarding_result') {
            $vendor = Auth::user()->vendor;
            if ($vendor) {
                $dataAwarding = $this->findVendorAwardingByVendor($tender->tender_number, $vendor->id);
            } else {
                $dataAwarding = $this->findVendorAwarding($tender->tender_number)->toArray();
            }
        }
        $data = (array)$dataScore;

        if (isset($dataAwarding)) {
            $data = $dataAwarding;
        }

        $output = [];
        foreach ($data as $row) {
            $row = (array)$row;

            if ($row["status"] == TenderVendorSubmission::STATUS[3] || isset($row['awarding_status'])) {

                $row['IsNegotiation'] = false;
                $row['HasAwarding'] = false;
                $row['po_number'] = "";
                $row['sap_po_number'] = "";
                $row['action_awarding_status'] = null;
                $row['action_details_awarding'] = null;
                $row['tender_submission_method'] = $submissionMethod[$tender->submission_method];

                if (isset($dataAwarding)) {
                    $row['score_tc'] = $scoreTech[$row['vendor_id']];
                    $row['score_com'] = $scoreComm[$row['vendor_id']];
                }

                if (count($dataNegoDate) > 0) {
                    if (!empty($dataNegoDate[$row['vendor_id']])) {
                        $row['IsNegotiation'] = true;
                        $row['submission_date'] = $dataNegoDate[$row['vendor_id']];
                    }
                }

                if (count($dataVendorStatusAwarding) > 0) {
                    if (isset($dataVendorStatusAwarding[$row['vendor_id']])) {
                        $row['status'] = $dataVendorStatusAwarding[$row['vendor_id']];
                    }
                }

                if (count($dataStatusAwarding) > 0) {
                    if (isset($dataStatusAwarding[$row['vendor_id']])) {
                        $row['HasAwarding'] = true;
                        $row['action_awarding_status'] = $dataStatusAwarding[$row['vendor_id']];
                        $row['action_details_awarding'] = $dataStatusAwarding[$row['vendor_id']];
                    }
                }

                if (count($dataVendorPO) > 0) {
                    if (isset($dataVendorPO[$row['vendor_id']])) {
                        $row['po_number'] = $dataVendorPO[$row['vendor_id']];
                        $row['sap_po_number'] = $dataVendorPOSAP[$row['vendor_id']];
                    }
                }

                $output[] = $row;
            }
        }

        return DataTables::of($output)
            ->addColumn('status_text', function ($row) {
                if ($row['IsNegotiation'] == true || $row['HasAwarding'] == true)
                    return __('tender.process_status.' . $row['status']);
                else
                    return __('tender.process_status.' . $row['status']) . " " . __('tender.' . $row["tender_submission_method"]);
            })
            ->addColumn('awarding_status_text', function ($row) {
                if (isset($row['awarding_status'])) {
                    return __('tender.process_status.' . $row['awarding_status']);
                }

                return "";
            })
            ->make(true);
    }

    /**
     * find data vendor tender header
     *
     * @param string $number
     * @param int $vendorId
     * @param string $stageType
     *
     * @return \Illuminate\Database\Eloquent\Builder $builder
     */
    public function findTenderAwardingHeader($number, $vendorId, $stageType)
    {
        try {
            $query = null;
            $query = TenderHeaderCommercialAwarding::where('tender_header_commercial_awarding.tender_number', $number)
                ->leftJoin('tender_header_technical_awarding', function ($join) {
                    $join->on(
                        "tender_header_commercial_awarding.tender_number",
                        "=",
                        "tender_header_technical_awarding.tender_number"
                    )
                        ->on(
                            "tender_header_commercial_awarding.vendor_id",
                            "=",
                            "tender_header_technical_awarding.vendor_id"
                        );
                })
                ->where('tender_header_commercial_awarding.vendor_id', $vendorId)
                ->select(
                    "tender_header_commercial_awarding.*",
                    "tender_header_technical_awarding.tkdn_percentage",
                    "tender_header_technical_awarding.tkdn_file",
                );

            return $query;
        } catch (Exception $e) {
            Log::error($this->logName . '::findTenderAwardingHeader error : ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * find data vendor tender header
     *
     * @param string $number
     * @param int $vendorId
     * @param string $stageType
     *
     * @return \Illuminate\Database\Eloquent\Builder $builder
     */
    public function findTenderHeader($number, $vendorId, $stageType)
    {
        try {
            $WORKFLOW_MAPPING_TYPE = TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE;

            $query = null;

            $query = TenderHeaderCommercial::where('tender_number', $number)
                ->where('submission_method', $WORKFLOW_MAPPING_TYPE["negotiation_commercial"])
                ->where('vendor_id', $vendorId);

            if ($query->count() <= 0) {
                $query = TenderHeaderCommercial::where('tender_number', $number)
                    ->where('submission_method', $WORKFLOW_MAPPING_TYPE["process_commercial_evaluation"])
                    ->where('vendor_id', $vendorId);
            }

            return $query;
        } catch (Exception $e) {
            Log::error($this->logName . '::findTenderHeader error : ' . $e->getMessage());
            throw $e;
        }
    }

    public function saveAwarding($params, $tender, $type)
    {
        $stageType = $params["stage_type"];

        if (isset($params["vendor_id"])) {
            $stageType = $this->getStageType($tender->tender_number, $params["vendor_id"]);
        }

        switch ($params['action_type']) {
            case "set-awarding":
                return $this->saveSetAwarding($params, $tender, $stageType);
                break;
            case "save-tender-items":
                return $this->saveItemsAwarding($params, $tender, $stageType);
                break;
            case "submit":
                return $this->submitAwarding($params, $tender, $stageType, $type);
                break;
            case "submit_po":
                return $this->submitAwardingPO($params, $tender, $stageType, $type);
                break;
            case "resubmit":
                return $this->reSubmitAwarding($params, $tender, $stageType, $type);
                break;
            case "upload-awarding-attachment":
                return $this->uploadAttachment($params, $tender, $stageType);
                break;
            case "delete-awarding-attachment":
                return $this->deleteAttachment($params, $tender, $stageType);
                break;
            case "save-tdkn-percentage":
                return $this->saveHeaderTechnical($params, $tender, $stageType);
                break;
            case "upload-tkdn-file":
                return $this->saveHeaderTechnical($params, $tender, $stageType);
                break;
        }
    }

    private function saveSetAwarding($params, $tender, $stageType)
    {
        try {
            DB::beginTransaction();

            $model = TenderVendorAwarding::where("tender_number", $tender->tender_number)
                ->where("vendor_id", $params["vendor_id"])->first();

            if ($model) {
                // if ($model->status == TenderSubmissionEnum::FLOW_STATUS[3]) {
                //     $model->status = TenderVendorSubmission::STATUS[2];
                // }

                $model->awarding_status = $params["awarding_status"];
                $model->save();
            } else {
                TenderVendorAwarding::updateOrCreate([
                    'tender_number' => $tender->tender_number,
                    'vendor_id' => $params["vendor_id"],
                ], [
                    'tender_number' => $tender->tender_number,
                    'vendor_id' => $params["vendor_id"],
                    'vendor_code' => $params["vendor_code"],
                    'status' => TenderVendorSubmission::STATUS[0],
                    'awarding_status' => $params["awarding_status"],
                ]);
            }

            // if set awarding to win then create data header
            if ($params["awarding_status"] == TenderVendorAwarding::STATUS[1]) {
                $this->saveTenderHeaderCommercial($params, $tender, $stageType);
                $this->saveTenderHeaderTechnical($params, $tender);

                TenderAwardingAttachment::updateOrCreate([
                    ["tender_number", "=", $tender->tender_number],
                    ["vendor_id", "=", $params["vendor_id"]],
                ], [
                    'tender_number' => $tender->tender_number,
                    'vendor_id' => $params["vendor_id"],
                    'vendor_code' => $params["vendor_code"],
                    'description' => "PO Document",
                    'status' => TenderVendorSubmission::STATUS[0],
                ]);
            }

            // if set awarding to lose then delete all data
            if ($params["awarding_status"] == TenderVendorAwarding::STATUS[2]) {
                TenderHeaderCommercialAwarding::where('tender_number', $tender->tender_number)
                    ->where('vendor_id', $params['vendor_id'])
                    ->forceDelete();

                TenderHeaderTechnicalAwarding::where('tender_number', $tender->tender_number)
                    ->where('vendor_id', $params['vendor_id'])
                    ->forceDelete();

                TenderAwardingAttachment::where('tender_number', $tender->tender_number)
                    ->where('vendor_id', $params['vendor_id'])
                    ->forceDelete();

                $this->deleteAllItems($params, $tender, $stageType);
            }

            DB::commit();
            return [
                'data' => $tender,
                'next' => null,
            ];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($this->logName . '::saveSetAwarding error : ' . $e->getMessage());
            throw $e;
        }
    }

    private function saveItemsAwarding($params, $tender, $stageType)
    {
        try {
            DB::beginTransaction();

            $this->deleteAllItems($params, $tender, $stageType);

            $this->saveTenderItemCommercial($params, $tender, $stageType);
            $this->saveTenderItemTechnical($params, $tender);

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

    private function submitAwarding($params, $tender, $stageType, $pageType)
    {
        try {
            DB::beginTransaction();

            // $this->deleteAllStatusRequestReSubmit($tender);
            $this->updateSubmitAll($tender, TenderVendorSubmission::STATUS[1]);

            if (isset($params["items"])) {

                foreach ($params["items"] as $item) {

                    TenderVendorAwarding::updateOrCreate([
                        'tender_number' => $tender->tender_number,
                        'vendor_id' => $item["vendor_id"],
                    ], [
                        'tender_number' => $tender->tender_number,
                        'vendor_id' => $item["vendor_id"],
                        'vendor_code' => $item["vendor_code"],
                        'status' => TenderVendorSubmission::STATUS[1]
                    ]);
                }
            }

            TenderReference::updateOrCreate([
                'tender_number' => $tender->tender_number,
                'ref_type' => $params['action_type'],
                'submission_method' => $stageType,
            ],[
                'tender_number' => $tender->tender_number,
                'ref_type' => $params['action_type'],
                'ref_value' => Carbon::now(),
                'submission_method' => $stageType,
            ]);

            // $this->finishStage($params, $tender, $stageType);
            // $this->completeEvaluation($tender, $params, $pageType);

            // $this->createPO($tender);

            DB::commit();
            (new TenderMailService)->sendEmailOnTenderAwardingSubmitted($tender, $stageType, $pageType);

            return [
                'data' => $tender,
                'next' => null,
            ];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($this->logName . '::submitAwarding error : ' . $e->getMessage());
            throw $e;
        }
    }

    private function submitAwardingPO($params, $tender, $stageType, $pageType)
    {
        try {
            DB::beginTransaction();

            $this->updateSubmitAll($tender, TenderVendorSubmission::STATUS[1]);

            $this->finishStage($params, $tender, $stageType);
            $this->completeEvaluation($tender, $params, $pageType);

            if (isset($params["items"])) {

                foreach ($params["items"] as $item) {

                    TenderVendorAwarding::updateOrCreate([
                        'tender_number' => $tender->tender_number,
                        'vendor_id' => $item["vendor_id"],
                    ], [
                        'tender_number' => $tender->tender_number,
                        'vendor_id' => $item["vendor_id"],
                        'vendor_code' => $item["vendor_code"],
                        'status' => TenderVendorSubmission::STATUS[1]
                    ]);
                }
            }

            $this->createPO($tender);
            $result = null;
            try {
                $result = (new PoRepository)->sapSend($tender->tender_number, "");
            } catch (Exception $e) {
                Log::error($this->logName . '::submitAwardingPO => PoRepository::sapSend error : ' . $e->getMessage());
            }

            TenderReference::updateOrCreate([
                'tender_number' => $tender->tender_number,
                'ref_type' => $params['action_type'],
                'submission_method' => $stageType,
            ],[
                'tender_number' => $tender->tender_number,
                'ref_type' => $params['action_type'],
                'ref_value' => Carbon::now(),
                'submission_method' => $stageType,
            ]);

            DB::commit();

            return [
                'data' => $tender,
                'sap_result' => $result,
                'next' => null,
            ];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($this->logName . '::submitAwarding error : ' . $e->getMessage());
            throw $e;
        }
    }

    private function reSubmitAwarding($params, $tender, $stageType, $pageType)
    {
        try {
            DB::beginTransaction();

            // $items = $this->findVendorAwarding($tender->tender_number, false, "only_not_has_po");
            // foreach ($items as $item) {
            //     $this->updateSubmitAllByVendor($tender, $item->vendor_id, TenderSubmissionEnum::FLOW_STATUS[3]);
            // }

            $this->updateSubmitAll($tender, TenderSubmissionEnum::FLOW_STATUS[3]);

            TenderReference::updateOrCreate([
                'tender_number' => $tender->tender_number,
                'ref_type' => $params['action_type'],
                'submission_method' => $stageType,
            ],[
                'tender_number' => $tender->tender_number,
                'ref_type' => $params['action_type'],
                'ref_value' => Carbon::now(),
                'submission_method' => $stageType,
            ]);

            DB::commit();

            (new TenderMailService)->sendEmailOnTenderAwardingResubmitted($tender, $stageType);

            return [
                'data' => $tender,
                'next' => null,
            ];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($this->logName . '::finishAwarding error : ' . $e->getMessage());
            throw $e;
        }
    }

    private function updateSubmitAll($tender, $status)
    {
        TenderVendorAwarding::where('tender_number', $tender->tender_number)
            ->update([
                "status" => $status
            ]);

        TenderHeaderCommercialAwarding::where('tender_number', $tender->tender_number)
            ->update([
                "status" => $status
            ]);

        TenderHeaderTechnicalAwarding::where('tender_number', $tender->tender_number)
            ->update([
                "status" => $status
            ]);

        TenderItemCommercialAwarding::where('tender_number', $tender->tender_number)
            ->update([
                "status" => $status
            ]);

        TenderItemTechnicalAwarding::where('tender_number', $tender->tender_number)
            ->update([
                "status" => $status
            ]);

        TenderVendorTaxCodeAwarding::where('tender_number', $tender->tender_number)
            ->update([
                "status" => $status
            ]);

        TenderVendorAdditionalCostAwarding::where('tender_number', $tender->tender_number)
            ->update([
                "status" => $status
            ]);

        TenderVendorItemTextAwarding::where('tender_number', $tender->tender_number)
            ->update([
                "status" => $status
            ]);
    }

    private function updateSubmitAllByVendor($tender, $vendorId, $status)
    {
        TenderVendorAwarding::where([
            ['tender_number', "=", $tender->tender_number],
            ['vendor_id', "=", $vendorId],
        ])->update([
            "status" => $status
        ]);

        TenderHeaderCommercialAwarding::where([
            ['tender_number', "=", $tender->tender_number],
            ['vendor_id', "=", $vendorId],
        ])->update([
            "status" => $status
        ]);

        TenderHeaderTechnicalAwarding::where([
            ['tender_number', "=", $tender->tender_number],
            ['vendor_id', "=", $vendorId],
        ])->update([
            "status" => $status
        ]);

        TenderItemCommercialAwarding::where([
            ['tender_number', "=", $tender->tender_number],
            ['vendor_id', "=", $vendorId],
        ])->update([
            "status" => $status
        ]);

        TenderItemTechnicalAwarding::where([
            ['tender_number', "=", $tender->tender_number],
            ['vendor_id', "=", $vendorId],
        ])->update([
            "status" => $status
        ]);

        TenderVendorTaxCodeAwarding::where([
            ['tender_number', "=", $tender->tender_number],
            ['vendor_id', "=", $vendorId],
        ])
            ->update([
                "status" => $status
            ]);

        TenderVendorAdditionalCostAwarding::where([
            ['tender_number', "=", $tender->tender_number],
            ['vendor_id', "=", $vendorId],
        ])
            ->update([
                "status" => $status
            ]);

        TenderVendorItemTextAwarding::where([
            ['tender_number', "=", $tender->tender_number],
            ['vendor_id', "=", $vendorId],
        ])
            ->update([
                "status" => $status
            ]);
    }

    private function deleteAllStatusRequestReSubmit($tender)
    {
        $status = TenderSubmissionEnum::FLOW_STATUS[3];

        TenderVendorAwarding::where('tender_number', $tender->tender_number)
            ->where('status', $status)
            ->delete();

        TenderHeaderCommercialAwarding::where('tender_number', $tender->tender_number)
            ->where('status', $status)
            ->delete();

        TenderHeaderTechnicalAwarding::where('tender_number', $tender->tender_number)
            ->where('status', $status)
            ->delete();

        TenderItemCommercialAwarding::where('tender_number', $tender->tender_number)
            ->where('status', $status)
            ->delete();

        TenderItemTechnicalAwarding::where('tender_number', $tender->tender_number)
            ->where('status', $status)
            ->delete();

        TenderVendorTaxCodeAwarding::where('tender_number', $tender->tender_number)
            ->where('status', $status)
            ->forceDelete();

        TenderVendorAdditionalCostAwarding::where('tender_number', $tender->tender_number)
            ->where('status', $status)
            ->delete();

        TenderVendorItemTextAwarding::where('tender_number', $tender->tender_number)
            ->where('status', $status)
            ->delete();
    }

    private function deleteAllItems($params, $tender, $stageType)
    {
        TenderItemCommercialAwarding::where('tender_number', $tender->tender_number)
            ->where('vendor_id', $params['vendor_id'])
            ->forceDelete();

        TenderItemTechnicalAwarding::where('tender_number', $tender->tender_number)
            ->where('vendor_id', $params['vendor_id'])
            ->forceDelete();

        TenderVendorTaxCodeAwarding::where('tender_number', $tender->tender_number)
            ->where('vendor_id', $params['vendor_id'])
            ->forceDelete();

        TenderVendorAdditionalCostAwarding::where('tender_number', $tender->tender_number)
            ->where('vendor_id', $params['vendor_id'])
            ->forceDelete();

        TenderVendorItemTextAwarding::where('tender_number', $tender->tender_number)
            ->where('vendor_id', $params['vendor_id'])
            ->forceDelete();
    }

    private function saveTenderHeaderCommercial($params, $tender, $stageType)
    {
        $tenderHeader = TenderHeaderCommercial::where('tender_number', $tender->tender_number)
            ->where('vendor_id', $params['vendor_id'])
            ->where('status', '!=', TenderSubmissionEnum::STATUS_ITEM[1])
            ->whereNull('deleted_at')
            ->where('submission_method', $stageType)
            ->first();

        if ($tenderHeader) {
            TenderHeaderCommercialAwarding::updateOrCreate([
                'tender_number' => $tender->tender_number,
                'vendor_id' => $params["vendor_id"],
            ], [
                'tender_number' => $tenderHeader->tender_number,
                'vendor_id' => $tenderHeader->vendor_id,
                'vendor_code' => $tenderHeader->vendor_code,
                'quotation_number' => $tenderHeader->quotation_number,
                'quotation_date' => $tenderHeader->quotation_date,
                'quotation_note' => $tenderHeader->quotation_note,
                'quotation_file' => $tenderHeader->quotation_file,
                'incoterm' => $tenderHeader->incoterm,
                'incoterm_location' => $tenderHeader->incoterm_location,
                'bid_bond_value' => $tenderHeader->bid_bond_value,
                'bid_bond_file' => $tenderHeader->bid_bond_file,
                'bid_bond_end_date' => $tenderHeader->bid_bond_end_date,
                'currency_code' => $tenderHeader->currency_code,
                'submission_method' => $tenderHeader->submission_method,
                'status' => TenderVendorSubmission::STATUS[0]
            ]);
        } else {
            Log::error($this->logName . '::copyTenderHeaderCommercial error : tender header commercial not found');
            throw new Exception("tender header commercial not found.");
        }
    }

    private function saveTenderItemCommercial($params, $tender, $stageType)
    {
        $itemIds = collect($params["items"])->pluck('item_id')->toArray();

        $tenderItems = TenderItemCommercial::where('tender_number', $tender->tender_number)
            ->where('vendor_id', $params['vendor_id'])
            ->where('status', '!=', TenderSubmissionEnum::STATUS_ITEM[1])
            ->whereNull('deleted_at')
            ->where('submission_method', $stageType)
            ->whereIn('item_id', $itemIds)
            ->get();

        if ($tenderItems->count()) {
            foreach ($tenderItems as $item) {
                if (empty($item->item_id)) {
                    throw new Exception("data tender items commercial not valid.");
                }

                TenderItemCommercialAwarding::updateOrCreate([
                    'tender_number' => $item->tender_number,
                    'vendor_id' => $item->vendor_id,
                    'item_id' => $item->item_id,
                ], [
                    'tender_number' => $tender->tender_number,
                    'vendor_id' => $item->vendor_id,
                    'vendor_code' => $item->vendor_code,
                    'item_id' => $item->item_id,
                    'est_unit_price' => $item->est_unit_price,
                    'price_unit' => $item->price_unit,
                    'subtotal' => $item->subtotal,
                    'currency_code' => $item->currency_code,
                    'compliance' => $item->compliance,
                    'overall_limit' => $item->overall_limit,
                    'submission_method' => $item->submission_method,
                    'status' => TenderVendorSubmission::STATUS[0]
                ]);

                $this->saveVendorTaxCodes($tender, $params, $stageType, $item);
                if ($tender->conditional_type == "CT2") {
                    $this->saveVendorAdditionalCost($tender, $params, $stageType, $item);
                }
            }

            if ($tender->conditional_type == "CT1") {
                $this->saveVendorAdditionalCost($tender, $params, $stageType);
            }
        } else {
            Log::info($this->logName . '::copyTenderItemCommercial error : tender items commercial not found');
            // throw new Exception("tender items commercial not found.");
        }
    }

    private function saveTenderHeaderTechnical($params, $tender)
    {
        $stageTypeTech = $this->getStageTypeTechnical($tender->tender_number, $params['vendor_id']);

        $tenderHeader = TenderHeaderTechnical::where('tender_number', $tender->tender_number)
            ->where('vendor_id', $params['vendor_id'])
            ->where('status', '!=', TenderSubmissionEnum::STATUS_ITEM[1])
            ->whereNull('deleted_at')
            ->where('submission_method', $stageTypeTech)
            ->first();

        if ($tenderHeader) {

            TenderHeaderTechnicalAwarding::updateOrCreate([
                'tender_number' => $tenderHeader->tender_number,
                'vendor_id' => $tenderHeader->vendor_id,
            ], [
                'tender_number' => $tenderHeader->tender_number,
                'vendor_id' => $tenderHeader->vendor_id,
                'vendor_code' => $tenderHeader->vendor_code,
                'status' => TenderVendorSubmission::STATUS[0],
                'quotation_number' => $tenderHeader->quotation_number,
                'quotation_date' => $tenderHeader->quotation_date,
                'tkdn_percentage' => $tenderHeader->tkdn_percentage,
                'quotation_file' => $tenderHeader->quotation_file,
                'tkdn_file' => $tenderHeader->tkdn_file,
                'proposed_item_file' => $tenderHeader->proposed_item_file,
                'submission_method' => $tenderHeader->submission_method,
                'deleted_at' => null,
            ]);
        } else {
            Log::info($this->logName . '::copyTenderHeaderTechnical error : tender header technical not found');
            // throw new Exception("tender header technical not found.");
        }
    }

    private function saveTenderItemTechnical($params, $tender)
    {
        $stageTypeTech = $this->getStageTypeTechnical($tender->tender_number, $params['vendor_id']);

        $itemIds = collect($params["items"])->pluck('item_id')->toArray();

        $tenderItems = TenderItemTechnical::where('tender_number', $tender->tender_number)
            ->where('vendor_id', $params['vendor_id'])
            ->where('status', '!=', TenderSubmissionEnum::STATUS_ITEM[1])
            ->whereNull('deleted_at')
            ->where('submission_method', $stageTypeTech)
            ->whereIn('item_id', $itemIds)
            ->get();

        if ($tenderItems->count()) {
            foreach ($tenderItems as $item) {

                if (empty($item->item_id)) {
                    throw new Exception("data tender items technical not valid.");
                }

                $tenderItemNego = new TenderItemTechnicalAwarding();
                $tenderItemNego->tender_number = $item->tender_number;
                $tenderItemNego->vendor_id = $item->vendor_id;
                $tenderItemNego->vendor_code = $item->vendor_code;
                $tenderItemNego->item_id = $item->item_id;
                $tenderItemNego->line_id = $item->line_id;
                $tenderItemNego->description = $item->description;
                $tenderItemNego->qty = $item->qty;
                $tenderItemNego->compliance = $item->compliance;
                $tenderItemNego->submission_method = $stageTypeTech;
                $tenderItemNego->save();

                $this->copyVendorItemText($tender, $params, $stageTypeTech, $item);
            }
        } else {
            Log::info($this->logName . '::copyTenderItemTechnical error : tender items technical not found');
            // throw new Exception("tender items technical not found.");
        }
    }

    private function copyVendorItemText($tender, $params, $stageType, $item)
    {
        try {
            $itemTexts = TenderVendorItemText::where('tender_number', $tender->tender_number)
                ->where('vendor_id', $params['vendor_id'])
                ->where('status', '!=', TenderSubmissionEnum::STATUS_ITEM[1])
                ->whereNull('deleted_at')
                ->where('submission_method', $stageType)
                ->get();

            if ($item) {
                $itemTexts = $itemTexts->where('item_id', $item->item_id);
            }

            // insert data;
            $data = [];
            if ($itemTexts->count() > 0) {
                foreach ($itemTexts as $item) {
                    $data = [
                        'tender_number' => $tender->tender_number,
                        'vendor_id' => $item->vendor_id,
                        'vendor_code' => $item->vendor_code,
                        'item_id' => $item->item_id,
                        'line_id' => $item->line_id,
                        'PREQ_NO' => $item->PREQ_NO,
                        'PREQ_ITEM' => $item->PREQ_ITEM,
                        'TEXT_ID' => $item->TEXT_ID,
                        'TEXT_ID_DESC' => $item->TEXT_ID_DESC,
                        'TEXT_FORM' => $item->TEXT_FORM,
                        'TEXT_LINE' => $item->TEXT_LINE,
                        'submission_method' => $stageType
                    ];

                    TenderVendorItemTextAwarding::create($data);
                }
            }

            return true;
        } catch (Exception $e) {
            Log::error($this->logName . '::copyVendorItemText error : ' . $e->getMessage());
            throw $e;
        }
    }

    private function saveVendorTaxCodes($tender, $params, $stageType, $item)
    {
        try {
            $itemTextCodes = TenderVendorTaxCode::where('tender_number', $tender->tender_number)
                ->where('vendor_id', $params['vendor_id'])
                ->where('status', '!=', TenderSubmissionEnum::STATUS_ITEM[1])
                ->whereNull('deleted_at')
                ->where('submission_method', $stageType)
                ->get();

            if ($item) {
                $itemTextCodes = $itemTextCodes->where('item_id', $item->item_id);
            }

            // insert data;
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
                        'submission_method' => $stageType,
                    ];

                    TenderVendorTaxCodeAwarding::create($data);
                }
            }

            return true;
        } catch (Exception $e) {
            Log::error($this->logName . '::copyVendorTaxCodes error : ' . $e->getMessage());
            throw $e;
        }
    }

    private function saveVendorAdditionalCost($tender, $params, $stageType, $item = null)
    {
        try {
            $itemAdditionalCosts = TenderVendorAdditionalCost::where('tender_number', $tender->tender_number)
                ->where('vendor_id', $params['vendor_id'])
                ->where('status', '!=', TenderSubmissionEnum::STATUS_ITEM[1])
                ->whereNull('deleted_at')
                ->where('submission_method', $stageType)
                ->get();

            if ($item) {
                $itemAdditionalCosts = $itemAdditionalCosts->where('item_id', $item->item_id);
            }

            // insert data;
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
                        'submission_method' => $stageType,
                    ];

                    TenderVendorAdditionalCostAwarding::create($data);
                }
            }

            return true;
        } catch (Exception $e) {
            Log::error($this->logName . '::copyVendorAdditionalCost error : ' . $e->getMessage());
            throw $e;
        }
    }

    public function getStageType($number, $vendorId)
    {
        try {
            $WORKFLOW_MAPPING_TYPE = TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE;

            $query = TenderHeaderCommercial::where('tender_number', $number)
                ->where('submission_method', $WORKFLOW_MAPPING_TYPE["negotiation_commercial"])
                ->where('vendor_id', $vendorId);

            if ($query->count() > 0) {
                return $WORKFLOW_MAPPING_TYPE["negotiation_commercial"];
            }

            return $WORKFLOW_MAPPING_TYPE["process_commercial_evaluation"];
        } catch (Exception $e) {
            Log::error($this->logName . '::getStageType error : ' . $e->getMessage());
            throw $e;
        }
    }

    public function getStageTypeTechnical($number, $vendorId)
    {
        try {
            $WORKFLOW_MAPPING_TYPE = TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE;

            $query = TenderItemCommercial::where('tender_number', $number)
                ->where('submission_method', $WORKFLOW_MAPPING_TYPE["negotiation_commercial"])
                ->where('vendor_id', $vendorId);

            if ($query->count() > 0) {
                return $WORKFLOW_MAPPING_TYPE["negotiation_technical"];
            }

            return $WORKFLOW_MAPPING_TYPE["process_technical_evaluation"];
        } catch (Exception $e) {
            Log::error($this->logName . '::getStageType error : ' . $e->getMessage());
            throw $e;
        }
    }

    public function findVendorAwarding($number, $onlyWinner = false, $filterPO = "")
    {
        try {
            $query = TenderVendorAwarding::select(
                'tender_vendors_awarding.vendor_id',
                'tender_vendors_awarding.tender_number',
                'tender_vendors_awarding.awarding_status',
                'vendors.vendor_code',
                'vendors.vendor_name',
                DB::raw('COALESCE(po_list.eproc_po_number, \'\') as po_number'),
                DB::raw('COALESCE(po_list.sap_po_number, \'\') as sap_po_number'),
                'po_list.sap_message',
                // DB::raw('MAX(submission_date) as submission_date'),
                DB::raw('MAX(tender_vendors_awarding.status) as status'),
                // DB::raw('SUM((CASE WHEN submission_method=3 THEN score ELSE 0 END)) as score_tc'),
                // DB::raw('SUM((CASE WHEN submission_method=4 THEN score ELSE 0 END)) as score_com')
            )
                // ->join('tender_vendor_submissions', function ($join) {
                //     $join->on("tender_vendors_awarding.tender_number", "=", "tender_vendor_submissions.tender_number")
                //         ->on("tender_vendors_awarding.vendor_id", "=", "tender_vendor_submissions.vendor_id");
                // })
                ->join('vendors', 'tender_vendors_awarding.vendor_id', 'vendors.id')
                ->leftJoin('po_list', function ($join) {
                    $join->on("tender_vendors_awarding.tender_number", "=", "po_list.tender_number")
                        ->on("tender_vendors_awarding.vendor_code", "=", "po_list.vendor_code")
                        ->whereNull("po_list.deleted_at");
                })
                ->where('tender_vendors_awarding.tender_number', $number)
                // ->where('tender_vendors_awarding.status', "!=", TenderSubmissionEnum::FLOW_STATUS[3])
                ->whereNull('vendors.deleted_at');
            // ->whereIn('tender_vendor_submissions.submission_method', [3, 4]);

            if ($onlyWinner) {
                $query = $query->where("tender_vendors_awarding.awarding_status", TenderVendorAwarding::STATUS[1]);
            }

            if ($filterPO == "only_has_po") {
                $query = $query->whereNotNull("po_list.eproc_po_number");
            }
            if ($filterPO == "only_not_has_po") {
                $query = $query->whereNull("po_list.eproc_po_number");
            }
            if ($filterPO == "only_has_sap_po") {
                $query = $query->whereNotNull("po_list.sap_po_number");
            }
            if ($filterPO == "only_not_has_sap_po") {
                $query = $query->whereNull("po_list.sap_po_number");
            }

            $query = $query->groupBy(
                'tender_vendors_awarding.vendor_id',
                'tender_vendors_awarding.tender_number',
                'tender_vendors_awarding.awarding_status',
                'vendors.vendor_code',
                'vendors.vendor_name',
                'po_list.eproc_po_number',
                'po_list.sap_po_number',
                'po_list.sap_message'
            );

            // $query = $query->havingRaw('MAX(submission_method)=?', [4])
            //     ->havingRaw('MAX(tender_vendor_submissions.status)=?', ['passed']);

            return $query->get();
        } catch (Exception $e) {
            Log::error($this->logName . '::findVendorAwarding error : ' . $e->getMessage());
            throw $e;
        }
    }

    public function findVendorAwardingByVendor($number, $vendorID)
    {
        try {
            return $this->findVendorAwarding($number)
                ->where("vendor_id", $vendorID)
                ->where("status", TenderSubmissionEnum::STATUS_ITEM[2])
                ->toArray();
        } catch (Exception $e) {
            Log::error($this->logName . '::findVendorAwardingByVendor error : ' . $e->getMessage());
            throw $e;
        }
    }

    public function findAwardingAttachment($number, $params)
    {
        try {
            $query = TenderAwardingAttachment::select("tender_awarding_attachment.*", DB::raw("0 as is_required"))
                ->where([
                    ['tender_number', '=', $number],
                    ['vendor_id', '=', $params["vendor_id"]],
                ]);

            return $query;
        } catch (Exception $e) {
            Log::error($this->logName . '::findAwwardingAttachment error : ' . $e->getMessage());
            throw $e;
        }
    }

    private function createPO($tender)
    {
        $items = $this->findVendorAwarding($tender->tender_number, true, "only_not_has_po");

        //$this->copyRefAssignPurchaseOrg($tender);
        foreach ($items as $item) {
            $draft = new PurchaseOrder([
                'tender_number' => $tender->tender_number,
                'vendor_code' => $item->vendor_code,
                'eproc_po_status' => TenderSubmissionEnum::STATUS_ITEM[1],
            ]);

            $draft->save();

            $prepend = config('eproc.po_number.prepend');
            $pad = config('eproc.po_number.pad');
            $draft->eproc_po_number = $prepend . str_pad($draft->id, $pad, '0', STR_PAD_LEFT);
            $draft->save();

            $this->copyPoHeader($tender, $draft, $item);

            $po_item = $this->copyItemsPO($tender, $draft->eproc_po_number, $item);

            $this->copyTenderHeaderCommercialPO($tender, $item->vendor_id, $draft->eproc_po_number);
            $this->copyTenderItemCommercialPO($tender, $item->vendor_id, $draft->eproc_po_number, $po_item);

            $this->copyTenderHeaderTechnicalPO($tender, $item->vendor_id, $draft->eproc_po_number);
            $this->copyTenderItemTechnicalPO($tender, $item->vendor_id, $draft->eproc_po_number, $po_item);
        }
    }

    private function copyPoHeader($tender, $poList,  $item)
    {
        $repo = new PoRepository();
        // $address = $repo->qryFindProfile("vendor_profile_generals", $item->vendor_id);
        // $this->copyAddress($address);
        $doc_type_data = DB::table("tender_document_type")->where("tender_number", $poList->tender_number)->where("vendor_code", $poList->vendor_code)->first();
        // $address = $repo->qryFindProfile("vendor_profile_generals", $item->vendor_id);
        $purchog = DB::table("ref_purchase_orgs")->where("id", $tender->purchase_org_id)->first();
        $ref_assign = DB::table("ref_assign_purchorg_compcode")->where("purchase_org_code", $purchog->org_code)->first();

        if (!isset($doc_type_data->document_type)) {
            throw new Exception("Please input document type for all supplier");
        }

        if (!isset($doc_type_data->document_date)) {
            throw new Exception("Please input document date for all supplier");
        }

        $data = array(
            'tender_number' => $poList->tender_number,
            'vendor_code' => $poList->vendor_code,
            'document_type' => $doc_type_data->document_type,
            'document_date' => $doc_type_data->document_date,
            'eproc_po_number' => $poList->eproc_po_number,
            // 'location_category' => $address->location_category,
            // 'vendor_profile_id' => $address->vendor_profile_id,
            // 'purchase_org_code' => $purchog->org_code,
            'assign_purchorg_company_code_id' => $ref_assign->id,
        );
        DB::table("po_header")->insert($data);
    }

    private function copyAddress($address)
    {
        //not yet in sap
    }

    private function copyTenderHeaderCommercialPO($tender, $vendor_id, $eproc_po_number)
    {
        $tenderHeaders = TenderHeaderCommercialAwarding::where('tender_number', $tender->tender_number)
            ->where('vendor_id', $vendor_id)
            ->where('status', '!=', TenderSubmissionEnum::STATUS_ITEM[1])
            ->whereNull('deleted_at')
            ->get();

        if ($tenderHeaders->count() > 0) {
            foreach ($tenderHeaders as $tenderHeader) {
                // dd($tenderHeader->quotation_date)
                $quotation_date = null;

                if ($tenderHeader->quotation_date && !empty($tenderHeader->quotation_date)) {
                    $quotation_date = Carbon::parse($tenderHeader->quotation_date)->setTimeZone(config('timezone'))->format('Y.m.d H:i');
                }
                $data = [
                    'tender_number' => $tenderHeader->tender_number,
                    'eproc_po_number' => $eproc_po_number,
                    'vendor_id' => $tenderHeader->vendor_id,
                    'vendor_code' => $tenderHeader->vendor_code,
                    'quotation_number' => $tenderHeader->quotation_number,
                    'quotation_date' => $quotation_date,
                    'quotation_note' => $tenderHeader->quotation_note,
                    'quotation_file' => $tenderHeader->quotation_file,
                    'incoterm' => $tenderHeader->incoterm,
                    'incoterm_location' => $tenderHeader->incoterm_location,
                    'bid_bond_value' => $tenderHeader->bid_bond_value,
                    'bid_bond_file' => $tenderHeader->bid_bond_file,
                    'bid_bond_end_date' => $tenderHeader->bid_bond_end_date,
                    'currency_code' => $tenderHeader->currency_code,
                    'submission_method' => $tenderHeader->submission_method,
                    'status' => TenderVendorSubmission::STATUS[0]
                ];

                DB::table("po_header_commercial_awarding")->insert($data);
            }
        }
    }

    private function copyTenderItemCommercialPO($tender, $vendor_id, $eproc_po_number, $po_item)
    {
        $tenderItems = TenderItemCommercialAwarding::where('tender_number', $tender->tender_number)
            ->where('vendor_id', $vendor_id)
            ->where('status', '!=', TenderSubmissionEnum::STATUS_ITEM[1])
            ->whereNull('deleted_at')
            ->get();

        if ($tenderItems->count()) {
            foreach ($tenderItems as $item) {
                $data = [
                    'tender_number' => $tender->tender_number,
                    'eproc_po_number' => $eproc_po_number,
                    'vendor_id' => $item->vendor_id,
                    'vendor_code' => $item->vendor_code,
                    'item_id' => $item->item_id,
                    'line_id' => $item->line_id,
                    'est_unit_price' => $item->est_unit_price,
                    'price_unit' => $item->price_unit,
                    'subtotal' => $item->subtotal,
                    'currency_code' => $item->currency_code,
                    'compliance' => $item->compliance,
                    'overall_limit' => $item->overall_limit,
                    'submission_method' => $item->submission_method,
                    'status' => TenderVendorSubmission::STATUS[0]
                ];

                DB::table("po_item_commercial_awarding")->insert($data);

                $this->copyVendorTaxCodesPO($tender, $vendor_id, $eproc_po_number, $po_item, $item);
                if ($tender->conditional_type == "CT2") {
                    $this->copyVendorAdditionalCostPO($tender, $vendor_id, $eproc_po_number, $po_item, $item);
                }
            }

            if ($tender->conditional_type == "CT1") {
                $this->copyVendorAdditionalCostPO($tender, $vendor_id, $eproc_po_number, $po_item);
            }
        }
    }

    private function copyTenderHeaderTechnicalPO($tender, $vendor_id, $eproc_po_number)
    {
        $tenderHeaders = TenderHeaderTechnicalAwarding::where('tender_number', $tender->tender_number)
            ->where('vendor_id', $vendor_id)
            ->where('status', '!=', TenderSubmissionEnum::STATUS_ITEM[1])
            ->whereNull('deleted_at')
            ->get();

        if ($tenderHeaders->count() > 0) {
            foreach ($tenderHeaders as $tenderHeader) {
                $quotation_date = null;
                $bid_bond_end_date = null;

                if ($tenderHeader->quotation_date && !empty($tenderHeader->quotation_date)) {
                    $quotation_date = Carbon::parse($tenderHeader->quotation_date)->setTimeZone(config('timezone'))->format('Y.m.d H:i');
                }
                $data = [
                    'tender_number' => $tenderHeader->tender_number,
                    'eproc_po_number' => $eproc_po_number,
                    'vendor_id' => $tenderHeader->vendor_id,
                    'vendor_code' => $tenderHeader->vendor_code,
                    'status' => TenderVendorSubmission::STATUS[0],
                    'quotation_number' => $tenderHeader->quotation_number,
                    'quotation_date' => $quotation_date,
                    'tkdn_percentage' => $tenderHeader->tkdn_percentage,
                    'quotation_file' => $tenderHeader->quotation_file,
                    'tkdn_file' => $tenderHeader->tkdn_file,
                    'proposed_item_file' => $tenderHeader->proposed_item_file,
                    'submission_method' => $tenderHeader->submission_method
                ];

                DB::table("po_header_technical_awarding")->insert($data);
            }
        }
    }

    private function copyTenderItemTechnicalPO($tender, $vendor_id, $eproc_po_number, $po_item)
    {
        $tenderItems = TenderItemTechnicalAwarding::where('tender_number', $tender->tender_number)
            ->where('vendor_id', $vendor_id)
            ->where('status', '!=', TenderSubmissionEnum::STATUS_ITEM[1])
            ->whereNull('deleted_at')
            ->get();

        if ($tenderItems->count()) {
            foreach ($tenderItems as $item) {
                $data = [
                    'tender_number' => $item->tender_number,
                    'eproc_po_number' => $eproc_po_number,
                    'vendor_id' => $item->vendor_id,
                    'vendor_code' => $item->vendor_code,
                    'item_id' => $item->item_id,
                    'line_id' => $item->line_id,
                    'description' => $item->description,
                    'qty' => $item->qty,
                    'compliance' => $item->compliance,
                    'submission_method' => $item->submission_method,
                    'status' => TenderVendorSubmission::STATUS[0]
                ];

                DB::table("po_item_technical_awarding")->insert($data);

                // $this->copyVendorItemTextPO($tender, $vendor_id, $eproc_po_number, $item);
            }
        }
    }

    /**
     * Copy sap prlist services to po item services
     *
     * @param \App\TenderParamater, $tender
     * @param \App\PoTenderItem, $item
     *
     */
    private function copyVendorItemTextPO($tender, $poItem)
    {
        try {
            $itemTexts = TenderVendorItemTextAwarding::where('tender_number', $tender->tender_number)
                ->where('vendor_id', $poItem->vendor_id)
                ->where('status', '!=', TenderSubmissionEnum::STATUS_ITEM[1])
                ->whereNull('deleted_at')
                ->where('item_id', $poItem->item_id)
                ->get();

            if ($itemTexts != null && $itemTexts->count() > 0) {
                // insert data;
                $data = [];
                foreach ($itemTexts as $item) {
                    $data = [
                        'tender_number' => $tender->tender_number,
                        'eproc_po_number' => $poItem->eproc_po_number,
                        'vendor_id' => $poItem->vendor_id,
                        'vendor_code' => $poItem->vendor_code,
                        'item_id' => $poItem->item_id,
                        'po_item' => $poItem->po_item,
                        'PREQ_NO' => $item->PREQ_NO,
                        'PREQ_ITEM' => $item->PREQ_ITEM,
                        'TEXT_ID' => $item->TEXT_ID,
                        'TEXT_ID_DESC' => $item->TEXT_ID_DESC,
                        'TEXT_FORM' => $item->TEXT_FORM,
                        'TEXT_LINE' => $item->TEXT_LINE,
                    ];
                    // DB::table("po_item_text")->insert($data);
                    PoTenderItemText::updateOrCreate($data);
                }
            }
            return true;
        } catch (Exception $e) {
            Log::error($this->logName . '::copyVendorItemTextPO error : ' . $e->getMessage());
            throw $e;
        }
    }

    private function copyVendorTaxCodesPO($tender, $vendor_id, $eproc_po_number, $po_item, $item)
    {
        try {
            $itemTextCodes = TenderVendorTaxCodeAwarding::where('tender_vendor_tax_codes_awarding.tender_number', $tender->tender_number)
                ->where('vendor_id', $vendor_id)
                ->where('status', '!=', TenderSubmissionEnum::STATUS_ITEM[1])
                ->whereNull('tender_vendor_tax_codes_awarding.deleted_at')
                ->select(
                    'tender_vendor_tax_codes_awarding.*',
                    'tender_items.number',
                    'tender_items.line_number',
                )
                ->leftJoin(
                    'tender_items',
                    'tender_vendor_tax_codes_awarding.item_id',
                    'tender_items.line_id'
                )
                ->get();

            if ($item) {
                $itemTextCodes = $itemTextCodes->where('item_id', $item->item_id);
            }

            // insert data;
            $data = [];
            if ($itemTextCodes->count() > 0) {
                foreach ($itemTextCodes as $item) {
                    $data = [
                        'tender_number' => $tender->tender_number,
                        'eproc_po_number' => $eproc_po_number,
                        'vendor_id' => $item->vendor_id,
                        'vendor_code' => $item->vendor_code,
                        'item_id' => $item->item_id,
                        'pr_number' => $item->number,
                        'line_id' => $item->line_id,
                        'pr_line_number' => $item->line_number,
                        'tax_code' => $item->tax_code,
                        'description' => $item->description,
                        'po_item' => $po_item,
                        // 'status' => TenderVendorSubmission::STATUS[0],
                        // 'submission_method' => $item->submission_method,
                    ];

                    DB::table("po_tax_codes")->insert($data);
                }
            }

            return true;
        } catch (Exception $e) {
            Log::error($this->logName . '::copyVendorTaxCodesPO error : ' . $e->getMessage());
            throw $e;
        }
    }

    private function copyVendorAdditionalCostPO($tender, $vendor_id, $eproc_po_number, $po_item, $item = null)
    {
        try {
            $itemAdditionalCosts = TenderVendorAdditionalCostAwarding::where('tender_vendor_additional_costs_awarding.tender_number', $tender->tender_number)
                ->where('vendor_id', $vendor_id)
                ->where('status', '!=', TenderSubmissionEnum::STATUS_ITEM[1])
                ->whereNull('tender_vendor_additional_costs_awarding.deleted_at')
                ->select(
                    'tender_vendor_additional_costs_awarding.*',
                    'tender_items.number',
                    'tender_items.line_number',
                )
                ->leftJoin(
                    'tender_items',
                    'tender_vendor_additional_costs_awarding.item_id',
                    'tender_items.line_id'
                )
                ->get();

            if ($item) {
                $itemAdditionalCosts = $itemAdditionalCosts->where('item_id', $item->item_id);
            }

            // insert data;
            $data = [];
            if ($itemAdditionalCosts->count() > 0) {
                foreach ($itemAdditionalCosts as $item) {
                    $data = [
                        'tender_number' => $tender->tender_number,
                        'eproc_po_number' => $eproc_po_number,
                        'vendor_id' => $item->vendor_id,
                        'vendor_code' => $item->vendor_code,
                        'item_id' => $item->item_id,
                        'pr_number' => $item->number,
                        'po_item' => $item->number,
                        'line_id' => $item->line_id,
                        'pr_line_number' => $item->line_number,
                        'conditional_code' => $item->conditional_code,
                        'conditional_name' => $item->conditional_name,
                        'percentage' => $item->percentage,
                        'value' => $item->value,
                        'calculation_pos' => $item->calculation_pos,
                        'conditional_type' => $item->conditional_type,
                        'po_item' => $po_item,
                        // 'status' => TenderVendorSubmission::STATUS[0],
                        // 'submission_method' => $item->submission_method,
                    ];

                    DB::table("po_additional_costs")->insert($data);
                }
            }

            return true;
        } catch (Exception $e) {
            Log::error($this->logName . '::copyVendorAdditionalCostPO error : ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Copy item po
     *
     * @param \App\TenderParamater, $tender
     * @param string, $eproc_po_number
     * @param \App\Models\TenderVendorAwarding, $item
     */
    private function copyItemsPO($tender, $eproc_po_number, $model)
    {
        $po_item = null;
        try {
            $tenderItems = TenderItem::where('tender_items.tender_number', $tender->tender_number)
                ->join("tender_item_commercial_awarding", function ($join) use ($model) {
                    $join->on("tender_items.line_id", "=", "tender_item_commercial_awarding.item_id")
                        ->where("tender_item_commercial_awarding.tender_number", $model->tender_number)
                        ->where("tender_item_commercial_awarding.vendor_id", $model->vendor_id);
                })
                ->whereNull('tender_items.deleted_at')
                ->whereNull('tender_item_commercial_awarding.deleted_at')
                ->select("tender_items.*")
                ->get();

            // insert data;
            $data = [];
            $doc_type_data = DB::table("tender_document_type")->where("tender_number", $tender->tender_number)
                ->where("vendor_code", $model->vendor_code)->first();
            if ($tenderItems->count() > 0) {
                foreach ($tenderItems as $item) {
                    $data = [
                        'tender_number' => $tender->tender_number,
                        'eproc_po_number' => $eproc_po_number,
                        'item_id' => $item->line_id,
                        'number' => $item->number,
                        'vendor_id' => $model->vendor_id,
                        'vendor_code' => $model->vendor_code,
                        'line_number' => $item->line_number,
                        'product_code' => $item->product_code,
                        'product_group_code' => $item->product_group_code,
                        'description' => $item->description,
                        'purch_group_code' => $item->purch_group_code,
                        'purch_group_name' => $item->purch_group_name,
                        'qty' => $item->qty,
                        'uom' => $item->uom,
                        'est_unit_price' => $item->est_unit_price,
                        'price_unit' => $item->price_unit,
                        'currency_code' => $item->currency_code,
                        'subtotal' => $item->subtotal,
                        'state' => $item->state,
                        'expected_delivery_date' => $doc_type_data->document_date,
                        'transfer_date' => $item->transfer_date,
                        'account_assignment' => $item->account_assignment,
                        'item_category' => $item->item_category,
                        'gl_account' => $item->gl_account,
                        'cost_code' => $item->cost_code,
                        'requisitioner' => $item->requisitioner,
                        'requisitioner_desc' => $item->requisitioner_desc,
                        'tracking_number' => $item->tracking_number,
                        'request_date' => $item->request_date,
                        'certification' => $item->certification,
                        'material_status' => $item->material_status,
                        'plant' => $item->plant,
                        'plant_name' => $item->plant_name,
                        'storage_loc' => $item->storage_loc,
                        'storage_loc_name' => $item->storage_loc_name,
                        'qty_ordered' => $item->qty_ordered,
                        'cost_desc' => $item->cost_desc,
                        'overall_limit' => $item->overall_limit,
                        'expected_limit' => $item->expected_limit,
                        'line_id' => $item->line_id
                    ];

                    $poItem = PoTenderItem::updateOrCreate($data);

                    $lastNo = 0;
                    // $lastData = collect(DB::select("select po_item from po_items order by id desc limit 1"))->first();
                    $lastData = collect(DB::select(
                        "select po_item from po_items where eproc_po_number=? and po_item is not null order by id desc limit 1",
                        [$eproc_po_number]
                    ))->first();
                    if (isset($lastData) && !empty($lastData)) {
                        $lastNo = intVal($lastData->po_item);
                    }

                    // $id = DB::table("po_items")->insertGetId($data);
                    // $id = $poItem->id;
                    $po_item = str_pad($lastNo + 10, 5, '0', STR_PAD_LEFT);
                    // DB::table('po_items')->where('id', $id)->update(array(
                    //     'po_item' => $po_item,
                    // ));
                    $poItem->po_item = $po_item;
                    $poItem->save();
                    $this->copyPoItemServices($tender, $poItem);
                    $this->copyVendorItemTextPO($tender, $poItem);
                }
            }

            return $po_item;
        } catch (Exception $e) {
            Log::error($this->logName . '::copyItemsPO error : ' . $e->getMessage());
            throw $e;
        }
    }


    /**
     * Copy sap prlist services to po item services
     *
     * @param \App\TenderParamater, $tender
     * @param \App\PoTenderItem, $item
     */
    private function copyPoItemServices($tender, $item)
    {
        try {
            // cek if item is services
            if ($item->item_category != 0) {
                $itemServices = SapPRListServices::where('BANFN', $item->number)
                    ->where('BNFPO', $item->line_number)
                    ->get();
                if ($itemServices != null && $itemServices->count() > 0) {
                    foreach ($itemServices as $service) {
                        $data = [
                            'tender_number' => $tender->tender_number,
                            'eproc_po_number' => $item->eproc_po_number,
                            'vendor_id' => $item->vendor_id,
                            'vendor_code' => $item->vendor_code,
                            'item_id' => $item->item_id,
                            'po_item' => $item->po_item,
                            'BANFN' => $service->BANFN,
                            'BNFPO' => $service->BNFPO,
                            'EXTROW' => $service->EXTROW,
                            'SRVPOS' => $service->SRVPOS,
                            'KTEXT1' => $service->KTEXT1,
                            'MENGE' => $service->MENGE,
                            'MEINS' => $service->MEINS,
                            'WAERS' => $service->WAERS,
                            'BRTWR' => $service->BRTWR,
                            'NETWR' => $service->NETWR,
                            'COST_CODE' => $service->COST_CODE,
                            'COST_DESC' => $service->COST_DESC,
                        ];
                        PoItemDetailServices::updateOrCreate($data);
                    }
                }
            }
            return true;
        } catch (Exception $e) {
            Log::error($this->logName . '::copyPoItemServices error : ' . $e->getMessage());
            throw $e;
        }
    }

    private function copyRefAssignPurchaseOrg($tender)
    {
        try {

            $refPurchaseOrg = RefPurchaseOrg::where("id", $tender->purchase_org_id)->first();
            $count = collect(DB::select("select * from ref_assign_purchaseorg_compcode where tender_number = ?", [$tender->tender_number]))->count();

            if ($refPurchaseOrg && $count <= 0 && $refPurchaseOrg->org_code == "1100") {
                // insert data;
                $data = [
                    'tender_number' => $tender->tender_number,
                    'purchase_compcode_id' => $tender->purchase_org_id
                ];

                DB::table("ref_assign_purchaseorg_compcode")->insert($data);
            }

            return true;
        } catch (Exception $e) {
            Log::error($this->logName . '::copyRefAssignPurchaseOrg error : ' . $e->getMessage());
            throw $e;
        }
    }

    private function uploadAttachment($params, $tender, $stageType)
    {
        try {
            DB::beginTransaction();

            $detail = null;
            if (!empty($params['id']) && $params['id'] != "null") {
                $detail = TenderAwardingAttachment::where('tender_number', $tender->tender_number)
                    ->where('id', $params['id'])
                    ->first();
            }

            if ($detail) {
                $detail->attachment = $params['attachment'];
                $detail->save();
            }

            DB::commit();
            return [
                'data' => $detail,
                'next' => null,
            ];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($this->logName . '::uploadAttachment error : ' . $e->getMessage());
            throw $e;
        }
    }

    private function deleteAttachment($params, $tender, $pageType)
    {
        try {
            DB::beginTransaction();
            $detail = TenderAwardingAttachment::where('tender_number', $tender->tender_number)
                ->where('id', $params['id'])
                ->firstOrFail();

            $detail->attachment = '';
            $detail->save();

            $result = TenderAwardingAttachment::where('tender_number', $tender->tender_number)
                ->where('id', $detail->id)
                ->orderBy('id', 'DESC')
                ->first();

            DB::commit();
            return [
                'data' => $result,
                'next' => null,
            ];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($this->logName . '::deleteSubmissionDetail error : ' . $e->getMessage());
            throw $e;
        }
    }

    public function isSubmitAwaridngByVendor($tenderNumber, $vendorId)
    {
        return TenderVendorAwarding::where([
            ["tender_number", "=", $tenderNumber],
            ["vendor_id", "=", $vendorId],
            ["status", "=", TenderSubmissionEnum::STATUS_ITEM[2]]
        ])->whereNull("deleted_at")->count() > 0;
    }

    public function isNextPOCreationByVendor($tenderNumber, $vendorId)
    {
        return TenderVendorAwarding::where([
            ["tender_number", "=", $tenderNumber],
            ["vendor_id", "=", $vendorId],
            ["status", "=", TenderSubmissionEnum::STATUS_ITEM[2]],
            ["awarding_status", "=", TenderVendorAwarding::STATUS[1]]
        ])->whereNull("deleted_at")->count() > 0 && TenderWorkflow::where('tender_number', $tenderNumber)
        ->where('page', "awarding_process")->where('is_done', 1)->count() > 0;
    }

    private function saveHeaderTechnical($params, $tender, $stageType)
    {
        try {
            DB::beginTransaction();

            $data = null;
            if (isset($params['tkdn_percentage'])) {
                $data = [
                    "tkdn_percentage" => $params['tkdn_percentage']
                ];
            }

            if (isset($params['attachment'])) {
                $data = [
                    "tkdn_file" => $params['attachment']
                ];
            }

            $header = null;
            if ($data) {
                $data["vendor_code"] = $params["vendor_code"];
                $data["submission_method"] = $this->getStageTypeTechnical($tender->tender_number, $params['vendor_id']);

                $header = TenderHeaderTechnicalAwarding::updateOrCreate(
                    [
                        'tender_number' => $tender->tender_number,
                        'vendor_id' => $params['vendor_id'],
                    ],
                    $data
                );
                //pastikan bukan draft.
                // if($header->status==TenderSubmissionEnum::STATUS_ITEM[1]){
                //     $header->status=TenderSubmissionEnum::STATUS_ITEM[2];
                //     $header->save();
                // }
            }

            DB::commit();
            return [
                'data' => $header,
                'next' => null,
            ];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($this->logName . '::saveHeaderTechnical error : ' . $e->getMessage());
            throw $e;
        }
    }
}
