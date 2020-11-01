<?php

namespace App\Http\Controllers;

use App\Enums\TenderSubmissionEnum;
use App\Models\TenderVendor;
use App\Models\TenderVendorSubmission;
use App\RefListOption;
use App\RefPurchaseGroup;
use App\RefPurchaseOrg;
use App\Repositories\TenderRepository;
use App\Repositories\TenderVendorRepository;
use App\TenderWorkflowHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use DB;

class AnnouncementController extends Controller
{
    protected $workflow;
    protected $repo;
    protected $fields = [];
    const FIELDS = ['tender_number','title','scope_of_work','tender_method','winning_method','submission_method','status','workflow_status','created_at','evaluation_method','internal_organization','purchase_organization'];

    public function __construct(TenderRepository $repo)
    {
        $this->middleware('auth', ['only' => ['tenderFollowed']]);
        $this->workflow = new TenderWorkflowHelper();
        $this->repo = $repo;
        $this->fields = $this::FIELDS;
    }

    public function index()
    {
        return 'not found';
    }

    public function open()
    {
        // set siion for sender sub menu back
        session()->put('tender_menu_back', url()->current());
        $vendor = Auth::user()->vendor ?? null;
        return view('announcements.open', array_merge($this->defaultViewData(),[
            'title' => __('tender.tender_open_title'),
            'fields' => $this->getFields(),
            'type' => 'open',
            'vendor' => $vendor,
            'isVendor' => $vendor != null,
        ]));
    }
    public function open_page()
    {
        // set siion for sender sub menu back
        session()->put('tender_menu_back', url()->current());
        $vendor = Auth::user()->vendor ?? null;
        return view('announcements.open_page', array_merge($this->defaultViewData(),[
            'title' => __('tender.tender_open_title'),
            'fields' => $this->getFields(),
            'type' => 'open',
            'vendor' => $vendor,
            'isVendor' => $vendor != null,
        ]));
    }

    public function tender()
    {
        // set session for sender sub menu back
        session()->put('tender_menu_back', url()->current());
        $vendor = Auth::user()->vendor ?? null;
        return view('announcements.tender', array_merge($this->defaultViewData(),[
            'title' => __('tender.tender_invitation_title'),
            'fields' => $this->getFields(),
            'type' => 'tender',
            'vendor' => $vendor,
            'isVendor' => $vendor != null,
        ]));
    }

    public function tenderFollowed()
    {
        // set session for sender sub menu back
        session()->put('tender_menu_back', url()->current());
        $vendor = Auth::user()->vendor ?? null;
        return view('announcements.tender_followed', array_merge($this->defaultViewData(),[
            'title' => __('tender.tender_followed_title'),
            'fields' => $this->getFields(),
            'type' => 'tender_followed',
            'vendor' => $vendor,
            'isVendor' => $vendor != null,
        ]));
    }

    private function defaultViewData()
    {
        return [
            'purchGroups' => RefPurchaseGroup::pluck('description', 'id'),
            'purchOrgs' => RefPurchaseOrg::pluck('description', 'id'),
            'tenderMethod' => RefListOption::where('type', 'tender_method_options')->where('deleteflg', false)->pluck('value', 'key'),
            'winningMethod' => RefListOption::where('type', 'winning_method_options')->where('deleteflg', false)->pluck('value', 'key'),
            'submissionMethod' => RefListOption::where('type', 'submission_method_options')->where('deleteflg', false)->pluck('value', 'key'),
            'evaluationMethod' => RefListOption::where('type', 'evaluation_method_options')->where('deleteflg', false)->pluck('value', 'key'),
            'workflowStatus' => TenderWorkflowHelper::getWorkflowStatusOption(), // trans('tender.tender_w_status'),
            'tenderStatus' => trans('tender.tender_status'),
        ];
    }

    public function saveTenderVendor($action, Request $request)
    {
        $tender = $this->repo->findTenderParameterById($request->input('id'), true);
        $params = [
            'tender_number' => $tender->tender_number,
            'vendor_id' => $request->input('vendor_id'),
            'tender_vendor_type' => $action == TenderVendor::STATUS[4] ? 2 : 1,
            'status' => $action,
        ];

        if($action == TenderVendor::STATUS[4]) {
            $result = (new TenderVendorRepository())->save($params);
        }else{
            $result = (new TenderVendorRepository())->updateByVendorId($tender, $params);
        }

        return response()->json([
            'status' => 200,
            'success' => true,
            'message' => 'data_saved',
            'data' => $result,
        ]);
    }

    public function openDatatable($type = null)
    {
        if (request()->ajax()) {
            $data = $this->repo->findAnnouncedTender($type);
            return DataTables::eloquent($data)
                ->filterColumn('workflow_status', function($query, $keyword) {
                    $values = TenderWorkflowHelper::getWorkflowValues($keyword);
                    if(!empty($values)){
                        $query->whereIn('workflow_values', $values);
                    }else{
                        $query->where('workflow_status', $keyword);
                    }
                })
                ->filterColumn('purchase_organization', function($query, $keyword) {
                    // $query->where('purchase_org_id', $keyword);
                    $query->where(DB::raw("LOWER(CAST(\"purchase_org_id\" as TEXT)) LIKE '%{$keyword}%'"));
                })
                ->filterColumn('internal_organization', function($query, $keyword) {
                    // $query->where('purchase_group_id', $keyword);
                    $query->where(DB::raw("LOWER(CAST(\"purchase_group_id\" as TEXT)) LIKE '%{$keyword}%'"));
                })
                ->filterColumn('pr_number', function($query, $keyword) {
                    $query->where('ti.pr_number', 'like', '%'.$keyword.'%');
                })
                ->addColumn('status_text', function ($row) {
                    // cek jika vendor tidak lulus process tender
                    if($row->submission_status && $row->submission_status == TenderVendorSubmission::STATUS[4]){
                        // $pages = explode('-',$row->workflow_values);
                        // if($pages && (!empty(TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE[$pages[0]]) && TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE[$pages[0]] != $row->vendor_submission_method)){
                            return __('tender.process_status.' . strtolower($row->submission_status));
                        // }
                    }
                    return __('tender.tender_status.' . strtolower($row->status));
                })
                ->editColumn('submission_status', function ($row) {
                    // cek jika vendor tidak lulus process tender
                    if($row->submission_status && $row->submission_status == TenderVendorSubmission::STATUS[4]){
                        // $pages = explode('-',$row->workflow_values);
                        // if($pages && (!empty(TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE[$pages[0]]) && TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE[$pages[0]] != $row->vendor_submission_method)){
                            return $row->submission_status;
                        // }
                    }
                    return null;
                })
                ->addColumn('workflow_status_text', function ($row) {
                    if($row->visibility_bid_document == 'PRIVATE'){
                        return '';
                    }
                    return TenderWorkflowHelper::getWorkflowStatusText($row);
                })

                ->addColumn('submission_method_text', function ($row) {
                    return __('tender.' . $row->submission_method_value);
                })
                ->addColumn('evaluation_method_text', function ($row) {
                    return __('tender.' . $row->evaluation_method_value);
                })
                ->addColumn('tender_method_text', function ($row) {
                    return __('tender.' . $row->tender_method_value);
                })
                ->addColumn('winning_method_text', function ($row) {
                    return __('tender.' . $row->winning_method_value);
                })
                ->addColumn('tender_vendor_status_text', function ($row) {
                    return __('tender.tender_vendor_status.' . $row->tender_vendor_status);
                })

                ->make(true);
        }
    }

    private function getFields()
    {
        return $this->fields;
    }

    public function getViewData(){
        return $this->defaultViewData();
    }
}
