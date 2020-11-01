<?php

namespace App\Repositories;

use App\Enums\TenderStatusEnum;
use App\Enums\TenderSubmissionEnum;
use App\Models\BaseModel;
use App\Models\SapPRListItemText;
use App\Models\TenderConfigApprovers;
use App\Models\TenderItemText;
use App\Models\TenderProposalApproval;
use App\Models\TenderCommercialApproval;
use App\Models\TenderVendor;
use App\Models\TenderVendorSubmission;
use App\Models\TenderWeighting;
use App\TenderItem;
use App\TenderParameter;
use App\TenderWorkflowHelper;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\DataTables;

class TenderRepository extends BaseRepository
{

    private $logName = 'TenderRepository';

    protected $workflow;
    protected $plant_onshore = "PLANT ONSHORE";
    protected $plant_offshore = "PLANT OFFSHORE";

    public function __construct()
    {
        $this->workflow = new TenderWorkflowHelper();
    }

    /**
     * find data tender paramter
     *
     * @return \Illuminate\Database\Eloquent\Collection $data
     *
     * @throws \Exception
     */
    public function findTenderParameter($asBuilder = false, $withTrashed=false)
    {
        try {
            $query = $this->queryTenderParameter(null, $withTrashed)
                ->select(
                    'tender_parameters.id',
                    'tender_parameters.tender_number',
                    'ti.pr_number',
                    'tender_parameters.title',
                    'tender_parameters.scope_of_work',
                    'tender_parameters.tender_method',
                    'tender_parameters.winning_method',
                    'tender_parameters.submission_method',
                    'tender_parameters.status',
                    'tender_parameters.workflow_status',
                    'tender_parameters.workflow_values',
                    'tender_parameters.retender_from',
                    'tender_parameters.tkdn_option',
                    DB::raw('TO_CHAR(tender_parameters.created_at, \'DD.MM.YYYY HH24:MI\') as created_at'),
                    'tender_parameters.created_by',
                    'tender_parameters.evaluation_method',
                    'tender_parameters.tkdn_option',
                    'ref_purchase_groups.description as internal_organization',
                    'ref_purchase_groups.group_code',
                    'ref_purchase_orgs.description as purchase_organization',
                    'ref_purchase_orgs.org_code',
                    DB::raw('sm.value as submission_method_value'),
                    DB::raw('em.value as evaluation_method_value'),
                    DB::raw('tm.value as tender_method_value'),
                    DB::raw('wm.value as winning_method_value')
                )
                ->join(DB::raw("(select ti.tender_number,STRING_AGG(CAST(ti.number as varchar), ' ,') as pr_number from tender_items ti ".($withTrashed ? "": "where ti.deleted_at is null")." group by tender_number) ti"), function ($join) {
                    $join->on('ti.tender_number', '=', 'tender_parameters.tender_number');
                });

            return $asBuilder ? $query : $query->get();
        } catch (Exception $e){
            Log::error($this->logName . '::findTenderParameter error : ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * find data tender for vendor and public
     *
     * @return \Illuminate\Database\Eloquent\Collection $data
     *
     * @throws \Exception
     */
    public function findAnnouncedTender($pageType)
    {
        try {
            $user = Auth::user();
            $vendorId = ($user && $user->vendor) ? $user->vendor->id : null;
            $query = $this->queryTenderParameter($pageType)
                ->select([
                    'tender_parameters.id',
                    'tender_parameters.tender_number',
                    // 'ti.pr_number',
                    'tender_parameters.title',
                    'tender_parameters.scope_of_work',
                    'tender_parameters.tender_method',
                    'tender_parameters.winning_method',
                    'tender_parameters.submission_method',
                    'tender_parameters.status',
                    'tender_parameters.workflow_status',
                    'tender_parameters.retender_from',
                    'tender_parameters.workflow_values',
                    'tender_parameters.visibility_bid_document',
                    DB::raw('TO_CHAR(tender_parameters.created_at, \'DD.MM.YYYY HH24:MI\') as created_at'),
                    'tender_parameters.created_by',
                    'tender_parameters.evaluation_method',
                    'ref_purchase_groups.description as internal_organization',
                    'ref_purchase_orgs.description as purchase_organization',
                    'tv.vendor_id',
                    'tv.tender_vendor_type as invitation_type',
                    'tv.status as tender_vendor_status',
                    'sub.status as submission_status',
                    'sub.submission_method as vendor_submission_method',
                    DB::raw('sm.value as submission_method_value'),
                    DB::raw('em.value as evaluation_method_value'),
                    DB::raw('tm.value as tender_method_value'),
                    DB::raw('wm.value as winning_method_value')
                ])
                ->leftJoin('tender_vendors as tv', function ($join) use($vendorId) {
                    $join->on('tv.tender_number', '=', 'tender_parameters.tender_number')
                    ->where('tv.vendor_id', $vendorId)
                    ->whereNull('tv.deleted_at')
                    ->limit(1);
                })
                ->leftJoin('tender_vendor_submissions as sub', function ($join) use($vendorId) {
                    $join->on('sub.tender_number', '=', 'tender_parameters.tender_number')
                        ->where('sub.vendor_id', $vendorId)
                        ->whereNull('sub.deleted_at')
                        ->where('sub.status', TenderVendorSubmission::STATUS[4]);
                })
                ->OfPublic(Auth::user(), $pageType, ['active','completed']);

                if($pageType == 'tender') { // page - tender invitation
                    $query = $query->whereNotNull('tv.vendor_id')
                        ->where('tv.tender_vendor_type', 1)
                        ->whereIn('tv.status', [TenderVendor::STATUS[1], TenderVendor::STATUS[3]]);
                } else if ($pageType == 'open') { // page - tender open
                    $query = $query->whereNull('tv.vendor_id');
                } else if ($pageType == 'tender_followed') { // page - tender followed
                    $query = $query->whereNotNull('tv.vendor_id')
                        ->whereIn('tv.status', [TenderVendor::STATUS[2], TenderVendor::STATUS[4]]);
                }

            return $query;
        } catch (Exception $e){
            Log::error($this->logName . '::findTenderParameter error : ' . $e->getMessage());
            Log::error($e);
            throw $e;
        }
    }

    /**
     * find data tender
     *
     * @return \Illuminate\Database\Eloquent\Builder
     *
     * @throws \Exception
     */
    private function queryTenderParameter($pageType = null, $withTrashed=false)
    {
        $query = TenderParameter::select(
            'tender_parameters.id',
            'tender_parameters.tender_number',
            'tender_parameters.title',
            'tender_parameters.scope_of_work',
            'tender_parameters.tender_method',
            'tender_parameters.winning_method',
            'tender_parameters.submission_method',
            'tender_parameters.status',
            'tender_parameters.workflow_status',
            'tender_parameters.retender_from',
            DB::raw('TO_CHAR(tender_parameters.created_at, \'DD.MM.YYYY HH24:MI\') as created_at'),
            'tender_parameters.created_by',
            'tender_parameters.evaluation_method',
            'ref_purchase_groups.description as internal_organization',
            'ref_purchase_groups.group_code',
            'ref_purchase_orgs.description as purchase_organization',
            'ref_purchase_orgs.org_code',
            DB::raw('sm.value as submission_method_value'),
            DB::raw('wm.value as evaluation_method_value'),
            DB::raw('tm.value as tender_method_value'),
            DB::raw('wm.value as winning_method_value')
        )
        ->join('ref_purchase_orgs', function ($join) {
            $join->on('ref_purchase_orgs.id', '=', 'tender_parameters.purchase_org_id');
        })
        ->join('ref_purchase_groups', function ($join) {
            $join->on('ref_purchase_groups.id', '=', 'tender_parameters.purchase_group_id');
        })
        ->leftJoin('ref_list_options as sm', function ($join) {
            $join->on('sm.key', '=', 'tender_parameters.submission_method')
                ->where('sm.type','submission_method_options');
        })
        ->leftJoin('ref_list_options as em', function ($join) {
            $join->on('em.key', '=', 'tender_parameters.evaluation_method')
                ->where('em.type','evaluation_method_options');
        })
        ->leftJoin('ref_list_options as tm', function ($join) {
            $join->on('tm.key', '=', 'tender_parameters.tender_method')
                ->where('tm.type','tender_method_options');
        })
        ->leftJoin('ref_list_options as wm', function ($join) {
            $join->on('wm.key', '=', 'tender_parameters.winning_method')
                ->where('wm.type','winning_method_options');
        })
        ->withTrashed($withTrashed)
        // ->orderBy('tender_parameters.created_at','asc')
        ;
        return $query;
    }

    /**
     * find data vendor by scope of supply
     *
     * @param App\TenderParameter $tender, tender number
     * @param string $type, item type
     *
     * @return \Yajra\DataTables\DataTables $dataTable
     *
     * @throws \Exception
     */
    public function findItem($tender, $type, $params = null)
    {
        $number = $tender->tender_number;
        switch($type){
            case 'items' :
                if(!empty($params['action']) && $params['action'] == 'detail-specification'){
                    return DataTables::of((new TenderItemSpecificationRepository)->findItemDetail($number, $params['category_id'])->get())
                        ->make(true);
                }else{
                    return DataTables::of((new TenderItemsRepository)->findByTenderNumber($number, $params))
                        ->make(true);
                }
            case 'internal_documents' :
                return DataTables::of((new TenderInternalDocumentRepository())->findByTenderNumber($number))
                    ->make(true);
            case 'general_documents' :
                return DataTables::of((new TenderGeneralDocumentRepository())->findByTenderNumber($number))
                    ->make(true);
            case 'proposed_vendors' :
                return DataTables::of((new TenderVendorRepository)
                    ->findByTenderNumber($number, [TenderVendor::STATUS[0],TenderVendor::STATUS[1],TenderVendor::STATUS[2],TenderVendor::STATUS[3]]))
                    ->addColumn('status_text', function ($row) use($tender) {
                        $status = $row->status;
                        if($row->status == 'invitation' && $tender->status == 'draft'){
                            $status = 'draft';
                        }
                        return __('tender.tender_vendor_status.' . $status);
                    })
                    ->addColumn('vendor_status_text', function ($row) {
                        return __('homepage.' . $row->vendor_status);
                    })
                    ->addColumn('vendor_status_text', function ($row) {
                        return __('homepage.' . $row->vendor_status);
                    })
                    ->editColumn('scope_of_supply1', function ($row) {
                        if(!empty($row->scope_of_supply)){
                            $scope_of_supply = explode(',',$row->scope_of_supply);
                            return count($scope_of_supply) > 0 ? $scope_of_supply[0] : '';
                        }
                        return '';
                    })
                    ->editColumn('scope_of_supply2', function ($row) {
                        if(!empty($row->scope_of_supply)){
                            $scope_of_supply = explode(',',$row->scope_of_supply);
                            return count($scope_of_supply) > 1 ? $scope_of_supply[1] : '';
                        }
                        return '';

                    })
                    ->editColumn('scope_of_supply3', function ($row) {
                        if(!empty($row->scope_of_supply)){
                            $scope_of_supply = explode(',',$row->scope_of_supply);
                            return count($scope_of_supply) > 2 ? $scope_of_supply[2] : '';
                        }
                        return '';
                    })
                    ->editColumn('scope_of_supply4', function ($row) {
                        if(!empty($row->scope_of_supply)){
                            $scope_of_supply = explode(',',$row->scope_of_supply);
                            return count($scope_of_supply) > 3 ? $scope_of_supply[3] : '';
                        }
                        return '';
                    })
                    ->make(true);
            case 'aanwijzings' :
                return DataTables::of((new TenderAanwijzingRepository)->findByTenderNumber($number))
                    ->addColumn('public_status_text', function ($row) {
                        return __('tender.status_item.' . strtolower($row->public_status));
                    })
                    ->addColumn('actionTable', function ($row) {
                        return '';
                    })
                    ->make(true);
            case 'weightings' :
                return DataTables::of((new TenderWeightingRepository)->findByTenderNumber($number, $params['method']))
                    ->addColumn('submission_method_text', function ($row) {
                        return __('tender.status_submission.'. TenderWeighting::TYPE[$row->submission_method]);
                    })
                    ->make(true);
            case 'evaluators' :
                return DataTables::of((new TenderEvaluatorRepository)->findByTenderNumber($number))
                    ->addColumn('submission_method_text', function ($row) {
                        return __('tender.status_submission.'. TenderWeighting::TYPE[$row->submission_method]);
                    })
                    ->addColumn('buyer_type_name_text', function ($row) {
                        $buyerTypeNames = '';
                        if(!empty($row->buyer_type_name)){
                            $nameArr = explode(',', $row->buyer_type_name);
                            foreach($nameArr as $k => $n){
                                $nameArr[$k] = __('tender.permissions.'.$n);
                            }
                            $buyerTypeNames = implode(',', $nameArr);
                        }
                        return $buyerTypeNames;
                    })
                    ->make(true);
            case 'bidding_document_requirements' :
                return DataTables::of((new TenderBidDocRequirementRepository)->findByTenderNumber($number))
                    ->addColumn('submission_method_text', function ($row) {
                        return __('tender.status_submission.'. TenderWeighting::TYPE[$row->submission_method]);
                    })
                    ->addColumn('stage_type_text', function ($row) {
                        return __('tender.status_stage_2.'. TenderWeighting::TYPE[$row->stage_type]);
                    })
                    ->addColumn('is_required_text', function ($row) {
                        return $row->is_required ? __('common.yes') : __('common.no');
                    })
                    ->make(true);
            case 'schedules' :
                return [
                    'schedules' => (new TenderScheduleRepository)->findByTenderNumber($number),
                    'signatures' => (new TenderSignatureRepository)->findByTenderNumber($number),
                ];
            case 'process_registration' :
                return DataTables::of((new TenderVendorRepository)
                    ->findByTenderNumber($number))
                    ->addColumn('status_text', function ($row) {
                        return __('tender.tender_vendor_status.' . $row->status);
                    })
                    ->make(true);
            case 'process_prequalification' :
                return (new TenderProcessRepository)->findItem($tender, 1, $params);
            case 'process_tender_evaluation' :
                // return (new TenderProcessRepository)->findItem($tender, $stageType, $params);
                if(!empty($params['action']) && $params['action'] == 'detail-specification'){
                    $stageType = !empty($params['stage_type']) ? $params['stage_type'] : 3;
                    return DataTables::of((new TenderItemSpecificationRepository)->findVendorItemDetail($tender, $params, $stageType)->get())
                        ->make(true);
                }else{
                    $stageType = !empty($params['stage_type']) ? $params['stage_type'] : 2;
                    return (new TenderProcessRepository)->findItem($tender, $stageType, $params);
                }
            case 'process_technical_evaluation' :
                $stageType = !empty($params['stage_type']) ? $params['stage_type'] : 3 ;
                // return (new TenderProcessRepository)->findItem($tender, $stageType, $params);
                if(!empty($params['action']) && $params['action'] == 'detail-specification'){
                    return DataTables::of((new TenderItemSpecificationRepository)->findVendorItemDetail($tender, $params, $stageType)->get())
                        ->make(true);
                }else{
                    return (new TenderProcessRepository)->findItem($tender, $stageType, $params);
                }
            case 'process_commercial_evaluation' :
                $stageType = !empty($params['stage_type']) ? $params['stage_type'] : 4 ;
                return (new TenderProcessRepository)->findItem($tender, $stageType, $params);
            case 'negotiation' :
                $stageType = !empty($params['stage_type']) ? $params['stage_type'] : TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE["negotiation_commercial"] ;
                return (new TenderProcessNegotiationRepository)->findItem($tender, $stageType, $params);
            case 'awarding_process' :
                    $stageType = !empty($params['stage_type']) ? $params['stage_type'] : TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE["awarding_process"] ;
                    return (new TenderProcessAwardingRepository)->findItem($tender, $stageType, $params);
            default :
                throw new Exception('Parameter type not found');
        }
    }

    /**
     * find all tender approver by purchasing org
     *
     * @param int $purchOrgId
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findConfigApprovers($purchOrgId)
    {
        try {
            $models = TenderConfigApprovers::where('purch_org_id',$purchOrgId)
                ->get();
            return $models;
        } catch (Exception $e) {
            Log::error($this->logName . '::findConfigApprovers error : ' . $e->getMessage());
            Log::error($e);
            throw $e;
        }
    }
    public function findProposalApproval($purchOrgId){
        try {
            $models = TenderProposalApproval::select(
                'tender_proposal_approvals.*',
                'u1.id as id_1',
                'u2.id as id_2',
                'u3.id as id_3',
                'u4.id as id_4',
                'u5.id as id_5',
                'u6.id as id_6',
                'u7.id as id_7',
                'u8.id as id_8',
            )
            ->join('ref_purchase_orgs', function($join){
                $join->on('tender_proposal_approvals.purch_org_code','ref_purchase_orgs.org_code');
            })
            ->leftJoin('users as u1', function($join){
                $join->on('tender_proposal_approvals.approver_1','u1.userid');
            })
            ->leftJoin('users as u2', function($join){
                $join->on('tender_proposal_approvals.approver_2','u2.userid');
            })
            ->leftJoin('users as u3', function($join){
                $join->on('tender_proposal_approvals.approver_3','u3.userid');
            })
            ->leftJoin('users as u4', function($join){
                $join->on('tender_proposal_approvals.approver_4','u4.userid');
            })
            ->leftJoin('users as u5', function($join){
                $join->on('tender_proposal_approvals.approver_5','u5.userid');
            })
            ->leftJoin('users as u6', function($join){
                $join->on('tender_proposal_approvals.approver_6','u6.userid');
            })
            ->leftJoin('users as u7', function($join){
                $join->on('tender_proposal_approvals.approver_7','u7.userid');
            })
            ->leftJoin('users as u8', function($join){
                $join->on('tender_proposal_approvals.approver_8','u8.userid');
            })
            ->where('ref_purchase_orgs.id',$purchOrgId)
            ->first();

            $output = [];
            for($i=1;$i<9;$i++){
                if(!is_null($models->{'approver_'.$i})){
                    $output[] = (object)[
                        'order' => $i,
                        'userid' => $models->{'approver_'.$i},
                        'user_id' => $models->{'id_'.$i}
                    ];
                }
            }
            return collect($output);
        } catch (Exception $e) {
            Log::error($this->logName . '::findProposalAproval error : ' . $e->getMessage());
            throw $e;
        }
    }
    public function findCommercialApproval($purchOrgId){
        try {
            $models = TenderCommercialApproval::select(
                'tender_commercial_approvals.*',
                'u1.id as id_1',
                'u2.id as id_2',
                'u3.id as id_3',
                'u4.id as id_4',
                'u5.id as id_5',
                'u6.id as id_6',
                'u7.id as id_7',
                'u8.id as id_8',
            )
            ->join('ref_purchase_orgs', function($join){
                $join->on('tender_commercial_approvals.purch_org_code','ref_purchase_orgs.org_code');
            })
            ->leftJoin('users as u1', function($join){
                $join->on('tender_commercial_approvals.approver_1','u1.userid');
            })
            ->leftJoin('users as u2', function($join){
                $join->on('tender_commercial_approvals.approver_2','u2.userid');
            })
            ->leftJoin('users as u3', function($join){
                $join->on('tender_commercial_approvals.approver_3','u3.userid');
            })
            ->leftJoin('users as u4', function($join){
                $join->on('tender_commercial_approvals.approver_4','u4.userid');
            })
            ->leftJoin('users as u5', function($join){
                $join->on('tender_commercial_approvals.approver_5','u5.userid');
            })
            ->leftJoin('users as u6', function($join){
                $join->on('tender_commercial_approvals.approver_6','u6.userid');
            })
            ->leftJoin('users as u7', function($join){
                $join->on('tender_commercial_approvals.approver_7','u7.userid');
            })
            ->leftJoin('users as u8', function($join){
                $join->on('tender_commercial_approvals.approver_8','u8.userid');
            })
            ->where('ref_purchase_orgs.id',$purchOrgId)
            ->get();

            $output = [];
            foreach($models as $model){
                $row = [];
                for($i=1;$i<9;$i++){
                    if(!is_null($model->{'approver_'.$i})){
                        $row[] = (object)[
                            'order' => $i,
                            'userid' => $model->{'approver_'.$i},
                            'user_id' => $model->{'id_'.$i}
                        ];
                    }
                }
                $output[$model->item_category] = collect($row);
            }
            return $output;
        } catch (Exception $e) {
            Log::error($this->logName . '::findCommercialAproval error : ' . $e->getMessage());
            throw $e;
        }
    }
    /**
     * find data TenderParameter by id
     *
     * @param int $primaryKey
     *
     * @return \App\TenderParameter $data
     */
    public function findTenderParameterById($primaryKey, $withThrow = true, $whiteRelation = false)
    {
        try {
            if($whiteRelation){
                $query = $this->queryTenderParameter()
                    ->select(
                        'tender_parameters.*',
                        'ref_purchase_groups.description as internal_organization',
                        'ref_purchase_orgs.description as purchase_organization',
                        DB::raw('sm.value as submission_method_value'),
                        DB::raw('em.value as evaluation_method_value'),
                        DB::raw('tm.value as tender_method_value')
                    )
                    ->where('tender_parameters.id', $primaryKey);
                    $query = $query->OfUser(Auth::user());
            }else{
                $query = TenderParameter::where('id', $primaryKey)
                    ->OfUser(Auth::user());
            }

            if ($withThrow) {
                return $query->firstOrFail(); // TenderParameter::findOrFail($primaryKey);
            } else {
                return $query->first(); //TenderParameter::find($primaryKey);
            }
        } catch (Exception $e) {
            Log::error($this->logName . '::findTenderParameterById error : ' . $e->getMessage());
            Log::error($e);
            throw $e;
        }
    }

    /**
     * add new record
     *
     * @param array $params
     *
     * @return $model
     *
     * @throws \Exception
     */
    public function save($tender, $type = 'parameters', $params, $files = null)
    {
        try {
            return $this->saveDefault($tender, $type, $params, $files);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($this->logName . '.save, ' . $e->getMessage(),['params' => $params]);
            throw $e;
        }
    }

    public function saveDefault($tender, $type = 'parameters', $params, $files = null)
    {
        $name = Auth::user()->name;
        $tables = $this->workflow->getTable($type);
        // $tender = TenderParameter::find($id);
        try {
            DB::beginTransaction();
            foreach ($tables as $table) {
                $data = [
                    'tender_number' => $tender->tender_number,
                ];
                $fields = Schema::getColumnListing($table);

                foreach ($fields as $field) {
                    if (array_key_exists($field, $params)) {
                        $data[$field] = $params[$field];
                    }
                }

                //START QUERY
                $query = DB::table($table)->where('tender_number', $tender->tender_number);

                //SAVE FILE IF EXISTS
                if($files != null && $files > 0){
                    foreach ($files as $key => $file) {
                        $path = Storage::putFileAs('public/tender/' . $tender->tender_number . '/' . $type, $file, $file->getClientOriginalName());
                        $data[$key] = $file->getClientOriginalName();
                    }
                }

                unset($data['id']);
                if ($query->count() > 0 && null !== $params['id']) {
                    $data['updated_at'] = now();
                    $data['updated_by'] = $name;
                    if (isset($params['id'])) {
                        $query->where('id', $params['id']);
                    }                
                    $affected = $query->update($data);
                    $returnId = isset($params['id']) ? $params['id'] : $tender->id;
                } else {
                    $data['created_at'] = now();
                    $data['created_by'] = $name;
                    $affected = $query->insert($data);
                    $returnId = DB::getPdo()->lastInsertId();
                }
                 $purchog = DB::table("ref_purchase_orgs")->where("id", $data["purchase_org_id"])->first();
                 $plant_name = $purchog->org_code == "1100" ? $this->plant_onshore : $this->plant_offshore;  
                 DB::table("tender_items")->where("tender_number", $tender->tender_number)->update(["plant"=>$purchog->org_code, "plant_name"=> $plant_name]);
            }
           
            DB::commit();
            return ['number' => $tender->tender_number, 'id' => $returnId];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($this->logName . '.save, ' . $e->getMessage());
            Log::error($e);
            throw $e;
        }
    }

    public function getNextPage($sequence_done, $type, $tender)
    {
        try {
            $next = "";
            $this->workflow->donePage($type, $tender);
            if ($sequence_done) {
                $pages = $this->workflow->getAllPages($tender);
                $idx = array_search($type, $pages);
                if ($idx !== false) {
                    // $nextType = $idx + 1 == count($pages['availables']) ? $pages['availables'][$idx] : $pages['availables'][$idx + 1];
                    $nextType = $pages[$idx + 1];
                    $next = route('tender.show', ['id' => $tender->id, 'type' => $nextType]);
                    $this->workflow->doneSequence($type, $nextType, $tender);
                }
            }
            return $next;
        } catch (Exception $e) {
            Log::error($this->logName . '.getNextPage, ' . $e->getMessage());
            Log::error($e);
            throw $e;
        }
    }

    public function saveTenderChanged($tender)
    {
        try {
            DB::beginTransaction();

            $tender->public_status = TenderStatusEnum::PUBLIC_STATUS[2];
            $tender->updated_at = Carbon::now();
            $tender->updated_by = Auth::user()->userid;
            $tender->save();

            (new TenderVendorItemRepository)->saveOnChangeTender($tender);

            DB::commit();
            return $tender;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($this->logName . '.saveTenderChanged, ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * add new record as draft
     *
     * @param array $params
     *
     * @return \App\TenderParameter new inserted data
     *
     * @throws \Exception
     */
    public function saveAsDraft($params)
    {
        $name = Auth::user()->name;
        $userId = Auth::user()->userid;
        $wf = $this->workflow->getWorkflowStart();
        $draft = new TenderParameter([
            'title' => $params['name'],
            'purchase_org_id' => $params['purchase_org_id'],
            'purchase_group_id' => $params['purchase_group_id'],
            'tender_method' => $params['tender_method'],
            'buyer' => $name,
            'prequalification' => $params['prequalification'],
            'eauction' => 0, // $params['eauction'],
            'submission_method' => $params['submission_method'],
            'evaluation_method' => $params['evaluation_method'],
            'created_by' => $userId,
            'created_at' => now(),
            'status' => 'draft', // $wf['tender_status'],
            'workflow_status' => $wf['workflow_status'],
            // 'workflow_values' => $wf['workflow_values'],
            'visibility_bid_document' => 'PRIVATE',
        ]);
        try {
            DB::beginTransaction();

            $draft->save();
            //$lastID = TenderParameter::latest()->first();
            $prepend = config('eproc.tender_number.prepend');
            $pad = config('eproc.tender_number.pad');
            $draft->tender_number = $prepend . str_pad($draft->id, $pad, '0', STR_PAD_LEFT);
            $draft->save();

            $sapPrRepo = new PRListRepository();
            $items = json_decode($params['items']);
            $arrItem = array();
            $purchog = DB::table("ref_purchase_orgs")->where("id",$params['purchase_org_id'])->first();
            
            foreach ($items as $item) {
                $array = array(
                    'tender_number' => $draft->tender_number,
                    'created_by' => $userId,
                    'created_at' => now(),
                );
                foreach ($item as $key => $value) {
                    if ($key != 'id') $array[$key] = $value;
                    if (in_array($key, ['expected_delivery_date','request_date'])){
                        $array[$key] = $value
                            ? Carbon::createFromFormat(BaseModel::DATE_FORMAT, $value)
                                    ->format(BaseModel::DB_DATE_FORMAT)
                            : null;
                    }
                }
                $array["plant"]= $purchog->org_code;
                $array["plant_name"] = $purchog->org_code == "1100" ? $this->plant_onshore : $this->plant_offshore;  
                $arrItem[] = $array;
            }
            DB::table((new TenderItem())->getTable())->insert($arrItem);
            DB::statement("update tender_items set line_id=id where line_id is null");

            $prItems = TenderItem::where('tender_number', $draft->tender_number)->get();
            if($prItems != null && $prItems->count() > 0){
                $arrItemText = [];

                foreach ($prItems as $item) {
                    $SapItemText = $sapPrRepo->findItemTexts($item->number, $item->line_number);
                    $counter = 0;
                    if($SapItemText != null && $SapItemText->count() > 0){
                        $counter++;
                        foreach ($SapItemText as $val) {
                            $arrItemText[] = [
                                'tender_number' => $item->tender_number,
                                'item_id' => $item ? $item->line_id : null,
                                'PREQ_NO' => $item->number ?? null,
                                'PREQ_ITEM' => $item->line_number ?? null,
                                'TEXT_ID' => $val->TEXT_ID,
                                'TEXT_ID_DESC' => $val->TEXT_ID_DESC,
                                'TEXT_FORM' => $counter < (count($SapItemText)) ? '*' : '',
                                'TEXT_LINE' => $val->TEXT_LINE,
                            ];
                        }
                    }
                }
                if(count($arrItemText) > 0) TenderItemText::insertBulk($arrItemText);
            }

            $this->workflow->startWorkflow($draft->tender_number);
            DB::commit();
            return $draft;
        } catch (Exception $e) {
            DB::rollback();
            Log::error($this->logName . '.saveAsDraft, ' . $e->getMessage(),['params' => $params]);
            Log::error($e);
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
            $user = Auth::user()->name;
            $tender = TenderParameter::find($primaryKey);
            $tender->updated_by = $user;
            $tender->status = 'discarded';
            $tender->save();
            $tender->delete();

            TenderItem::where('tender_number', $tender->tender_number)
                ->update(['updated_by' => $user, 'deleted_at' => now()]);
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($this->logName . '.saveAsDraft, ' . $e->getMessage());
            Log::error($e);
            throw $e;
        }
    }

    public function deleteItem($tender, $type, $itemId, $params = null)
    {
        switch($type){
            case 'items' :
                $params['id'] = $itemId;
                if(!empty($params['action']) && $params['action'] == 'detail-specification'){
                    return (new TenderItemSpecificationRepository)->delete($tender, $params);
                }else{
                    return (new TenderItemsRepository)->delete($itemId);
                }
            case 'internal_documents' :
                $path = 'public/tender/' . $tender->tender_number . '/' . $type;
                return (new TenderInternalDocumentRepository)->delete($itemId, $path);
            case 'general_documents' :
                $path = 'public/tender/' . $tender->tender_number . '/' . $type;
                return (new TenderGeneralDocumentRepository)->delete($itemId, $path);
            case 'proposed_vendors' :
                return (new TenderVendorRepository)->delete($itemId);
            case 'aanwijzings' :
                $path = 'public/tender/' . $tender->tender_number . '/' . $type;
                return (new TenderAanwijzingRepository)->delete($itemId, $path);
            case 'weightings' :
                return (new TenderWeightingRepository)->delete($itemId);
            case 'evaluators' :
                return (new TenderEvaluatorRepository)->delete($itemId);
            case 'bidding_document_requirements' :
                return (new TenderBidDocRequirementRepository)->delete($itemId);
            default :
                return $this->deleteItemDefault($tender, $type, $itemId);
        }
    }

    private function deleteItemDefault($tender, $type, $itemId)
    {
        // $user = Auth::user()->name;
        // $tender = TenderParameter::find($id);
        $number = $tender->tender_number;
        $tables = $this->workflow->getTable($type);
        $success = false;
        $message = "";

        if ($tables != null) {
            try {
                DB::beginTransaction();
                $files = [];
                foreach ($tables as $table) {
                    //get data
                    $data = DB::table($table)->where('id', $itemId)->where('tender_number', $number)->get();
                    $fields = Schema::getColumnListing($table);
                    foreach ($data as $key => $row) {
                        foreach ($fields as $field) {
                            //check if it is a file (has 'attachment' in the field name)
                            if (strpos($field, 'attachment') !== false) {
                                $filename = 'public/tender/' . $tender->tender_number . '/' . $type . '/' . $row->$field;
                                if (Storage::exists($filename)) {
                                    //store filename to temporary variable to delete later
                                    $files[] = $filename;
                                }
                            }
                        }
                    }
                    //delete the row [hard delete].
                    DB::table($table)->where('id', $itemId)->where('tender_number', $number)->delete();
                    //TODO: set is done false if there is no rows anymore in particular page.
                    //$pages = $this->workflow->doneSequence($type, $tender->tender_number, $tender->status, $tender->workflow_status);
                }
                //delete the files after making sure everything ok.
                foreach ($files as $filename) {
                    Storage::delete($filename);
                }


                DB::commit();
                $success = true;
                $message = "data_deleted";
            } catch (Exception $e) {
                DB::rollback();
                $message = "data_not_deleted";
                Log::error($e);
            }
        }

        return response()->json([
            'status' => 200,
            'success' => $success,
            'message' => $message,
            'data' => ['number' => $tender->tender_number, 'id' => $itemId],
        ]);
    }
}
