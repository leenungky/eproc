<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

use App\Mail\TestMail;
use View;
use DB;
use Schema;

use App\Repositories\VendorRepository;
use App\Repositories\TenderRepository;
use App\Vendor;
use App\VendorProfile;
use App\VendorProfileDetailStatus;
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
use App\VendorWorkflow;
use App\VendorApproval;
use App\User;
use App\RefBank;
use App\RefCountry;
use App\RefCompanyType;
use App\RefListOption;
use App\Models\Ref\RefCurrency;
use App\Models\Ref\RefScopeOfSupply;
use App\Models\Ref\RefPostalCode;
use App\Traits\AccessLog;
use App\Jobs\ProcessEmail;


class VendorProfileController extends Controller
{
    use AccessLog;
    private $logName = VendorProfileController::class;

    public function __construct() {
        $this->middleware('auth');
        $this->vendorRepo = new VendorRepository();
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

    public function view_show_profile() {
        $vendorId = Auth::user()->ref_id;
        $arrColumnVal = array();
        $arrColumnVal['id'] = $vendorId;
        $data = $this->specific_find($arrColumnVal);
        $data['commentsHistory'] = $this->vendorRepo->getCommentHistoryByVendorId($vendorId);
        $data['vendor'] = $data;
        $data["doc_expiry"] = $this->vendorRepo->getBusinessPermitsStatus($vendorId);
        $data['checklist'] = $this->get_profile_checklist($vendorId);
        $data['evaluation_score'] = $this->vendorRepo->getLastEvaluationScore($vendorId);
        $data['type'] = "tender_followed";
        $data['isVendor'] = auth()->user()->isVendor();
        
        $ann = new \App\Http\Controllers\AnnouncementController(new TenderRepository());
        $data['fields'] = $ann::FIELDS;
        $annView = $ann->getViewData();
        foreach($annView as $key=>$val){
            $data[$key] = $val;
        }
        return View::make('vendor/profiles/show', $data);
    }

    public function specific_find($column) {
        try {
            $profile = $this->vendorRepo->getVendorById($column['id']); //Vendor::with('profile')->find($column['id']);
            return $profile;
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
                    })
                    ->where($column)->orderby('vendor_profiles.id', 'desc')->first();
        } catch (QueryException $e) {
            Log::error($e->getMessage());
        }
    }

    public function view_edit_profile(Request $request, $submenu = 'general') {
        $vendorId = Auth::user()->ref_id;
        $arrColumnVal = array();
        $arrColumnVal['id'] = $vendorId;
        $data = $this->show_changes_profiles($arrColumnVal);
        $data['vendor'] = $data;
        $data['submenu'] = $submenu;
        $data['profiles'] = [];        
        $vendorInfo = Vendor::select('vendor_group')->where('id', $vendorId)->first();

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
        $data['profiles'] = $this->vendorRepo->getProfileDetailsByVendorId(Auth::user()->ref_id, $submenu)->get();
        $vp = VendorProfile::where('vendor_id',Auth::user()->ref_id)->first();
        $data['blacklisted'] = $vp->is_blacklisted; 
        // var_dump($data['profiles']);exit;
        $data['checklist'] = $this->get_profile_checklist($vendorId);
        $data['type'] = $submenu;
        $data['storage'] = asset('storage/vendor/profiles/'.$vendorId.'/'.$submenu);
        $data['accordionMenu'] = 'layouts.vendor_menu';
        $data['profileUrl'] = route('profile.show');
        
        return View::make("vendor/profiles/$menu/" . $submenu, $data);
    }

    public function create_profile(Request $request, $tableInput){
        if(in_array($tableInput, $this->tableKeys)){
            $name = Auth::user()->name;
            $input = $request->input();
            $table = $this->tables[$tableInput];
            $message = '';
            $success = false;                
            $returnId = '';
            if ($table == "vendor_profile_pics"){
                $isValidEmail = $this->vendorRepo->isCekValidEmail($input["email"]);
                if (!$isValidEmail){
                    return response()->json([
                        'status' => 200,
                        'success' => false,
                        'message' => "Email format not valid",
                    ], 200);
                    exit();
                }
            }
            try{                
                DB::beginTransaction();                
                $fields = Schema::getColumnListing($table);
                if($table === 'vendor_profile_generals'){
                    foreach($fields as $field){
                        if(array_key_exists($field, $input)){
                            if ($field=="address_5" || $field=="province" || $field=="city" || $field=="sub_district"){
                                $data[$field] = $input[$field]!=null? $input[$field] : "";  
                            }else
                                $data[$field] = $input[$field];                              
                        }else{
                            if ($field=="province" ||  $field=="city" || $field=="sub_district"){
                                $data[$field] = "";  
                            }
                        }
                    }
                }else{
                    foreach($fields as $field){
                        if(array_key_exists($field, $input)){
                            $data[$field] = $input[$field];                        
                        }
                    }
                }

                //SAVE FILE IF EXISTS
                $vp = VendorProfile::find($input['vendor_profile_id']);
                if($request->file()>0){
                    foreach($request->file() as $key=>$file){
                        $path = Storage::putFileAs('public/vendor/profiles/'.$vp->vendor_id.'/'.$tableInput , $file, $file->getClientOriginalName() );
                        $data[$key] = $file->getClientOriginalName();
                    }
                }

                // VALIDATE INSERT SAME DATA CONDITION
                $isValid = true;
                if($table === 'vendor_profile_generals'){
                    if($data['location_category'] === 'Head Office'){
                        $generalData = DB::table($table)->where([
                            "vendor_profile_id" => $data['vendor_profile_id'],
                            "location_category" => $data['location_category']
                        ])->whereNull("deleted_at")->first();
                        if($generalData){
                            $isValid = false;
                            $message=__('homepage.you_can_not_add_more_than_one_head_office');
                        }
                    }                             
                    // // Check validation
                    // $taxDocumentNumber = VendorProfileTax::where([
                    //         "vendor_profile_id" => $data['vendor_profile_id']
                    //     ])->pluck('tax_document_number')->toArray();
                    // $arrCity = [$input['city']];
                    // $validateCityID = $this->validate_city_and_idcard($arrCity, $taxDocumentNumber);
                    // if($validateCityID){
                    //     //$isValid = false;
                    //     //$message = "Candidate or Vendor has already exist by City and Tax Identification Number/ID Card!";
                    // }
                } else if ($table === 'vendor_profile_bodbocs'){
                    $isCompanyHead = isset($data['company_head']);
                    $data['company_head'] = false;
                    if($isCompanyHead){
                        $data['company_head'] = true;
                        $bodbocData = DB::table($table)->where([
                            "vendor_profile_id" => $data['vendor_profile_id'],
                            "company_head" => $isCompanyHead
                        ])->whereNull("deleted_at")->first();
                        if($bodbocData){
                            $isValid = false;
                            $message="You can not add more than one company head";
                        }
                    }
                } else if ($table === 'vendor_profile_pics'){
                    $isPrimaryData = isset($data['primary_data']);
                    if($isPrimaryData){
                        $picData = DB::table($table)->where([
                            "vendor_profile_id" => $data['vendor_profile_id'],
                            "primary_data" => $isPrimaryData
                        ])->whereNull("deleted_at")->first();
                        if($picData){
                            $isValid = false;
                            $message="You can not add more than one PIC";
                        }
                    }
                // } else if ($table === 'vendor_profile_taxes'){
                //     // Check validation
                //     $generalCity = VendorProfileGeneral::where([
                //             "vendor_profile_id" => $data['vendor_profile_id']
                //         ])->pluck('city')->toArray();
                //     $arrTaxNumber = [$input['tax_document_number']];
                //     $validateCityID = $this->validate_city_and_idcard($generalCity, $arrTaxNumber);
                //     DB::rollBack();
                //     var_dump($validateCityID);
                //     exit;
                //     $validateCityID = $this->validate_city_and_idcard($arrCity, $taxDocumentNumber);
                //     if($validateCityID){
                //         //$isValid = false;
                //         //$message = "Candidate or Vendor has already exist by City and Tax Identification Number/ID Card!";
                //     }
                }

                //Validate identification type, idcard, and city
                           
                if($table=='vendor_profile_generals'){
                    $vendor = Vendor::find($vp->vendor_id);
                    if ($vendor->vendor_group=="local" && $request->country=="ID"){
                        if ($request->province==null){
                            $isValid = false;
                            $message="Province can't be blank for local company";
                        }                     
                        if ($request->city==null){
                            $isValid = false;
                            $message="City can't be blank";    
                        }
                    }
                }

                if($isValid){
                    if(!$this->validate_city_and_idcard($request, $table, $vp, 'create')){
                        $isValid = false;
                        $message="Duplicate City and Tax Identification Number or ID Card. Please check your data";
                    }
                }
                
                //INSERT QUERY
                unset($data['id']);
                $data['created_at'] = date("Y-m-d H:i:s");
                $data['created_by'] = $name;
                if($isValid){
                    $this->log("============table =========".$table."===========". json_encode($data));
                    $affected = DB::table($table)->insert($data);
                    $returnId = DB::getPdo()->lastInsertId();
                    if($returnId){
                        $this->generate_profile_status_checklist($tableInput, $input['vendor_profile_id'], ['none', 'warning', 'not-finish', 'finish'], 'not-finish');
                    }
                    DB::commit();
                    $success= true;
                    $message=__('homepage.the_data_has_been_added_successfully');
                } else{
                    DB::rollback();     
                    $success= false;
                }               
            }catch(Exception $e){
                DB::rollback();
                $success=false;
                $message=__('homepage.error');
            }
            return response()->json([
                'status' => 200,
                'success' => $success,
                'message' => $message,
                'data' => ['id'=>$returnId],
            ]);
        }else{
            return response()->json([
                'status' => 404,
                'success' => false,
                'message' => "page_not_found",
            ], 404);
        }
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
            }else if($table === 'vendor_profile_experience'){
                $tmp = DB::table($table)
                    ->select("$table.*", 
                        'ref_countries.country_description',
                        'ref_provinces.region_description',
                        'ref_cities.city_description',
                        'ref_sub_districts.district_description'
                    )
                    ->join('ref_countries', function ($join) use ($table) {
                        $join->on('ref_countries.country_code', '=', $table.'.country')
                        ->whereNull('ref_countries.deleted_at');
                    })
                    ->leftJoin('ref_provinces', function ($join) use ($table) {
                        $join->on('ref_provinces.region_code', '=', $table.'.province');
                        $join->on('ref_provinces.country_code', '=', $table.'.country')
                        ->whereNull('ref_provinces.deleted_at');
                    })
                    ->leftJoin('ref_cities', function ($join) use ($table) {
                        $join->on('ref_cities.city_code', '=', $table.'.city');
                        $join->on('ref_cities.country_code', $table.'.country');
                        $join->on('ref_cities.region_code', $table.'.province')
                        ->whereNull('ref_cities.deleted_at');
                    })
                    ->leftJoin('ref_sub_districts', function ($join) use ($table) {
                        $join->on('ref_sub_districts.district_code', '=', $table.'.sub_district');
                        $join->on('ref_sub_districts.country_code', $table.'.country');
                        $join->on('ref_sub_districts.region_code', $table.'.province');
                        $join->on('ref_sub_districts.city_code', $table.'.city')
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

    public function update_profile(Request $request, $tableInput){
        if(in_array($tableInput, $this->tableKeys)){
            $name = Auth::user()->name;
            $input = $request->input();
            $table = $this->tables[$tableInput];
            $continue = true;
            $returnId = $input['id'];
            $message = "";
            try{
                DB::beginTransaction();

                $fields = Schema::getColumnListing($table);
                if($request->input('edit_type') == 'current'){
                    $old_data = DB::table($table)->where('id',$input['id'])->first();
                }

                foreach($fields as $field){
                    if(array_key_exists($field, $input)){
                        $data[$field] = $input[$field];
                    }
                    if($request->input('edit_type') == 'current'){
                        //retain old current file
                        if(strpos($field,"attachment")!==false){
                            $data[$field] = $old_data->$field;
                        }
                    }
                }
    
                //SAVE FILE IF EXISTS
                $vp = VendorProfile::find($input['vendor_profile_id']);
                if($request->file()>0){
                    if($request->input('edit_type') != 'current'){
                        $old = DB::table($table)->where('id',$input['id'])->get();
                        if(count($old)>0){
                            foreach($request->file() as $key=>$file){
                                $filename = 'public/vendor/profiles/'.$vp->vendor_id.'/'.$tableInput.'/'.$old[0]->$key;
                                Storage::delete($filename);
                            }
                        }
                    }
                    foreach($request->file() as $key=>$file){
                        $filename = 'public/vendor/profiles/'.$vp->vendor_id.'/'.$tableInput.'/'.$file->getClientOriginalName();
                        if(Storage::exists($filename)){
                            $continue = false;
                            $message = "duplicate_file: ".$filename;
                        }
                    }
                    if($continue){
                        foreach($request->file() as $key=>$file){
                            $path = Storage::putFileAs('public/vendor/profiles/'.$vp->vendor_id.'/'.$tableInput , $file, $file->getClientOriginalName() );
                            $data[$key] = $file->getClientOriginalName();
                        }
                    }
                }
                
                // Case when no duplicate file validation is OK
                if($continue){
                    // VALIDATE UPDATE SAME DATA CONDITION FROM ANOTHER DATA
                    if($table === 'vendor_profile_generals'){
                        if($data['location_category'] === 'Head Office'){
                            $generalData = DB::table($table)->where([
                                "vendor_profile_id" => $data['vendor_profile_id'],
                                "location_category" => $data['location_category']
                            ])
                            ->where("id", "<>", $input['id'])
                            ->whereNull("deleted_at")->first();
                            if($generalData){
                                $continue = false;
                                $message=__('homepage.you_can_not_add_more_than_one_head_office');
                            }
                        }                    
                    } else if ($table === 'vendor_profile_bodbocs'){
                        $isCompanyHead = isset($data['company_head']);
                        if($isCompanyHead){
                            $bodbocData = DB::table($table)->where([
                                "vendor_profile_id" => $data['vendor_profile_id'],
                                "company_head" => $isCompanyHead
                            ])
                            ->where("id", "<>", $input['id'])
                            ->whereNull("deleted_at")->first();
                            if($bodbocData){
                                $continue = false;
                                $message="You can not add more than one company head";
                            }
                        }
                    } else if ($table === 'vendor_profile_pics'){
                        $isPrimaryData = isset($data['primary_data']);
                        if($isPrimaryData){
                            $picData = DB::table($table)->where([
                                "vendor_profile_id" => $data['vendor_profile_id'],
                                "primary_data" => $isPrimaryData
                            ])
                            ->where("id", "<>", $input['id'])
                            ->whereNull("deleted_at")->first();
                            if($picData){
                                $continue = false;
                                $message="You can not add more than one PIC";
                            }
                        }
                    // } else if ($table === 'vendor_profile_taxes'){
                    //     // Check validation
                    //     $generalCity = VendorProfileGeneral::where([
                    //             "vendor_profile_id" => $data['vendor_profile_id']
                    //         ])->pluck('city')->toArray();
                    //     $arrTaxNumber = [$input['tax_document_number']];
                    //     $validateCityID = $this->validate_city_and_idcard($generalCity, $arrTaxNumber);
                    //     if($validateCityID){
                    //         //$isValid = false;
                    //         //$message = "Candidate or Vendor has already exist by City and Tax Identification Number/ID Card!";
                    //     }
                    }
                }
                // check duplicate id and city
                if($continue){
                    if(!$this->validate_city_and_idcard($request, $table, $vp, 'update')){
                        $continue = false;
                        $message="Duplicate City and Tax Identification Number or ID Card. Please check your data";
                    }
                }
                if($continue){
                    // Check Change Current Data or New Data want to Edit
                    if($request->input('edit_type') == 'current'){
                        // INSERTING NEW ROW DATA                     
                        $data['created_at'] = now();
                        $data['created_by'] = $name;
                        $data['parent_id'] = $input['id'];
                        unset($data['id']);
                        $affected = DB::table($table)->insert($data);
                        if(!$affected){
                            $continue = false;
                            $message=__('homepage.failed_to_update_current_data');
                        } else {
                            $message=__('homepage.successfully_updated_current_data');
                        }
                        $returnId = DB::getPdo()->lastInsertId();                        
                    } else {
                        // UPDATING NEW ROW DATA has been inserted previously
                        $data['updated_at'] = now();
                        $data['parent_id'] = 0;
                        $affected = DB::table($table)->where('id', $input['id'])->update($data);        
                        if(!$affected){
                            $continue = false;
                            $message=__('homepage.failed_to_update_new_data');
                        } else {
                            $message=__('homepage.successfully_updated_new_data');
                        }
                        $returnId = $input['id'];                  
                    }                    
                } else {
                }
                // Case when insert or update has been processed OK
                if($continue){
                    $this->generate_profile_status_checklist($tableInput, $input['vendor_profile_id'], ['none', 'warning', 'not-finish', 'finish'], 'not-finish');
                    DB::commit();
                } else {
                    DB::rollback();                                
                }
                $success = $continue;
            }catch(Exception $e){
                DB::rollback();
            }
            return response()->json([
                'status' => 200,
                'success' => $success,
                'message' => $message,
                'data' => ['id' => $returnId],
            ]);
        }else{
            return response()->json([
                'status' => 404,
                'success' => false,
                'message' => "page_not_found",
            ], 404);
        }
    }

    public function revert_profile(Request $request, $tableInput){
        $input = $request->input();
        if(in_array($tableInput, $this->tableKeys)){
            $table = $this->tables[$tableInput];
            $success = false;
            try{
                
                // delete file if exists
            //    $old = DB::table($table)->where('id',$request->input('id'))->get();
            //    $vp = VendorProfile::find($request->input('vendor_profile_id'));
            //    foreach($old as $row){
            //        foreach($row as $key=>$value){
            //            if(strpos($key,'attachment') !== false || strpos($key,'letter') !== false){
            //                $filename = 'public/vendor/profiles/'.$vp->vendor_id.'/'.$tableInput.'/'.$row->$key;
            //                if(Storage::exists($filename)){
            //                    Storage::delete($filename);
            //                }
            //            }
            //        }
            //    }
                DB::beginTransaction();
                DB::table($table)->where(['id'=>$request->input('id'), 'is_current_data'=>false, 'is_submitted'=>false])->delete();
                
                $this->generate_profile_status_checklist($tableInput, $input['vendor_profile_id'], ['warning', 'not-finish', 'finish'], 'not-finish');
                
                DB::commit();
                $success=true;
            }catch(Exception $e){
                DB::rollback();
            }
            
            return response()->json([
                'status' => 200,
                'success' => $success,
                'message' => $success ? "data_saved" : "data_not_saved",
                'data' => ['id'=>$request->input('id')],
            ]);
        }else{
            return response()->json([
                'status' => 404,
                'success' => false,
                'message' => "page_not_found",
            ], 404);
        }
    }

    public function revertall_profile($tableInput, $id = null, Request $request){
        $input = $request->input();
        if(in_array($tableInput, $this->tableKeys)){
            $table = $this->tables[$tableInput];
            $success = false;
            try{
                DB::beginTransaction();
                
                // delete file if exists
                $old = DB::table($table)->where('vendor_profile_id',$id)->get();
                $vp = VendorProfile::find($request->input('vendor_profile_id'));
                foreach($old as $row){
                    foreach($row as $key=>$value){
                        if(strpos($key,'attachment') !== false || strpos($key,'letter') !== false){
                            $filename = 'public/vendor/profiles/'.$vp->vendor_id.'/'.$tableInput.'/'.$row->$key;
                            if(Storage::exists($filename)){
                                Storage::delete($filename);
                            }
                        }
                    }
                }

                DB::table($table)
                        ->where(['vendor_profile_id'=>$id, 'is_current_data'=>false, 'is_submitted'=>false])
                        ->delete();
                
                $this->generate_profile_status_checklist($tableInput, $input['vendor_profile_id'], ['warning', 'not-finish', 'finish'], 'none');
                
                DB::commit();
                $success=true;
            }catch(Exception $e){
                DB::rollback();
            }
            
            return response()->json([
                'status' => 200,
                'success' => $success,
                'message' => $success ? "data_saved" : "data_not_saved",
                'data' => ['id'=>$id],
                ]);
        }else{
            return response()->json([
                'status' => 404,
                'success' => false,
                'message' => "page_not_found",
            ], 404);
        }
    }
    
    public function finishall_profile($tableInput, $id = null){
        if(in_array($tableInput, $this->tableKeys)){
            $table = $this->tables[$tableInput];
            try{
                DB::beginTransaction();
                                
                $affected = DB::table($table)->where(['vendor_profile_id'=>$id])->update([
                    'is_finished' => true
                ]);
                
                $resultId = $this->generate_profile_status_checklist($tableInput, $id, ['not-finish'], 'finish');
                if($resultId){
                    DB::commit();
                    $success=true;
                } else {
                    DB::rollback();
                    $success=false;
                }                          
            }catch(Exception $e){
                DB::rollback();
            }
            
            return response()->json([
                'status' => 200,
                'success' => $success,
                'message' => $success ? __("homepage.the_data_has_been_finished_successfully") : __("homepage.unavailable_data_to_be_finished"),
                'data' => ['id'=>$id],
                ]);
        }else{
            return response()->json([
                'status' => 404,
                'success' => false,
                'message' => "page_not_found",
            ], 404);
        }
    }    
    
    public function send_submission(Request $request){
        $success = false;
        $userid = auth()->user()->userid;
        $vendorProfileID = $request->input('id');
        $vendorID = $request->input('vendor_id');
        $registrationStatus = $request->input('registration_status');
        // Get Vendor Info
        $vendorInfo = $this->vendorRepo->getVendorById($vendorID);
        try{
            DB::beginTransaction();
            if($registrationStatus === 'vendor'){
                VendorProfileDetailStatus::where(['vendor_profile_id'=>$vendorProfileID])->update(['update_vendor_data_status' => 'Pending Approval', 'is_submitted'=>true, 'is_revised' => false, 'is_approved' => false]);
            } else {
                VendorProfileDetailStatus::where(['vendor_profile_id'=>$vendorProfileID])->update(['is_submitted'=>true, 'is_revised' => false, 'is_approved' => false]);                
            }
            
            // Run Workflow            
            // Complete Workflow before
            $flow = VendorWorkflow::where('vendor_id', $vendorID)
                ->whereNull('finished_at')
                ->orderBy('id', 'desc')
                ->first();
            if($flow){
                $flow->update([
                    'finished_at' => now(), 
                    'remarks' => null, 
                    'created_by' => $userid
                ]);
            }else{
                //no workflow before, means new workflow.
                if($registrationStatus === 'vendor'){
                    //resubmission workflow...
                    $vendorWorkflow = new VendorWorkflow([
                        'vendor_id' => $vendorID,
                        'activity' => 'Resubmission',
                        'remarks' => null,
                        'started_at' => now(),
                        'finished_at' => now(),
                        'created_by' => $userid
                    ]);
                    $vendorWorkflow->save();
                }
            }

            // Generate Workflow By System
            $workflow = config('workflow.vendor-submission.tasks');
            $i = 0;
            foreach($workflow as $task){
                $vendorWorkflow = new VendorWorkflow([
                    'vendor_id' => $vendorID,
                    'activity' => $task['activity'],
                    'remarks' => $task['remarks'],
                    'started_at' => $task['started_at']=='now' ? now() : null,
                    'finished_at' => $task['finished_at']=='now' ? now() : null,
                    'created_by' => null
                ]);
                $vendorWorkflow->save();
                // Generate Approval By System
                $vendorApproval = new VendorApproval([
                    'vendor_id' => $vendorID,
                    'as_position' => 'vendor',
                    'approver' => $task['approver'],
                    'sequence_level' => $i,
                    'is_done' => false,
                    'created_by' => $userid
                ]);
                $vendorApproval->save();
                $i++;
            }            
            DB::commit(); 
            $returnstatus = 200;
            $returnsuccess = true;
            $returnmessage = $success ? "data_saved" : "data_not_saved";
            try {
                $adminEmail = User::role('Admin Vendor')->join("user_extensions","user_extensions.user_id","=","users.id")->where("user_extensions.status","=",1)->whereNotNull("users.email")->pluck('email')->toArray(); 
                $picEmail = $vendorInfo->pic_email;
                
                $arrdata = [];                
                $arrdata['vendor_name'] = $vendorInfo->company_name;
                $arrdata['registration_status'] = $vendorInfo->registration_status;
                $arrdata['purchasing_org'] = $vendorInfo->purchase_org_code;
                $arrdata['purchasing_org_description'] = $vendorInfo->purchase_org_description;
                // Case when Submit Company Profile as Candidate
                if($registrationStatus === 'candidate'){
                    $recipients = [];
                    $ccs = [];
                    // Send Mail to PIC Vendor
                    array_push($recipients, $picEmail);
                    $ccs = $adminEmail;                
                    $arrdata['mailtype'] = 'send_submission_profile';
                    $arrdata['subject'] = "SUBMITTED: Vendor Registration - $vendorInfo->registration_status $vendorInfo->company_name";
                    $objData1 = (object) $arrdata;
                    if ($this->vendorRepo->isValidEmail($recipients) && $this->vendorRepo->isValidEmail($ccs) ){
                        ProcessEmail::dispatch($recipients, $ccs, $objData1);
                        // Mail::to($vendorInfo->pic_email)->cc($adminEmail)->send(new TestMail($objData1));
                    }else{
                        $this->log("===========email failed==============. email :".json_encode($recipients).", cc: ".json_encode($ccs).",  obj: ".json_encode($objData1));
                    }     
                    
                    // Send Mail to Admin Vendor
                    $recipients = $adminEmail;
                    $arrdata['mailtype'] = 'candidate_for_next_approval';                
                    $arrdata['subject'] = "FOR APPROVAL: Vendor Registration - $vendorInfo->registration_status $vendorInfo->company_name";
                    $objData2 = (object) $arrdata;
                    if ($this->vendorRepo->isValidEmail($recipients)){
                        ProcessEmail::dispatch($recipients, null, $objData2);
                        // Mail::to($adminEmail)->send(new TestMail($objData2));
                    }else{
                        $this->log("===========email failed==============. email :".json_encode($recipients).", obj: ".json_encode($objData2));
                    }
                }
                
                // Case when Submit Company Profile as Vendor (Update Data)
                if($registrationStatus === 'vendor'){
                    $recipients = [];
                    $ccs = [];
                    // Send Mail to PIC Vendor
                    array_push($recipients, $picEmail);
                    $ccs = $adminEmail;                
                    $arrdata['mailtype'] = 'updatevendor_submission_profile';
                    $arrdata['subject'] = "SUBMITTED: Vendor Registration - $vendorInfo->registration_status $vendorInfo->company_name";
                    $objData1 = (object) $arrdata;
                    if ($this->vendorRepo->isValidEmail($recipients) && $this->vendorRepo->isValidEmail($ccs) ){
                        ProcessEmail::dispatch($recipients, $ccs, $objData1);
                        // Mail::to($vendorInfo->pic_email)->cc($adminEmail)->send(new TestMail($objData1));
                    }else{
                        $this->log("===========email failed==============. email :".json_encode($recipients).", cc: ".json_encode($ccs).",  obj: ".json_encode($objData1));
                    }
                    
                    // Send Mail to Admin Vendor
                    $recipients = $adminEmail;
                    $arrdata['mailtype'] = 'vendordata_for_next_approval';                
                    $arrdata['subject'] = "FOR APPROVAL: Vendor Registration - Approved Vendor $vendorInfo->company_name";
                    $objData2 = (object) $arrdata;
                    if ($this->vendorRepo->isValidEmail($recipients)){
                        ProcessEmail::dispatch($recipients, null, $objData2);
                        // Mail::to($adminEmail)->send(new TestMail($objData2));
                    }else{
                        $this->log("===========email failed==============. email :".json_encode($recipients).", obj: ".json_encode($objData2));
                    }
                }
            } catch (Exception $ex) {
                Log::error($e->getMessage());
            }         
        } catch(Exception $e){
            DB::rollback();
            $returnstatus = 500;
            $returnsuccess = false;
            $returnmessage = 'ERROR: ' . $e->getMessage();
        }
        
        return response()->json([
            'status' => $returnstatus,
            'success' => $returnsuccess,
            'message' => $returnmessage,
            'data' => ['id'=>$request->input('id')],
        ]);
    }
    
    public function update_status_checklist($profileID, $profileKey, $oldStatus, $status = null){
        return VendorProfileDetailStatus::where([
            'vendor_profile_id' => $profileID,
            'deleted_at' => null            
        ])->whereIn($profileKey, $oldStatus)->update([$profileKey => $status]);
    }
    
    public function generate_profile_status_checklist($table, $profileId, $oldStatus, $status){
        $result = null;
        switch ($table) {
            case 'general':
                // Update status 
                $result = $this->update_status_checklist($profileId, 'general_status', $oldStatus, $status);
                break;
            case 'deeds':
                $result = $this->update_status_checklist($profileId, 'deed_status', $oldStatus, $status);
                break;
            case 'shareholders':
                $result = $this->update_status_checklist($profileId, 'shareholder_status', $oldStatus, $status);
                break;
            case 'bod-boc':
                $result = $this->update_status_checklist($profileId, 'bodboc_status', $oldStatus, $status);
                break;
            case 'business-permit':
                $result = $this->update_status_checklist($profileId, 'businesspermit_status', $oldStatus, $status);
                break;
            case 'pic':
                $result = $this->update_status_checklist($profileId, 'pic_status', $oldStatus, $status);
                break;
            case 'tools':
                $result = $this->update_status_checklist($profileId, 'equipment_status', $oldStatus, $status);
                break;
            case 'expert':
                $result = $this->update_status_checklist($profileId, 'expert_status', $oldStatus, $status);
                break;
            case 'certification':
                $result = $this->update_status_checklist($profileId, 'certification_status', $oldStatus, $status);
                break;
            case 'competency':
                $result = $this->update_status_checklist($profileId, 'scopeofsupply_status', $oldStatus, $status);
                break;
            case 'work-experience':
                $result = $this->update_status_checklist($profileId, 'experience_status', $oldStatus, $status);
                break;
            case 'bank-account':
                $result = $this->update_status_checklist($profileId, 'bankaccount_status', $oldStatus, $status);
                break;
            case 'financial':
                $result = $this->update_status_checklist($profileId, 'financial_status', $oldStatus, $status);
                break;
            case 'tax-document':
                $result = $this->update_status_checklist($profileId, 'tax_status', $oldStatus, $status);
                break;
            default:
                break;
        }
        return $result;
    }
    
    public function validate_city_and_idcard($request, $table, $profile, $type){
        $tableToCheck = [
            'vendor_profile_generals',
            'vendor_profile_taxes'
        ];
        if(in_array($table,$tableToCheck)){
            $duplicate = false;
            $vendor = Vendor::find($profile->vendor_id);
            $vendorType = $vendor->vendor_group;
            $vendorId = $vendor->id;
            if($table=='vendor_profile_generals'){
                Log::debug("======Check General Profile Duplicate======");
                $city = $request->city;
                $taxes = VendorProfileTax::where('vendor_profile_id',$profile->id)
                    ->where('is_current_data',true)
                    ->get();
                foreach($taxes as $tax){
                    $identification_type = "";
                    $identity_number = "";
                    switch($tax->tax_document_type){
                        case 'ID1':
                            $identification_type = 'tin';
                            $identity_number = $tax->tax_document_number;
                        break;
                        case 'ID4':
                            $identification_type = 'id-card';
                            $identity_number = $tax->tax_document_number;
                        break;
                        default:
                        break;
                    }
                    if($type=='create'){
                        $duplicate = $duplicate || $this->vendorRepo->isDuplicateVendor($vendorType, $identification_type, $identity_number, $city, $vendorId);
                    }else{
                        $duplicate = $duplicate || $this->vendorRepo->isDuplicateVendor($vendorType, $identification_type, $identity_number, $city, $vendorId);
                    }
                }
            }else if($table=='vendor_profile_taxes'){
                $identification_type = '';
                $identity_number = '';
                switch($request->tax_document_type){
                    case 'ID1':
                        $identification_type = 'tin';
                        $identity_number = $request->tax_document_number;
                    break;
                    case 'ID4':
                        $identification_type = 'id-card';
                        $identity_number = $request->tax_document_number;
                    break;
                    default:
                    break;
                }

                if(in_array($identification_type,['tin', 'id-card'])){
                    Log::debug("======Check Tax Profile Duplicate======");
                    $generals = VendorProfileGeneral::where('vendor_profile_id',$profile->id)
                        ->where('is_current_data',true)
                        ->get();
                    foreach($generals as $general){
                        $city = $general->city;
                        if($type=='create'){
                            $duplicate = $duplicate || $this->vendorRepo->isDuplicateVendor($vendorType, $identification_type, $identity_number, $city, $vendorId);
                        }else{
                            $duplicate = $duplicate || $this->vendorRepo->isDuplicateVendor($vendorType, $identification_type, $identity_number, $city, $vendorId);
                        }
                    }
                }
            }
            return !$duplicate;
        }else{
            return true;
        }
    }
}
