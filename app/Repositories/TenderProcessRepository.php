<?php

namespace App\Repositories;

use App\Enums\TenderStatusEnum;
use App\Enums\TenderSubmissionEnum;
use App\Jobs\SendEmail;
use App\Mail\QueuingMail;
use App\Models\BaseModel;
use App\Models\TenderAdditionalCost;
use App\Models\TenderBiddingDocumentRequirement;
use App\Models\TenderComment;
use App\Models\TenderEvaluationNote;
use App\Models\TenderHeaderCommercial;
use App\Models\TenderHeaderTechnical;
use App\Models\TenderItemCommercial;
use App\Models\TenderItemTechnical;
use App\Models\TenderLogs;
use App\Models\TenderReference;
use App\Models\TenderVendor;
use App\Models\TenderVendorScores;
use App\Models\TenderVendorSubmission;
use App\Models\TenderVendorSubmissionDetail;
use App\RefListOption;
use App\Scopes\PublicViewScope;
use App\Scopes\VendorViewScope;
use App\Services\TenderMailService;
use App\TenderParameter;
use App\TenderItem;
use App\TenderWorkflow;
use App\TenderWorkflowHelper;
use App\Vendor;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class TenderProcessRepository extends BaseRepository
{

    protected $logName = 'TenderProcessRepository';
    protected $fields = [
        'registration' => ['vendor_code', 'vendor_name', 'status', 'registered_at'],
        'pre_qualification1' => ['vendor_code', 'vendor_name', 'status', 'submission_date'],
        'pre_qualification2' => ['vendor_code', 'vendor_name', 'status', 'submission_date'],
        'pre_qualification3' => ['vendor_code', 'vendor_name', 'score'],
        'bid_opening1' => ['vendor_code', 'vendor_name', 'status', 'submission_date'],
        'bid_opening2' => ['vendor_code', 'vendor_name', 'status', 'submission_date'],
        'bid_opening3' => ['vendor_code', 'vendor_name', 'score'],
        'negotiation' => ['vendor_code', 'vendor_name', 'score_tc', 'score_com'],
    ];


    public function __construct()
    {
    }

    public function fields($type)
    {
        return $this->fields[$type];
    }

    /**
     * find data vendor submission by tender number
     *
     * @param string $number
     * @param string $stageType
     *
     * @return \Illuminate\Database\Eloquent\Collection $data
     */
    public function findVendorSubmission($number, $stageType = null)
    {
        try {
            $query = TenderVendorSubmission::select(
                'tender_vendor_submissions.id',
                'tender_vendor_submissions.vendor_id',
                'tender_vendor_submissions.tender_number',
                'vendors.vendor_code',
                'vendors.vendor_name',
                'tender_vendor_submissions.status',
                'tender_vendor_submissions.submission_date',
                'tender_vendor_submissions.score'
            )->join('vendors', 'tender_vendor_submissions.vendor_id', 'vendors.id')
                ->where('tender_vendor_submissions.tender_number', $number)
                ->whereNull('vendors.deleted_at');

            if (!empty($stageType)) {
                $query = $query->where('submission_method', $stageType);
            }
            return $query->get();
        } catch (Exception $e) {
            Log::error($this->logName . '::findVendorSubmission error : ' . $e->getMessage());
            throw $e;
        }
    }

    protected function findVendorSubmission2Score($number, $stageType, $onlyPassed = false)
    {
        try {
            $query = TenderVendorSubmission::select(
                // 'tender_vendor_submissions.id',
                'tender_vendor_submissions.vendor_id',
                'tender_vendor_submissions.tender_number',
                'vendors.vendor_code',
                'vendors.vendor_name',
                // 'tender_vendor_submissions.status',
                DB::raw('MAX(submission_date) as submission_date'),
                DB::raw('MAX(tender_vendor_submissions.status) as status'),
                DB::raw('SUM((CASE WHEN submission_method=3 THEN score ELSE 0 END)) as score_tc'),
                DB::raw('SUM((CASE WHEN submission_method=4 THEN score ELSE 0 END)) as score_com')
            )
                ->join('vendors', 'tender_vendor_submissions.vendor_id', 'vendors.id')
                ->where('tender_vendor_submissions.tender_number', $number)
                ->whereNull('vendors.deleted_at')
                ->groupBy('vendor_id', 'tender_number', 'vendor_code', 'vendor_name');

            $query = $query->whereIn('submission_method', $stageType);
            if ($onlyPassed) {
                $query = $query->havingRaw('MAX(submission_method)=?', [4])
                    ->havingRaw('MAX(tender_vendor_submissions.status)=?', ['passed']);
            }

            return $query->get();
        } catch (Exception $e) {
            Log::error($this->logName . '::findVendorSubmission2Score error : ' . $e->getMessage());
            throw $e;
        }
    }

    protected function findVendorSubmissionScorePassed($number, $stageTech, $stageComm)
    {
        try {

            $sSQL = "select tvc.vendor_id
                        , tvc.tender_number
                        , v.vendor_code
                        , v.vendor_name
                        , MAX(tvc.submission_date) as submission_date
                        , MAX(tvc.status) as status
                        , SUM(COALESCE(tvt.score, 0)) as score_tc
                        , SUM(COALESCE(tvc.score, 0)) as score_com
                    from (select * from tender_vendor_submissions where submission_method = ?) tvc
                    inner join vendors v on tvc.vendor_id = v.id
                    left join (select * from tender_vendor_submissions where submission_method = ?) tvt
                        on tvc.vendor_id = tvt.vendor_id and tvc.line_id = tvt.line_id
                    where tvc.tender_number=? and tvc.status = 'passed'
                    group by tvc.vendor_id, tvc.tender_number, v.vendor_code, v.vendor_name;";

            return collect(DB::select(DB::raw($sSQL), [$stageComm, $stageTech, $number]));
        } catch (Exception $e) {
            Log::error($this->logName . '::findVendorSubmissionScorePassed error : ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * find data vendor submission data that did not pass
     *
     * @param string $number
     * @param int $vendorId
     *
     * @return \App\Models\TenderVendorSubmission $data
     */
    public function findSubmissionDidNotPass($number, $vendorId)
    {
        try {
            $query = TenderVendorSubmission::where('tender_number', $number)
                ->where('vendor_id', $vendorId)
                ->where('status', TenderVendorSubmission::STATUS[4]);

            return $query->first();
        } catch (Exception $e) {
            Log::error($this->logName . '::findSubmissionDidNotPass error : ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * find if vendor is a winner
     *
     * @param string $number
     * @param int $vendorId
     *
     * @return \App\Models\TenderVendorSubmission $data
     */
    public function isVendorWinning($number, $vendorId)
    {
        try {
            $query = TenderVendorSubmission::where('tender_number', $number)
                ->where('vendor_id', $vendorId)
                ->where('submission_method', 4)
                ->where('status', TenderVendorSubmission::STATUS[3]);

            return $query->count() > 0;
        } catch (Exception $e) {
            Log::error($this->logName . '::isVendorWinning error : ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * find data vendor submission by tender number
     *
     * @param string $number
     * @param int $vendorId
     * @param string $stageType
     *
     * @return \Illuminate\Database\Eloquent\Collection $data
     */
    public function findVendorSubmissionByVendor($number, $vendorId, $stageType = null)
    {
        try {
            $query = TenderVendorSubmission::select(
                'tender_vendor_submissions.id',
                'tender_vendor_submissions.vendor_id',
                'tender_vendor_submissions.tender_number',
                'vendors.vendor_code',
                'vendors.vendor_name',
                'tender_vendor_submissions.status',
                'tender_vendor_submissions.submission_date',
                'tender_vendor_submissions.action_status'
            )->join('vendors', 'tender_vendor_submissions.vendor_id', 'vendors.id')
                ->where('tender_vendor_submissions.tender_number', $number)
                ->where('tender_vendor_submissions.vendor_id', $vendorId)
                ->whereNull('tender_vendor_submissions.deleted_at')
                ->whereNull('vendors.deleted_at');

            if (!empty($stageType)) {
                $query = $query->where('submission_method', $stageType);
            }

            return $query->first();
        } catch (Exception $e) {
            Log::error($this->logName . '::findVendorSubmissionByVendor error : ' . $e->getMessage());
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
                    // $join->on('tender_vendor_submission_detail.bidding_document_ids', 'tender_bidding_document_requirements.line_id');
                    TenderVendorSubmissionDetail::vendorStatus($join)
                        ->on('tender_vendor_submission_detail.bidding_document_id', 'tender_bidding_document_requirements.line_id')
                        ->where('tender_vendor_submission_detail.vendor_id', DB::raw($vendorId))
                        ->whereNull('tender_vendor_submission_detail.deleted_at');
                }
            )
                ->where('tender_bidding_document_requirements.tender_number', $number)
                ->whereNotIn('tender_vendor_submission_detail.submission_method', [6])
                ->whereNull('tender_vendor_submission_detail.deleted_at')
                ->whereNull('tender_bidding_document_requirements.deleted_at');

            if (!$defaultIsEmpty) {
                $query = $query->where('tender_vendor_submission_detail.vendor_id', $vendorId);
            }

            if (!empty($stageType)) {
                $query = $query->where('tender_bidding_document_requirements.submission_method', $stageType);
            }

            return $query;
        } catch (Exception $e) {
            Log::error($this->logName . '::findVendorSubmissionDetail error : ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * find data vendor submission history
     *
     * @param string $number
     * @param int $userId
     * @param string $pageType
     *
     * @return \Illuminate\Database\Eloquent\Builder $builder
     */
    public function findVendorSubmissionHistory($number, $userId, $pageType)
    {
        try {
            $query = TenderLogs::select(
                'tender_logs.created_by as user_id',
                'tender_logs.activity',
                'tender_logs.ref_number',
                'tender_logs.properties',
                'tender_logs.updated_at as activity_date'
            )
            ->where('tender_logs.ref_number', $number)
            ->where('tender_logs.page_type', $pageType)
            ->where(function ($query) use ($userId) {
                $query->where('tender_logs.model_type', 'App\Models\TenderVendorSubmission')
                    // ->where('tender_logs.activity', '!=', TenderSubmissionEnum::STATUS_ITEM[1])
                    ->where('tender_logs.user_id', $userId);
            })
            ->orWhere(function ($query) use ($number, $pageType) {
                $query->where('tender_logs.model_type', 'App\TenderParameter')
                    ->where('tender_logs.ref_number', $number)
                    ->whereIn('tender_logs.activity', TenderSubmissionEnum::FLOW_STATUS)
                    ->where('tender_logs.page_type', $pageType);
            })
            ->orWhere(function ($query) use ($number, $pageType, $userId) {
                $query->where('tender_logs.model_type', 'App\Models\TenderReference')
                    ->where('tender_logs.ref_number', $number)
                    ->where('tender_logs.page_type', $pageType);
                if($pageType == 'negotiation_commercial'){
                    $query = $query->where(function ($subquery) use ($userId) {
                        $includeActivity = [TenderSubmissionEnum::FLOW_STATUS[1], TenderSubmissionEnum::FLOW_STATUS[2], TenderSubmissionEnum::FLOW_STATUS[4]];
                        $subquery->where('tender_logs.user_id', $userId)
                            ->whereIn('tender_logs.activity', $includeActivity)
                            ->orWhereNotIn('tender_logs.activity', $includeActivity);
                    });
                }else{
                    $query =  $query->whereIn('tender_logs.activity', TenderSubmissionEnum::FLOW_STATUS);
                }
            })
            ->whereNotNull('tender_logs.user_id')
            ->with('user')
            ->orderBy('tender_logs.created_at', 'asc');
            // $data = $query->get();
            return $query->get();
        } catch (Exception $e) {
            Log::error($this->logName . '::findVendorSubmissionHistory error : ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * find data submission evaluation notes
     *
     * @param string $number
     * @param int $noteType
     * @param string $stageType
     *
     * @return \Illuminate\Database\Eloquent\Builder $builder
     */
    public function findEvaluationNotes($number, $noteType, $stageType)
    {
        try {
            $query = TenderEvaluationNote::where('tender_number', $number)
                ->where('note_type', $noteType)
                ->where('submission_method', $stageType)
                ->orderBy('updated_at', 'DESC');
            return $query->first();
        } catch (Exception $e) {
            Log::error($this->logName . '::findEvaluationNotes error : ' . $e->getMessage());
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
            $query = null;
            if ($stageType == TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE['process_technical_evaluation']) {
                $query = TenderHeaderTechnical::where('tender_number', $number)
                    ->where('vendor_id', $vendorId);
            } else if ($stageType == TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE['process_commercial_evaluation']) {
                $query = TenderHeaderCommercial::where('tender_number', $number)
                    ->where('vendor_id', $vendorId)
                    ->where('submission_method', $stageType);
            } else if ($stageType == TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE['negotiation_commercial']) {
                $query = TenderHeaderCommercial::where('tender_number', $number)
                    ->where('submission_method', $stageType)
                    ->where('vendor_id', $vendorId);
            }

            return $query;
        } catch (Exception $e) {
            Log::error($this->logName . '::findTenderHeader error : ' . $e->getMessage());
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
        return (new TenderVendorItemRepository)->findTenderComparisonItems($tender, $stageType);
    }

    public function rawAdditionalCost($number, $type, $subtotal = 0)
    {
        return (new TenderVendorItemRepository)->rawAdditionalCost($number, $type, $subtotal);
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
        return (new TenderVendorItemRepository)->findTenderSummaryItems($tender, $stageType);
    }

    /**
     * find data vendor submission comments
     *
     * @param string $number
     * @param int $noteType
     * @param string $stageType
     *
     * @return \Illuminate\Database\Eloquent\Builder $builder
     */
    public function findComments($number, $vendorCode, $stageType)
    {
        try {
            $query = TenderComment::where('tender_number', $number)
                ->where('submission_method', $stageType)
                ->where(function ($query) use ($vendorCode) {
                    $query->where('user_id_from', $vendorCode)
                        ->orWhere('user_id_to', $vendorCode);
                })
                ->orderBy('id', 'asc');
            return $query->get();
        } catch (Exception $e) {
            Log::error($this->logName . '::findComments error : ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * find data
     *
     * @param App\TenderParameter $tender, tender number
     * @param string $stageType, item type
     * @param string $params, request parameter
     *
     * @return \Yajra\DataTables\DataTables $dataTable
     *
     * @throws \Exception
     */
    public function findItem($tender, $stageType, $params = null)
    {
        $number = $tender->tender_number;
        $pageTypes = array_flip(TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE);
        if (!empty($params['action_type'])) {
            switch ($params['action_type']) {
                case 'submission-header':
                    $stageTypeFlow = $stageType;
                    if (isset($params['vendor_id']) && !empty($params['vendor_id'])) {
                        $vendorId = $params['vendor_id'];
                    } else {
                        $vendorId = Auth::user()->vendor ? Auth::user()->vendor->id : 0;
                    }
                    if ($stageType == TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE["awarding_process"]) {
                        if ($params['actionView'] == "awarding_result") {
                            return [
                                'success' => true,
                                'data' => (new TenderProcessAwardingRepository)->findTenderAwardingHeader($number, $vendorId, $stageTypeFlow)->first(),
                            ];
                        } else {
                            $stageTypeFlow = (new TenderProcessAwardingRepository)->getStageType($number, $params["vendor_id"]);
                        }
                    }
                    return [
                        'success' => true,
                        'data' => $this->findTenderHeader($number, $vendorId, $stageTypeFlow)->first(),
                    ];
                    break;
                case 'submission-items':
                    return (new TenderVendorItemRepository)->findDataTable($number, $stageType, $params);
                    break;

                case 'awarding-attacment':
                    return DataTables::of((new TenderProcessAwardingRepository)->findAwardingAttachment($number, $params)->get())
                        ->addColumn('is_required_text', function ($row) {
                            return $row->is_required ? __('common.yes') : __('common.no');
                        })
                        ->addColumn('status_text', function ($row) {
                            return __('tender.process_status.' . $row->status);
                        })
                        ->make(true);
                    break;

                case 'submission-detail':
                    $vendorId = Auth::user()->vendor ? Auth::user()->vendor->id : 0;
                    return DataTables::of($this->findVendorSubmissionDetail($number, $vendorId, $stageType, true)->get())
                        ->addColumn('is_required_text', function ($row) {
                            return $row->is_required ? __('common.yes') : __('common.no');
                        })
                        ->addColumn('status_text', function ($row) {
                            return __('tender.process_status.' . $row->status);
                        })
                        ->make(true);
                case 'submission-detail-admin':
                    $query = null;
                    if ($stageType == TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE["awarding_process"]) {
                        $stageTypeFlow = (new TenderProcessAwardingRepository)->getStageType($number, $params["vendor_id"]);
                        if ($stageTypeFlow == TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE["negotiation_commercial"]) {
                            $query = (new TenderProcessNegotiationRepository)->findVendorSubmissionDetail($number, $params['vendor_id'], $stageTypeFlow);
                        } else {
                            $query = $this->findVendorSubmissionDetail($number, $params['vendor_id'], $stageTypeFlow);
                        }
                    } else {
                        $query = $this->findVendorSubmissionDetail($number, $params['vendor_id'], $stageType);
                    }

                    return DataTables::of($query->get())
                        ->addColumn('is_required_text', function ($row) {
                            return $row->is_required ? __('common.yes') : __('common.no');
                        })
                        ->addColumn('status_text', function ($row) {
                            return __('tender.process_status.' . $row->status);
                        })
                        ->make(true);
                case 'submission-history':
                    if ($params['vendor_id']) {
                        $vendor = Vendor::find($params['vendor_id']);
                    } else {
                        $vendor = Auth::user()->vendor;
                    }
                    return DataTables::of($this->findVendorSubmissionHistory($number, $vendor->vendor_code ?? '', $pageTypes[$stageType]))
                        ->addColumn('role', function ($row) {
                            $roleName = $row->user && $row->user->isVendor() ? 'Vendor' : '';
                            if (empty($roleName)) {
                                $roleName = $row->user && $row->user->buyer() ? 'Buyer' : 'Admin';
                            }
                            return $roleName;
                        })
                        ->addColumn('activity_text', function ($row) {
                            return __('tender.log_status.' . $row->activity);
                        })
                        ->make(true);
                case 'evaluation-notes':
                    $stageType = !empty($params['stage_type']) ? $params['stage_type'] : $stageType;
                    return [
                        'success' => true,
                        'data' => $this->findEvaluationNotes($number, $params['note_type'], $stageType)
                    ];
                case 'comments':
                    return [
                        'success' => true,
                        'data' => $this->findComments($number, $params['vendor_code'], $params['stage_type'])
                    ];
                    break;
                case 'submission-scoring':
                    return [
                        'success' => true,
                        'data' => (new TenderWeightingRepository)->findVendorScores($number, $params['vendor_id'], $stageType)
                    ];
                    break;
                case 'comparison-items':
                    $q = $this->findTenderComparisonItems($tender, $stageType);
                    if ($stageType == 3) $q->whereNotNull('compliance');
                    return DataTables::eloquent($q)
                        ->make(true);
                case 'comparison-items-report':
                    return $this->findTenderComparisonItems($tender, $stageType)
                        ->where('v.id', $params['vendor_id'])->get();
                case 'summary-items':
                    $q = $this->findTenderSummaryItems($tender, $stageType);
                    if ($stageType == 3) $q->whereNotNull('compliance');
                    return DataTables::eloquent($q)
                        // ->editColumn('additional_cost', function ($row) use ($number) {
                        //     $addCost = TenderAdditionalCost::where('tender_number', $number)
                        //         ->where('conditional_type', 'CT1')->first();
                        //     if ($addCost) {
                        //         $cost = DB::select($this->rawAdditionalCost($number, 'CT1', $row->subtotal_vendor));
                        //         return ($cost) ? $cost[0]->additional_cost : 0;
                        //     } else {
                        //         return $row->additional_cost;
                        //     }
                        // })
                        ->make(true);
            }
        } else {
            // pre qualification All Tab
            if ($stageType == 1) {
                return DataTables::of($this->findVendorSubmission($number, $stageType))
                    ->addColumn('status_text', function ($row) {
                        return __('tender.process_status.' . $row->status);
                    })
                    ->make(true);
            }
            // 1 - envelope
            else if ($stageType == TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE['process_tender_evaluation']) {
                return $this->findVendorListStage2($tender, $stageType, $params);
            }
            // negotiation
            else if (in_array($stageType, [
                TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE['negotiation_technical'],
                TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE['negotiation_commercial']
            ])) {
                $data = $this->findVendorSubmissionScorePassed($number, 3, 4)->toArray();
                $dataNego = $this->findVendorSubmission2Score($number, [5, 6], false);
                $dataNegoStatus = $dataNego->pluck('status', 'vendor_id')->toArray();
                $dataNegoDate = $dataNego->pluck('submission_date', 'vendor_id')->toArray();
                $tenderVendor = TenderVendor::where('tender_number', $number)->pluck('negotiation_status', 'vendor_id')->toArray();
                $submissionMethod = RefListOption::where('type', 'submission_method_options')->where('deleteflg', false)->pluck('value', 'key');
                $output = [];

                foreach ($data as $row) {
                    $row = (array)$row;

                    if ($row["status"] == TenderVendorSubmission::STATUS[3]) {
                        $row['action_negotiation_status'] = $tenderVendor[$row['vendor_id']];
                        $row['negotiation_status'] = $tenderVendor[$row['vendor_id']];

                        // $row['status'] = TenderVendorSubmission::STATUS[0];
                        $row['tender_submission_method'] = $submissionMethod[$tender->submission_method];
                        $row['IsNegotiation'] = false;
                        $row['submission_date'] = "";

                        if (count($dataNegoStatus) > 0) {
                            if (!empty($dataNegoStatus[$row['vendor_id']])) {
                                $row['IsNegotiation'] = true;
                                $row['status'] = $dataNegoStatus[$row['vendor_id']];
                            }
                            if (!empty($dataNegoDate[$row['vendor_id']])) {
                                $row['submission_date'] = $dataNegoDate[$row['vendor_id']];
                            }
                        }

                        if (isset($params['actionView']) && $params['actionView'] == 'negotiation') {
                            if ($row['negotiation_status'] == 'started' || empty($row['negotiation_status']))
                                continue;

                            $tenderItemComm = TenderItemCommercial::where("tender_number", $row['tender_number'])
                                ->where("vendor_id", $row['vendor_id'])
                                ->where("submission_method", TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE["negotiation_commercial"])
                                ->get();

                            $row['total_comply'] = $tenderItemComm->where("compliance", "comply")
                                ->count();

                            $row['total_deviate'] = $tenderItemComm->where("compliance", "deviate")
                                ->count();

                            $row['total_no_quote'] = $tenderItemComm->where("compliance", "no_quote")
                                ->count();
                        }

                        $output[] = $row;
                    }
                }
                return DataTables::of($output)
                    ->addColumn('status_text', function ($row) {
                        if ($row['IsNegotiation'] == true)
                            return __('tender.process_status.' . $row['status']);
                        else
                            return __('tender.process_status.' . $row['status']) . " " . __('tender.' . $row["tender_submission_method"]);
                    })
                    ->make(true);
            }
            // awarding process
            else if ($stageType == TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE['awarding_process']) {
                return (new TenderProcessAwardingRepository)->findItemAwarding($tender, $params);
            }
            // 2 - envelope or 2 - Stage
            else {
                return $this->findVendorListStage3($tender, $stageType, $params);
            }
        }
    }

    /**
     * find tender vendor followed for 2 envelope / 2 stage
     *
     * @param string $number, tender number
     * @param string $stageType, item type
     *
     * @return \Yajra\DataTables\DataTables $dataTable
     *
     * @throws \Exception
     */
    protected function findVendorListFollowed($number, $stageType, $hasPrequalification)
    {
        try {
            if ($hasPrequalification) {
                // $number = $tender->tender_number;
                $query = TenderVendorSubmission::select(
                    'tvs.id',
                    'tvs.vendor_id',
                    'tvs.tender_number',
                    'vendors.vendor_code',
                    'vendors.vendor_name',
                    'tvs.status',
                    'tvs.submission_date',
                    'tvs.score'
                )
                    ->join('vendors', 'tender_vendor_submissions.vendor_id', 'vendors.id')
                    ->leftJoin('tender_vendor_submissions as tvs', function ($join) use ($stageType) {
                        $join->on('tvs.tender_number', 'tender_vendor_submissions.tender_number');
                        $join->on('tvs.vendor_id', 'tender_vendor_submissions.vendor_id');
                        $join->on('tvs.submission_method', DB::raw($stageType));
                    })
                    ->where('tender_vendor_submissions.tender_number', $number)
                    ->whereNull('vendors.deleted_at')
                    ->where('tender_vendor_submissions.submission_method', TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE['process_prequalification'])
                    ->whereNull('vendors.deleted_at')
                    ->whereIn('tender_vendor_submissions.status', [TenderVendorSubmission::STATUS[3]]);

                if (TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE['process_commercial_evaluation'] == $stageType) {
                    $techStage = TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE['process_technical_evaluation'];
                    $query = $query->join('tender_vendor_submissions as tc', function ($join) use ($techStage) {
                        $join->on('tc.tender_number', 'tender_vendor_submissions.tender_number');
                        $join->on('tc.vendor_id', 'tender_vendor_submissions.vendor_id');
                        $join->on('tc.submission_method', DB::raw($techStage))
                            ->whereIn('tc.status', [TenderVendorSubmission::STATUS[3]]);
                    });
                }
                return $query;
            } else {
                // $number = $tender->tender_number;
                $query = TenderVendor::select(
                    'tender_vendor_submissions.id',
                    'tender_vendor_submissions.vendor_id',
                    'tender_vendor_submissions.tender_number',
                    'vendors.vendor_code',
                    'vendors.vendor_name',
                    'tender_vendor_submissions.status',
                    'tender_vendor_submissions.submission_date',
                    'tender_vendor_submissions.score'
                )
                    ->join('vendors', 'tender_vendors.vendor_id', 'vendors.id')
                    ->leftJoin('tender_vendor_submissions', function ($join) use ($stageType) {
                        $join->on('tender_vendor_submissions.tender_number', 'tender_vendors.tender_number');
                        $join->on('tender_vendor_submissions.vendor_id', 'tender_vendors.vendor_id');
                        $join->on('tender_vendor_submissions.submission_method', DB::raw($stageType));
                    })
                    ->where('tender_vendors.tender_number', $number)
                    ->whereNull('vendors.deleted_at')
                    ->whereIn('tender_vendors.status', [TenderVendor::STATUS[2], TenderVendor::STATUS[4]]);

                if (TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE['process_commercial_evaluation'] == $stageType) {
                    $techStage = TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE['process_technical_evaluation'];
                    $query = $query->join('tender_vendor_submissions as tc', function ($join) use ($techStage) {
                        $join->on('tc.tender_number', 'tender_vendor_submissions.tender_number');
                        $join->on('tc.vendor_id', 'tender_vendor_submissions.vendor_id');
                        $join->on('tc.submission_method', DB::raw($techStage))
                            ->whereIn('tc.status', [TenderVendorSubmission::STATUS[3]]);
                    });
                }
                return $query;
            }
        } catch (Exception $e) {
            Log::error($this->logName . '::findVendorListFollowed error : ' . $e->getMessage());
            throw $e;
        }
    }
    /**
     * find tender vendor followed for 1 envelope
     *
     * @param string $number, tender number
     * @param string $stageType, item type
     *
     * @return \Yajra\DataTables\DataTables $dataTable
     *
     * @throws \Exception
     */
    protected function findVendorListFollowed2($number, $stageType, $hasPrequalification)
    {
        try {
            if ($hasPrequalification) {
                $query = TenderVendorSubmission::select(
                    // 'tender_vendor_submissions.id',
                    'tender_vendor_submissions.vendor_id',
                    'tender_vendor_submissions.tender_number',
                    'vendors.vendor_code',
                    'vendors.vendor_name',
                    // 'tender_vendor_submissions.status',
                    DB::raw('MAX(tvs2.submission_date) as submission_date'),
                    DB::raw('MAX(tvs2.status) as status'),
                    DB::raw('SUM((CASE WHEN tvs2.submission_method=3 THEN tvs2.score ELSE 0 END)) as score_tc'),
                    DB::raw('SUM((CASE WHEN tvs2.submission_method=4 THEN tvs2.score ELSE 0 END)) as score_com')
                )
                    ->join('vendors', 'tender_vendor_submissions.vendor_id', 'vendors.id')
                    ->leftJoin('tender_vendor_submissions as tvs2', function ($join) use ($stageType) {
                        $join->on('tvs2.tender_number', 'tender_vendor_submissions.tender_number');
                        $join->on('tvs2.vendor_id', 'tender_vendor_submissions.vendor_id');

                        $join->on(function ($query) use ($stageType) {
                            $query->on('tvs2.submission_method', DB::raw($stageType[0]));
                            $query->orOn('tvs2.submission_method', DB::raw($stageType[1]));
                        });
                    })
                    ->where('tender_vendor_submissions.tender_number', $number)
                    ->where('tender_vendor_submissions.submission_method', TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE['process_prequalification'])
                    ->whereNull('vendors.deleted_at')
                    ->whereIn('tender_vendor_submissions.status', [TenderVendorSubmission::STATUS[3]])
                    ->groupBy('tender_vendor_submissions.vendor_id', 'tender_vendor_submissions.tender_number', 'vendors.vendor_code', 'vendors.vendor_name');
            } else {
                $query = TenderVendor::select(
                    // 'tender_vendor_submissions.id',
                    'tender_vendors.vendor_id',
                    'tender_vendors.tender_number',
                    'vendors.vendor_code',
                    'vendors.vendor_name',
                    // 'tender_vendor_submissions.status',
                    DB::raw('MAX(tvs2.submission_date) as submission_date'),
                    DB::raw('MAX(tvs2.status) as status'),
                    DB::raw('SUM((CASE WHEN tvs2.submission_method=3 THEN tvs2.score ELSE 0 END)) as score_tc'),
                    DB::raw('SUM((CASE WHEN tvs2.submission_method=4 THEN tvs2.score ELSE 0 END)) as score_com')
                )
                    ->join('vendors', 'tender_vendors.vendor_id', 'vendors.id')
                    ->leftJoin('tender_vendor_submissions as tvs2', function ($join) use ($stageType) {
                        $join->on('tvs2.tender_number', 'tender_vendors.tender_number');
                        $join->on('tvs2.vendor_id', 'tender_vendors.vendor_id');

                        $join->on(function ($query) use ($stageType) {
                            $query->on('tvs2.submission_method', DB::raw($stageType[0]));
                            $query->orOn('tvs2.submission_method', DB::raw($stageType[1]));
                        });
                    })
                    ->where('tender_vendors.tender_number', $number)
                    ->whereNull('vendors.deleted_at')
                    ->whereIn('tender_vendors.status', [TenderVendor::STATUS[2], TenderVendor::STATUS[4]])
                    ->groupBy('tender_vendors.vendor_id', 'tender_vendors.tender_number', 'vendors.vendor_code', 'vendors.vendor_name');
            }
            return $query->get();
        } catch (Exception $e) {
            Log::error($this->logName . '::findVendorListFollowed2 error : ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * find data vendor list 1 envelope
     *
     * @param App\TenderParameter $tender, tender number
     * @param string $stageType, item type
     * @param string $params, request parameter
     *
     * @return \Yajra\DataTables\DataTables $dataTable
     *
     * @throws \Exception
     */
    protected function findVendorListStage2($tender, $stageType, $params = null)
    {
        $number = $tender->tender_number;
        // Tab Overview
        if (!empty($params['tab']) && $params['tab'] == 'overview') {
            return DataTables::of($this->findVendorListFollowed2($number, [3, 4], $tender->prequalification == 1))
                ->addColumn('status_text', function ($row) {
                    return !empty($row->status) ? __('tender.process_status.' . $row->status) : '';
                })
                ->make(true);
        }
        // Tab Evaluation
        else {
            return DataTables::of($this->findVendorSubmission2Score($number, [3, 4]))
                ->addColumn('status_text', function ($row) {
                    return __('tender.process_status.' . $row->status);
                })
                ->make(true);
        }
    }

    protected function findVendorListStage3($tender, $stageType, $params = null)
    {
        $number = $tender->tender_number;
        // Tab Overview
        if (!empty($params['tab']) && $params['tab'] == 'overview') {
            return DataTables::eloquent($this->findVendorListFollowed($number, $stageType, $tender->prequalification == 1))
                ->addColumn('status_text', function ($row) {
                    return !empty($row->status) ? __('tender.process_status.' . $row->status) : '';
                })
                ->make(true);
        }
        // Tab Evaluation
        else {
            return DataTables::of($this->findVendorSubmission($number, $stageType))
                ->addColumn('status_text', function ($row) {
                    return __('tender.process_status.' . $row->status);
                })
                ->make(true);
        }
    }

    /**
     * save data tender process
     *
     * @param string $number
     * @param int $noteType
     * @param string $pageType
     *
     * @return array $result
     */
    public function saveProcess($params, $tender, $pageType)
    {
        $stageType = !empty($params['stage_type'])
            ? $params['stage_type']
            : TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE[$pageType];
        switch ($params['action_type']) {
            case TenderSubmissionEnum::FLOW_STATUS[1]:
            case TenderSubmissionEnum::FLOW_STATUS[3]:
                return $this->startStage($params, $tender, $pageType, $stageType);
            case TenderSubmissionEnum::FLOW_STATUS[2]:
            case TenderSubmissionEnum::FLOW_STATUS[4]:
                return $this->openStage($params, $tender, $stageType);
            case TenderSubmissionEnum::FLOW_STATUS[5]:
                return $this->finishStage($params, $tender, $stageType);
            case 'request-submission-detail':
                return $this->saveSubmissionDetail($tender, $stageType);
            case 'upload-submission-detail':
                return $this->uploadSubmissionDetail($params, $tender, $stageType);
            case 'delete-all-submission-detail':
                return $this->resetSubmissionDetail($tender, $stageType);
            case 'submit-submission-detail':
                return $this->submitSubmissionDetail($tender, $stageType, TenderVendorSubmission::STATUS[1]);
            case 'resubmit-submission-detail':
                return $this->submitSubmissionDetail($tender, $stageType, TenderVendorSubmission::STATUS[2]);
            case 'evaluate-submission-detail':
                return $this->evaluateSubmissionDetail($params, $tender);
            case 'evaluate-submission':
                if ($stageType == 2) {
                    return $this->evaluateSubmission2Score($params, $tender, [3, 4]);
                }
                return $this->evaluateSubmission($params, $tender);
            case TenderSubmissionEnum::FLOW_STATUS[6]:
                return $this->completeEvaluation($tender, $params, $pageType);
            case 'delete-submission-detail':
                return $this->deleteSubmissionDetail($tender, $params, $pageType);
            case 'save-evaluation-notes':
                return $this->saveEvaluationNotes($params, $tender, $stageType);
            case 'save-comments':
                $params['user_id_from'] = Auth::user()->userid;
                $params['from_name'] = Auth::user()->name;
                $params['user_id_to'] = $params['to'] ?? null;
                $result = $this->saveComments($params, $tender, $stageType);
                if (Auth::user()->isVendor()) {
                    $result['data'] = $this->findComments($tender->tender_number, Auth::user()->vendor->vendor_code, $stageType);
                } else {
                    $result['data'] =  $this->findComments($tender->tender_number, $params['user_id_to'], $stageType);
                }
                return $result;
            case 'save-scoring':
                return $result = $this->saveScoring($params, $tender, $stageType);
            case 'save-tender-header':
                return $result = $this->saveTenderHeader($params, $tender, $stageType);
            case 'save-tender-items':
                return $result = $this->saveTenderItems($params, $tender, $stageType);
            case 'save-addinfo':
                return $result = $this->saveAddInfo($params, $tender, $stageType);
            default:
                return ['data' => null, 'next' => null];
        }
    }

    /**
     * start prequalification submission
     *
     * @param array $params
     * @param \App\TenderParameter $tender
     * @param string $pageType, page type
     * @param string $stageType
     *
     * @return array data
     *
     * @throws \Exception
     */
    public function startStage($params, $tender, $pageType, $stageType)
    {
        try {
            DB::beginTransaction();
            if ($params['action_type'] == TenderSubmissionEnum::FLOW_STATUS[1]) {
                $prevPage = $tender->workflow_values;
                if ($prevPage == 'process_registration') {
                    TenderWorkflow::where('tender_number', $tender->tender_number)
                        ->where('page', $prevPage)
                        ->update(['is_done' => 1]);
                }
            }
            // update tender parameter status
            $workflow = TenderWorkFlow::where('tender_number', $tender->tender_number)
                ->where('page', $pageType)
                ->first();
            if (!is_null($workflow)) {
                if (
                    $tender->submission_method == '2E'
                    && $stageType == TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE['process_commercial_evaluation']
                    && $params['action_type'] == TenderSubmissionEnum::FLOW_STATUS[1]
                ) {
                    // skip when tender commercial start and submission_method=2E
                } else {
                    $tender->status = $workflow->status;
                    $tender->workflow_status = $workflow->workflow_status;
                    $tender->workflow_values = $workflow->page . '-' . $stageType . '-' . $params['action_type'];
                    $tender->save();
                }
            }

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

            DB::commit();

            if ($params['action_type'] == TenderSubmissionEnum::FLOW_STATUS[1]) {
                (new TenderMailService)->sendEmailOnTenderStarted($tender, $stageType, $params);
            } else if ($params['action_type'] == TenderSubmissionEnum::FLOW_STATUS[3]) {
                (new TenderMailService)->sendEmailOnTenderResubmitted($tender, $stageType, $params);
            }

            return [
                'data' => $tender,
                'next' => null,
            ];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($this->logName . '::startStage error : ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * open pre qualification submission
     *
     * @param array $params
     * @param \App\TenderParameter $tender
     *
     * @return array data
     *
     * @throws \Exception
     */
    public function openStage($params, $tender, $stageType)
    {
        try {
            DB::beginTransaction();
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

            $submission = TenderVendorSubmission::where('tender_number', $tender->tender_number)
                ->where('submission_method', $stageType)
                ->get();

            foreach ($submission as $sub) {
                TenderVendorSubmission::deleteDraftStatus($sub, $params['action_type']);
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

    public function finishStage($params, $tender, $stageType)
    {
        try {
            DB::beginTransaction();
            $workflowValues = explode('-', $tender->workflow_values);
            $tender->workflow_values = (count($workflowValues) == 3)
                ? $workflowValues[0] . '-' . $workflowValues[1] . '-' . $params['action_type']
                : $workflowValues[0] . '-' . $params['action_type'];
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

            DB::commit();
            return [
                'data' => $tender,
                'next' => null,
            ];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($this->logName . '::finishStage error : ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * add vendor submission detail submission
     *
     * @param \App\TenderParameter $tender
     * @param string $stageType, stage type
     *
     * @return array data
     *
     * @throws \Exception
     */
    private function saveSubmissionDetail($tender, $stageType)
    {
        try {
            $vendor = Auth::user()->vendor;
            DB::beginTransaction();
            $bidDoc = TenderBiddingDocumentRequirement::where('tender_number', $tender->tender_number)
                ->where('submission_method', $stageType)
                ->get();
            if ($bidDoc->count() > 0) {
                foreach ($bidDoc as $doc) {
                    TenderVendorSubmissionDetail::create([
                        'tender_number' => $tender->tender_number,
                        'vendor_id' => $vendor->id,
                        'vendor_code' => $vendor->vendor_code,
                        'bidding_document_id' => $doc->line_id,
                        'submission_method' => $stageType,
                        'status' => TenderVendorSubmissionDetail::STATUS[1],
                        'order' => $doc->order,
                    ]);
                }

                if (
                    $stageType == TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE['process_technical_evaluation'] ||
                    $stageType == TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE['process_commercial_evaluation']
                ) {
                    (new TenderVendorItemRepository())->initialVendorItems([
                        'vendor_id' => $vendor->id,
                        'vendor_code' => $vendor->vendor_code,
                    ], $tender, $stageType);
                }
            }
            DB::commit();
            return [
                'data' => $tender,
                'next' => null,
            ];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($this->logName . '::saveSubmissionDetail error : ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * refresh vendor submission detail submission
     *
     * @param \App\TenderParameter $tender
     * @param string $stageType, stage type
     *
     * @return array data
     *
     * @throws \Exception
     */
    public function refreshSubmissionDetail($tender, $stageType)
    {
        try {
            $vendors = TenderVendor::where('tender_number', $tender->tender_number)
                ->whereIn('status', [TenderVendor::STATUS[2], TenderVendor::STATUS[4]])
                ->get();
            DB::beginTransaction();
            $bidDoc = TenderBiddingDocumentRequirement::where('tender_number', $tender->tender_number)
                ->where('submission_method', $stageType)
                ->where('public_status', TenderStatusEnum::PUBLIC_STATUS[2])
                ->withoutGlobalScope(PublicViewScope::class)
                ->get();
            if ($bidDoc->count() > 0) {
                if ($vendors->count() > 0) {
                    foreach ($bidDoc as $doc) {
                        foreach ($vendors as $vendor) {
                            TenderVendorSubmissionDetail::updateOrCreate([
                                'tender_number' => $tender->tender_number,
                                'vendor_id' => $vendor->id,
                                'bidding_document_id' => $doc->line_id,
                            ], [
                                'tender_number' => $tender->tender_number,
                                'vendor_id' => $vendor->vendor_id,
                                // 'vendor_code' => $vendor->vendor_code,
                                'bidding_document_id' => $doc->line_id,
                                'status' => TenderVendorSubmissionDetail::STATUS[1],
                                'order' => $doc->order,
                            ]);
                        }
                    }
                }
            }
            DB::commit();
            return [
                'data' => $tender,
                'next' => null,
            ];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($this->logName . '::refreshSubmissionDetail error : ' . $e->getMessage());
            throw $e;
        }
    }

    private function saveEvaluationNotes($params, $tender, $stageType)
    {
        try {
            DB::beginTransaction();
            $result = TenderEvaluationNote::updateOrCreate([
                'id' => $params['id'] ?? null,
            ], [
                'tender_number' => $tender->tender_number,
                'notes' => $params['notes'],
                'note_type' => $params['note_type'],
                'submission_method' => $stageType,
            ]);

            DB::commit();
            return [
                'data' => $result,
                'next' => null,
            ];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($this->logName . '::saveEvaluationNotes error : ' . $e->getMessage());
            throw $e;
        }
    }
    private function saveComments($params, $tender, $stageType)
    {
        try {
            DB::beginTransaction();
            $result = TenderComment::updateOrCreate([
                'id' => $params['id'] ?? null,
            ], [
                'tender_number' => $tender->tender_number,
                'user_id_from' => $params['user_id_from'],
                'from_name' => $params['from_name'],
                'user_id_to' => $params['user_id_to'],
                'to_name' => $params['to_name'] ?? '',
                'comments' => $params['comments'] ?? '',
                'status' => $params['status'] ?? 1,
                'submission_method' => $stageType,
            ]);

            DB::commit();
            return [
                'data' => $result,
                'next' => null,
            ];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($this->logName . '::saveComments error : ' . $e->getMessage());
            throw $e;
        }
    }
    private function saveAddInfo($params, $tender, $stageType)
    {
        try {
            DB::beginTransaction();
            $result = TenderParameter::updateOrCreate([
                'id' => $params['id'] ?? null,
            ], [
                'client_name' => $params['client_name'],
                'project_name' => $params['project_name'],
                'remarks' => $params['remarks'],
            ]);

            DB::commit();
            return [
                'data' => $result,
                'next' => null,
            ];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($this->logName . '::saveAddInfo error : ' . $e->getMessage());
            throw $e;
        }
    }
    /**
     * save upload document vendor submission detail
     *
     * @param array $params
     * @param \App\TenderParameter $tender
     *
     * @return array data
     *
     * @throws \Exception
     */
    private function uploadSubmissionDetail($params, $tender, $stageType)
    {
        try {
            $vendor = Auth::user()->vendor;
            DB::beginTransaction();

            $detail = null;
            if (!empty($params['id']) && $params['id'] != "null") {
                $detail = TenderVendorSubmissionDetail::where('tender_number', $tender->tender_number)
                    ->where('id', $params['id'])
                    ->first();
            }

            if (!$detail) {
                $detail = new TenderVendorSubmissionDetail([
                    'tender_number' => $tender->tender_number,
                    'vendor_id' => $vendor->id,
                    'vendor_code' => $vendor->vendor_code,
                    'bidding_document_id' => $params['line_id'],
                    'status' => TenderVendorSubmissionDetail::STATUS[1],
                    'order' => $params['order'] ?? 0,
                ]);
            }

            $detail->attachment = $params['attachment'];
            $detail->vendor_code = $vendor ? $vendor->vendor_code : null;
            $detail->save();

            // set vendor draft
            TenderVendorSubmission::updateOrCreate([
                'tender_number' => $tender->tender_number,
                'vendor_id' => $vendor->id,
                'submission_method' => $stageType,
            ], [
                'tender_number' => $tender->tender_number,
                'vendor_id' => $vendor->id,
                'submission_date' => Carbon::now(),
                'submission_method' => $stageType,
                'status' => TenderVendorSubmissionDetail::STATUS[1],
            ]);

            DB::commit();
            return [
                'data' => $detail,
                'next' => null,
            ];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($this->logName . '::uploadSubmissionDocument error : ' . $e->getMessage());
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

            $details = TenderVendorSubmissionDetail::select('tender_vendor_submission_detail.*')
                ->join('tender_bidding_document_requirements as tbdr', 'tbdr.line_id', 'tender_vendor_submission_detail.bidding_document_id')
                ->where('tender_vendor_submission_detail.tender_number', $tender->tender_number)
                ->where('tender_vendor_submission_detail.vendor_id', $vendor->id)
                ->where('tbdr.submission_method', $stageType)
                ->get();

            foreach ($details as $d) {
                $d->attachment = '';
                $d->vendor_code = $vendor ? $vendor->vendor_code : null;
                $d->save();
            }

            if (in_array($stageType, [
                TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE['process_technical_evaluation'],
                TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE['process_commercial_evaluation'],
                TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE['negotiation_commercial'],
            ])) {
                (new TenderVendorItemRepository())->resetInitialVendorItems([
                    'vendor_id' => $vendor->id,
                    'vendor_code' => $vendor->vendor_code,
                ], $tender, $stageType);
            }

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
    /**
     * submit submission detail
     *
     * @param \App\TenderParameter $tender
     * @param string $stageType, stage type
     *
     * @return array data
     *
     * @throws \Exception
     */
    private function submitSubmissionDetail($tender, $stageType, $status)
    {
        try {
            $vendor = Auth::user()->vendor;
            DB::beginTransaction();
            $data = [
                'tender_number' => $tender->tender_number,
                'vendor_id' => $vendor->id,
                'submission_date' => Carbon::now(),
                'submission_method' => $stageType,
                'status' => $status,
                'action_status' => TenderStatusEnum::ACT_NEW,
            ];

            TenderVendorSubmission::updateOrCreate([
                'tender_number' => $tender->tender_number,
                'vendor_id' => $vendor->id,
                'submission_method' => $stageType,
            ], $data);

            if ($stageType == TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE['negotiation_commercial']) {

                $data["submission_method"] = TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE['negotiation_technical'];
                // triger tender item technical negotiation
                TenderVendorSubmission::updateOrCreate([
                    'tender_number' => $tender->tender_number,
                    'vendor_id' => $vendor->id,
                    'submission_method' => TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE['negotiation_technical'],
                ], $data);

                $data["submission_method"] = $stageType;

                // update negotiation_status = submitted
                TenderVendor::updateOrCreate([
                    'tender_number' => $tender->tender_number,
                    'vendor_id' => $vendor->id,
                ], [
                    'tender_number' => $tender->tender_number,
                    'vendor_id' => $vendor->id,
                    'negotiation_status' => $status,
                ]);
            }

            if ($stageType == TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE['process_commercial_evaluation']) {
                (new TenderVendorItemRepository())->_submitTenderVendorTechnicalSkip([
                    'vendor_id' => $vendor->id,
                    'vendor_code' => $vendor->vendor_code,
                ], $tender);
            }

            // reset tender score on vendor submit or resubmit
            TenderVendorScores::where('tender_number', $tender->tender_number)
                ->where('submission_method', $stageType)
                ->update([
                    'score' => 0,
                ]);

            DB::commit();
            return [
                'data' => $tender,
                'next' => null,
            ];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($this->logName . '::submitSubmissionDetail error : ' . $e->getMessage());
            throw $e;
        }
    }
    /**
     * delete submission detail
     *
     * @param \App\TenderParameter $tender
     * @param string $pageType, workflow page type
     *
     * @return array data
     *
     * @throws \Exception
     */
    private function deleteSubmissionDetail($tender, $params, $pageType)
    {
        try {
            $vendor = Auth::user()->vendor;
            DB::beginTransaction();
            $detail = TenderVendorSubmissionDetail::where('tender_number', $tender->tender_number)
                ->where('id', $params['id'])
                ->firstOrFail();
            $detail->attachment = '';
            $detail->vendor_code = $vendor ? $vendor->vendor_code : null;
            $detail->save();

            $result = TenderVendorSubmissionDetail::where('tender_number', $tender->tender_number)
                ->where('line_id', $detail->line_id)
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
    /**
     * submit submission detail
     *
     * @param array params
     * @param \App\TenderParameter $tender
     *
     * @return array data
     *
     * @throws \Exception
     */
    private function evaluateSubmissionDetail($params, $tender)
    {
        try {
            DB::beginTransaction();
            $detail = TenderVendorSubmissionDetail::where('tender_number', $tender->tender_number)
                ->where('id', $params['id'])
                ->firstOrFail();
            $detail->status = $params['status'];
            $detail->save();
            DB::commit();
            return [
                'data' => $detail,
                'next' => null,
            ];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($this->logName . '::evaluateSubmissionDetail error : ' . $e->getMessage());
            throw $e;
        }
    }
    /**
     * evaluate submission
     *
     * @param array params
     * @param \App\TenderParameter $tender
     *
     * @return array data
     *
     * @throws \Exception
     */
    private function evaluateSubmission($params, $tender)
    {
        try {
            DB::beginTransaction();
            $detail = TenderVendorSubmission::where('tender_number', $tender->tender_number)
                ->where('id', $params['id'])
                ->firstOrFail();
            $detail->status = $params['status'];
            $detail->save();
            DB::commit();
            return [
                'data' => $detail,
                'next' => null,
            ];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($this->logName . '::evaluateSubmission error : ' . $e->getMessage());
            throw $e;
        }
    }
    private function evaluateSubmission2Score($params, $tender, $stageType)
    {
        try {
            DB::beginTransaction();
            $detail = TenderVendorSubmission::where('tender_number', $tender->tender_number)
                ->whereIn('submission_method', $stageType)
                ->where('vendor_id', $params['vendor_id'])
                ->update(['status' => $params['status']]);
            DB::commit();
            return [
                'data' => $detail,
                'next' => null,
            ];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($this->logName . '::evaluateSubmission2Score error : ' . $e->getMessage());
            throw $e;
        }
    }
    /**
     * complete evaluation
     *
     * @param \App\TenderParameter $tender
     * @param string $pageType, workflow page type
     * @param array params
     *
     * @return array data
     *
     * @throws \Exception
     */
    public function completeEvaluation($tender, $params, $pageType)
    {
        try {
            $stageType = !empty($params['stage_type'])
                ? $params['stage_type']
                : TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE[$pageType];

            DB::beginTransaction();
            TenderWorkflow::where('tender_number', $tender->tender_number)
                ->where('page', $pageType)
                ->update(['is_done' => 1]);

            $pages = (new TenderWorkflowHelper())->getAllPages($tender);
            $idx = array_search($pageType, $pages);
            $next = '';
            if ($idx !== false) {
                $nextPage = $pages[$idx + 1];
                $next = route('tender.show', ['id' => $tender->id, 'type' => $nextPage]);
                // update tender parameter status
                $workflow = TenderWorkFlow::where('tender_number', $tender->tender_number)
                    ->where('page', $nextPage)
                    ->first();
                if (!is_null($workflow)) {
                    $tender->status = $workflow->status;
                    $tender->workflow_status = $workflow->workflow_status;
                    $tender->workflow_values = $workflow->page; // .'-'.$params['action_type'];
                    $tender->save();
                }
            }

            DB::commit();

            (new TenderMailService)->sendEmailOnTenderEvaluate($tender, $stageType);

            return [
                'data' => $tender,
                'next' => $next,
            ];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($this->logName . '::completeEvaluation error : ' . $e->getMessage());
            throw $e;
        }
    }
    private function saveScoring($params, $tender, $stageType)
    {
        try {
            DB::beginTransaction();
            // delete before save;
            TenderVendorScores::where('tender_number', $tender->tender_number)
                ->where('vendor_id', $params['scores'][0]['vendor_id'])
                ->delete();

            if (isset($params['scores']) && count($params['scores']) > 0) {
                $scores = 0;
                foreach ($params['scores'] as $score) {
                    TenderVendorScores::create([
                        'tender_number' => $tender->tender_number,
                        'weight_id' => $score['weight_id'],
                        'vendor_id' => $score['vendor_id'],
                        'vendor_code' => $score['vendor_code'],
                        'submission_method' => $stageType,
                        'score' => $score['score'],
                    ]);
                    $scores += $score['weight'] / 100 * $score['score'];
                }
                TenderVendorSubmission::where('tender_number', $tender->tender_number)
                    ->where('submission_method', $stageType)
                    ->where('vendor_id', $params['scores'][0]['vendor_id'])
                    ->update(['score' => $scores]);
            }

            DB::commit();
            return [
                'data' => $tender,
                'next' => null,
            ];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($this->logName . '::saveScoring error : ' . $e->getMessage());
            throw $e;
        }
    }

    protected function saveTenderHeader($params, $tender, $stageType)
    {
        try {
            DB::beginTransaction();
            $data = [
                'tender_number' => $tender->tender_number,
                'vendor_id' => $params['vendor_id'],
                'vendor_code' => $params['vendor_code'],
                'quotation_number' => $params['quotation_number'],
                'quotation_date' => !empty($params['quotation_date'])
                    ? Carbon::createFromFormat(BaseModel::DATETIME_FORMAT, $params['quotation_date'])->format(BaseModel::DB_DATETIME_FORMAT)
                    : null,
                'quotation_note' => $params['quotation_note'],
                'status' => $params['status'] ?? TenderSubmissionEnum::STATUS_ITEM[1],
            ];
            if (!empty($params['quotation_file']) && $params['quotation_file'] != 'undefined') {
                $data['quotation_file'] = $params['quotation_file'];
            }

            $result = '';
            if (
                $stageType == TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE['process_technical_evaluation'] ||
                $stageType == TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE['negotiation_technical']
            ) {
                $data['tkdn_percentage'] = $params['tkdn_percentage'] ?? null;
                if (!empty($params['proposed_item_file']) && $params['proposed_item_file'] != 'undefined') {
                    $data['proposed_item_file'] = $params['proposed_item_file'];
                }
                if (!empty($params['tkdn_file']) && $params['tkdn_file'] != 'undefined') {
                    $data['tkdn_file'] = $params['tkdn_file'];
                }
                $data['submission_method'] = $stageType;
                if (!empty($params['id'])) {
                    $model = TenderHeaderTechnical::where('id', $params['id'])->first();
                    $result = $model->fill($data)->save();
                } else {
                    $result = TenderHeaderTechnical::create($data);
                }
            } else if (
                $stageType == TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE['process_commercial_evaluation'] ||
                $stageType == TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE['negotiation_commercial']
            ) {
                $data['incoterm'] = $params['incoterm'] ?? null;
                $data['incoterm_location'] = $params['incoterm_location'] ?? null;
                $data['bid_bond_value'] = $params['bid_bond_value'] ?? null;
                $data['currency_code'] = $params['currency_code'] ?? null;
                $data['bid_bond_end_date'] = $params['bid_bond_end_date'] ?? null;
                if (!empty($params['bid_bond_file']) && $params['bid_bond_file'] != 'undefined') {
                    $data['bid_bond_file'] = $params['bid_bond_file'];
                }
                $data['submission_method'] = $stageType;

                if (!empty($params['id'])) {
                    // $model = TenderHeaderCommercial::where('id', $params['id'])->first();
                    $model = TenderHeaderCommercial::where('tender_number', $tender->tender_number)
                        ->where('submission_method', $stageType)
                        ->where('vendor_id', $params['vendor_id'])
                        ->first();

                    if ($model && $model->currency_code != $data['currency_code']) {
                        (new TenderVendorItemRepository)->updateCurrencyCode($tender, $params['vendor_id'], $params['currency_code'], $stageType);
                    }
                    if(!$model) $model = new TenderHeaderCommercial();
                    $result = $model->fill($data)->save();
                    // $result = TenderHeaderCommercial::where('id', $params['id'])->update($data);
                } else {
                    $result = TenderHeaderCommercial::create($data);
                    (new TenderVendorItemRepository)->updateCurrencyCode($tender, $params['vendor_id'], $params['currency_code'], $stageType);
                }
            }

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
                'data' => $result,
                'next' => null,
            ];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($this->logName . '::saveTenderHeader error : ' . $e->getMessage());
            throw $e;
        }
    }
    protected function saveTenderItems($params, $tender, $stageType)
    {
        return (new TenderVendorItemRepository)->saveTenderVendorItems($params, $tender, $stageType);
    }
    public function saveNegotiation($params, $tender, $type)
    {
        try {
            if (isset($params['vendor_id'])) {
                DB::beginTransaction();

                $tenderVendor = TenderVendor::where('tender_number', $tender->tender_number)
                    ->where('vendor_id', $params['vendor_id'])->first();
                if ($params['action_type'] == 'start') {
                    if ($tenderVendor) {
                        $tenderVendor->negotiation_status = 'started';
                        $tenderVendor->save();
                    }
                } else if ($params['action_type'] == 'finish') {
                    if ($tenderVendor) {
                        $tenderVendor->negotiation_status = 'finished';
                        $tenderVendor->save();
                    }
                }

                DB::commit();
                return [
                    'data' => $tender,
                    'next' => null,
                ];
            } else {
                return $this->saveProcess($params, $tender, $type);
            }
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($this->logName . '::saveNegotiation error : ' . $e->getMessage());
            Log::error($e);
            throw $e;
        }
    }
}
