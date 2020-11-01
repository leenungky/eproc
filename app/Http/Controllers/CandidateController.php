<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

use App\SapConnector;
use App\Mail\TestMail;
use View;
use DB;
use DataTables;

use App\Repositories\VendorRepository;
use App\Repositories\BuyerRepository;
use App\Vendor;
use App\VendorProfile;
use App\VendorHistoryStatus;
use App\VendorProfileDetailStatus;
use App\VendorWorkflow;
use App\VendorApproval;
use App\VendorProfileGeneral;
use App\VendorProfileDeed;
use App\VendorProfileShareholder;
use App\VendorProfileBodboc;
use App\VendorProfileBusinessPermit;
use App\VendorProfilePic;
use App\VendorProfileTool;
use App\VendorProfileExpert;
use App\VendorProfileCertification;
use App\VendorProfileCompetency;
use App\VendorProfileExperience;
use App\VendorProfileBankAccount;
use App\VendorProfileFinancial;
use App\VendorProfileTax;
use App\User;
use App\RefBank;
use App\RefCountry;
use App\RefCompanyType;
use App\RefPurchaseOrg;
use App\RefListOption;
use App\Models\Ref\RefCurrency;
use App\Models\Ref\RefScopeOfSupply;
use App\Models\Ref\RefPostalCode;
use App\Traits\AccessLog;
use App\Jobs\ProcessEmail;

class CandidateController extends Controller {
    
    use AccessLog;

    protected $userlogged;
    protected $activity;
    protected $vendorRepo;
    
    protected $general, $deeds, $shareholder, $bodboc, $businesspermit, $pic, $equipment, 
            $expert, $certification, $scopesupply, $experience, $bankaccount, $financial, $tax;

    public function __construct() {
        $this->middleware('auth');
        $this->vendorRepo = new VendorRepository();
        $this->buyerRepo = new BuyerRepository();
        $this->tables = [
            'general' => with(new VendorProfileGeneral)->getTable(),
            'deeds' => with(new VendorProfileDeed)->getTable(),
            'shareholders' => with(new VendorProfileShareholder)->getTable(),
            'bod-boc' => with(new VendorProfileBodboc)->getTable(),
            'business-permit' => with(new VendorProfileBusinessPermit)->getTable(),
            'pic' => with(new VendorProfilePic)->getTable(),
            'tools' => with(new VendorProfileTool)->getTable(),
            'expert' => with(new VendorProfileExpert)->getTable(),
            'certification' => with(new VendorProfileCertification)->getTable(),
            'competency' => with(new VendorProfileCompetency)->getTable(),
            'work-experience' => with(new VendorProfileExperience)->getTable(),
            'bank-account' => with(new VendorProfileBankAccount)->getTable(),
            'financial' => with(new VendorProfileFinancial)->getTable(),
            'tax-document' => with(new VendorProfileTax)->getTable(),
        ];
        $this->tableKeys = array_keys($this->tables);
    }
    //
    public function index() {
        //
        return View::make('candidates/list');
    }

    // View Applicants
    public function view_candidates(Request $request){
        return View::make('candidates.list');
    }
    
    // Datatables ServerSide
    public function datatable_list_candidates(Request $request) {
        if (request()->ajax()) {
            $this->userlogged = auth()->user()->userid; // admin / finance / qmr
            $query = $this->vendorRepo->getListVendorByType('candidate')
                ->addSelect('vendor_workflows.activity')
                ->join('vendor_workflows', function ($join) {
                    $join->on('vendor_workflows.vendor_id', '=', 'vendors.id')
                    ->whereNotNull('vendor_workflows.started_at')
                    ->whereNull('vendor_workflows.finished_at')
                    ->whereNull('vendor_workflows.deleted_at');
                });
                $query->orderBy('vendor_profiles.id','desc');
                // Log::info($query->toSql());
            $vendor = $query->get();
                
            $array = array(
                'Status' => true,
                'Data' => $vendor,
                'Message' => ''
            );
            $result = (object) $array;
            $data = $result->Data;
            // Log::info($query->toSql());
            return DataTables::of($data)
                ->editColumn('created_at', function ($vendor) {
                    //change over here
                    return date('d.m.Y H:i', strtotime($vendor->created_at));
                })
                ->editColumn('updated_at', function ($vendor) {
                    //change over here
                    return date('d.m.Y H:i', strtotime($vendor->updated_at));
                })
                ->make(true);
        }
    }

    public function specific_find($column) {
        try {
            $column['vendors.id'] = $column['id'];
            unset($column['id']);
            $this->userlogged = auth()->user()->userid;
            if($this->userlogged === 'admin'){
                $this->activity = ['Approval by Admin','Initial Submission'];                
            } else {
                $this->activity = ['Approval by QMR'];
            }
            
            $vendor =  $this->vendorRepo->getQueryVendorById($column['vendors.id'])
            ->addSelect('vendor_history_statuses.status','vendor_profile_detail_statuses.is_submitted')
            ->leftJoin('vendor_history_statuses', "vendor_history_statuses.vendor_id", '=', "vendors.id")
            ->where('vendors.registration_status', 'candidate')
            ->where($column)->orderby('vendors.id', 'DESC')
            ->first();
            return $vendor;
        } catch (QueryException $e) {
            Log::error($e->getMessage());
        }
    }
    
