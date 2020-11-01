<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Mail\TestMail;

use App\Models\Ref\RefPostalCode;
use App\RefCompanyType;
use App\RefPurchaseOrg;
use App\RefCountry;
use App\Vendor;
use App\VendorHistoryStatus;
use App\VendorProfile;
use App\VendorProfileDetailStatus;
use App\VendorWorkflow;
use App\VendorApproval;
use App\VendorProfileGeneral;
use App\VendorProfileBodboc;
use App\VendorProfilePic;
use App\VendorProfileTax;
use App\User;
use App\Repositories\VendorRepository;
use App\Repositories\BuyerRepository;
use App\RefCompanyGroup;
use View;
use DB;
use DataTables;
use File;
use Validator;
use App\Traits\AccessLog;
use App\Jobs\ProcessEmail;

class ApplicantController extends Controller {
    
    use AccessLog;
    
    protected $general, $deeds, $shareholder, $bodboc, $businesspermit, $pic, $equipment, 
            $expert, $certification, $scopesupply, $experience, $bankaccount, $financial, $tax;


    public function __construct() {
        /*
          $segments =  Request::segments();
          if(count($segments) > 1){
          if ($segments[1] === 'register' || $segments[1] === 'create'){
          // no need auth
          } else {
          $this->middleware('auth');
          }
          }
         * 
         */
        $this->vendorRepo = new VendorRepository();
        $this->buyerRepo = new BuyerRepository();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return View::make('applicants.list');
    }
    
    public function registration(){        
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
        $data['selectCompanyType'] = RefCompanyType::withTrashed(false)->orderby('id', 'ASC')->get();
        $data['selectPurchasingOrg'] = RefPurchaseOrg::select('id',DB::raw("CONCAT(org_code,' - ',description) as organization"))->withTrashed(false)->orderby('id', 'ASC')->pluck('organization', 'id');
        $data['selectCountry'] = RefCountry::select('country_code','country_description')->withTrashed(false)->orderby('country_description', 'ASC')->pluck('country_description', 'country_code');
        $data['selectProvince'] = [];
        $data['selectCity'] = [];
        $data['selectSubDistrict'] = [];
        $data['attachmentList'] = [];
        return View::make('applicants/registration', $data);
    }
    
