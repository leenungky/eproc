<?php

namespace App\Http\Controllers;

use App\Enums\TenderStatusEnum;
use App\Enums\TenderSubmissionEnum;
use App\Http\Requests\AanwidzingRequest;
use App\Http\Requests\TenderProcessNegotiationRequest;
use App\Http\Requests\TenderProcessRequest;
use App\Http\Requests\TenderScheduleRequest;
use App\Models\BaseModel;
use App\Models\Ref\RefCurrency;
use App\Models\TenderHeaderCommercial;
use App\Models\TenderHeaderTechnical;
use App\Models\TenderReference;
use App\Models\TenderVendor;
use App\Models\TenderVendorAwarding;
use App\Models\TenderVendorSubmission;
use App\Models\TenderWeighting;
use App\RefPurchaseGroup;
use App\RefPurchaseOrg;
use App\RefPlant;
use App\RefListOption;
use App\Repositories\TenderAanwijzingRepository;
use App\Repositories\TenderBidDocRequirementRepository;
use App\Repositories\TenderEvaluatorRepository;
use App\Repositories\TenderGeneralDocumentRepository;
use App\Repositories\TenderInternalDocumentRepository;
use App\Repositories\TenderItemSpecificationRepository;
use App\Repositories\TenderItemsRepository;
use App\Repositories\TenderProcessAwardingRepository;
use App\Repositories\TenderProcessNegotiationRepository;
use App\Repositories\TenderProcessRepository;
use App\Repositories\TenderRepository;
use App\Repositories\TenderScheduleRepository;
use App\Repositories\TenderSignatureRepository;
use App\Repositories\TenderVendorRepository;
use App\Repositories\TenderWeightingRepository;
use App\Services\TenderMailService;
use App\TenderWorkflow;
use App\TenderWorkflowHelper;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;


class TenderController extends Controller
{
    protected $workflow;
    protected $repo;

    public function __construct(TenderRepository $repo)
    {
        $this->middleware('auth');
        $this->workflow = new TenderWorkflowHelper();
        $this->repo = $repo;
    }