    public function get_comment_history($column){
        try {
            if (isset($column['id'])) {
                $column['vendor_workflows.vendor_id'] = $column['id'];
                // remove ambiguos
                unset($column['id']);
            }

            return 
                VendorWorkflow::select(
                    'vendor_workflows.vendor_id',
                    'vendor_workflows.activity',
                    'vendor_workflows.remarks',
                    'vendor_workflows.started_at',
                    'vendor_workflows.finished_at',
                    'vendor_workflows.created_by'
                )->join('vendors', function ($join) {
                    $join->on('vendor_workflows.vendor_id', '=', 'vendors.id')
                    ->whereNull('vendors.deleted_at');
                })
                ->where($column)->orderby('vendor_workflows.id', 'DESC')->get();
        } catch (QueryException $e) {
            Log::error($e->getMessage());
        }
    }

    public function show_profiles(Request $request, $vendorId) {
        $arrColumnVal = array();
        foreach ($request->except('_token') as $key => $value) {
            $arrColumnVal[$key] = $value;
        }
        $currentUser = auth()->user();
        $this->userlogged = $currentUser->userid;

        $vendor =  $this->vendorRepo->getQueryVendorById($vendorId)
            ->addSelect('vendor_history_statuses.status','vendor_profile_detail_statuses.is_submitted')
            ->leftJoin('vendor_history_statuses', "vendor_history_statuses.vendor_id", '=', "vendors.id")
            ->first();
        $data['candidate_vendor'] = $vendor;
        $data['candidate'] = $this->vendorRepo->getProfileByVendorId($vendorId);
        $data['commentsHistory'] = $this->vendorRepo->getCommentHistoryByVendorId($vendorId);
        $data['profilesubmission'] = $this->vendorRepo->getProfileSubmission($vendorId);
        $data['evaluation_score'] = $this->vendorRepo->getLastEvaluationScore($vendorId);
        $approver = $this->vendorRepo->getProfileApproval($vendorId, 'vendor');
        $data['canProcess'] = false;
        if($approver){
            $vendor = $this->vendorRepo->getVendorById($vendorId);
            $samePurchOrg = $this->buyerRepo->userHavePurchaseOrganization($currentUser,$vendor->purchase_org_id);
            $data['canProcess'] = $currentUser->hasRole('Super Admin') || ($samePurchOrg && $currentUser->can('candidate_approval') && $currentUser->hasRole($approver->approver));
        }
        // if($approver && $approver->approver == $this->userlogged){
        //     $data['canProcess'] = true;
        // }
        $data['registrationStatus'] = $vendor->registration_status;
        $data['accordionMenu'] = 'layouts.candidate_menu';
        // get next approval
        $getNextApprover = VendorWorkflow::where('vendor_id', $vendorId)
                    ->whereNull('started_at')
                    ->whereNull('finished_at')
                    ->orderby('id', 'asc')
                    ->count();
        $data['finalapprover'] = $getNextApprover === 0 ? 'true' : 'false';
        return View::make('candidates/profile', $data);
    }
    