    public function submit_form(Request $request){
        try{
            $maxSize = config('eproc.upload_file_size');
            $userid = auth()->user()->userid ?? 'applicant';
            $arrValidate = [
                'vendor_name' => 'required',
                'company_type_id' => 'required',
                'purchase_org_id' => 'required',
                'president_director' => 'required',
                'country' => 'required', 
                'postal_code' => 'required', 
                'phone_number' => 'required',
                'pic_full_name' => 'required', 
                'pic_mobile_number' => 'required',
                'pic_email' => 'required|regex:/(.+)@(.+)\.(.+)/i'
            ];
            $vendorType = $request->vendor_group;
            if ($vendorType == "local"){ // local
                if(trim($request->country)=='ID'){
                    $arrValidate['street'] = 'required';
                    $arrValidate['village'] = 'required';
                    $arrValidate['rt'] = 'required';
                    $arrValidate['rw'] = 'required';
                    $arrValidate['province'] = 'required';
                    $arrValidate['city'] = 'required';
                    $arrValidate['sub_district'] = 'required';                 
                }
            } else { // foreign
                $arrValidate['address_1'] = 'required';
            }
            // Add validation identity number (NPWP/TIN or ID CARD Number)
            $arrValidate['identity_number'] = 'required';

            if($request->file('identity_attachment') !== null){
                $arrValidate['identity_attachment'] = "max:$maxSize|mimes:jpeg,jpg,gif,pdf";
            }
            if($request->file('pkp_attachment') !== null){
                $arrValidate['pkp_attachment'] = "max:$maxSize";
            }        

            $tin_number = null;
            $tin_attachment = null;
            $idcard_number = null;
            $idcard_attachment = null;
            $pkp_type = $request->input('pkp_type');
            $pkp_number = $request->input('pkp_number');
            $pkp_attachment = null !== $request->file('pkp_attachment') ? $request->file('pkp_attachment')->getClientOriginalName() : '';
            $non_pkp_number = $request->input('non_pkp_number');
            if($request->input('identification_type') === 'tin'){
                $tin_number = $request->input('identity_number');
                $tin_attachment = null !== $request->file('identity_attachment') ? $request->file('identity_attachment')->getClientOriginalName() : '';
            } else if($request->input('identification_type') === 'id-card'){
                $idcard_number = $request->input('identity_number');
                $idcard_attachment = null !== $request->file('identity_attachment') ? $request->file('identity_attachment')->getClientOriginalName() : '';
                $pkp_type = null;
                $pkp_number = null;
                $pkp_attachment = null;
                $non_pkp_number = null;
                if($request->input('vendor_group') === 'foreign'){
                    $tin_number = config('eproc.vendor_management.tin_foreign');
                }
            }
            $phone_number = $request->input('phone_number');
            if((trim($request->phone_number) !== '' || $request->phone_number !== null) && (trim($request->phone_number_ext) !== '' || $request->phone_number_ext !== null)){
                $phone_number = $request->phone_number . '-' . $request->phone_number_ext;
            }
            $fax_number = $request->input('fax_number');
            if((trim($request->fax_number) !== '' || $request->fax_number !== null) && (trim($request->fax_number_ext) !== '' || $request->fax_number_ext !== null)){
                $fax_number = $request->fax_number . '-' . $request->fax_number_ext;
            }

            $validator = Validator::make($request->all(), $arrValidate);
            if ($validator->fails()) {
                return response()->json([
                    'status' => 200,
                    'success' => false,
                    'message' => $this->vendorRepo->getMessage($validator),
                ], 200);
                exit();
            }

            DB::beginTransaction();                
            // Create Vendor Header        

            // Check existing Data
            // Get Current Condition Vendor Data
            $vendorQueryData = Vendor::select(
                    'vendors.id',
                    'vendors.tin_number',
                    'vendors.idcard_number',
                    'vendors.city',
                    'vendor_profile_taxes.tax_document_number',
                    'vendor_profile_generals.city'
                )
                ->join('ref_company_types', function ($join) {
                    $join->on('ref_company_types.id', '=', 'vendors.company_type_id')
                        ->whereNull('ref_company_types.deleted_at');
                })
                ->join('vendor_history_statuses', function ($join) {
                    $join->on('vendor_history_statuses.vendor_id', '=', 'vendors.id')
                        ->where('vendor_history_statuses.status', '<>', 'rejected')
                        ->whereNull('vendor_history_statuses.deleted_at');
                })
                ->leftJoin("vendor_profiles", function($join){
                    $join->on("vendor_profiles.vendor_id", "=", "vendors.id")
                        ->whereNull('vendor_profiles.deleted_at');
                })
                ->leftJoin('vendor_profile_generals', function ($join) {
                    $join->on('vendor_profile_generals.vendor_profile_id', '=', 'vendor_profiles.id')
                        ->where('vendor_profile_generals.is_current_data', true)
                        ->whereNull('vendor_profile_generals.deleted_at');
                })
                ->leftJoin('vendor_profile_taxes', function ($join) {
                    $join->on('vendor_profile_taxes.vendor_profile_id', '=', 'vendor_profiles.id')
                        ->where('vendor_profile_taxes.is_current_data', true)
                        ->whereNull('vendor_profile_taxes.deleted_at');
                }) 
                // check case when registration_status is applicant, then check compare validation value from vendors table 
                // and case when status is candidate or vendor then check compare value validation from table profiles tax or general
                ->whereRaw("CASE WHEN vendor_profile_taxes.id is null THEN "
                        . "     CASE WHEN ? = 'tin' THEN "
                        . "         vendors.tin_number = ? "
                        . "     ELSE"
                        . "         vendors.idcard_number = ? "
                        . "     END "
                        . "ELSE "
                        . "     vendor_profile_taxes.tax_document_number = ? "
                        . "END", [$request->identification_type, $request->identity_number, $request->identity_number, $request->identity_number]);
            $vendorData = [];
            if ($vendorType == "local"){
//                $vendorData = $vendorQueryData->whereRaw(("case when vendor_profile_generals.id is null then vendors.city = '" . $request->city . "' else vendor_profile_generals.city = '" . $request->city . "' end"))->first();
                $vendorData = $vendorQueryData
                ->whereRaw("CASE WHEN vendor_profile_generals.id is null THEN "
                        . "     vendors.city = ? "
                        . "ELSE "
                        . "     vendor_profile_generals.city = ? "
                        . "END", [$request->city, $request->city])->first();
            } else {
                $vendorData = $vendorQueryData->first();
            }
            if($vendorData){
                DB::rollBack();
                return response()->json([
                    'status' => 200,
                    'success' => false,
                    'message' => "Applicant has already exist by City and Tax Identification Number or ID Card!",
                ], 200);
            } else {                
                $arrayRequestHeader = [
                    'vendor_name' => $request->input('vendor_name'),
                    'company_type_id' => $request->input('company_type_id'),
                    'purchase_org_id' => $request->input('purchase_org_id'),
                    'president_director' => $request->input('president_director'),
                    'country' => $request->input('country'),
                    'postal_code' => $request->input('postal_code')!=null ? $request->input('postal_code') : "",
                    'phone_number' => $phone_number,
                    'fax_number' => $fax_number,
                    'company_email' => $request->input('company_email'),
                    'company_site' => $request->input('company_site'),
                    'pic_full_name' => $request->input('pic_full_name'),
                    'pic_mobile_number' => $request->input('pic_mobile_number'),
                    'pic_email' => $request->input('pic_email'),
                    'tender_ref_number' => $request->input('tender_ref_number'),
                    'vendor_group' => $request->input('vendor_group'),
                    'identification_type' => $request->input('identification_type'),
                    'tin_number' => $tin_number,
                    'tin_attachment' => $tin_attachment,
                    'idcard_number' => $idcard_number,
                    'idcard_attachment' => $idcard_attachment,
                    'pkp_type' => $pkp_type,
                    'pkp_number' => $pkp_number,
                    'pkp_attachment' => $pkp_attachment,
                    'non_pkp_number' => $non_pkp_number
                ];
                if($request->input('vendor_group') === "local"){
                    $arrayRequestHeader['street'] = $request->input('street');
                    $arrayRequestHeader['building_name'] = $request->input('building_name');
                    $arrayRequestHeader['kavling_floor_number'] = $request->input('kavling_floor_number');
                    $arrayRequestHeader['village'] = $request->input('village');
                    $arrayRequestHeader['rt'] = $request->input('rt');
                    $arrayRequestHeader['rw'] = $request->input('rw');
                    $arrayRequestHeader['province'] = $request->input('province');
                    $arrayRequestHeader['city'] = $request->input('city');
                    $arrayRequestHeader['sub_district'] = $request->input('sub_district');
                    $arrayRequestHeader['house_number'] = $request->input('house_number');
                } else {
                    $arrayRequestHeader['address_1'] = $request->input('address_1');
                    $arrayRequestHeader['address_2'] = $request->input('address_2');
                    $arrayRequestHeader['address_3'] = $request->input('address_3');
                }
                $vendor = new Vendor($arrayRequestHeader);
                //dd($vendor);
                $vendor->save();
                $lastID = $vendor->id;

                // Create History
                $vendorHistory = new VendorHistoryStatus([
                    'vendor_id' => $lastID,
                    'status' => 'submit',
                    'description' => 'Form Submission',
                    'version' => '0',
                    'remarks' => null,
                    'created_by' => 'applicant'
                ]);
                $vendorHistory->save();

                // Generate Workflow By System
                $workflow = config('workflow.applicant-submission.tasks');
                foreach($workflow as $task){
                    $vendorWorkflow = new VendorWorkflow([
                        'vendor_id' => $lastID,
                        'activity' => $task['activity'],
                        'remarks' => $task['remarks'],
                        'started_at' => $task['started_at']=='now' ? now() : null,
                        'finished_at' => $task['finished_at']=='now' ? now() : null,
                        'permission' => $task['permission'],
                        'created_by' => $userid
                    ]);
                    $vendorWorkflow->save();
                }

                // Generate Approval By System
                $vendorApproval = new VendorApproval([
                    'vendor_id' => $lastID,
                    'as_position' => 'candidate',
                    'approver' => 'admin',
                    'sequence_level' => 0,
                    'is_done' => false,
                    'created_by' => 'applicant'
                ]);
                $vendorApproval->save();

                // Upload File
                $path1 = [];
                $path2 = [];
                if($request->file('pkp_attachment') !== null){
                    $path1 = Storage::putFileAs(
                                'public/vendor/profiles/' . $lastID,
                                $request->file('pkp_attachment'), $request->file('pkp_attachment')->getClientOriginalName()
                    );
                } 
                if($request->file('tin_attachment') !== null) {
                    $path2 = Storage::putFileAs(
                                'public/vendor/profiles/' . $lastID,
                                $request->file('npwp_tin_attachment'), $request->file('npwp_tin_attachment')->getClientOriginalName()
                    );
                }
                if($request->file('identity_attachment') !== null) {
                    $path2 = Storage::putFileAs(
                                'public/vendor/profiles/' . $lastID,
                                $request->file('identity_attachment'), $request->file('identity_attachment')->getClientOriginalName()
                    );
                }
                DB::commit();
                
                @$this->sendEmail($request, 'registration');
                
                return response()->json([
                    'status' => 200,
                    'success' => true,
                    'message' => "Applicant data has been submitted successfully!",
                ], 200);
            }
        } catch (Exception $e) {
            DB::rollBack();            
            return response()->json([
                'status' => $e->getCode() ? $e->getCode() : 500,
                'success' => false,
                'message' => "Applicant data has failed to be registered!",
            ], 200);
        }        
    }

