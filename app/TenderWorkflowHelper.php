<?php

namespace App;

use App\Enums\TenderStatusEnum;
use App\Models\TenderEvaluator;
use App\Models\TenderVendor;
use App\Repositories\TenderEvaluatorRepository;
use App\Repositories\TenderProcessAwardingRepository;
use App\Repositories\TenderVendorRepository;
use App\Repositories\TenderProcessRepository;
use App\TenderWorkflow;
use DB;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Schema;

class TenderWorkflowHelper
{
    private $logName = 'TenderWorkflowHelper';

    const WorkflowValues = [
        'process_registration' => ['process_registration'],
        'process_prequalification' => [
            'process_prequalification-start', 'process_prequalification-request_resubmission',
            'process_prequalification-open', 'process_prequalification-open_resubmission',
            'process_prequalification-1-start', 'process_prequalification-1-request_resubmission',
            'process_prequalification-1-open', 'process_prequalification-1-open_resubmission'
        ],
        'process_bid_opening' => [
            'process_tender_evaluation',
            'process_tender_evaluation-3-start', 'process_tender_evaluation-3-request_resubmission',
            'process_tender_evaluation-3-open', 'process_tender_evaluation-3-open_resubmission',
            'process_tender_evaluation-4-start', 'process_tender_evaluation-4-request_resubmission',
            'process_tender_evaluation-4-open', 'process_tender_evaluation-4-open_resubmission',
            'process_technical_evaluation', 'process_commercial_evaluation',
            'process_technical_evaluation-3-start', 'process_technical_evaluation-3-request_resubmission',
            'process_commercial_evaluation-3-start', 'process_commercial_evaluation-4-request_resubmission',
            'process_tender_evaluation-3-finish',
        ],
        'process_bid_evaluation' => [
            'process_tender_evaluation-4-finish', 'process_commercial_evaluation-4-finish',
            'process_technical_evaluation-open', 'process_technical_evaluation-open_resubmission',
            'process_commercial_evaluation-open', 'process_commercial_evaluation-open_resubmission',
            'process_technical_evaluation-3-open', 'process_technical_evaluation-3-open_resubmission',
            'process_commercial_evaluation-4-open', 'process_commercial_evaluation-4-open_resubmission'

        ],
    ];

    public $workflows;
    public $pages;
    public $pagesAvailable;
    public $tenderStatusOptions;
    public $guarded;

    public function __construct()
    {
        $this->workflows = config('workflow.tender.order');
        $this->pages = config('workflow.tender.pages');
        $this->pagesAvailable = config('workflow.tender.pages_available');
        $this->tenderStatusOptions = config('eproc.tender_status_options');
        $this->guarded = ['id', 'created_at', 'created_by', 'updated_at', 'updated_by', 'deleted_at', 'deleted_by'];
    }

    public function getWorkflowStart()
    {
        foreach ($this->workflows as $wfKey => $wfValue) {
            break;
        }
        foreach ($this->tenderStatusOptions as $tKey => $tValue) {
            break;
        }
        return array(
            'tender_status' => $tKey,
            'workflow_status' => $wfKey,
            // 'workflow_values' => json_encode($wfValue),
        );
    }

    public function startWorkflow($tenderId)
    {
        $sequence = 0;
        $array = [];
        $status = config('workflow.tender.status');
        $pageStatus = config('workflow.tender.page_status');
        foreach ($this->workflows as $k => $wf) {
            foreach ($wf as $value) {
                $sequence++;
                foreach ($this->pagesAvailable[$value] as $page) {
                    $array[] = [
                        'tender_number' => $tenderId,
                        'status' => $status[$pageStatus[$page]],
                        'workflow_status' => $k,
                        'page' => $page,
                        'sequence' => $sequence,
                        'created_at' => now(),
                    ];
                }
            }
        }
        return TenderWorkflow::insert($array);
    }

    public function restartWorkflow($tenderId)
    {
        $sequence = 0;
        $status = config('workflow.tender.status');
        $pageStatus = config('workflow.tender.page_status');
        TenderWorkflow::where('tender_number', $tenderId)
            ->whereIn('page', ['process_bid_opening', 'process_bid_evaluation'])
            ->forceDelete();
        $tender = TenderParameter::where('tender_number', $tenderId)
            ->where('workflow_values', 'process_bid_opening')
            ->first();

        if ($tender) {
            if ($tender->submission_method == '1E')
                $tender->workflow_values = 'process_tender_evaluation';
            else if ($tender->submission_method == '2E')
                $tender->workflow_values = 'process_technical_evaluation';

            $tender->update();
        }


        foreach ($this->workflows as $k => $wf) {
            foreach ($wf as $value) {
                $sequence++;
                foreach ($this->pagesAvailable[$value] as $page) {
                    $data = [
                        'tender_number' => $tenderId,
                        'status' => $status[$pageStatus[$page]],
                        'workflow_status' => $k,
                        'page' => $page,
                        'sequence' => $sequence,
                        'created_at' => now(),
                    ];
                    $res = TenderWorkflow::where('tender_number', $tenderId)
                        ->where('page', $page)
                        ->update($data);
                    if (!$res) {
                        TenderWorkflow::insert($data);
                    }
                }
            }
        }
        return true;
    }