    public function approval(Request $request){
        $userid = auth()->user()->userid;        
        $reqData = $request->all();
        
        $vendorID = $reqData['vendor_id'];
        $vendorProfileID = $reqData['vendor_profile_id'];
        $vendorGroup = $reqData['vendor_group'];
        $companyType = $reqData['company_type'];
        $remarks = $reqData['remarks'];
        $history_status = $reqData['status_key'] == 'next' ? 'approved' : 'revise';
        $description = $reqData['status'];
        $status = 'Approved';
        $returnstatus = 200;
        $returnsuccess = false;
        $returnmessage = '';
        try {
            DB::beginTransaction();
            // ***********************
            // Update Data to Database
            // ***********************    
            $vendorData = $this->vendorRepo->getVendorById($vendorID);
            $registrationStatus = $vendorData->registration_status;
            
            $historyParams['status'] = $history_status;
            $historyParams['description'] = $history_status;
            $historyParams['remarks'] = $remarks;
            $historyParams['created_by'] = $userid;
            $this->update_history_status($vendorID, $historyParams);
            // Get next approval
            $nextApprover = (int) $this->get_next_approver($vendorID);
            $request->next_approval = $nextApprover;
            // Update Workflow 
            $workflowParams['status'] = $history_status;
            $workflowParams['remarks'] = $remarks;
            $workflowParams['created_by'] = $userid;
            $this->update_workflow($vendorID, $workflowParams);
            // Update Candidate Approval Status
            if($nextApprover > 0){ // Approval by Admin
                VendorApproval::where('vendor_id', $vendorID)
                    ->where(['as_position' => 'vendor', 'approver' => 'Admin Vendor', 'is_done' => false])
                    ->whereNull('deleted_at')
                    ->update(['is_done' => true]);
            } else { // Approval by QMR
                VendorApproval::where('vendor_id', $vendorID)
                    ->where(['as_position' => 'vendor', 'approver' => 'QMR', 'is_done' => false])
                    ->whereNull('deleted_at')
                    ->update(['is_done' => true]);
            }
            // check approval status (revise or next/approve)
            if($history_status === 'revise'){
                // Cancel All Approval from other role / user
                VendorApproval::where('vendor_id', $vendorID)
                    ->where(['as_position' => 'vendor', 'is_done' => false])
                    ->whereNull('deleted_at')
                    ->delete();
                // Update Candidate Profile Detail
                $this->set_profile_detail_status($vendorGroup, $companyType, 'not-finish');                                                                
                
                $detailStatusParams['is_approved'] = false;
                $detailStatusParams['is_revised'] = true;
                $detailStatusParams['is_submitted'] = false;
                // Case when Update Vendor Data
                if($registrationStatus === 'vendor'){
                    // Update Profile Detail Status                    
                    $detailStatusParams['update_vendor_data_status'] = 'Rejected';
                    $this->update_profile_detail_status($vendorID, $detailStatusParams);
                } else {
                    // Update Profile Detail Status
                    $this->update_profile_detail_status($vendorID, $detailStatusParams);
                    // Reset BP and SAP Vendor Code
                    Vendor::where('id', $vendorID)->whereNull('deleted_at')->update(['business_partner_code' => null, 'sap_vendor_code' => null, 'already_exist_sap' => false]);
                }
                
                DB::commit();
                $status = 'Revise';                                
                $returnsuccess = true;
                $returnmessage = 'SUCCESS: Data has been ' . $status . '!';                
                // Send Mail            
                $this->sendEmail($request, $history_status, $vendorData, $nextApprover);
            } else if($history_status === 'approved'){
                if($registrationStatus !== 'vendor'){
                    //save bp and sap vendor code if exists
                    if($reqData['already_exist_sap'] == 1){
                        $business_partner_code = $reqData['business_partner_code'];
                        $sap_vendor_code = $reqData['sap_vendor_code'];
                        Vendor::where('id', $vendorID)
                            ->whereNull('deleted_at')
                            ->update(['business_partner_code' => $business_partner_code, 'sap_vendor_code' => $sap_vendor_code, 'already_exist_sap' => true]);
                    } else {
                        Vendor::where('id', $vendorID)
                            ->whereNull('deleted_at')
                            ->update(['business_partner_code' => null, 'sap_vendor_code' => null, 'already_exist_sap' => false]);
                    }
                }
                // Case when is final approver (Appoval to become a Vendor)
                if($nextApprover === 0){
                    // Update Candidate Profile Detail
                    $this->set_profile_detail_status($vendorGroup, $companyType, 'none');
                    // Update Profile Detail Status
                    $detailStatusParams['is_approved'] = true;
                    $detailStatusParams['is_revised'] = false;
                    $detailStatusParams['is_submitted'] = false;
                    // Case when Update Vendor Data
                    if($registrationStatus === 'vendor'){
                        // Update Profile Detail Status                    
                        $detailStatusParams['update_vendor_data_status'] = 'Approved';
                        $this->update_profile_detail_status($vendorID, $detailStatusParams);
                    } else {
                        // Update Profile Detail Status
                        $this->update_profile_detail_status($vendorID, $detailStatusParams);                        
                        // Update to become Vendor
                        Vendor::where('id', $vendorID)
                            ->whereNull('deleted_at')
                            ->update(['registration_status' => 'vendor']);
                        //add avl number and date
                        $vp = VendorProfile::where('vendor_id',$vendorID)->first();
                        $vp->avl_no = $this->vendorRepo->getNextAvlNo();
                        $vp->avl_date = now();
                        $vp->save();
                    }                                        
                    // Update Current Data
                    $this->update_current_data($vendorID);
                    
                    // ***********************
                    // Create or Update to SAP
                    // ***********************
                    Log::debug("Start sending to SAP...");                    
                    $sapSync = $this->vendorRepo->sap_create_change_vendor($vendorID);
                    Log::debug($sapSync);
                    if($sapSync['status']){
                        //update partner no and vendor no
                        if($sapSync['data']['O_DATA']['PARTNER_NO'] != '' && $sapSync['data']['O_DATA']['VENDOR_NO'] != ''){
                            $v = Vendor::where('id', $vendorID)->first();
                            $v->business_partner_code = $sapSync['data']['O_DATA']['PARTNER_NO'];
                            $v->sap_vendor_code = $sapSync['data']['O_DATA']['VENDOR_NO'];
                            $v->save();
                        }
                        DB::commit();
                        $returnsuccess = true;
                        $returnmessage = 'SUCCESS: Data has been ' . $status . '!';
                        // Send Mail            
                        $this->sendEmail($request, $history_status, $vendorData, $nextApprover);
                    } else {
                        DB::rollback();
                        $returnstatus = 500;
                        $returnsuccess = false;
                        $returnmessage = 'ERROR: '.$sapSync['message'];
                    }
                } else {
                    DB::commit();
                    $returnsuccess = true;
                    $returnmessage = 'SUCCESS: Data has been ' . $status . '!';
                    // Send Mail            
                    $this->sendEmail($request, $history_status, $vendorData, $nextApprover);
                }                
            }          
            Log:info($returnmessage);
            return response()->json([
                'status' => $returnstatus,
                'success' => $returnsuccess,
                'message' => $returnmessage,
            ], 200);
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
        }
        
    }
    
    public function update_approvals($vendorID, $params){
        // Update self approver
        $approver = $this->vendorRepo->getProfileApproval($vendorID, 'vendor');
        $approval = VendorApproval::where('vendor_id', $vendorID)
                ->where(['as_position' => 'vendor', 'approver' => $approver->approver, 'is_done' => false])
                ->whereNull('deleted_at')
                ->update(['is_done' => true]);
        if($params['status'] == 'revise'){
            // Cancel All Approval from other role / user
            VendorApproval::where('vendor_id', $vendorID)
                ->where(['as_position' => 'vendor', 'is_done' => false])
                ->whereNull('deleted_at')
                ->delete();
        }    
        return $approval;
    }    
    
    public function update_history_status($vendorID, $params){
        // Delete last history
        VendorHistoryStatus::where('vendor_id', $vendorID)->delete();
        // Create Vendor History
        // Check version of history status
        $version = '0';
        $versionHistory = $this->get_status_version_tracking($vendorID, $params['status'], $params['description']);
        if(!empty($versionHistory)){
            $version = (int) $versionHistory->version + 1;
        }
        $vendorHistory = new VendorHistoryStatus([
            'vendor_id' => $vendorID,
            'status' => $params['status'],
            'description' => $params['description'],
            'version' => $version,
            'remarks' => $params['remarks'],
            'created_by' => $params['created_by']
        ]);
        return $vendorHistory->save();
    }
    