    // View Applicants
    public function view_applicants(Request $request){
        $data['storage']=asset('storage/vendor/profiles');
        return View::make('applicants.list',$data);
    }
    
    // Datatables ServerSide
    public function datatable_list_applicants(Request $request) {
        if (request()->ajax()) {
            $vendor = $this->vendorRepo->getListVendorByType('applicant')
                ->addSelect('vendor_workflows.activity')
                ->addSelect('vendor_history_statuses.status')
                ->join('vendor_history_statuses', function ($join) {
                    $join->on('vendor_history_statuses.vendor_id', '=', 'vendors.id')
                    ->whereNull('vendor_history_statuses.deleted_at');
                })
                ->leftJoin('vendor_workflows', function ($join) {
                    $join->on('vendor_workflows.vendor_id', '=', 'vendors.id')
                    ->whereNotNull('vendor_workflows.started_at')
                    ->whereNull('vendor_workflows.finished_at')
                    ->whereNull('vendor_workflows.deleted_at');
                })
                ->whereNull('vendors.vendor_code')
                ->orderBy('vendors.id','desc')
                ->get();

            $array = array(
                'Status' => true,
                'Data' => $vendor,
                'Message' => ''
            );
            $result = (object) $array;
            $data = $result->Data;
            return DataTables::of($data)->editColumn('created_at', function ($vendor) {
                //change over here
                return date('d.m.Y H:i', strtotime($vendor->created_at));
            })->make(true);
        }
    }