    public function getCurrentStatus($tender)
    {
        $excludes = $this->pageExcludes($tender);
        return TenderWorkflow::where('tender_number', $tender->tender_number)
            ->where('is_done', 0)
            ->orderBy('sequence', 'asc')
            ->whereNotIn('page', $excludes)
            ->orderBy('id', 'asc')
            ->first();
    }
    public function getCurrentSequence($tender)
    {
        $excludes = $this->pageExcludes($tender);
        //get smallest sequence number where is_done = 0 for tenderNumber
        $sequence = TenderWorkflow::where('tender_number', $tender->tender_number)
            ->where('is_done', 0)
            ->whereNotIn('page', $excludes)
            ->min('sequence');

        //already finished for current vendor
        if (is_null($sequence)) {
            $sequence = TenderWorkflow::where('tender_number', $tender->tender_number)
                ->max('sequence');
        }

        return $sequence;
    }

    public function getCurrentAvailable($tender)
    {
        $tenderNumber = $tender->tender_number;
        //return available pages by current sequences
        $sequence = $this->getCurrentSequence($tender);
        $editables = [];
        $availables = [];

        $workflowList = TenderWorkflow::select(
            '*',
            DB::raw($sequence . '=sequence as editable')
        )
            ->where('tender_number', $tenderNumber)
            ->where('sequence', '<=', $sequence)
            ->orderBy('sequence', 'asc')
            ->orderBy('id', 'asc')
            ->get();
        //dd($tender, $tenderNumber, $sequence,$workflowList);

        $processPrequalificationDone = true;
        $processBidOpeningDone = true;
        foreach ($workflowList as $workflow) {
            if ($tender->aanwijzing != 1 && $workflow->page == 'aanwijzings') {
                continue;
            }
            if ($tender->prequalification != 1 && $workflow->page == 'process_prequalification') {
                continue;
            }
            if ($tender->auctions != 1 && $workflow->page == 'auction') {
                continue;
            }
            if ($tender->submission_method != '1E' && $workflow->page == 'process_tender_evaluation') {
                continue;
            }
            if (
                $tender->submission_method == '1E' &&
                in_array($workflow->page, ['process_technical_evaluation', 'process_commercial_evaluation'])
            ) {
                continue;
            }

            if ($workflow->editable) {
                if ($workflow->page == 'process_prequalification') {
                    $processPrequalificationDone = $workflow->is_done == 1;
                    $editables[] = $workflow->page;
                } else if ($workflow->page == 'process_tender_evaluation') {
                    if ($processPrequalificationDone) {
                        $processBidOpeningDone = $workflow->is_done == 1;
                        $editables[] = $workflow->page;
                    } else {
                        continue;
                    }
                } else if ($workflow->page == 'process_technical_evaluation') {
                    if ($processPrequalificationDone) {
                        $processBidOpeningDone = $workflow->is_done == 1;
                        $editables[] = $workflow->page;
                    } else {
                        continue;
                    }
                } else if ($workflow->page == 'process_commercial_evaluation') {
                    if ($processPrequalificationDone) {
                        if ($tender->submission_method == '2E') {
                            $editables[] = $workflow->page;
                        } else if ($tender->submission_method == '2S' && $processBidOpeningDone) {
                            $editables[] = $workflow->page;
                        } else {
                            continue;
                        }
                    } else {
                        continue;
                    }
                } else {
                    $editables[] = $workflow->page;
                }
            } else {
                if ($workflow->page == 'parameters' && $tender->workflow_status == 'tender_requirements') {
                    $editables[] = $workflow->page;
                }
            }
            $availables[] = $workflow->page;
        }
        return ['editables' => $editables, 'availables' => $availables];
    }