    public function update_workflow($vendorID, $params){
        VendorWorkflow::where('vendor_id', $vendorID)
            ->whereNotNull('started_at')
            ->whereNull('finished_at')
            ->update([
                'finished_at' => now(), 
                'remarks' => $params['remarks'],
                'created_by' => auth()->user()->userid
            ]);
        if($params['status'] === 'revise'){
            // Stoping All Workflow then create new workflow re-initial submission
            VendorWorkflow::where('vendor_id', $vendorID)
            ->whereNull('finished_at')
            ->update([
                'finished_at' => now(), 
                'remarks' => $params['remarks'],
                'created_by' => auth()->user()->userid
            ]); //stopping tasks that has not yet been started (started_at is null)
            $vendorWorkflow = new VendorWorkflow([
                'vendor_id' => $vendorID,
                'activity' => 'Re-Initial Submission',
                'remarks' => $params['remarks'],
                'started_at' => now(),
                'finished_at' => null,
            ]);
            return $vendorWorkflow->save();
        } else {
            // Run workflow to next approval
            $flow = VendorWorkflow::where('vendor_id', $vendorID)
                ->whereNull('started_at')
                ->whereNull('finished_at')
                ->orderby('id', 'asc')
                ->first();
            return $flow ? $flow->update(['started_at' => now()]) : true;
        }
    }
    
    public function get_next_approver($vendorID){
        //start next task (if exists)
        return $outstandingTaskCount = VendorWorkflow::where('vendor_id', $vendorID)
            ->whereNull('started_at')
            ->whereNull('finished_at')
            ->orderby('id', 'asc')
            ->count();
    }


    public function update_profile_detail_status($vendorID, $params){
        return VendorProfileDetailStatus::join('vendor_profiles', function($join) use($vendorID){
                $join->on('vendor_profiles.id', '=', 'vendor_profile_detail_statuses.vendor_profile_id')
                        ->whereNull('vendor_profiles.deleted_at')
                        ->where('vendor_profiles.vendor_id', $vendorID);
            })
            ->whereNull('vendor_profile_detail_statuses.deleted_at')
            ->update([
                'general_status' => $this->general,
                'deed_status' => $this->deeds,
                'shareholder_status' => $this->shareholder,
                'bodboc_status' => $this->bodboc,
                'businesspermit_status' => $this->businesspermit,
                'pic_status' => $this->pic,
                'equipment_status' => $this->equipment,
                'expert_status' => $this->expert,
                'certification_status' => $this->certification,
                'scopeofsupply_status' => $this->scopesupply,
                'experience_status' => $this->experience,
                'bankaccount_status' => $this->bankaccount,
                'financial_status' => $this->financial,
                'tax_status' => $this->tax,
                'is_approved' => $params['is_approved'], 
                'is_revised' => $params['is_revised'], 
                'is_submitted' => $params['is_submitted'],
                'update_vendor_data_status' => isset($params['update_vendor_data_status']) ? $params['update_vendor_data_status'] : DB::raw("vendor_profile_detail_statuses.update_vendor_data_status")
            ]);
    }    
    
    public function get_status_version_tracking($id, $status, $description){
        $arrCondition = [
            'vendor_id' => $id,
            'status' => $status,
            'description' => $description,
        ];
        return VendorHistoryStatus::select('version')->where($arrCondition)->withTrashed()
                ->orderby('id', 'desc')->first();
    }
    
    public function set_profile_detail_status($vendorGroup, $companyType, $profileStatus){
        if($companyType === 'PT' || $companyType === 'CV' || $companyType === 'Yayasan' || $companyType === 'Koperasi'){
            $this->define_profiles_required(
                        $profileStatus, $profileStatus, 'none', $profileStatus, $profileStatus, $profileStatus, 
                        'none', 'none', 'none', $profileStatus, 'none', 
                        $profileStatus, 'none', $profileStatus);
        } else if($companyType === 'Perorangan' || $companyType === 'Others'){
            $this->define_profiles_required(
                        $profileStatus, 'none', 'none', 'none', 'none', $profileStatus, 
                        'none', 'none', 'none', 'none', 'none', 
                        $profileStatus, 'none', $profileStatus);
        } else if($companyType === 'Toko'){
            $this->define_profiles_required(
                        $profileStatus, 'none', 'none', 'none', 'none', $profileStatus, 
                        'none', 'none', 'none', 'none', 'none', 
                        $profileStatus, 'none', 'none');
        } else {
            $this->define_profiles_required(
                        $profileStatus, $profileStatus, $profileStatus, $profileStatus, $profileStatus, $profileStatus, 
                        $profileStatus, 'none', $profileStatus, $profileStatus, $profileStatus, 
                        $profileStatus, $profileStatus, $profileStatus);
        }
        if($vendorGroup === 'foreign'){
            $this->define_profiles_required(
                        $profileStatus, 'none', 'none', 'none', 'none', $profileStatus, 
                        'none', 'none', $profileStatus, $profileStatus, 'none', 
                        $profileStatus, 'none', 'none');
        }
    }
    