    public function index()
    {
        abort_if(Gate::denies('tender_index'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $fields = explode(",", "tender_number,pr_number,title,scope_of_work,tender_method,winning_method,submission_method,status,workflow_status,retender_from,created_at,created_by,evaluation_method,internal_organization,purchase_organization");

        // set session for sender sub menu back
        session()->put('tender_menu_back', url()->current());
        return view('tender.list', [
            'fields' => $fields,
            'purchGroups' => RefPurchaseGroup::pluck('description', 'id'),
            'purchOrgs' => RefPurchaseOrg::pluck('description', 'id'),
            'tenderMethod' => RefListOption::where('type', 'tender_method_options')->where('deleteflg', false)->pluck('value', 'key'),
            'winningMethod' => RefListOption::where('type', 'winning_method_options')->where('deleteflg', false)->pluck('value', 'key'),
            'submissionMethod' => RefListOption::where('type', 'submission_method_options')->where('deleteflg', false)->pluck('value', 'key'),
            'evaluationMethod' => RefListOption::where('type', 'evaluation_method_options')->where('deleteflg', false)->pluck('value', 'key'),
            'workflowStatus' => TenderWorkflowHelper::getWorkflowStatusOption(), // trans('tender.tender_w_status'),
            'tenderStatus' => trans('tender.tender_status'),
        ]);
    }

    public function show($id, $type = 'parameters', $action = '', $param_id = '')
    {
        abort_if(Gate::denies('tender_' . $type . '_read'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $tender = $this->repo->findTenderParameterById($id, true);
        try {
            $method = 'showTender' . str_replace('_', '', ucwords($type, '_'));
            //dd($method, $tender, $type, $action);
            if (method_exists($this, $method)) {
                return $this->$method($tender, $type, $action, $param_id);
            } else {
                // dd($method);
                return $this->showDefault($tender, $type, $action);
            }
        } catch (Exception $e) {
            Log::error($e);
            throw $e;
        }
    }
    private function isPageEditable($tender, $type, $pages)
    {
        $user = Auth::user();
        $isAllowChange = true;
        if ($tender->status == 'active') { // && !$user->hasRole('Super Admin')
            $isAllowChange = TenderWorkflowHelper::isAllowTender($tender, $user);
        }
        if ($isAllowChange) {
            if (Gate::allows('tender_' . $type . '_create') || Gate::allows('tender_' . $type . '_update')) {
                if (in_array($tender->status, ['cancelled', 'discarded', 'completed'])) {
                    return false;
                } else if ($type == 'aanwijzings') {
                    return $tender->aanwijzing == 1;
                } else if ($tender->status == 'draft' && $tender->workflow_status == 'procurement_approval') {
                    return $tender->workflow_values == 'procurement_approval-rejected';
                } else if ($tender->status == 'active' && $type == 'parameters') {
                    return false;
                } else if ($tender->status == 'active' && $type != 'parameters') {
                    return true;
                } else {
                    return in_array($type, $pages['editables']) ? true : false;
                }
            }
        }
        return false;
    }

    private function prepareAllowedPage($availablePage, $vendor = null, $tender = false)
    {
        $pageAllowed = [];
        if (count($availablePage) > 0) {
            $isAllowChange = true;
            $submission = null;
            if ($tender != false) {
                $tenderNumber = $tender->tender_number;
                if ($vendor) {
                    $isAllowChange = TenderWorkflowHelper::isAllowTender($tender, Auth::user());
                    $submission = (new TenderProcessRepository)->findSubmissionDidNotPass($tenderNumber, $vendor->id);
                }
            }
            foreach ($availablePage as $page) {
                if (Gate::allows('tender_' . $page . '_read')) {
                    $pageAllowed[] = $page;
                    // check if vendor is registered
                    if (!$isAllowChange && $page == 'schedules') {
                        break;
                    }
                    if (
                        $submission && isset(TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE[$page]) &&
                        $submission->submission_method == TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE[$page]
                    ) {
                        break;
                    }
                }
            }
        }
        return $pageAllowed;
    }

    private function defaultViewData($tender, $type = 'parameters', $action = '')
    {
        $isVendor = Auth::user()->isVendor();
        $pages = $this->workflow->getCurrentAvailable($tender);
        $pages['availables'] = $this->prepareAllowedPage($pages['availables'], Auth::user()->vendor, $tender);
        // validate page
        if (!in_array($type, $pages['availables'])) {
            abort(404);
        }
        $next = array_search($type, $pages['availables']);
        if ($next !== false) {
            $next = $next + 1 == count($pages['availables']) ? $pages['availables'][$next] : $pages['availables'][$next + 1];
        }

        $arr_return = [
            'id'                => $tender->id,
            'type'              => $type,
            'tender'            => $tender,
            'editable'          => $this->isPageEditable($tender, $type, $pages), // in_array($type, $pages['editables']) ? true : false,
            'next'              => $next,
            'pages'             => $this->prepareAllowedPage($this->workflow->getAllPages($tender)),
            'availablePages'    => $pages['availables'],
            'isVendor' => $isVendor,
            'canCreate' => Gate::allows('tender_' . $type . '_create'),
            'canUpdate' => Gate::allows('tender_' . $type . '_update'),
            'canDelete' => Gate::allows('tender_' . $type . '_delete') && $tender->status == 'draft',
            'submissionMethod' => RefListOption::where('type', 'submission_method_options')->where('deleteflg', false)->pluck('value', 'key'),
        ];
        return $arr_return;
    }
    public function showDefault($tender, $type = 'parameters')
    {
        $storage = asset('storage/tender/' . $tender->tender_number . '/' . $type);
        if (empty($tender->visibility_bid_document)) $tender->visibility_bid_document = 'PRIVATE';
        $arr_view_data = $this->defaultViewData($tender, $type);
        $arr_data = array_merge($arr_view_data, [
            'tenderData'        => $this->workflow->getData($type, $tender->tender_number),
            'storage'           => $storage,
            'purchGroups'       => RefPurchaseGroup::all(),
            'purchOrgs'         => RefPurchaseOrg::all(),
            'plants'            => RefPlant::all(),
            'incotermOptions'   => RefListOption::where('type', 'incoterm_options')->where('deleteflg', false)->pluck('value', 'key'),
            'tenderMethod'      => RefListOption::where('type', 'tender_method_options')->where('deleteflg', false)->pluck('value', 'key'),
            'submissionMethod'  => RefListOption::where('type', 'submission_method_options')->where('deleteflg', false)->pluck('value', 'key'),
            'evaluationMethod'  => RefListOption::where('type', 'evaluation_method_options')->where('deleteflg', false)->pluck('value', 'key'),
            'winningMethod'     => RefListOption::where('type', 'winning_method_options')->where('deleteflg', false)->pluck('value', 'key'),
            'conditionalType'   => RefListOption::where('type', 'conditional_type_option')->where('deleteflg', false)->orderBy('key', 'asc')->pluck('value', 'key'),
            'validityOptions'   => RefListOption::where('type', 'validity_quotation_options')->where('deleteflg', false)->orderBy('id', 'asc')->pluck('value', 'key'),
            'bidVisibility'     => RefListOption::where('type', 'bid_visibility_options')->where('deleteflg', false)->pluck('value', 'key'),
            'tkdnOptions'       => RefListOption::where('type', 'tkdn_options')->where('deleteflg', false)->pluck('value', 'key'),
        ]);
        return view('tender.form.' . $type, $arr_data);
    }

    #region Show
    public function showTenderInternalDocuments($tender, $type)
    {
        $tRepo = new TenderInternalDocumentRepository();
        return view('tender.form.' . $type, array_merge($this->defaultViewData($tender, $type), [
            'storage'           => asset('storage/tender/' . $tender->tender_number . '/' . $type),
            'tenderData'        => [
                'tender_internal_documents' => [
                    'fields' => $tRepo->fields()
                ],
            ],
        ]));
    }
    private function showTenderGeneralDocuments($tender, $type)
    {
        $tRepo = new TenderGeneralDocumentRepository();
        return view('tender.form.' . $type, array_merge($this->defaultViewData($tender, $type), [
            'storage'           => asset('storage/tender/' . $tender->tender_number . '/' . $type),
            'tenderData'        => [
                'tender_general_documents' => [
                    'fields' => $tRepo->fields()
                ],
            ],
        ]));
    }
    private function showTenderItems($tender, $type, $action = '')
    {
        $tRepo = new TenderItemsRepository();
        if ($action == 'detail-specification') {
            return $this->showTenderItemsDetail($tender, $type);
        } else {
            $arr_data =  array_merge($this->defaultViewData($tender, $type), [
                'tenderData'        => [
                    'tender_items' => [
                        'fields' => $tRepo->fields(),
                        'service_fields' => $tRepo->fields('prlist_services'),
                        'item_text_fields' => $tRepo->fields('prlist_item_text')
                    ],
                ],
                'conditionalTypeList' =>  $tRepo->findConditionalType(),
                'taxCodes' =>  $tRepo->findTaxCodes(),
                'categories' => (new TenderItemSpecificationRepository)->findCategories($tender->tender_number),
            ]);

            return view('tender.form.' . $type, $arr_data);
        }
    }
    private function showTenderItemsDetail($tender, $type)
    {
        $defaultViewData = $this->defaultViewData($tender, $type);
        // dd($defaultViewData['editable']);
        $tRepo = new TenderItemSpecificationRepository();
        $categories = $tRepo->findCategories($tender->tender_number, $defaultViewData['editable']);
        return view('tender.form.items_detail_specification', array_merge($this->defaultViewData($tender, $type), [
            'tenderData'        => [
                'fields' => $tRepo->fields('field1'),
            ],
            'categories' => $categories->toArray(),
        ]));
    }
    private function showTenderProposedVendors($tender, $type)
    {
        $tRepo = new TenderVendorRepository();
        return view('tender.form.' . $type, array_merge($this->defaultViewData($tender, $type), [
            'tenderData'        => [
                'tender_vendors' => [
                    'fields' => $tRepo->fields()
                ],
            ],
            'scopeOfSupplies' => $tRepo->findScopeOfSupplies()->get(),
        ]));
    }
    private function showTenderAanwijzings($tender, $type)
    {
        $tRepo = new TenderAanwijzingRepository();
        $storage = asset('storage/tender/' . $tender->tender_number . '/' . $type);
        return view('tender.form.' . $type, array_merge($this->defaultViewData($tender, $type), [
            'storage'           => $storage,
            'tenderData'        => [
                'tender_aanwijzings' => [
                    'fields' => $tRepo->fields()
                ],
            ],
        ]));
    }
    private function showTenderWeightings($tender, $type)
    {
        $tRepo = new TenderWeightingRepository();
        return view('tender.form.' . $type, array_merge($this->defaultViewData($tender, $type), [
            'tenderData'        => [
                'tender_weightings' => [
                    'fields' => $tRepo->fields()
                ],
            ],
            'typeOptions' => TenderWeighting::getEnableType(),
        ]));
    }
    private function showTenderEvaluators($tender, $type)
    {
        $tRepo = new TenderEvaluatorRepository();
        $tenderTypes = TenderWeighting::getEnableType();
        if ($tender->prequalification == 0) {
            unset($tenderTypes[1]);
        }
        unset($tenderTypes[2]);
        return view('tender.form.' . $type, array_merge($this->defaultViewData($tender, $type), [
            'tenderData'        => [
                'tender_evaluators' => [
                    'fields' => $tRepo->fields()
                ],
            ],
            // 'buyers' => $tRepo->findBuyerOptions(),
            'buyers' => (new TenderSignatureRepository)->findBuyerOptions($tender->purchase_org_id),
            'buyerTypes' => $tRepo->findBuyerTypeOptions(),
            'stageTypeOptions' => $tenderTypes,
            'submissionMethodOptions' => $tenderTypes,
        ]));
    }
    private function showTenderBiddingDocumentRequirements($tender, $type)
    {
        $tRepo = new TenderBidDocRequirementRepository();
        $tenderTypes = TenderWeighting::getEnableType();
        if ($tender->prequalification == 0) {
            unset($tenderTypes[1]);
        }
        unset($tenderTypes[2]);
        unset($tenderTypes[6]);
        return view('tender.form.' . $type, array_merge($this->defaultViewData($tender, $type), [
            'tenderData'        => [
                'tender_bidding_document_requirements' => [
                    'fields' => $tRepo->fields()
                ],
            ],
            'stageTypeOptions' => $tenderTypes,
            'submissionMethodOptions' => $tenderTypes,
        ]));
    }
    private function showTenderSchedules($tender, $type)
    {
        $scheduleRepo = new TenderScheduleRepository();
        $signRepo = new TenderSignatureRepository();

        return view('tender.form.' . $type, array_merge($this->defaultViewData($tender, $type), [
            'tenderData'        => [
                'schedules' => $scheduleRepo->findByTenderNumber($tender->tender_number),
                'signatures' => $signRepo->findByTenderNumber($tender->tender_number),
            ],
            'isChanged' => $tender->action_status == TenderStatusEnum::ACT_CHANGE && $tender->public_status == TenderStatusEnum::PUBLIC_STATUS[5],
            'scheduleTypes' => $scheduleRepo->getTypeOptions($tender),
            // 'approvers' => $this->repo->findConfigApprovers($tender->purchase_org_id),
            'approvers' => $this->repo->findProposalApproval($tender->purchase_org_id),
            'buyerOptions' => $signRepo->findBuyerOptions($tender->purchase_org_id),
            // 'positionOption' => $scheduleRepo->findPositionOptions(),
        ]));
    }
    private function showTenderProcurementApproval($tender, $type)
    {
        $signRepo = new TenderSignatureRepository();
        $user = Auth::user();
        $lines = $signRepo->findApprover($tender->tender_number, $user);
        $hasPendingApproval = false;
        $approver = null;
        if ($lines) {
            foreach ($lines as $line) {
                $approver = $line;
                $hasPendingApproval = $line->status == 'draft';
                if ($line->status == 'draft') break;
            }
        }
        return view('tender.form.' . $type, array_merge($this->defaultViewData($tender, $type), [
            'tenderData'        => [
                'signatures' => $signRepo->findByTenderNumber($tender->tender_number, 2),
            ],
            'approver' => $approver,
            'hasPendingApproval' => $hasPendingApproval
        ]));
    }
    private function showTenderProcessRegistration($tender, $type)
    {
        return view('tender.form.' . $type, array_merge($this->defaultViewData($tender, $type), [
            'tenderData'        => [
                'tender_vendors' => [
                    'fields' => (new TenderProcessRepository)->fields('registration')
                ],
            ],
            'schedule' => (new TenderScheduleRepository)->findByType($tender->tender_number, 1),
        ]));
    }
    private function showTenderProcessPrequalification($tender, $type)
    {
        $vendor = Auth::user()->vendor;
        $viewData = array_merge($this->defaultViewData($tender, $type), [
            'tenderData'        => [
                'process_prequalification' => [
                    'fields1' => (new TenderProcessRepository)->fields('pre_qualification1'),
                    'fields2' => (new TenderProcessRepository)->fields('pre_qualification2'),
                    'fields3' => (new TenderProcessRepository)->fields('pre_qualification3')
                ],
            ],
            'storage' => asset('storage/tender/' . $tender->tender_number . '/' . $type),
            'schedule' => (new TenderScheduleRepository)->findByType($tender->tender_number, 2),
        ]);
        if ($vendor) {
            $pRepo = new TenderProcessRepository();
            $viewData['hasDocument'] = $pRepo->findVendorSubmissionDetail($tender->tender_number, $vendor->id, 1)
                ->count() > 0;
            $viewData['submissionData'] = $pRepo->findVendorSubmissionByVendor($tender->tender_number, $vendor->id, 1);
            $viewData['vendor'] = $vendor;
            $tenderVendor =  (new TenderVendorRepository())->findByVendor($tender->tender_number, $vendor->id);
            $viewData['isRegistered'] = $tenderVendor && in_array($tenderVendor->status, [TenderVendor::STATUS[2], TenderVendor::STATUS[4]]);
        } else {
            // $viewData['signatures'] = (new TenderSignatureRepository())->findByTenderNumber($tender->tender_number);
            $viewData['teams'] = (new TenderEvaluatorRepository())->findAll($tender->tender_number)
                ->where('submission_method', TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE['process_prequalification'])
                ->get();
            $viewData['canStart'] = TenderWorkflowHelper::can('prequalification_start', $tender->tender_number);
            $viewData['canOpen'] = TenderWorkflowHelper::can('prequalification_open', $tender->tender_number);
            $viewData['canFinish'] = TenderWorkflowHelper::can('prequalification_finish', $tender->tender_number);
        }
        return view('tender.form.' . $type, $viewData);
    }
    private function showTenderProcessTenderEvaluation($tender, $type, $action = '', $vendorId = null)
    {
        if (!Auth::user()->isVendor() && $action == 'commercialApproval') {
            //to another function to show approval commercial data
            return $this->showTenderCommercialApproval($tender, $type, $action, $vendorId);
        }
        $vendor = Auth::user()->vendor;
        if ($action == 'detail-specification') {
            if ($vendor) $vendorId = $vendor->id;
            return $this->showTenderProcessItemsDetail($tender, $type, $vendorId);
        } else {
            $pRepo = new TenderProcessRepository();
            $itemRepo = new TenderItemsRepository();
            $signRepo = (new TenderSignatureRepository());
            $viewData = array_merge($this->defaultViewData($tender, $type), [
                'tenderData'        => [
                    'process_tender_evaluation' => [
                        'fields1' => $pRepo->fields('bid_opening1'),
                        'fields2' => $pRepo->fields('bid_opening2'),
                        'fields3' => ['vendor_code', 'vendor_name', 'score_tc', 'score_com'],
                        'prlist' => $itemRepo->fields(),
                        'service_fields' => $itemRepo->fields('prlist_services'),
                        'item_text_fields' => $itemRepo->fields('prlist_item_text')
                    ],
                ],
                'conditionalType'   => RefListOption::where('type', 'conditional_type_option')->where('deleteflg', false)->orderBy('key', 'asc')->pluck('value', 'key'),
                'storage' => asset('storage/tender/' . $tender->tender_number . '/' . $type),
                'schedule' => (new TenderScheduleRepository)->findByType($tender->tender_number, 3),
                'date_now' => Carbon::now()->format(BaseModel::DATETIME_FORMAT),
                'taxCodes' =>  $itemRepo->findTaxCodes(),
                'conditionalTypeList' =>  $itemRepo->findConditionalType(),
                'technical' => [
                    'quo_validity_date' => TenderReference::QuotationValidityEndDate($tender->tender_number, 3),
                ],
                'commercial' => [
                    'currencies' => RefCurrency::orderBy('currency')->pluck('description', 'currency'),
                    'quo_validity_date' => TenderReference::QuotationValidityEndDate($tender->tender_number, 3),
                    'incotermOptions'   => RefListOption::where('type', 'incoterm_options')->where('deleteflg', false)->pluck('value', 'key'),
                ],
            ]);
            if ($vendor) {
                $viewData['vendor'] = $vendor;
                $viewData['editableItem'] = $viewData['editable'];
                $tenderVendor =  (new TenderVendorRepository())->findByVendor($tender->tender_number, $vendor->id);
                $viewData['isRegistered'] = $tenderVendor && in_array($tenderVendor->status, [TenderVendor::STATUS[2], TenderVendor::STATUS[4]]);
                $viewData['technical']['submissionData'] = $pRepo->findVendorSubmissionByVendor($tender->tender_number, $vendor->id, 3);
                $viewData['technical']['hasDocument'] = $pRepo->findVendorSubmissionDetail($tender->tender_number, $vendor->id, 3)->count() > 0;
                $viewData['technical']['header'] = $pRepo->findTenderHeader($tender->tender_number, $vendor->id, 3)->first() ?? new TenderHeaderTechnical();
                $viewData['commercial']['hasDocument'] = $pRepo->findVendorSubmissionDetail($tender->tender_number, $vendor->id, 4)->count() > 0;
                $viewData['commercial']['header'] = $pRepo->findTenderHeader($tender->tender_number, $vendor->id, 4)->first() ?? new TenderHeaderCommercial();
                $viewData['commercial']['submissionData'] = $pRepo->findVendorSubmissionByVendor($tender->tender_number, $vendor->id, 4);
                if ($viewData['commercial']['header']->incoterm == '') $viewData['commercial']['header']->incoterm = $tender->incoterm;
                $viewData['hasResubmission'] = TenderReference::hasResubmission($tender->tender_number);
            } else {
                $viewData['editableItem'] = false;
                $viewData['signatures'] = (new TenderSignatureRepository())->findByTenderNumber($tender->tender_number);
                $viewData['technical']['canStart'] = TenderWorkflowHelper::can(['technical_start', 'single_envelope_start'], $tender->tender_number);
                $viewData['technical']['canOpen'] = TenderWorkflowHelper::can(['technical_open', 'single_envelope_open'], $tender->tender_number);
                $viewData['technical']['canFinish'] = TenderWorkflowHelper::can(['technical_finish', 'single_envelope_finish'], $tender->tender_number);
                $viewData['commercial']['canStart'] = TenderWorkflowHelper::can(['commercial_start', 'single_envelope_start'], $tender->tender_number);
                $viewData['commercial']['canOpen'] = TenderWorkflowHelper::can(['commercial_open', 'single_envelope_open'], $tender->tender_number);
                $viewData['commercial']['canFinish'] = TenderWorkflowHelper::can(['commercial_finish', 'single_envelope_finish'], $tender->tender_number);
                $viewData['vendorSelected'] = session()->get('vendorSelected');
                session()->forget('vendorSelected');
                $viewData['approvers'] = $this->repo->findCommercialApproval($tender->purchase_org_id);
                $viewData['buyerOptions'] = $signRepo->findCommercialBuyerOptions($tender->purchase_org_id);
                $viewData['commercialSignatures'] = $signRepo->findCommercialSignaturesByTenderNumber($tender->tender_number);
                $viewData['haveCommSignature'] = $viewData['commercialSignatures']->count() > 0;
            }
            return view('tender.form.' . $type, $viewData);
        }
    }

    private function showTenderProcessTechnicalEvaluation($tender, $type, $action = '', $vendorId = null)
    {
        $vendor = Auth::user()->vendor;
        if ($action == 'detail-specification') {
            if ($vendor) $vendorId = $vendor->id;
            return $this->showTenderProcessItemsDetail($tender, $type, $vendorId);
        } else {
            $pRepo = new TenderProcessRepository();
            $scheduleType = $tender->submission_method == '2E' ? 3 : 4;
            $itemRepo = new TenderItemsRepository();
            $viewData = array_merge($this->defaultViewData($tender, $type), [
                'tenderData'        => [
                    'process_technical_evaluation' => [
                        'fields1' => $pRepo->fields('bid_opening1'),
                        'fields2' => $pRepo->fields('bid_opening2'),
                        'fields3' => ['vendor_code', 'vendor_name', 'score'],
                        'prlist' => $itemRepo->fields(),
                        'service_fields' => $itemRepo->fields('prlist_services'),
                        'item_text_fields' => $itemRepo->fields('prlist_item_text')
                    ],
                ],
                'conditionalType'   => RefListOption::where('type', 'conditional_type_option')->where('deleteflg', false)->orderBy('key', 'asc')->pluck('value', 'key'),
                'storage' => asset('storage/tender/' . $tender->tender_number . '/' . $type),
                'schedule' => (new TenderScheduleRepository)->findByType($tender->tender_number, $scheduleType),
                'date_now' => Carbon::now()->format(BaseModel::DATETIME_FORMAT),
                'taxCodes' =>  $itemRepo->findTaxCodes(),
                'conditionalTypeList' =>  $itemRepo->findConditionalType(),
                'technical' => [
                    'quo_validity_date' => TenderReference::QuotationValidityEndDate($tender->tender_number, 3),
                ],
            ]);
            if ($vendor) {
                $viewData['vendor'] = $vendor;
                $viewData['editableItem'] = $viewData['editable'];
                $tenderVendor =  (new TenderVendorRepository())->findByVendor($tender->tender_number, $vendor->id);
                $viewData['isRegistered'] = $tenderVendor && in_array($tenderVendor->status, [TenderVendor::STATUS[2], TenderVendor::STATUS[4]]);
                $viewData['technical']['submissionData'] = $pRepo->findVendorSubmissionByVendor($tender->tender_number, $vendor->id, 3);
                $viewData['technical']['hasDocument'] = $pRepo->findVendorSubmissionDetail($tender->tender_number, $vendor->id, 3)->count() > 0;
                $viewData['technical']['header'] = $pRepo->findTenderHeader($tender->tender_number, $vendor->id, 3)->first() ?? new TenderHeaderTechnical();
                $viewData['hasResubmission'] = TenderReference::hasResubmission($tender->tender_number);
            } else {
                $viewData['editableItem'] = false;
                $viewData['signatures'] = (new TenderSignatureRepository())->findByTenderNumber($tender->tender_number);
                if ($tender->submission_method == '2E') {
                    $viewData['canStart'] = TenderWorkflowHelper::can('dual_envelope_start', $tender->tender_number);
                } else {
                    $viewData['canStart'] = TenderWorkflowHelper::can('technical_start', $tender->tender_number);
                }
                $viewData['canOpen'] = TenderWorkflowHelper::can('technical_open', $tender->tender_number);
                $viewData['canFinish'] = TenderWorkflowHelper::can('technical_finish', $tender->tender_number);
                $viewData['vendorSelected'] = session()->get('vendorSelected');
                session()->forget('vendorSelected');
            }
            return view('tender.form.' . $type, $viewData);
        }
    }
    private function showTenderProcessCommercialEvaluation($tender, $type, $action = '', $paramId = '')
    {
        if (!Auth::user()->isVendor() && $action == 'commercialApproval') {
            //to another function to show approval commercial data
            return $this->showTenderCommercialApproval($tender, $type, $action, $paramId);
        }
        $vendor = Auth::user()->vendor;
        $pRepo = new TenderProcessRepository();
        $scheduleType = $tender->submission_method == '2E' ? 3 : 5;
        $itemRepo = new TenderItemsRepository();
        $signRepo = (new TenderSignatureRepository());

        $viewData = array_merge($this->defaultViewData($tender, $type), [
            'tenderData'        => [
                'process_commercial_evaluation' => [
                    'fields1' => $pRepo->fields('bid_opening1'),
                    'fields2' => $pRepo->fields('bid_opening2'),
                    'fields3' => ['vendor_code', 'vendor_name', 'score'],
                    'prlist' => $itemRepo->fields(),
                    'service_fields' => $itemRepo->fields('prlist_services'),
                    'item_text_fields' => $itemRepo->fields('prlist_item_text')
                ],
            ],
            'conditionalType'   => RefListOption::where('type', 'conditional_type_option')->where('deleteflg', false)->orderBy('key', 'asc')->pluck('value', 'key'),
            'storage' => asset('storage/tender/' . $tender->tender_number . '/' . $type),
            'schedule' => (new TenderScheduleRepository)->findByType($tender->tender_number, $scheduleType),
            'date_now' => Carbon::now()->format(BaseModel::DATETIME_FORMAT),
            'taxCodes' =>  $itemRepo->findTaxCodes(),
            'conditionalTypeList' =>  $itemRepo->findConditionalType(),
            'commercial' => [
                'currencies' => RefCurrency::orderBy('currency')->pluck('description', 'currency'),
                'isStarted' => TenderReference::isStarted($tender->tender_number),
                'quo_validity_date' => TenderReference::QuotationValidityEndDate($tender->tender_number, 4),
                'incotermOptions'   => RefListOption::where('type', 'incoterm_options')->where('deleteflg', false)->pluck('value', 'key'),
            ],
        ]);
        if ($vendor) {
            $viewData['vendor'] = $vendor;
            $viewData['editableItem'] = $viewData['editable'];
            $tenderVendor =  (new TenderVendorRepository())->findByVendor($tender->tender_number, $vendor->id);
            $viewData['isRegistered'] = $tenderVendor && in_array($tenderVendor->status, [TenderVendor::STATUS[2], TenderVendor::STATUS[4]]);
            $viewData['commercial']['submissionData'] = $pRepo->findVendorSubmissionByVendor($tender->tender_number, $vendor->id, 4);
            $viewData['commercial']['hasDocument'] = $pRepo->findVendorSubmissionDetail($tender->tender_number, $vendor->id, 4)->count() > 0;
            $viewData['commercial']['header'] = $pRepo->findTenderHeader($tender->tender_number, $vendor->id, 4)->first() ?? new TenderHeaderTechnical();
            if ($viewData['commercial']['header']->incoterm == '') $viewData['commercial']['header']->incoterm = $tender->incoterm;
        } else {
            $viewData['editableItem'] = false;
            $viewData['signatures'] = (new TenderSignatureRepository())->findByTenderNumber($tender->tender_number);
            if ($tender->submission_method == '2E') {
                $viewData['canStart'] = TenderWorkflowHelper::can('dual_envelope_start', $tender->tender_number);
            } else {
                $viewData['canStart'] = TenderWorkflowHelper::can('commercial_start', $tender->tender_number);
            }
            $viewData['canOpen'] = TenderWorkflowHelper::can('commercial_open', $tender->tender_number);
            $viewData['canFinish'] = TenderWorkflowHelper::can('commercial_finish', $tender->tender_number);
            $viewData['approvers'] = $this->repo->findCommercialApproval($tender->purchase_org_id);
            $viewData['buyerOptions'] = $signRepo->findCommercialBuyerOptions($tender->purchase_org_id);
            $viewData['commercialSignatures'] = $signRepo->findCommercialSignaturesByTenderNumber($tender->tender_number);
            $viewData['haveCommSignature'] = $viewData['commercialSignatures']->count() > 0;
        }
        return view('tender.form.' . $type, $viewData);
    }
    private function showTenderProcessItemsDetail($tender, $type, $vendorId)
    {
        // $defaultViewData = $this->defaultViewData($tender, $type);
        $tRepo = new TenderItemSpecificationRepository();
        $categories = $tRepo->findVendorCategories($tender->tender_number, $vendorId);
        session()->put('vendorSelected', $vendorId);
        return view('tender.form.items_detail_specification', array_merge($this->defaultViewData($tender, $type), [
            'tenderData'        => [
                'fields' => $tRepo->fields('field1'),
            ],
            'categories' => $categories->toArray(),
            'vendorId' => $vendorId,
        ]));
    }

    public function showTenderNegotiation($tender, $type)
    {
        $vendor = Auth::user()->vendor;
        $pRepo = new TenderProcessRepository();
        $itemRepo = new TenderItemsRepository();
        $negotiationType = 6;

        $fields = ['vendor_code', 'vendor_name', 'status', 'submission_date', 'action_negotiation_status', 'score_tc', 'score_com'];
        $fields2 = ['vendor_code', 'vendor_name', 'score_tc', 'score_com', 'total_comply', 'total_deviate', 'total_no_quote'];
        $fields3 = (new TenderProcessRepository)->fields('negotiation');
        $viewData = array_merge($this->defaultViewData($tender, $type), [
            'tenderData'        => [
                'negotiation' => [
                    'fields' => $fields,
                    'fields2' => $fields2,
                    'fields3' => $fields3,
                    'prlist' => $itemRepo->fields(),
                    'service_fields' => $itemRepo->fields('prlist_services'),
                    'item_text_fields' => $itemRepo->fields('prlist_item_text')
                ],
            ],
            'taxCodes' =>  $itemRepo->findTaxCodes(),
            'conditionalTypeList' =>  $itemRepo->findConditionalType(),
            'conditionalType'   => RefListOption::where('type', 'conditional_type_option')->where('deleteflg', false)->orderBy('key', 'asc')->pluck('value', 'key'),
            'storage' => asset('storage/tender/' . $tender->tender_number . '/' . $type),
            'date_now' => Carbon::now()->format(BaseModel::DATETIME_FORMAT),
            'negotiation' => [
                'currencies' => RefCurrency::orderBy('currency')->pluck('description', 'currency'),
                'quo_validity_date' => TenderReference::QuotationValidityEndDate($tender->tender_number, $negotiationType),
                'incotermOptions'   => RefListOption::where('type', 'incoterm_options')->where('deleteflg', false)->pluck('value', 'key'),
            ],
        ]);

        if ($vendor) {
            $viewData['vendor'] = $vendor;
            $viewData['editableItem'] = $viewData['editable'];
            $tenderVendor =  (new TenderVendorRepository())->findByVendor($tender->tender_number, $vendor->id);
            $viewData['tenderVendor'] = $tenderVendor;
            $viewData['isRegistered'] = $tenderVendor && in_array($tenderVendor->status, [TenderVendor::STATUS[2], TenderVendor::STATUS[4]]);
            $viewData['negotiation']['submissionData'] = $pRepo->findVendorSubmissionByVendor($tender->tender_number, $vendor->id, $negotiationType);
            $viewData['negotiation']['hasDocument'] = (new TenderProcessNegotiationRepository())->findVendorSubmissionDetail($tender->tender_number, $vendor->id, $negotiationType)->count() > 0;
            $viewData['negotiation']['header'] = $pRepo->findTenderHeader($tender->tender_number, $vendor->id, $negotiationType)->first() ?? new TenderHeaderCommercial();
            if ($viewData['negotiation']['header']->incoterm == '') $viewData['negotiation']['header']->incoterm = $tender->incoterm;
        } else {
            $viewData['editableItem'] = false;
            $viewData['signatures'] = (new TenderSignatureRepository())->findByTenderNumber($tender->tender_number);
            $viewData['canStart'] = Gate::allows('tender_' . $type . '_create') && TenderWorkflowHelper::can('negotiation_start', $tender->tender_number);
            $canFinish = TenderVendor::whereNotNull("negotiation_status")
                ->where("tender_number", $tender->tender_number)
                ->whereIn("negotiation_status", [
                    TenderSubmissionEnum::FLOW_STATUS[5],
                    TenderSubmissionEnum::FLOW_STATUS[6],
                ])
                ->count() <= 0;

            $viewData['canFinish'] = $canFinish && Gate::allows('tender_' . $type . '_update') && TenderWorkflowHelper::can('negotiation_finish', $tender->tender_number);
            $viewData['canOpen'] = Gate::allows('tender_' . $type . '_update') && TenderWorkflowHelper::can('negotiation_open', $tender->tender_number);
            $viewData['canResubmission'] = $viewData['canOpen'] && TenderVendor::whereNotNull("negotiation_status")
                ->where("tender_number", $tender->tender_number)
                ->whereIn("negotiation_status", [
                    TenderSubmissionEnum::FLOW_STATUS[2],
                    TenderSubmissionEnum::FLOW_STATUS[4],
                    TenderSubmissionEnum::FLOW_STATUS[5],
                    TenderSubmissionEnum::FLOW_STATUS[6],
                ])
                ->count() > 0;
            $viewData['vendorSelected'] = session()->get('vendorSelected');
            session()->forget('vendorSelected');
        }

        return view('tender.form.' . $type, $viewData);
    }

    public function showTenderAwardingProcess($tender, $type)
    {
        $vendor = Auth::user()->vendor;
        $pRepo = new TenderProcessRepository();
        $itemRepo = new TenderItemsRepository();
        $negotiationType = 6;

        $fields = ['vendor_code', 'vendor_name', 'action_awarding_status', 'action_details_awarding', 'score_tc', 'score_com'];
        $fields2 = ['vendor_code', 'vendor_name', 'awarding_status', 'action_details_awarding', 'po_number', 'sap_po_number', 'score_tc', 'score_com'];
        $fields4 = ['vendor_code', 'vendor_name', 'awarding_status', 'action_details_awarding', 'po_number', 'sap_po_number'];
        $fields3 = (new TenderProcessRepository)->fields('negotiation');
        $viewData = array_merge($this->defaultViewData($tender, $type), [
            'tenderData'        => [
                'awarding_process' => [
                    'fields' => $fields,
                    'fields2' => $fields2,
                    'fields3' => $fields3,
                    'fields4' => $fields4,
                    'prlist' => $itemRepo->fields(),
                    'service_fields' => $itemRepo->fields('prlist_services'),
                    'item_text_fields' => $itemRepo->fields('prlist_item_text')
                ],
            ],
            'taxCodes' =>  $itemRepo->findTaxCodes(),
            'conditionalTypeList' =>  $itemRepo->findConditionalType(),
            'conditionalType'   => RefListOption::where('type', 'conditional_type_option')->where('deleteflg', false)->orderBy('key', 'asc')->pluck('value', 'key'),
            'storage' => asset('storage/tender/' . $tender->tender_number . '/' . $type),
            'date_now' => Carbon::now()->format(BaseModel::DATETIME_FORMAT),
            'awarding_process' => [
                'currencies' => RefCurrency::orderBy('currency')->pluck('description', 'currency'),
                'quo_validity_date' => TenderReference::QuotationValidityEndDate($tender->tender_number, $negotiationType),
                'incotermOptions'   => RefListOption::where('type', 'incoterm_options')->where('deleteflg', false)->pluck('value', 'key'),
            ],
        ]);

        if ($vendor) {
            $viewData['vendor'] = $vendor;
            $viewData['editableItem'] = $viewData['editable'];
            $tenderVendor =  (new TenderVendorRepository())->findByVendor($tender->tender_number, $vendor->id);
            $viewData['tenderVendor'] = $tenderVendor;
            $viewData['isRegistered'] = $tenderVendor && in_array($tenderVendor->status, [TenderVendor::STATUS[2], TenderVendor::STATUS[4]]);
            $viewData['awarding_process']['submissionData'] = $pRepo->findVendorSubmissionByVendor($tender->tender_number, $vendor->id, $negotiationType);
            $viewData['awarding_process']['hasDocument'] = (new TenderProcessNegotiationRepository())->findVendorSubmissionDetail($tender->tender_number, $vendor->id, $negotiationType)->count() > 0;
            $viewData['awarding_process']['header'] = $pRepo->findTenderHeader($tender->tender_number, $vendor->id, $negotiationType)->first() ?? new TenderHeaderCommercial();
            if ($viewData['awarding_process']['header']->incoterm == '') $viewData['awarding_process']['header']->incoterm = $tender->incoterm;
        } else {
            $viewData['editableItem'] = false;
            $viewData['signatures'] = (new TenderSignatureRepository())->findByTenderNumber($tender->tender_number);
            $viewData['canWin'] = Gate::allows('tender_' . $type . '_create');

            $allowSubmit = TenderVendorAwarding::where("tender_number", $tender->tender_number)
                ->whereIn("status", ["draft", "request_resubmission"])->count() > 0;

            $allowSubmitPO = (new TenderProcessAwardingRepository)->findVendorAwarding($tender->tender_number, true, "only_not_has_sap_po")->count() > 0;

            $allowReSubmit = TenderVendorAwarding::where("tender_number", $tender->tender_number)
                ->where("status", "submitted")->count() > 0 && TenderVendorAwarding::where("tender_number", $tender->tender_number)
                ->where("status", "request_resubmission")->count() <= 0 && TenderWorkflow::where('tender_number', $tender->tender_number)
                ->where('page', "awarding_process")->where('is_done', 1)->count() > 0;


            $viewData['canSubmit'] = $allowSubmit && Gate::allows('tender_' . $type . '_update');
            $viewData['canSubmitPO'] = $allowSubmitPO && Gate::allows('tender_' . $type . '_update');
            $viewData['canReSubmit'] = $allowReSubmit && Gate::allows('tender_' . $type . '_update');

            // cek jika submit awarding sudah pernah dilakukan
            $tenderRef = TenderReference::where('tender_number', $tender->tender_number)
                            ->where('ref_type', 'submit')
                            ->where('submission_method', TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE['awarding_process'])
                            ->first();
            if($tenderRef == null){
                $viewData['canSubmit'] = true;
                $viewData['canReSubmit'] = false;
            }
        }
        return view('tender.form.' . $type, $viewData);
    }

    private function getTenderCommercialApprovalData($tender, $type, $action, $paramId)
    {
        $signRepo = new TenderSignatureRepository();
        $user = Auth::user();
        $lines = $signRepo->findCommercialApprover($tender->tender_number, $user);
        $hasPendingApproval = false;
        $approver = null;
        if ($lines) {
            foreach ($lines as $line) {
                $approver = $line;
                $hasPendingApproval = $line->status == 'draft';
                if ($line->status == 'draft') break;
            }
        }
        $data = [
            'tender' => $tender,
            'tenderData'        => [
                'signatures' => $signRepo->findCommercialByTenderNumber($tender->tender_number, 2),
            ],
            'approver' => $approver,
            'hasPendingApproval' => $hasPendingApproval
        ];
        return $data;
    }
    private function showTenderCommercialApproval($tender, $type, $action, $paramId)
    {
        // $data = array_merge($this->defaultViewData($tender, $type), $data);
        $data = $this->getTenderCommercialApprovalData($tender, $type, $action, $paramId);

        return response()->json([
            'status' => 200,
            'success' => true,
            'message' => '',
            'data' => $data
        ]);
    }
    #endregion

    #region Save
    public function save($id, $type = 'parameters', Request $request)
    {
        // $tender = $this->repo->findTenderParameterById($id, true);
        // if (empty($type)) {
        //     $activePages = $this->workflow->getCurrentStatus($tender->tender_number);
        //     $type = $activePages->page;
        // }
        // abort_if(Gate::denies('tender_' . $type . '_create') && Gate::denies('tender_' . $type . '_update'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $tender = $this->repo->findTenderParameterById($id, true);
        switch ($type) {
            case 'items':
                return $this->saveTenderItems($tender, $type, $request);
            case 'internal_documents':
                return $this->saveTenderInternalDocuments($tender, $type, $request);
            case 'general_documents':
                return $this->saveTenderGeneralDocuments($tender, $type, $request);
            case 'proposed_vendors':
                return $this->saveTenderVendor($tender, $type, $request);
            case 'aanwijzings':
                return $this->saveTenderAanwijzing(
                    $tender,
                    $type,
                    App::makeWith(AanwidzingRequest::class, ['tender' => $tender])
                );
            case 'weightings':
                return $this->saveTenderWeighting($tender, $type, $request);
            case 'evaluators':
                return $this->saveTenderEvaluators($tender, $type, $request);
            case 'bidding_document_requirements':
                return $this->saveTenderBidDocRequirement($tender, $type, $request);
            case 'schedules':
                return $this->saveTenderSchedule(
                    $tender,
                    $type,
                    App::makeWith(TenderScheduleRequest::class, ['tender' => $tender])
                );
            case 'procurement_approval':
                return $this->saveProcurementApproval($tender, $type, $request);
            case 'process_prequalification':
                return $this->saveProcessPrequalification($tender, $type, App::makeWith(TenderProcessRequest::class, ['tender' => $tender, 'type' => $type]));
            case 'process_tender_evaluation':
            case 'process_technical_evaluation':
            case 'process_commercial_evaluation':
                // return $this->saveProcessTenderEvaluation($tender, $type, App::makeWith(TenderProcessRequest::class, ['tender' => $tender]));
                return $this->saveTenderProcess($tender, $type, App::makeWith(TenderProcessRequest::class, ['tender' => $tender, 'type' => $type]));
            case 'negotiation':
                if (auth()->user()->user_type == 'vendor') {
                    return $this->saveNegotiationProcessVendor($tender, $type, App::makeWith(TenderProcessRequest::class, ['tender' => $tender, 'type' => $type]));
                } else {
                    return $this->saveNegotiationProcess($tender, $type, App::makeWith(TenderProcessNegotiationRequest::class, ['tender' => $tender, 'type' => $type]));
                }
            case 'awarding_process':
                if (auth()->user()->user_type == 'vendor') {
                } else {
                    return $this->saveAwardingProcess($tender, $type, App::makeWith(TenderProcessNegotiationRequest::class, ['tender' => $tender, 'type' => $type]));
                }
            default:
                return $this->saveDefault($tender, $type, $request);
        }
    }
    private function getSequenceDone(Request $request)
    {
        $sequence_done = $request->input('sequence_done', false);
        return $sequence_done === true || $sequence_done === 'true';
    }
    private function saveDefault($tender, $type = 'parameters', Request $request)
    {
        try {
            $params = $request->all();
            $sequence_done = $this->getSequenceDone($request);
            $next = $this->repo->getNextPage($sequence_done, $type, $tender);

            $result = $this->repo->save($tender, $type, $params, $request->file());

            return response()->json([
                'status' => 200,
                'success' => true,
                'message' => 'data_saved',
                'data' => $result,
                'next' => $next, // $this->repo->getNextPage($sequence_done, $type, $tender),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 200,
                'success' => false,
                'message' => 'data_not_saved',
            ]);
        }
    }
    private function saveTenderInternalDocuments($tender, $type, Request $request)
    {
        $params = $request->all();
        //SAVE FILE IF EXISTS
        $files = $request->file();
        // $params['attachment'] = '';
        if ($files != null && $files > 0) {
            foreach ($files as $key => $file) {
                $path = Storage::putFileAs('public/tender/' . $tender->tender_number . '/' . $type, $file, $file->getClientOriginalName());
                $params['attachment'] = $file->getClientOriginalName();
            }
        }

        $params['tender_number'] = $tender->tender_number;
        $result = (new TenderInternalDocumentRepository())->save($params);
        $sequence_done = $this->getSequenceDone($request);
        return response()->json([
            'status' => 200,
            'success' => true,
            'message' => 'data_saved',
            'data' => $result,
            'next' => $this->repo->getNextPage($sequence_done, $type, $tender),
        ]);
    }
    private function saveTenderGeneralDocuments($tender, $type, Request $request)
    {
        $params = $request->all();
        //SAVE FILE IF EXISTS
        $files = $request->file();
        // $params['attachment'] = '';
        if ($files != null && $files > 0) {
            foreach ($files as $key => $file) {
                $path = Storage::putFileAs('public/tender/' . $tender->tender_number . '/' . $type, $file, $file->getClientOriginalName());
                $params['attachment'] = $file->getClientOriginalName();
            }
        }

        $params['tender_number'] = $tender->tender_number;
        $result = (new TenderGeneralDocumentRepository())->save($params);
        $sequence_done = $this->getSequenceDone($request);
        return response()->json([
            'status' => 200,
            'success' => true,
            'message' => 'data_saved',
            'data' => $result,
            'next' => $this->repo->getNextPage($sequence_done, $type, $tender),
        ]);
    }
    private function saveTenderVendor($tender, $type, Request $request)
    {
        try {
            $params = $request->all();
            $result = (new TenderVendorRepository())->insertBulk($tender, $params);

            $sequence_done = $this->getSequenceDone($request);
            return response()->json([
                'status' => 200,
                'success' => true,
                'message' => 'data_saved',
                'data' => $result,
                'next' => $this->repo->getNextPage($sequence_done, $type, $tender),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 200,
                'success' => false,
                'message' => 'data_not_saved',
            ]);
        }
    }
    private function saveTenderAanwijzing($tender, $type, AanwidzingRequest $request)
    {
        $aRepo = new TenderAanwijzingRepository();

        $params = $request->all();
        //SAVE FILE IF EXISTS
        $files = $request->file();
        if ($files != null && $files > 0) {
            foreach ($files as $key => $file) {
                $path = Storage::putFileAs('public/tender/' . $tender->tender_number . '/' . $type, $file, $file->getClientOriginalName());
                $params['result_attachment'] = $file->getClientOriginalName();
            }
        }

        $params['tender_number'] = $tender->tender_number;
        $result = $aRepo->save($params);

        if ($params['public_status'] == TenderStatusEnum::PUBLIC_STATUS[2]) {
            // send email to vendor
            $aRepo->sendEmail($tender, $params);
        }

        $sequence_done = $this->getSequenceDone($request);
        return response()->json([
            'status' => 200,
            'success' => true,
            'message' => 'data_saved',
            'data' => $result,
            'next' => $this->repo->getNextPage($sequence_done, $type, $tender),
        ]);
    }
    private function saveTenderWeighting($tender, $type, Request $request)
    {
        try {
            $params = $request->all();
            $params['tender_number'] = $tender->tender_number;
            $result = (new TenderWeightingRepository())->save($params);

            $sequence_done = $this->getSequenceDone($request);
            return response()->json([
                'status' => 200,
                'success' => true,
                'message' => 'data_saved',
                'data' => $result,
                'next' => $this->repo->getNextPage($sequence_done, $type, $tender),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 200,
                'success' => false,
                'message' => 'data_not_saved',
            ]);
        }
    }
    private function saveTenderEvaluators($tender, $type, Request $request)
    {
        try {
            $params = $request->all();
            $params['tender_number'] = $tender->tender_number;
            $result = (new TenderEvaluatorRepository())->save($params);
            $sequence_done = $this->getSequenceDone($request);
            return response()->json([
                'status' => 200,
                'success' => true,
                'message' => 'data_saved',
                'data' => $result,
                'next' => $this->repo->getNextPage($sequence_done, $type, $tender),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 200,
                'success' => false,
                'message' => 'data_not_saved',
            ]);
        }
    }
    private function saveTenderItems($tender, $type, Request $request)
    {
        try {
            $params = $request->all();
            if (!empty($params['action']) && $params['action'] == 'detail-specification') {
                $result = (new TenderItemSpecificationRepository())->save($tender, $params, $type);
            } else {
                $result = (new TenderItemsRepository())->save($tender, $params);
            }

            $sequence_done = $this->getSequenceDone($request);
            return response()->json([
                'status' => 200,
                'success' => true,
                'message' => 'data_saved',
                'data' => $result,
                'next' => $this->repo->getNextPage($sequence_done, $type, $tender),
            ]);
        } catch (Exception $e) {
            Log::error($e);
            return response()->json([
                'status' => 200,
                'success' => false,
                'message' => 'data_not_saved',
            ]);
        }
    }
    private function saveTenderBidDocRequirement($tender, $type, Request $request)
    {
        try {
            $params = $request->all();
            $params['tender_number'] = $tender->tender_number;
            $result = (new TenderBidDocRequirementRepository())->save($params);

            $sequence_done = $this->getSequenceDone($request);
            return response()->json([
                'status' => 200,
                'success' => true,
                'message' => 'data_saved',
                'data' => $result,
                'next' => $this->repo->getNextPage($sequence_done, $type, $tender),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 200,
                'success' => false,
                'message' => 'data_not_saved',
            ]);
        }
    }
    private function saveTenderSchedule($tender, $type, TenderScheduleRequest $request)
    {
        $signRepo = (new TenderSignatureRepository());
        $params = $request->all();
        $actionType = $request->get('actionType', null);
        unset($params['actionType']);
        $success = true;
        $message = 'data_saved';
        $next = '';
        if ($actionType == 'schedule') {
            $params['tender_number'] = $tender->tender_number;
            $result = (new TenderScheduleRepository())->save($params);
        } else if ($actionType == 'signature') {
            $result = $signRepo->saveBulk($params, $tender->tender_number);
        } else if ($actionType == 'changed') {
            $mailService = (new TenderMailService());
            $mailService->sendEmailOnTenderAnnouncement($tender);
            $mailService->sendEmailOnProposalChange($tender);
            $result = $this->repo->saveTenderChanged($tender);
        } else { // action type = submit
            $result = $signRepo->resetStatus($tender->tender_number);
            $sequence_done = $this->getSequenceDone($request);
            // $tender->public_status = TenderStatusEnum::PUBLIC_STATUS[2];
            $next = $this->repo->getNextPage($sequence_done, $type, $tender);
            $result = null;

            // send email to first approver
            $signRepo->sendEmailOnProposalSubmit($tender);
        }

        return response()->json([
            'status' => 200,
            'success' => $success,
            'message' => $message,
            'data' => $result,
            'next' => $next,
        ]);
    }
    private function saveProcurementApproval($tender, $type, Request $request)
    {
        try {
            $params = $request->only(['id', 'notes', 'status']);
            $result = (new TenderSignatureRepository())->save($params, $tender, $type);
            return response()->json([
                'status' => 200,
                'success' => true,
                'message' => 'data_saved',
                'data' => $result['data'],
                'next' => $result['next'],
            ]);
        } catch (Exception $e) {
            Log::error($e);
            return response()->json([
                'status' => 200,
                'success' => false,
                'message' => 'data_not_saved',
            ]);
        }
    }
    private function saveProcessPrequalification($tender, $type, TenderProcessRequest $request)
    {
        $params = $request->all();
        $files = $request->file();
        if ($files != null && $files > 0) {
            $vendor = Auth::user()->vendor;
            foreach ($files as $key => $file) {
                $fileName = $vendor->vendor_code . '/' . $params['line_id'] . '/attachment/' . $file->getClientOriginalName(); // uniqid().'_'.$file->getClientOriginalName();
                $path = Storage::putFileAs('public/tender/' . $tender->tender_number . '/' . $type, $file, $fileName);
                $params['attachment'] = $fileName;
            }
        }
        $result = (new TenderProcessRepository())->saveProcess($params, $tender, $type);
        return response()->json([
            'status' => 200,
            'success' => true,
            'message' => 'data_saved',
            'data' => $result['data'],
            'next' => $result['next'],
        ]);
    }
    private function saveNegotiationProcessVendor($tender, $type, TenderProcessRequest $request)
    {
        $params = $request->all();
        $files = $request->file();
        if ($files != null && $files > 0) {
            $vendor = Auth::user()->vendor;
            foreach ($files as $key => $file) {
                $fileName = $vendor->vendor_code . '/' . $key . '/' . uniqid() . '/' . $file->getClientOriginalName();
                $path = Storage::putFileAs('public/tender/' . $tender->tender_number . '/' . $type, $file, $fileName);
                $params[$key] = $fileName;
            }
        }
        $result = (new TenderProcessNegotiationRepository())->saveNegotiationVendor($params, $tender, $type);
        return response()->json([
            'status' => 200,
            'success' => $result['success'] ?? true,
            'message' => $result['message'] ?? 'data_saved',
            'data' => $result['data'],
            'next' => $result['next'],
        ]);
    }
    private function saveNegotiationProcess($tender, $type, TenderProcessNegotiationRequest $request)
    {
        $params = $request->all();
        $files = $request->file();
        if ($files != null && $files > 0) {
            $vendor = Auth::user()->vendor;
            foreach ($files as $key => $file) {
                $fileName = $vendor->vendor_code . '/' . $key . '/' . uniqid() . '/' . $file->getClientOriginalName();
                $path = Storage::putFileAs('public/tender/' . $tender->tender_number . '/' . $type, $file, $fileName);
                $params[$key] = $fileName;
            }
        }
        $result = (new TenderProcessNegotiationRepository())->saveNegotiation($params, $tender, $type);
        return response()->json([
            'status' => 200,
            'success' => true,
            'message' => 'data_saved',
            'data' => $result['data'],
            'next' => $result['next'],
        ]);
    }

    private function saveAwardingProcess($tender, $type, TenderProcessNegotiationRequest $request)
    {
        $params = $request->all();
        $files = $request->file();
        if ($files != null && $files > 0) {
            $vendor_code = "";
            $vendor = Auth::user()->vendor;
            if($vendor){
                $vendor_code = $vendor->vendor_code;
            }
            foreach ($files as $key => $file) {
                $fileName = $vendor_code . '/' . $key . '/' . uniqid() . '/' . $file->getClientOriginalName();
                $path = Storage::putFileAs('public/tender/' . $tender->tender_number . '/' . $type, $file, $fileName);
                $params[$key] = $fileName;
            }
        }
        $result = (new TenderProcessAwardingRepository())->saveAwarding($params, $tender, $type);
        return response()->json([
            'status' => 200,
            'success' => true,
            'message' => 'data_saved',
            'sap_result' => $result["sap_result"] ?? null,
            'data' => $result['data'],
            'next' => $result['next'],
        ]);
    }

    private function saveTenderProcess($tender, $type, TenderProcessRequest $request)
    {
        $params = $request->all();
        if (!empty($params['action']) && $params['action'] == 'detail-specification') {
            $result = (new TenderItemSpecificationRepository())->save($tender, $params, $type);
        } else if ($params['action_type'] == 'commercialSignature') {
            $result = (new TenderSignatureRepository())->saveCommercialSignature($tender, $params, $type);
            if ($result['isCompleteApproval']) {
                $params['action_type'] = TenderSubmissionEnum::FLOW_STATUS[6];
                $result = (new TenderProcessRepository())->saveProcess($params, $tender, $type);
                $action = 'commercialApproval';
                $result['approvalData'] = $this->getTenderCommercialApprovalData($tender, $type, $action, null);
            }
        } else {
            // dd($params);
            $files = $request->file();
            if ($files != null && $files > 0) {
                $vendor = Auth::user()->vendor;
                foreach ($files as $key => $file) {
                    $fileName = $vendor->vendor_code . '/' . $key . '/' . uniqid() . '/' . $file->getClientOriginalName();
                    $path = Storage::putFileAs('public/tender/' . $tender->tender_number . '/' . $type, $file, $fileName);
                    $params[$key] = $fileName;
                }
            }
            $result = (new TenderProcessRepository())->saveProcess($params, $tender, $type);
        }

        return response()->json([
            'status' => 200,
            'success' => true,
            'message' => 'data_saved',
            'data' => $result['data'] ?? null,
            'next' => $result['next'] ?? null,
            'approvalData' => $result['approvalData'] ?? null,
        ]);
    }

    public function storeDraft(Request $request)
    {
        try {
            $draft = $this->repo->saveAsDraft($request->all());
            return response()->json([
                'status' => 200,
                'success' => true,
                'message' => 'data_saved',
                'data' => ['id' => $draft->id, 'number' => $draft->tender_number],
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 200,
                'success' => false,
                'message' => 'data_not_saved',
            ]);
        }
    }
    #endregion

    public function delete($id)
    {
        try {
            $this->repo->delete($id);
            return response()->json([
                'status' => 200,
                'success' => true,
                'message' => 'data_deleted',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 200,
                'success' => false,
                'message' => 'data_not_deleted',
            ]);
        }
    }

    public function deleteItem($id, $type, $itemId, Request $request)
    {
        $tender = $this->repo->findTenderParameterById($id);
        try {
            $result = $this->repo->deleteItem($tender, $type, $itemId, $request->all());
            $success = true;
            $message = 'data_deleted';
        } catch (Exception $e) {
            $success = false;
            $message = 'data_not_deleted';
            Log::error($e);
        }

        return response()->json([
            'status' => 200,
            'success' => $success,
            'message' => $message,
            'data' => ['number' => $tender->tender_number, 'id' => $itemId],
        ]);
    }

    public function datatable_serverside(Request $request)
    {
        if (request()->ajax()) {
            // $data = $this->repo->findTenderParameter();
            return DataTables::eloquent($this->repo->findTenderParameter(true, true))
                ->filterColumn('workflow_status', function ($query, $keyword) {
                    $values = TenderWorkflowHelper::getWorkflowValues($keyword);
                    if (!empty($values)) {
                        $query->whereIn('workflow_values', $values);
                    } else {
                        $query->where('workflow_status', $keyword);
                    }
                })
                ->filterColumn('purchase_organization', function ($query, $keyword) {
                    $query->where('purchase_org_id', $keyword);
                })
                ->filterColumn('internal_organization', function ($query, $keyword) {
                    $query->where('purchase_group_id', $keyword);
                })
                ->filterColumn('pr_number', function ($query, $keyword) {
                    $query->where('ti.pr_number', 'like', '%' . $keyword . '%');
                })
                ->addColumn('status_text', function ($row) {
                    return __('tender.tender_status.' . strtolower($row->status));
                })
                ->addColumn('workflow_status_text', function ($row) {
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
                ->editColumn('created_at', function ($row) {
                    return date('d.m.Y H:i', strtotime($row->created_at));
                })
                // ->orderColumns(['id'], '-:column $1')
                ->make(true);
        }
    }

    public function getDatatableVendor(Request $request)
    {
        if (request()->ajax()) {
            $data = (new TenderVendorRepository)->findVendorByScopeOfSupply($request->all());

            return DataTables::of($data)
                ->addColumn('vendor_status_text', function ($row) {
                    return __('homepage.' . $row->vendor_status);
                })
                ->editColumn('scope_of_supply1', function ($row) {
                    if (!empty($row->scope_of_supply)) {
                        $scope_of_supply = explode(',', $row->scope_of_supply);
                        return count($scope_of_supply) > 0 ? $scope_of_supply[0] : '';
                    }
                    return '';
                })
                ->editColumn('scope_of_supply2', function ($row) {
                    if (!empty($row->scope_of_supply)) {
                        $scope_of_supply = explode(',', $row->scope_of_supply);
                        return count($scope_of_supply) > 1 ? $scope_of_supply[1] : '';
                    }
                    return '';
                })
                ->editColumn('scope_of_supply3', function ($row) {
                    if (!empty($row->scope_of_supply)) {
                        $scope_of_supply = explode(',', $row->scope_of_supply);
                        return count($scope_of_supply) > 2 ? $scope_of_supply[2] : '';
                    }
                    return '';
                })
                ->editColumn('scope_of_supply4', function ($row) {
                    if (!empty($row->scope_of_supply)) {
                        $scope_of_supply = explode(',', $row->scope_of_supply);
                        return count($scope_of_supply) > 3 ? $scope_of_supply[3] : '';
                    }
                    return '';
                })
                ->make(true);
        }
    }


    public function getDatatableItem($id, $type = 'parameters', Request $request)
    {
        if (request()->ajax()) {
            $tender = $this->repo->findTenderParameterById($id, true);
            return $this->repo->findItem($tender, $type, $request->all());
        }
    }
}