    public function getData($type, $id)
    {
        $data = [];
        try {
            foreach ($this->pages[$type] as $table) {
                $fields = [];
                foreach (Schema::getColumnListing($table) as $field) {
                    if (!in_array($field, $this->guarded)) $fields[] = $field;
                }
                $data[$table] = [
                    'data' => DB::table($table)
                        ->where('tender_number', $id)
                        ->whereNull('deleted_at')
                        ->get(),
                    'fields' => $fields,
                ];
            }
        } catch (Exception $e) {
            $data[$table] = [
                'data' => null,
                'fields' => $fields,
            ];
            Log::error($this->logName . '.getNextPage, ' . $e->getMessage());
            Log::error($e);
            // throw $e;
        }
        return $data;
    }
    public function getTable($type)
    {
        if (array_key_exists($type, $this->pages)) {
            return $this->pages[$type];
        } else {
            return null;
        }
    }
    public function donePage($page, $tender)
    {
        $sequence = $this->getCurrentSequence($tender);
        $workflow = TenderWorkFlow::where('tender_number', $tender->tender_number)
            ->where('sequence', $sequence)
            ->where('status', strtolower($tender->status))
            ->where('workflow_status', $tender->workflow_status)
            ->where('page', $page)
            ->where('is_done', 0)
            ->first();
        if (!is_null($workflow)) {
            $workflow->is_done = 1;
            $workflow->save();
        }

        return $this->getCurrentAvailable($tender);
    }

    public function doneSequence($prevPage, $nextPage, $tender)
    {
        // update all page as done for previous workflow sequence
        $prevWorkflow = TenderWorkFlow::where('tender_number', $tender->tender_number)
            ->where('page', $prevPage)
            ->first();
        TenderWorkFlow::where('tender_number', $tender->tender_number)
            ->where('sequence', $prevWorkflow->sequence)
            ->where('is_done', 0)
            ->update(['is_done' => 1]);

        // update tender parameter status
        $workflow = TenderWorkFlow::where('tender_number', $tender->tender_number)
            ->where('page', $nextPage)
            ->first();
        if (!is_null($workflow)) {
            $tender->status = $workflow->status;
            $tender->workflow_status = $workflow->workflow_status;
            $tender->workflow_values = $workflow->page;
            // $tender->public_status = TenderStatusEnum::PUBLIC_STATUS[2];
            $tender->save();
        }

        return $this->getCurrentAvailable($tender);
    }

    public function doneApprovalProposal($status, $tender, $page)
    {
        $pages = $this->getAllPages($tender);
        $idx = array_search($page, $pages);

        // update all page as done for previous workflow sequence
        $curWorkflow = TenderWorkFlow::where('tender_number', $tender->tender_number)
            ->where('page', $page)
            ->first();

        $nextPage = null;
        if ($status == 'rejected') {
            $nextPage = $pages[$idx];
            $workflow = TenderWorkFlow::where('tender_number', $tender->tender_number)
                ->where('page', $nextPage)
                ->first();

            $tender->status = $workflow->status;
            $tender->workflow_status = $workflow->workflow_status;
            $tender->workflow_values = $workflow->page . '-rejected';
            $tender->public_status = TenderStatusEnum::PUBLIC_STATUS[1];
            $tender->save();
        } else if ($status == 'approved') {
            // updated workflow next
            TenderWorkFlow::where('tender_number', $tender->tender_number)
                ->where('sequence', $curWorkflow->sequence)
                ->where('is_done', 0)
                ->update(['is_done' => 1]);

            $nextPage = $pages[$idx + 1];
            $workflow = TenderWorkFlow::where('tender_number', $tender->tender_number)
                ->where('page', $nextPage)
                ->first();

            // update tender parameter status
            if (!is_null($workflow)) {
                $tender->status = $workflow->status;
                $tender->workflow_status = $workflow->workflow_status;
                $tender->workflow_values = $workflow->page;
                $tender->public_status = TenderStatusEnum::PUBLIC_STATUS[2];
                $tender->save();
            }
        }



        return [
            'tender' => $tender,
            'page' => $nextPage,
        ];
    }

    public function getAllPages($tender = null)
    {
        if ($tender == null) {
            return array_keys($this->pages);
        } else {
            $query = TenderWorkFlow::where('tender_number', $tender->tender_number)
                ->orderBy('sequence')
                ->orderBy('id');
            $excludes = $this->pageExcludes($tender);
            $arrPages = $query->whereNotIn('page', $excludes)
                ->pluck('page')
                ->toArray();
            return $arrPages = array_diff($arrPages, $excludes);
        }
    }

    private function pageExcludes($tender)
    {
        $excludes = [];
        if ($tender->aanwijzing != 1) {
            $excludes[] = 'aanwijzings';
        }
        if ($tender->prequalification != 1) {
            $excludes[] = 'process_prequalification';
        }
        if ($tender->auctions != 1) {
            $excludes[] = 'auction';
        }

        if ($tender->submission_method != '1E') {
            $excludes[] = 'process_tender_evaluation';
        }
        if ($tender->submission_method == '1E') {
            $excludes[] = 'process_technical_evaluation';
            $excludes[] = 'process_commercial_evaluation';
        }
        if (Auth::user()->isVendor()) {
            $vendor = Auth::user()->vendor;
            $excludes[] = 'awarding_approval';
            if ($tender->visibility_bid_document == 'PRIVATE') {
                if (!(new TenderProcessRepository)->isVendorWinning($tender->tender_number, $vendor->id)) {
                    $excludes[] = 'negotiation';
                    $excludes[] = 'awarding_process';
                    $excludes[] = 'po_creation';
                }
            }

            $isAllowPageAwarding = (new TenderProcessAwardingRepository)->isSubmitAwaridngByVendor($tender->tender_number, $vendor->id);
            if (!$isAllowPageAwarding) {
                $excludes[] = 'awarding_process';
                $excludes[] = 'po_creation';
            }

            $isAllowPagePOCreation = (new TenderProcessAwardingRepository)->isNextPOCreationByVendor($tender->tender_number, $vendor->id);
            if (!$isAllowPagePOCreation) {
                $excludes[] = 'po_creation';
            }
        }
        return $excludes;
    }