    public function specific_find($column) {        
        if (isset($column['id'])) {
            $column['vendors.id'] = $column['id'];
            // remove ambiguos
            unset($column['id']);
        }
        // DB::enableQueryLog();
        $vendor =  $this->vendorRepo->getQueryApplicantById($column['vendors.id'])
            ->addSelect('vendor_history_statuses.status')
            ->leftJoin('vendor_history_statuses', "vendor_history_statuses.vendor_id", '=', "vendors.id")
            ->where('vendors.registration_status', 'applicant')
            ->where($column)->orderby('vendors.id', 'DESC')
            ->first();
        return $vendor;
        
    }
    
    public function get_workflow($column){
        return VendorWorkflow::where($column)->whereNull('vendor_workflows.deleted_at')->orderby('vendor_workflows.id', 'desc')->get();
    }
    
    public function get_status($column){
        return VendorWorkflow::select("vendor_workflows.*", "vendor_history_statuses.status")
                ->join('vendors', 'vendors.id', '=', 'vendor_workflows.vendor_id')
                ->join('vendor_history_statuses', function ($join) {
                    $join->on('vendor_history_statuses.vendor_id', '=', 'vendors.id')
                    ->whereNull('vendor_history_statuses.deleted_at');
                })
                ->where($column)->whereNull('vendor_workflows.deleted_at')->orderby('vendor_workflows.id', 'desc')->first();
    }