    public function view_detail_profiles(Request $request, $vendorid = '', $submenu = ''){
        $vendorID = $vendorid;
        $arrColumnVal = array();
        $arrColumnVal['id'] = $vendorID;
        $data = $this->show_changes_profiles($arrColumnVal);
        $data['vendor'] = $data;
        $data['submenu'] = $submenu;
        $data['profiles'] = [];
        $vendorInfo = Vendor::select('vendor_group')->where('id', $vendorID)->first();

        switch ($submenu) {
            //ADMINISTRATION
            case 'general':
                $menu = 'administration';
                if($vendorInfo->vendor_group === 'local'){
                    $data['fields'] = [
                        'company_name','company_type','location_category',
                        'street','house_number','building_name','kavling_floor_number','rt','rw','village',
                        'country','province','city','sub_district',
                        'postal_code','phone_number','fax_number','website','company_email'
                    ];                    
                } else {
                    $data['fields'] = [
                        'company_name','company_type','location_category',
                        'address_1','address_2','address_3',
                        'country','postal_code','phone_number','fax_number','website','company_email'
                    ];
                }
                $data['companyTypes'] = RefCompanyType::all();
                $data['selectCountry'] = RefCountry::select('country_code','country_description')->withTrashed(false)->orderby('country_description', 'ASC')->pluck('country_description', 'country_code');
                $data['attachmentList'] = [];

                $refPostalCodes = RefPostalCode::all();
                $postalCodes = [];
                foreach($refPostalCodes as $postalcode){
                    $postalCodes[$postalcode->country_code] = [
                        'length' => $postalcode->length,
                        'required' => $postalcode->required,
                        'check_rule' => $postalcode->check_rule
                    ];
                }
                $data['postalCodes'] = $postalCodes;
            break;
            case 'deeds':
                $menu = 'administration';
                $data['fields'] = [
                    'deed_type','deed_number','deed_date','notary_name','sk_menkumham_number','sk_menkumham_date',
                    'attachment'
                ];
                $data['attachmentList'] = ['attachment'];
            break;
            case 'shareholders':
                $menu = 'administration';
                $data['fields'] = ['full_name','nationality','share_percentage','email', 'ktp_number', 'ktp_attachment'];    
                $data['attachmentList'] = ['ktp_attachment'];
            break;
            case 'bod-boc':
                $menu = 'administration';
                $data['fields'] = ['board_type','is_person_company_shareholder','full_name','nationality', 
                        'position', 'email', 'phone_number', 'company_head'];    
                $data['attachmentList'] = [];
            break;
            case 'business-permit':
                $menu = 'administration';
                $data['fields'] = ['business_permit_type','attachment','business_class','business_permit_number','valid_from_date', 'valid_thru_date', 'issued_by'];
                $data['attachmentList'] = ['attachment'];
            break;
            case 'pic':
                $menu = 'administration';
                $data['fields'] = ['username','full_name','email','phone', 'primary_data'];
                $data['attachmentList'] = [];
            break;

            //FINANCES
            case 'bank-account':
                $menu = 'finance';
                $data['fields'] = ['account_holder_name','account_number','currency','bank_name_description','bank_address','bank_statement_letter'];
                $data['currencies'] = RefCurrency::orderBy('currency')->pluck('description','currency');
                $data['banks'] = RefBank::where('deleteflg','')->orderBy('description')->pluck('description','id');
                $data['attachmentList'] = ['bank_statement_letter'];
            break;
            case 'tax-document':
                $menu = 'finance';
                $data['taxDocumentTypes'] = RefListOption::where('type','vendor_tax_codes')->where('deleteflg', false)->pluck('value','key');
                $data['fields'] = ['tax_document_type_description','tax_document_number','issued_date','tax_document_attachment'];
                $data['attachmentList'] = ['tax_document_attachment'];
            break;
            case 'financial':
                $menu = 'finance';
                $data['auditType'] = ['Audited', 'Non Audited'];
                $data['currencies'] = RefCurrency::orderBy('currency')->pluck('description','currency');
                $data['activa'] = [
                    'assets' => [
                        'current_assets' => [
                            'cash',
                            'bank',
                            'receivables'=>[
                                'short_term_investments',
                                'long_term_investments',
                                'total_receivables',
                            ],
                            'inventories',
                            'work_in_progress',
                            'total_current_assets'
                        ],
                        'fixed_assets' => [
                            'equipments_and_machineries',
                            'fixed_inventories',
                            'buildings',
                            'lands',
                            'total_fixed_assets'
                        ],
                        'other_assets',
                    ],
                ];
                $data['passiva'] = [
                    'liabilities'=> [
                        'short_term_debts' => [
                            'incoming_debts',
                            'taxes_payables',
                            'other_payables',
                            'total_short_term_debts'
                        ],
                        'long_term_payables',
                        'total_net_worth'
                    ]
                ];
                $data['merge'] = array_merge($data['activa'],['total_assets'],$data['passiva'],['total_liabilities']);
                $data['mergeCnt'] = 28;
                $data['fields'] = ['financial_statement_date','public_accountant_full_name','audit','financial_statement_year','valid_thru_date','currency','financial_statement_attachment'];
                $data['attachmentList'] = ['financial_statement_attachment'];
                $data['businessClass'] = ['Small'=>'0','Medium'=>'500000000','Large'=>'1000000000'];
            break;

            //EXPERIENCES
            case 'tools':
                $menu = 'workexperience';
                $data['fields'] = ['equipment_type','total_qty','measurement','brand','condition','location','manufacturing_date','ownership'];
                $data['attachmentList'] = [];
                $data['equipmentTypes'] = ['01'=>'01 - Bahan Baku/Barang Dagangan'];
            break;
            case 'expert':
                $menu = 'workexperience';
                $data['fields'] = [
                    'full_name','date_of_birth','education','university','experts_university','major','ktp_number','address','job_experience',
                    'years_experience','certification_number','attachment'
                ];
                $data['attachmentList'] = ['attachment'];
            break;
            case 'certification':
                $menu = 'workexperience';
                $data['certifications'] = ['ISO','OHSAS','ASME','API','TKDN','Others'];
                $data['fields'] = [
                    'certification_type','description','valid_from_date','valid_thru_date','attachment'
                ];
                $data['attachmentList'] = ['attachment'];
            break;
            case 'competency':
                $menu = 'workexperience';
                $data['classifications'] = RefScopeOfSupply::orderBy('id')->pluck('description','id');
                $data['subclassifications'] = ['011'=>'011 - Phospate Rock'];
                $data['vendorTypes'] = ['Agent','Distributor','Manufacturer','Supplier','Subcontractor'];
                $data['fields'] = [
                    'classification_description','detail_competency','vendor_type','attachment'
                ];
                $data['attachmentList'] = ['attachment'];
            break;
            case 'work-experience':
                $menu = 'workexperience';
                $data['fields'] = [
                    'classification_description','project_name','project_location','contract_owner','country',
                    'province','city','sub_district','postal_code','address','contact_person','phone_number','contract_number','valid_from_date',
                    'valid_thru_date','currency','contract_value','bast_wan_date','bast_wan_number','bast_wan_attachment',
                ];
                $data['currencies'] = RefCurrency::orderBy('currency')->pluck('description','currency');
                $data['classifications'] = RefScopeOfSupply::orderBy('id')->pluck('description','id');
                $data['subclassifications'] = ['011'=>'011 - Phospate Rock'];
                $data['countries'] = RefCountry::select('country_code','country_description')->withTrashed(false)->orderby('country_description', 'ASC')->pluck('country_description', 'country_code');
                $data['provinces'] = [];
                $data['cities'] = [];
                $data['subdistricts'] = [];
                $data['attachmentList'] = ['bast_wan_attachment'];
            break;

            default:
                $menu = 'administration';
                $submenu = 'general';
                $data['fields'] = [
                    'company_name','company_type','location_category','country','province','city','sub_district',
                    'postal_code','address','phone_number','fax_number','website','company_email'
                ];
                $data['companyTypes'] = RefCompanyType::all();
                $data['attachmentList'] = [];
                $refPostalCodes = RefPostalCode::all();
                $postalCodes = [];
                foreach($refPostalCodes as $postalcode){
                    $postalCodes[$postalcode->country_code] = [
                        'length' => $postalcode->length,
                        'required' => $postalcode->required,
                        'check_rule' => $postalcode->check_rule
                    ];
                }
                $data['postalCodes'] = $postalCodes;
            break;
        }
        $data['profiles'] = $this->vendorRepo->getProfileDetailsByVendorId($vendorID, $submenu)->get();
        $vp = VendorProfile::where('vendor_id',$vendorID)->first();
        $data['blacklisted'] = $vp->is_blacklisted; 
        $data['checklist'] = $this->get_profile_checklist($vendorID);
        // $data['candidate'] = $this->specific_find($arrColumnVal);
        $data['candidate'] = $this->vendorRepo->getProfileByVendorId($vendorID);
        $data['type'] = $submenu;
        $vendor = Vendor::find($vendorID);
        $data['registrationStatus'] = $vendor->registration_status == 'applicant' ? 'candidate' : $vendor->registration_status;
        $data['storage'] = asset('storage/vendor/profiles/'.$vendorID.'/'.$submenu);
        $data['accordionMenu'] = 'layouts.candidate_menu';
        $segment = request()->segment(1);
        $data['profileUrl'] = route(substr($segment,0,strlen($segment)-1).'.profile', $data['candidate']->vendor_id);
        return View::make("vendor/profiles/$menu/" . $submenu, $data);
    }    
    