    /**
     * user allows access by tender permission
     *
     * @param $permissionName
     * @param $tenderNumber
     *
     * @return bool
     */
    public static function can($permissionName, $tenderNumber)
    {
        $user = Auth::user();
        if ($user->hasRole('Super Admin')) return true;
        if ($user) {
            $query = TenderEvaluator::join('tender_evaluator_has_permissions as tehp', 'tehp.evaluator_id', 'tender_evaluators.line_id')
                ->join('tender_permissions as tp', 'tp.id', 'tehp.permission_id')
                ->where('tender_number', $tenderNumber)
                ->where('buyer_user_id', $user->id);
            // ->where('tp.name', $permissionName)
            // ->count() > 0;
            if (is_array($permissionName)) {
                $query = $query->whereIn('tp.name', $permissionName);
            } else {
                $query = $query->where('tp.name', $permissionName);
            }
            return $query->count() > 0;
        }
        return false;
    }

    /**
     * user has access by tender permission
     *
     * @param $permissionName
     * @param $tenderNumber
     *
     * @return bool
     */
    public static function has($permissionName, $tenderNumber = '')
    {
        $user = Auth::user();
        if ($user->hasRole('Super Admin')) return true;
        if ($user) {
            $query = TenderEvaluator::join('tender_evaluator_has_permissions as tehp', 'tehp.evaluator_id', 'tender_evaluators.line_id')
                ->join('tender_permissions as tp', 'tp.id', 'tehp.permission_id')
                ->where('buyer_user_id', $user->id);

            if (is_array($permissionName)) {
                $query = $query->whereIn('tp.name', $permissionName);
            } else {
                $query = $query->where('tp.name', $permissionName);
            }

            if (!empty($tenderNumber)) {
                $query = $query->where('tender_number', $tenderNumber);
            }
            return $query->count() > 0;
        }
        return false;
    }

    /**
     * user has access by tender permission
     *
     * @param $tender
     * @param $user
     *
     * @return bool
     */
    public static function isAllowTender($tender, $user)
    {
        $tenderNumber = $tender->tender_number;
        if (!$user->isVendor()) {
            if ($user->hasRole('Super Admin')) return true;
            if ($tender->created_by == $user->userid) return true;
            $isTenderTeam = (new TenderEvaluatorRepository)->findByTenderNumber($tenderNumber, $user->id);
            return $isTenderTeam != null;
        } else if ($user->isVendor()) {
            $proposedVendor = (new TenderVendorRepository)->findByVendor($tenderNumber, $user->vendor->id);
            return ($proposedVendor != null) && in_array($proposedVendor->status, [TenderVendor::STATUS[2], TenderVendor::STATUS[4]]);
        }
        return false;
    }

    public static function getWorkflowStatusText($tender)
    {
        if ($tender->workflow_status == 'tender_process') {
            $status = $tender->workflow_values;
            foreach (static::WorkflowValues as $key => $val) {
                if (in_array($tender->workflow_values, $val)) {
                    $status = $key;
                    break;
                }
            }
            return __('tender.tender_w_status.' . $status);
        } else if ($tender->workflow_status == 'procurement_approval') {
            $status = $tender->workflow_values;
            foreach (static::WorkflowValues as $key => $val) {
                if (in_array($tender->workflow_values, $val)) {
                    $status = $key;
                    break;
                }
            }
            return __('tender.tender_w_status.' . $status);
        }

        return __('tender.tender_w_status.' . $tender->workflow_status);
    }
    public static function getWorkflowValues($_key)
    {
        foreach (static::WorkflowValues as $key => $val) {
            if ($key == $_key) {
                return $val;
            }
        }
        return null;
    }

    public static function reopenWorkflow($tender, $flow)
    {
        TenderWorkflow::where('tender_number', $tender->tender_number)
            ->where('workflow_status', $flow)
            ->update(['is_done' => 0]);
    }
    public static function getWorkflowStatusOption()
    {
        $optionsList = trans('tender.tender_w_status');
        $excludeOption = ['awarding_approval','po_creation','awarding'];
        return array_diff_key($optionsList, array_flip($excludeOption));
    }
}