    public function show_profile(Request $request) {
        $arrColumnVal = array();
        
        // Applicant Profile
        foreach ($request->except('_token') as $key => $value) {
            $arrColumnVal[$key] = $value;
        }
        $data = $this->specific_find($arrColumnVal);
        
        // Workflow / Comments History
        $arrCondWorkflow = [
            'vendor_id' => $request->id
        ];
        $workflow_data = $this->get_workflow($arrCondWorkflow);
        
        // Waiting Approval Status
        $arrWaitingApproval = [
            'vendor_workflows.vendor_id' => $request->id,
            'vendor_workflows.activity' => 'Approval By Admin',
            'vendor_workflows.finished_at' => NULL
        ];
        $waiting_approval = $this->get_status($arrWaitingApproval);

        $canApprove = $this->buyerRepo->userHavePurchaseOrganization(auth()->user(),$data->purchase_org_id);
        // $canApprove = $canApprove && auth()->user()->hasPermission();

        $data['applicant'] = $data;
        $data['listworkflow'] = $workflow_data;
        $data['approvalstatus'] = $waiting_approval;
        $data['canApprove'] = $canApprove;
        $data['storage']=asset('storage/vendor/profiles');
        return View::make('applicants/profile', $data);
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

    public function approval(Request $request) {        
        $reqData = $request->all();
        $vendorID = $reqData['vendor_id'];
        $status = $reqData['status'];
        $desc_status = $reqData['status'];
        $remarks = $reqData['remarks'];
        $userid = auth()->user()->userid;    
        // Get Vendor Info
        $arrCondition = [
            'vendors.id' => $vendorID
        ];
        $vendorInfo = Vendor::select('vendors.*', 'ref_company_types.company_type')
                ->join('ref_company_types', function ($join) {
                    $join->on('ref_company_types.id', '=', 'vendors.company_type_id')
                    ->whereNull('ref_company_types.deleted_at');
                })
                ->where($arrCondition)->orderby('vendors.id', 'desc')->first();         
        if($status === 'Applicant Rejected by Admin'){
            // Update Vendor History
            VendorHistoryStatus::where('vendor_id', $vendorID)->delete();

            // Create Vendor history
            $status = 'rejected';
            $description = $desc_status;
            $version = '0';
            $versionHistory = $this->get_status_version_tracking($reqData['vendor_id'], $status, $description);
            if(!empty($versionHistory)){
                $version = (int) $versionHistory->version + 1;
            }
            $vendorHistory = new VendorHistoryStatus([
                'vendor_id' => $vendorID,
                'status' => $status,
                'description' => $description,
                'version' => $version,
                'remarks' => $remarks,
                'created_by' => 'applicant'
            ]);
            $vendorHistory->save();
            
            // Complete Workflow before
            VendorWorkflow::where('vendor_id', $vendorID)
                ->whereNull('finished_at')
                ->update(['finished_at' => now(), 'remarks' => $remarks]);
            
            // Update Approval
            VendorApproval::where('vendor_id', $vendorID)
                ->where(['as_position'=>'candidate', 'approver' => 'admin', 'is_done' => false])
                ->whereNull('deleted_at')
                ->update(['is_done' => true, 'created_by' => auth()->user()->userid]);
            
            // Email to PIC
            @$this->sendEmail($request, $status);
            
            return redirect('/admin/applicants')->withSuccess('Applicant data has been ' . $status . '!');
        } else {
            $status = 'approved';
        }     
        
        $arr_registration_number = $this->vendorRepo->createRegistrationNumber($reqData, $vendorInfo);
        $registration_number = $arr_registration_number["registration_number"];
        $last_number = $arr_registration_number["nextNumber"];
        //dd($registration_number,$last_number);
        try {
            DB::beginTransaction();            
            // Generate Vendor Code
            // Generate Registration Number
            
            if ($vendorInfo->vendor_group=="foreign")
                RefCompanyGroup::where('name', "foreign")->update(['last_number' => $last_number]);                        
            else
                RefCompanyGroup::where('name', "local")->update(['last_number' => $last_number]);                        
            
            Vendor::where('id', $vendorID)
                ->whereNull('deleted_at')
                ->update(['vendor_code' => $registration_number, 'registration_status' => 'candidate']);                        
            
            // Update Vendor History
            VendorHistoryStatus::where('vendor_id', $vendorID)
                    ->delete();
            // Create Vendor history            
            $description = $desc_status;
            $version = '0';
            $versionHistory = $this->get_status_version_tracking($reqData['vendor_id'], $status, $description);
            if(!empty($versionHistory)){
                $version = (int) $versionHistory->version + 1;
            }
            $vendorHistory = new VendorHistoryStatus([
                'vendor_id' => $vendorID,
                'status' => $status,
                'description' => $description,
                'version' => $version,
                'remarks' => $remarks,
                'created_by' => 'applicant'
            ]);
            $vendorHistory->save();
            
            // Run Workflow
            // Generate Workflow By System
            // Complete Workflow before
            VendorWorkflow::where('vendor_id', $vendorID)
                ->whereNull('finished_at')
                ->update(['finished_at' => now(), 'remarks' => $remarks]);
            
            $activity = '';
            $started_at = now();
            $finished_at = null;
            for($i = 0; $i < 1; $i++){
                switch ($i) {
                    case 0:
                        $activity = 'Initial Submission';
                        $finished_at = null;
                        $remarks = null;
                        break;
                    default:
                        $activity = null;
                        $finished_at = null;
                        $remarks = null;
                        break;
                }
                $vendorWorkflow = new VendorWorkflow([
                    'vendor_id' => $vendorID,
                    'activity' => $activity,
                    'remarks' => $remarks,
                    'started_at' => $started_at,
                    'finished_at' => $finished_at,
                    'created_by' => null
                ]);
                $vendorWorkflow->save();
            }
            
            // Update Approval
            VendorApproval::where('vendor_id', $vendorID)
                ->where(['as_position'=>'candidate', 'approver' => 'admin', 'is_done' => false])
                ->whereNull('deleted_at')
                ->update(['is_done' => true]);
            
            // Create Initial Company Profile
            // Initial Vendor Profile
            $vendorProfile = new VendorProfile([
                'vendor_id' => $vendorID,
                'company_name' => $reqData['company_name'],
                'company_type' => $reqData['company_type'],
                'company_category' => null,
                'company_status' => 'ACTIVE',
                'active_skl_number' => null,
                'active_skl_attachment' => null,
                'company_warning' => 'GREEN',
                'created_by' => $userid
            ]);
            $vendorProfile->save();                                          
            
            // Define required / mandatory each of detail profiles
            // warning is mandatory, none is not mandatory (string)
            // Define by Company Type
            $companyType = $vendorInfo->company_type;
            // $general, $deeds, $shareholder, $bodboc, $businesspermit, $pic, 
            // $equipment, $expert, $certification, $scopesupply, $experience, 
            // $bankaccount, $financial, $tax
            if($companyType === 'PT' || $companyType === 'CV' || $companyType === 'Yayasan' || $companyType === 'Koperasi'){
                $this->define_profiles_required(
                            'warning', 'warning', 'none', 'warning', 'warning', 'not-finish', 
                            'none', 'none', 'none', 'warning', 'none', 
                            'warning', 'none', 'warning');
            } else if($companyType === 'Perorangan' || $companyType === 'Others'){
                $this->define_profiles_required(
                            'warning', 'none', 'none', 'none', 'none', 'not-finish', 
                            'none', 'none', 'none', 'none', 'none', 
                            'warning', 'none', 'warning');
            } else if($companyType === 'Toko'){
                $this->define_profiles_required(
                            'warning', 'none', 'none', 'none', 'none', 'not-finish', 
                            'none', 'none', 'none', 'none', 'none', 
                            'warning', 'none', 'none');
            } else {
                $this->define_profiles_required(
                            'warning', 'warning', 'warning', 'warning', 'warning', 'not-finish', 
                            'warning', 'none', 'warning', 'warning', 'warning', 
                            'warning', 'warning', 'warning');
            }
            $vendorType = $vendorInfo->vendor_group;
            if($vendorType === 'foreign'){
                $this->define_profiles_required(
                            'warning', 'none', 'none', 'none', 'none', 'not-finish', 
                            'none', 'none', 'warning', 'warning', 'none', 
                            'warning', 'none', 'none');
            }

            // Initial Insert into General
            $arrayProfileGeneral = [
                'vendor_profile_id' => $vendorProfile->id,
                'company_name' => $vendorInfo->vendor_name,
                'company_type_id' => $vendorInfo->company_type_id,
                'location_category' => null,
                'country' => $vendorInfo->country,
                'postal_code' => $vendorInfo->postal_code,
                'phone_number' => $vendorInfo->phone_number,
                'fax_number' => $vendorInfo->fax_number,
                'website' => $vendorInfo->company_site,
                'company_email' => $vendorInfo->company_email,
                'parent_id' => 0,
                'primary_data' => true,
                'is_current_data' => true,
                'created_by' => $userid
            ];
            if($vendorInfo->vendor_group === 'local'){
                $arrayProfileGeneral['street'] = $vendorInfo->street;
                $arrayProfileGeneral['building_name'] = $vendorInfo->building_name;
                $arrayProfileGeneral['kavling_floor_number'] = $vendorInfo->kavling_floor_number;
                $arrayProfileGeneral['village'] = $vendorInfo->village;
                $arrayProfileGeneral['rt'] = $vendorInfo->rt;
                $arrayProfileGeneral['rw'] = $vendorInfo->rw;
                $arrayProfileGeneral['province'] = $vendorInfo->province;
                $arrayProfileGeneral['city'] = $vendorInfo->city;
                $arrayProfileGeneral['sub_district'] = $vendorInfo->sub_district;
                $arrayProfileGeneral['house_number'] = $vendorInfo->house_number;
            } else {
                $arrayProfileGeneral['address_1'] = $vendorInfo->address_1;
                $arrayProfileGeneral['address_2'] = $vendorInfo->address_2;
                $arrayProfileGeneral['address_3'] = $vendorInfo->address_3;
            }
            $vendorProfileGeneral = new VendorProfileGeneral($arrayProfileGeneral);
            $vendorProfileGeneral->save();            
            
            // Initial insert into BOD & BOC
            $vendorProfileBodBoc = new VendorProfileBodboc([
                'vendor_profile_id' => $vendorProfile->id,
                'board_type' => 'BOD (Board of Director)',
                'is_person_company_shareholder' => false,
                'full_name' => $vendorInfo->president_director,
                'nationality' => null,
                'position' => 'President Director',
                'email' => null,
                'phone_number' => null,
                'company_head' => true,
                'parent_id' => 0,
                'is_current_data' => true,
                'created_by' => $userid
            ]);
            $vendorProfileBodBoc->save();

            // Initial insert into PIC 
            $vendorProfilePIC = new VendorProfilePic([
                'vendor_profile_id' => $vendorProfile->id,
                'username' => $registration_number,
                'full_name' => $vendorInfo->pic_full_name,
                'email' => $vendorInfo->pic_email,
                'phone' => $vendorInfo->pic_mobile_number,
                'primary_data' => true,
                'parent_id' => 0,
                'is_current_data' => true,
                'created_by' => $userid
            ]);
            $vendorProfilePIC->save();
            
            // Initial insert into Tax (if available)
            //=======================================
            $path = 'public/vendor/profiles/'.$vendorProfile->vendor_id.'/';
            $pathTo = $path.'tax-document/';
            if(!Storage::exists($pathTo)) {
                Storage::makeDirectory($pathTo, 0777, true); //creates directory
            }

            $preInsert = [
                //1. tin files
                'ID1' => [$vendorInfo->tin_number, $vendorInfo->tin_attachment, true],
                //2. non pkp files
                'ID2' => [$vendorInfo->non_pkp_number, null, true],
                //3. pkp files
                'ID3' => [$vendorInfo->pkp_number, $vendorInfo->pkp_attachment, $vendorInfo->pkp_type=='pkp'],
                //4. id card files
                'ID4' => [$vendorInfo->idcard_number, $vendorInfo->idcard_attachment, true],
            ];
            foreach($preInsert as $taxType=>$taxInfo){
                //taxInfo[2] is true for ID1, and check for pkp type for ID2
                if(!is_null($taxInfo[0]) && $taxInfo[2]){
                    $vendorProfileTax = new VendorProfileTax([
                        'vendor_profile_id' => $vendorProfile->id,
                        'tax_document_type' => $taxType,
                        'tax_document_number' => $taxInfo[0],
                        'issued_date' => null,
                        'tax_document_attachment' => $taxInfo[1],
                        'parent_id' => 0,
                        'is_current_data' => true,
                        'created_by' => $userid
                    ]);
                    if($taxType=='ID1' && $vendorInfo->vendor_group=='foreign'){
                        $vendorProfileTax->tax_document_type = "ZZ1";
                    }
                    $vendorProfileTax->save();
    
                    //copy tax files to tax folder (if exists)
                    if(!is_null($taxInfo[1])){
                        $file_from = $path.$taxInfo[1];
                        $file_to = $pathTo.$taxInfo[1];
                        if(!Storage::exists($file_to)) Storage::copy($file_from, $file_to);
                    }
                }
            }

            // Generate Checklist Profile
            //==============================
            $vendorProfileStatus = new VendorProfileDetailStatus([
                'vendor_profile_id' => $vendorProfile->id,
                'general_status' => $this->general,
                'deed_status' => $this->deeds,
                'shareholder_status' => $this->shareholder,
                'bodboc_status' => $this->bodboc,
                'businesspermit_status' => $this->businesspermit,
                'pic_status' => $this->pic,
                'equipment_status' => $this->equipment,
                'certification_status' => $this->certification,
                'scopeofsupply_status' => $this->scopesupply,
                'experience_status' => $this->experience,
                'bankaccount_status' => $this->bankaccount,
                'financial_status' => $this->financial,
                'tax_status' => $this->tax,
                'created_by' => auth()->user()->userid
            ]);
            $vendorProfileStatus->save();            
            
            // Insert users ID
            $setpassword = Str::random(10);
            $users = new User([
                'name' => $vendorInfo->vendor_name,
                'userid' => $registration_number,
                'user_type' => 'vendor',
                'ref_id' => $vendorID,
                'email' => $vendorInfo->company_email,
                'email_verified_at' => now(),
                'password' => Hash::make($setpassword)
            ]);
            $users->save();

            // Set User Role
            $users->assignRole('vendor');
            
            DB::commit();
            
            // Sendmail to Candidates     
            $this->sendEmail($request, $status, ['reg'=>$registration_number, 'pass'=>$setpassword]);
            return redirect('/admin/applicants')->withSuccess('Applicant data has been ' . $status . '!');
        } catch (Exception $e) {
            DB::rollBack();
            // used case when using api json response
            // $status = $e->getCode() ? $e->getCode() : 500;
            // $data = null;
            // $errorMsg = null;
            return redirect('/admin/applicants')->withErrors('Applicant data has been ' . $status . '!');
        }
    }

    public function sendEmail($request, $status, $data = null){
        $currentUser = auth()->user();
        $recipients = [];
        $ccs = [];
        try{
            if($status=="registration"){
                // Get Purc Org Data
                $purch_org = RefPurchaseOrg::find($request->input('purchase_org_id'));
                $company = RefCompanyType::find($request->input('company_type_id'));
                $picEmail = $request->pic_email;
                
                array_push($recipients, $picEmail);

                // Email to PIC Vendor
                $arrdata = [];
                $arrdata['mailtype'] = 'registration_for_pic';
                $arrdata['vendor_name'] = $request->vendor_name;
                $arrdata['purchasing_org'] = $purch_org->org_code;
                $arrdata['purchasing_org_description'] = $purch_org->description;
                $arrdata['registration_status'] = 'Applicant';
                $arrdata['subject'] = "SUBMITTED: Vendor Registration - " . $request->vendor_name;
                $arrdata = (object) $arrdata;
                if ($this->vendorRepo->isValidEmail($recipients)){
                    ProcessEmail::dispatch($recipients, null, $arrdata);
                    // Mail::to($request->pic_email)->send(new TestMail($arrdata));
                }else{
                    $this->log("===========email failed==============. email :".json_encode($picEmail).", obj: ".json_encode($arrdata));
                }
                
                // Email to Admin Vendor
                $recipients = User::role('Admin Vendor')->join("user_extensions","user_extensions.user_id","=","users.id")->where("user_extensions.status","=",1)->whereNotNull("users.email")->pluck('email', 'name')->toArray();
                foreach ($recipients as $name => $email) {
                    $arrAdminEmail = [$email];
                    $arrdata = [];
                    $arrdata['mailtype'] = 'registration_for_admin';
                    $arrdata['vendor_name'] = $request->vendor_name;
                    $arrdata['purchasing_org'] = $purch_org->org_code;
                    $arrdata['purchasing_org_description'] = $purch_org->description;
                    $arrdata['subject'] = "FOR APPROVAL: Vendor Registration - " . $request->vendor_name;
                    $arrdata = (object) $arrdata;
                    if ($this->vendorRepo->isValidEmail($arrAdminEmail)){
                        ProcessEmail::dispatch($arrAdminEmail, null, $arrdata);
                        // Mail::to($email)->send(new TestMail($arrdata));
                    }else{
                        $this->log("===========email failed==============. email :".json_encode($arrAdminEmail).", obj: ".json_encode($arrdata));
                    }
                }
            }

            if($status=='rejected'){
                // Email to PIC Vendor
                $vendorInfo = $this->vendorRepo->getQueryApplicantById($request->vendor_id)->first();
                
                $adminEmail = User::role('Admin Vendor')->join("user_extensions","user_extensions.user_id","=","users.id")->where("user_extensions.status","=",1)->whereNotNull("users.email")->pluck('email')->toArray();
                $picEmail = $vendorInfo->pic_email;
                
                array_push($recipients, $picEmail);
                $ccs = $adminEmail;

                $arrdata = [];
                $arrdata['mailtype'] = 'applicant_rejection';
                $arrdata['vendor_name'] = $vendorInfo->vendor_name;
                $arrdata['comments'] = $request->remarks;
                $arrdata['admin_onshore_email'] = $currentUser->email;
                $arrdata['subject'] = "REJECTED: Vendor Registration - Applicant " . $vendorInfo->vendor_name;
                $arrdata = (object) $arrdata;

                if ($this->vendorRepo->isValidEmail($recipients) && $this->vendorRepo->isValidEmail($ccs)){
                    ProcessEmail::dispatch($recipients, $ccs, $arrdata);
                    // Mail::to($vendorInfo->pic_email)->cc($adminEmail)->send(new TestMail($arrdata));
                }else{
                    $this->log("===========email failed==============. email :".json_encode($recipients).", CC : ".json_encode($ccs).", obj: ".json_encode($arrdata));
                }
            }

            if($status=='approved'){
                // Email to PIC Vendor
                $vendorInfo = $this->vendorRepo->getVendorById($request->vendor_id);
                $adminEmail = User::role('Admin Vendor')->join("user_extensions","user_extensions.user_id","=","users.id")->where("user_extensions.status","=",1)->whereNotNull("users.email")->pluck('email')->toArray();
                $picEmail = $vendorInfo->pic_email;
                
                array_push($recipients, $picEmail);
                $ccs = $adminEmail;

                $arrdata = [];
                $arrdata['mailtype'] = 'applicant_approval';
                $arrdata['vendor_name'] = $vendorInfo->vendor_name;
                $arrdata['purchasing_org'] = $vendorInfo->purchase_org_code;
                $arrdata['purchasing_org_description'] = $vendorInfo->purchase_org_description;
                $arrdata['registration_status'] = 'Candidate';
                $arrdata['username'] = $data['reg'];
                $arrdata['password'] = $data['pass'];
                $arrdata['subject'] = "APPROVED: Vendor Registration - Applicant " . $vendorInfo->vendor_name;
                $arrdata = (object) $arrdata;

                if ($this->vendorRepo->isValidEmail($recipients) && $this->vendorRepo->isValidEmail($ccs)){
                    ProcessEmail::dispatch($recipients, $ccs, $arrdata);
                    // Mail::to($vendorInfo->pic_email)->cc($adminEmail)->send(new TestMail($arrdata));
                }else{                    
                    $this->log("===========email failed==============. email :".json_encode($recipients).", CC : ".json_encode($ccs).", obj: ".json_encode($arrdata));
                }
            }
        } catch (Exception $e){
            Log::error($e->getMessage());
        }                
    }

   
}