    public function find_profile(Request $request, $tableInput){
        if(in_array($tableInput, $this->tableKeys)){
            $table = $this->tables[$tableInput];
            if($table === 'vendor_profile_generals'){
                $tmp = DB::table($table)
                    ->select("$table.*", 
                        'vendors.vendor_group',
                        'ref_countries.country_description',
                        'ref_provinces.region_description',
                        'ref_cities.city_description',
                        'ref_sub_districts.district_description'
                    )
                    ->join('vendor_profiles', function ($join) {
                        $join->on('vendor_profiles.id', '=', "vendor_profile_generals.vendor_profile_id")
                        ->whereNull('vendor_profiles.deleted_at');
                    })
                    ->join('vendors', function ($join) {
                        $join->on('vendors.id', '=', "vendor_profiles.vendor_id")
                        ->whereNull('vendors.deleted_at');
                    })
                    ->join('ref_countries', function ($join) {
                        $join->on('ref_countries.country_code', '=', 'vendor_profile_generals.country')
                        ->whereNull('ref_countries.deleted_at');
                    })
                    ->leftJoin('ref_provinces', function ($join) {
                        $join->on('ref_provinces.region_code', '=', 'vendor_profile_generals.province');
                        $join->on('ref_provinces.country_code', '=', 'vendor_profile_generals.country')
                        ->whereNull('ref_provinces.deleted_at');
                    })
                    ->leftJoin('ref_cities', function ($join) {
                        $join->on('ref_cities.city_code', '=', 'vendor_profile_generals.city');
                        $join->on('ref_cities.country_code', 'vendor_profile_generals.country');
                        $join->on('ref_cities.region_code', 'vendor_profile_generals.province')
                        ->whereNull('ref_cities.deleted_at');
                    })
                    ->leftJoin('ref_sub_districts', function ($join) {
                        $join->on('ref_sub_districts.district_code', '=', 'vendor_profile_generals.sub_district');
                        $join->on('ref_sub_districts.country_code', 'vendor_profile_generals.country');
                        $join->on('ref_sub_districts.region_code', 'vendor_profile_generals.province');
                        $join->on('ref_sub_districts.city_code', 'vendor_profile_generals.city')
                        ->whereNull('ref_sub_districts.deleted_at');
                    })
                    ->whereNull("$table.deleted_at")
                    ->where("$table.id", $request->get('id'))
                    ->orderBy("$table.id", 'DESC')
                    ->first();
            }else {
                $tmp = DB::table($table)
                    ->whereNull('deleted_at')
                    ->where('id', $request->get('id'))
                    ->orderBy('id', 'DESC')
                    ->first();
            }
            return response()->json($tmp);
        }else{
            return response()->json([]);
        }
    }
    
    public function show_changes_profiles($column){
        try {
            if (isset($column['id'])) {
                $column['vendor_profiles.vendor_id'] = $column['id'];
                // remove ambiguos
                unset($column['id']);
            }
            return VendorProfile::select("vendor_profiles.*", "vendors.vendor_group")
                    ->join('vendors', function($join){
                        $join->on("vendors.id", "=", "vendor_profiles.vendor_id")
                                ->whereNull("vendors.deleted_at");
                    })->where($column)->orderby('id', 'desc')->first();
        } catch (QueryException $e) {
            Log::error($e->getMessage());
        }
    }
    
    public function get_profile_checklist($id){
        $vendorID = $id;
        return VendorProfileDetailStatus::select("vendor_profile_detail_statuses.*")
            ->join('vendor_profiles', function ($join) {
                $join->on('vendor_profiles.id', '=', 'vendor_profile_detail_statuses.vendor_profile_id')
                ->whereNull('vendor_profiles.deleted_at');
            })
            ->where('vendor_profiles.vendor_id', $vendorID)
            ->whereNull('vendor_profile_detail_statuses.deleted_at')->first();
    }
    
    public function define_profiles_required($general, $deeds, $shareholder, $bodboc, $businesspermit, $pic, $equipment, $expert, $certification, $scopesupply, $experience, $bankaccount, $financial, $tax){
        $this->general = $general;
        $this->deeds = $deeds;
        $this->shareholder = $shareholder;
        $this->bodboc = $bodboc;
        $this->businesspermit = $businesspermit;
        $this->pic = $pic;
        $this->equipment = $equipment;
        $this->expert = $expert;
        $this->certification = $certification;
        $this->scopesupply = $scopesupply;
        $this->experience = $experience;
        $this->bankaccount = $bankaccount;
        $this->financial = $financial;
        $this->tax = $tax;
    }
    
    public function update_current_data($vendorID){
        $deleteArray = [];
        foreach ($this->tables as $key => $table) {
            $deleteArray[$key] = DB::table($table)->select("$table.id")
                ->join("$table as b", function($join) use ($table){
                    $join->on(DB::raw("b.parent_id"), "=", "$table.id")
                            ->whereNull(DB::raw("b.deleted_at"));
                })
                ->join('vendor_profiles', function($join) use ($table){
                    $join->on("vendor_profiles.id", "=", "$table.vendor_profile_id")
                            ->whereNull("vendor_profiles.deleted_at");
                })
                ->where([
                    "vendor_profiles.vendor_id" => $vendorID,
                    "$table.parent_id" => 0
                ])->delete();
            // Update current data of General
            DB::table($table)
                ->join('vendor_profiles', function($join) use ($table){
                    $join->on("vendor_profiles.id", "=", "$table.vendor_profile_id")
                        ->whereNull("vendor_profiles.deleted_at");
                })
                ->where([
                    'vendor_profiles.vendor_id' => $vendorID,
                    'is_current_data' => false
                ])->update([
                    'is_current_data' => true,
                    'parent_id' => 0
                ]);
        }
        return $deleteArray;
    }
    
    public function sendEmail($request, $status, $vendorInfo, $nextApprover = null){
        $adminEmail = User::role('Admin Vendor')->join("user_extensions","user_extensions.user_id","=","users.id")->where("user_extensions.status","=",1)->whereNotNull("users.email")->whereNotNull("users.email")->pluck('email')->toArray();
        $qmrEmail = User::role('QMR')->join("user_extensions","user_extensions.user_id","=","users.id")->where("user_extensions.status","=",1)->whereNotNull("users.email")->whereNotNull("users.email")->pluck('email')->toArray();      
        $picEmail = empty($vendorInfo->pic_email) ? $vendorInfo->company_email : $vendorInfo->pic_email;
        $registrationStatus = $vendorInfo->registration_status;
        $recipients = [];
        $ccs = [];
        try {
            if($status == "revise"){
                $picEmail = empty($vendorInfo->pic_email) ? $vendorInfo->company_email : $vendorInfo->pic_email;
                
                $arrdata = [];
                $arrdata['vendor_name'] = $vendorInfo->vendor_name;
                $arrdata['purchasing_org'] = $vendorInfo->purchase_org_code;
                $arrdata['purchasing_org_description'] = $vendorInfo->purchase_org_description;
                $arrdata['comments'] = $request->remarks;
                $adminVendors = User::role('Admin Vendor')->join("user_extensions","user_extensions.user_id","=","users.id")->where("user_extensions.status","=",1)->whereNotNull("users.email")->get();
                $adminOnshoreEmails = [];
                $adminOffshoreEmails = [];
                foreach($adminVendors as $adminVendor){
                    $purchorgs = $this->buyerRepo->getUserPurchaseOrganization(auth()->user())->toArray();
                    if(in_array(1,$purchorgs)){
                        $adminOnshoreEmails[] = $adminVendor->email;
                    }
                    if(in_array(2,$purchorgs)){
                        $adminOffshoreEmails[] = $adminVendor->email;
                    }
                }


                $arrdata['admin_onshore_email'] = count($adminOnshoreEmails)>0 ? $adminOnshoreEmails[0] : auth()->user()->email;
                $arrdata['admin_offshore_email'] = count($adminOffshoreEmails)>0 ? $adminOffshoreEmails[0] : auth()->user()->email;
                if($registrationStatus === 'vendor'){
                    $arrdata['mailtype'] = 'revise_vendordata_for_pic';
                    $arrdata['subject'] = 'REVISED: Vendor Registration - ' . $vendorInfo->registration_status . ' ' . $vendorInfo->vendor_name;
                    $arrdata['registration_status'] = 'Vendor';
                } else {
                    $arrdata['mailtype'] = 'revise_candidate_for_pic';
                    $arrdata['subject'] = 'REVISED: Vendor Registration - ' . $vendorInfo->registration_status . ' ' . $vendorInfo->vendor_name;
                    $arrdata['registration_status'] = 'Candidate';
                }
                $objData = (object) $arrdata;
                if($nextApprover > 0){
                    // Send Mail to PIC, CC to Admin Vendor
                    array_push($recipients, $picEmail);
                    $ccs = $adminEmail;
                    if ($this->vendorRepo->isValidEmail($recipients) && $this->vendorRepo->isValidEmail($ccs)){
                        ProcessEmail::dispatch($recipients, $ccs, $objData);
//                        Mail::to($vendorInfo->pic_email)->cc($adminEmail)->send(new TestMail($arrdata));
                    }else{
                        $this->log("===========email failed==============. email :".json_encode($recipients).", cc: ".json_encode($ccs).", obj: ".json_encode($arrdata));
                    }
                } else {
                    // Send Mail to PIC, CC to Admin Vendor, QMR
                    array_push($recipients, $picEmail);
                    $ccs = array_merge($adminEmail, $qmrEmail);
                    if ($this->vendorRepo->isValidEmail($recipients) && $this->vendorRepo->isValidEmail($ccs)){
                        ProcessEmail::dispatch($recipients, $ccs, $objData);
//                            Mail::to($vendorInfo->pic_email)->cc(array_merge($adminEmail, $qmrEmail))->send(new TestMail($arrdata));
                    }else{
                        $this->log("===========email failed==============. email :".json_encode($recipients).", CC : ".json_encode($ccs).", obj: ".json_encode($arrdata));
                    }
                }
            } else if($status == "approved"){
                if($nextApprover > 0){
                    // Send Mail to QMR, CC to Admin Vendor
                    $recipients = $qmrEmail;
                    $ccs = $adminEmail;
                    $arrdata = [];
                    $arrdata['vendor_name'] = $vendorInfo->vendor_name;
                    $arrdata['purchasing_org'] = $vendorInfo->purchase_org_code;
                    $arrdata['purchasing_org_description'] = $vendorInfo->purchase_org_description;
                    $arrdata['registration_status'] = 'Vendor';
                    if($registrationStatus === 'vendor'){
                        $arrdata['mailtype'] = 'vendordata_for_final_approval';
                        $arrdata['subject'] = 'FOR APPROVAL: Vendor Registration - ' . $vendorInfo->registration_status . ' ' . $vendorInfo->vendor_name;
                    } else {
                        $arrdata['mailtype'] = 'candidate_for_final_approval';
                        $arrdata['subject'] = 'FOR APPROVAL: Vendor Registration - ' . $vendorInfo->registration_status . ' ' . $vendorInfo->vendor_name;                        
                    }
                    $objData = (object) $arrdata;
                    if ($this->vendorRepo->isValidEmail($recipients) && $this->vendorRepo->isValidEmail($ccs)){
                        ProcessEmail::dispatch($recipients, $ccs, $objData);
//                        Mail::to($qmrEmail)->cc($adminEmail)->send(new TestMail($objData));
                    } else {
                        $this->log("===========email failed==============. email :".json_encode($recipients).", cc: ".json_encode($ccs).", obj: ".json_encode($objData));
                    }
                } else {
                    // Send Mail to PIC Vendor, CC to Admin Vendor
                    array_push($recipients, $picEmail);
                    $ccs = $adminEmail;
                    $arrdata = [];
                    $arrdata['vendor_name'] = $vendorInfo->vendor_name;
                    $arrdata['purchasing_org'] = $vendorInfo->purchase_org_code;
                    $arrdata['purchasing_org_description'] = $vendorInfo->purchase_org_description;
                    $arrdata['company_type'] = $vendorInfo->company_type;
                    $arrdata['company_description'] = $vendorInfo->company_type_description;
                    $arrdata['registration_status'] = 'Vendor';
                    if($registrationStatus === 'vendor'){
                        $arrdata['mailtype'] = 'vendordata_has_approved';
                        $arrdata['subject'] = 'APPROVED: Vendor Registration - ' . $vendorInfo->registration_status . ' ' . $vendorInfo->vendor_name;
                    } else {
                        $arrdata['mailtype'] = 'candidate_has_approved';
                        $arrdata['subject'] = 'APPROVED: Vendor Registration - ' . $vendorInfo->registration_status . ' ' . $vendorInfo->vendor_name;
                    }
                    $objData = (object) $arrdata;
                    if ($this->vendorRepo->isValidEmail($recipients) && $this->vendorRepo->isValidEmail($ccs)){
                        ProcessEmail::dispatch($recipients, $ccs, $objData);
//                        Mail::to($vendorInfo->pic_email)->cc($adminEmail)->send(new TestMail($arrdata));
                    } else {
                        $this->log("===========email failed==============. email :".json_encode($recipients).", cc: ".json_encode($ccs).", obj: ".json_encode($objData));
                    }
                }
            }
        } catch (Exception $e){
            Log::error($e->getMessage());
        }                
    }
}
