<?php

namespace App\Http\Controllers;

use App\Http\Controllers\TenderController;

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

use App\Models\TenderWeighting;
use App\RefPurchaseGroup;
use App\RefPurchaseOrg;
use App\RefPlant;
use App\RefListOption;
use App\RefCountry;
use App\Vendor;
use App\Models\Ref\RefPostalCode;
use App\Repositories\PoRepository;
use App\Repositories\VendorRepository;
use App\Repositories\TenderItemsRepository;
use App\Repositories\PoTenderItemsRepository;
use App\Repositories\TenderItemSpecificationRepository;
use Schema;
use App\VendorProfile;

use App\TenderWorkflowHelper;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use App\Repositories\TenderRepository;
use App\Repositories\TenderProcessRepository;
use App\RefCompanyType;
use \DB;

class POController extends Controller
{
    protected $workflow;
    protected $repo;
    protected $repoPo;

    public function __construct(Request $request, TenderRepository $repo, PoRepository $repoPo)
    {
        $this->middleware('auth');
        $this->workflow = new TenderWorkflowHelper();
        $this->repo = $repo;
        $this->repoPo = $repoPo;
        $this->vendorRepo = new VendorRepository();
    }

    public function index(){   
        // $this->repoPo->replikasi();g
        // $this->repoPo->sapSend("RFQ-0000075","");    
        $arr_data = array();
        $arr_data['tenderData']['tender_po_index']['fields'] = $this->repoPo->fields();
        return view('po.index', $arr_data);
    }

    public function show($id, $type = 'parameters', $action = ''){   
        //abort_if(Gate::denies('tender_' . $type . '_read'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $tender = $this->repo->findTenderParameterById($id, true);
        try {
            $method = 'showTender' . str_replace('_', '', ucwords($type, '_'));
            //dd($method, $tender, $type, $action);
            if (method_exists($this, $method)) {
                return $this->$method($tender, $type, $action);
            } else {
                return $this->showDefault($tender, $type, $action);
            }
        } catch (Exception $e) {
            Log::error($e);
            throw $e;
        }
     }

         #region Save
    public function save(Request $request, $id, $vcode, $type = 'parameters'){
        if ($type=="document_type" || $type=="document_date" || $type=="delivery_date"){
            return $this->saveTenderItems($id ,$vcode, $type, $request);   
        }else{
            $tender = $this->repo->findTenderParameterById($id, true);
            return $this->saveTenderItems($tender,$vcode, $type, $request);   
        }
    }

    public function delete($id)
    {
        try {
            $this->repoPo->delete($id);
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
     
     public function showDetail(Request $request, $id, $vcode, $type = 'parameters', $action = ''){       
        $tender = $this->repoPo->findTenderParameterByTenderNumber($id, true);
        try {
            $method = 'showTender' . str_replace('_', '', ucwords($type, '_'));
            if (method_exists($this, $method)) {
                if ($type=="ItemList" || $type=="total"){
                    return $this->$method($request, $tender,  $vcode, $type);
                } else if ($type=="po_creation_detail" || $type=="detail"){
                    return $this->$method($request, $tender,  $vcode, $type);
                } else{
                    return $this->$method($tender,  $vcode, $type, $action);
                }
            } else {
                return $this->showDefault($tender, $type, $action);
            }
        } catch (Exception $e) {
            Log::error($e);
            throw $e;
        }
     }

     public function datatable_serverside(Request $request){
        if (request()->ajax()) {            
            $data = $this->repoPo->findlistPo2();
            $isVendor = Auth::user()->isVendor();    
            if ($isVendor){
                $vendor = Vendor::where("vendor_code", Auth::user()->userid)->first(); 
                $data = $data->where("vendors.id", $vendor->id);
            }
            $data = $data->withTrashed()->get();    
            return DataTables::of($data)
                 ->make(true);
        }
     }

     public function datatable_tenderserverside(Request $request, $id){
        if (request()->ajax()) {            
            $data = $this->repoPo->findlistPoByTender($id)->get();
            return DataTables::of($data)
                ->make(true);
        }
    }

     public function showTenderItemList(Request $request, $tender, $vcode, $type)
     {
         if (request()->ajax()) {
             return $this->repoPo->findItemPo($tender, $vcode, $type, $request->all());
         }
     }

    public function showTenderTotal($request, $tender, $vcode, $type ){
        $total = $this->repoPo->getTotalAddHeaderCost($request, $tender, $vcode);        
        $arr_data = array("data" => $total , "success" => true);
        return response()->json($arr_data);
    }

     public function find_data(Request $request, $type){
        if ($type=="address"){
            $data = $this->repoPo->findAddressByCategory($request->all());
            $data= array_merge(["success"=> true, "code"=>"1"], $data);
            return response()->json($data);
        }elseif ($type=="detail_address"){
            $tablename = "po_vendor_profile";
            $data = $this->repoPo->findProfile($request, $tablename);
            if (empty($data)){
                $tablename = "vendor_profile_generals";
                $data = $this->repoPo->findProfile($request, $tablename);
            }
            return response()->json($data);
        }
     }

     public function update_profile(Request $request){
            
            $name = Auth::user()->name;
            $input = $request->input();
            $table = "po_vendor_profile";
            $continue = true;
            $returnId = $input['id'];
            $message = "";
            $success = false;
            
            try{
                DB::beginTransaction();
                
                $fields = Schema::getColumnListing($table);
                if($request->input('edit_type') == 'current'){
                    $old_data = DB::table($table)->where('vendor_profile_id',$input['id'])->first();
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
                $vp = VendorProfile::find($input['id']);
                if($request->file()>0){
                    if($request->input('edit_type') != 'current'){
                        $old = DB::table($table)->where('vendor_profile_id',$input['id'])->get();
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
                    //dd($table);
                    if($table === 'po_vendor_profile'){
                        //if($data['location_category'] === 'Head Office'){
                            $id = $input['id'];
                            $generalData = DB::table($table)->where([
                                "vendor_profile_id" => $data['vendor_profile_id'],
                                "location_category" => $data['location_category']
                            ])
                            ->where("vendor_profile_id", "<>", $input['id'])
                            ->first();
                           
                            if($generalData){
                                $continue = false;
                                $message=__('homepage.you_can_not_add_more_than_one_head_office');
                            }
                        //}                    
                   
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
                    //dd($request->input('edit_type'));
                    // Check Change Current Data or New Data want to Edit
                    //if($request->input('edit_type') == 'current'){
                        // INSERTING NEW ROW DATA                     
                        $data['created_at'] = Date("Y-m-d h:i:s");
                        $data['created_by'] = $name;
                        //dd("a");
                        $dt = DB::table($table)->where("vendor_profile_id",$input["id"])->first();
                        
                        if (empty($dt)){
                            //dd($table, $data);
                            $affected = DB::table($table)->insert($data);
                            //$returnId = DB::getPdo()->lastInsertId();  
                        }else{
                            $data['parent_id'] = $dt->id;
                            $affected = DB::table($table)->where("vendor_profile_id",$input["id"])->update($data);  
                            //dd($table, $data, $affected);
                        }
                        if(!$affected){
                            $continue = false;
                            $message=__('homepage.failed_to_update_current_data');
                        } else {
                            $message=__('homepage.successfully_updated_current_data');
                        }
                        
                    //}                 
                } 
                // Case when insert or update has been processed OK
                if($continue){
                    //$this->generate_profile_status_checklist($tableInput, $input['vendor_profile_id'], ['none', 'warning', 'not-finish', 'finish'], 'not-finish');
                    DB::commit();
                } else {
                    DB::rollback();                                
                }
                $success = $continue;
            }catch(Exception $e){                
                Log::error($e);
                DB::rollback();                
            }
            return response()->json([
                'status' => 200,
                'success' => $success,
                'message' => $message,
                'data' => ['id' => $returnId],
            ]);
        
    }

     private function showTenderDetail($request, $tender, $vendorCode, $type){
        $tRepo = new TenderItemsRepository();
        //$fields = $this->repoPo->replikasiByCondition($tender, $vendorCode, $type);
        $fields = $this->repoPo->fieldsItem();
        $models = $this->repoPo->findItemDetail($request->get("eproc_po_number"), $tender, $vendorCode);
        $storage = asset('storage/tender/' . $tender->tender_number . '/' . $type);
        $arr_default = $this->defaultViewData($tender, $type);
        $arr_data =array_merge($arr_default, [
           'storage'           => $storage,
           'tenderData'        => [
               'tender_detail' => [
                   'fields' =>  $fields,
                   'fields_item' =>  $tRepo->fields(),
                   'service_fields' => $tRepo->fields('prlist_services'),
                    'item_text_fields' => $tRepo->fields('prlist_item_text')
               ],
               'models' => $models,              
           ],
           'tender' => $tender,
           'conditionalTypeList' =>  $tRepo->findConditionalType(),
           'taxCodes' =>  $tRepo->findTaxCodes(),
           'categories' => (new TenderItemSpecificationRepository)->findCategories($tender->tender_number),
           'vendor' => $models["vendor"],
           'purchase_org_all' => RefPurchaseOrg::all(),
           'companyTypes' => RefCompanyType::all(),
           'selectCountry'=> RefCountry::select('country_code','country_description')->withTrashed(false)->orderby('country_description', 'ASC')->pluck('country_description', 'country_code'),
           'incotermOptions'   => RefListOption::where('type', 'incoterm_options')->where('deleteflg', false)->pluck('value', 'key'),
           'eproc_po_number' => $request->get("eproc_po_number")
        ]);
       $postalCodes = [];
       $refPostalCodes = RefPostalCode::all();
       foreach($refPostalCodes as $postalcode){
           $postalCodes[$postalcode->country_code] = [
               'length' => $postalcode->length,
               'required' => $postalcode->required,
               'check_rule' => $postalcode->check_rule
           ];
       }
       $arr_data['postalCodes'] = $postalCodes;
       //dd($arr_data);
        return view('po.' . $type, $arr_data);
    }

    

    private function showTenderPoCreationDetail($request, $tender, $vendorCode, $type)
    {
        $tRepo = new TenderItemsRepository();
        $fields = $this->repoPo->fieldsItem();
         $models = $this->repoPo->findItemDetail($request->get("eproc_po_number"), $tender, $vendorCode);
         $storage = asset('storage/tender/' . $tender->tender_number . '/' . $type);
         $arr_default = $this->defaultViewData($tender, $type);
         $fieflds = $this->repoPo->fieldsItem();
         $arr_data =array_merge($arr_default, [
            'storage'           => $storage,
            'tenderData'        => [
                'tender_po_creation_detail' => [
                    'fields' =>  $fields,
                    'fields_item' =>  $tRepo->fields(),
                    'service_fields' => $tRepo->fields('prlist_services'),
                    'item_text_fields' => $tRepo->fields('prlist_item_text')
                ],
                'models' => $models,
                'tender_id' => $tender->id,               
            ],
            'tender' => $tender,
            'conditionalTypeList' =>  $tRepo->findConditionalType(),
            'taxCodes' =>  $tRepo->findTaxCodes(),
            'categories' => (new TenderItemSpecificationRepository)->findCategories($tender->tender_number),
            'vendor' => $models["vendor"],
            'purchase_org_all' => RefPurchaseOrg::all(),
            'companyTypes' => RefCompanyType::all(),
            'selectCountry'=> RefCountry::select('country_code','country_description')->withTrashed(false)->orderby('country_description', 'ASC')->pluck('country_description', 'country_code'),
            'incotermOptions'   => RefListOption::where('type', 'incoterm_options')->where('deleteflg', false)->pluck('value', 'key'),
            'eproc_po_number' => $request->get("eproc_po_number")
        ]);
        $postalCodes = [];
        $refPostalCodes = RefPostalCode::all();
        foreach($refPostalCodes as $postalcode){
            $postalCodes[$postalcode->country_code] = [
                'length' => $postalcode->length,
                'required' => $postalcode->required,
                'check_rule' => $postalcode->check_rule
            ];
        }
        $arr_data['postalCodes'] = $postalCodes;
        return view('po.' . $type, $arr_data);
     }

     private function showTenderPoCreation($tender, $type)
     {
         $storage = asset('storage/tender/' . $tender->tender_number . '/' . $type);
         $arr_default = $this->defaultViewData($tender, $type);
         $arr_data =array_merge($arr_default, [
            'storage'           => $storage,
            'tenderData'        => [
                'tender_po_creation' => [
                    'fields' => $this->repoPo->fields()
                ],
                'tender_id' => $tender->id,
                'selectCountry'=> RefCountry::select('country_code','country_description')->withTrashed(false)->orderby('country_description', 'ASC')->pluck('country_description', 'country_code'),
            ],
        ]);

        //dd($arr_data["tenderData"]["tender_id"]);
         return view('po.' . $type, $arr_data);
     }

    private function showTenderTotalCost($tender, $type){

    }
 
     private function defaultViewData($tender, $type = 'parameters', $action = '')
    {
        $isVendor = Auth::user()->isVendor();
        $pages = $this->workflow->getCurrentAvailable($tender);
        $pages['availables'] = $this->prepareAllowedPage($pages['availables'], Auth::user()->vendor, $tender);
        
        if (!in_array($type, $pages['availables'])) {
            //abort(404);
        }
        $next = array_search($type, $pages['availables']);
      
        if ($next !== false) {
            $next = $next + 1 == count($pages['availables']) ? $pages['availables'][$next] : $pages['availables'][$next + 1];
        }

        return [
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
    }
    public function showDefault($tender, $type = 'parameters')
    {
        $storage = asset('storage/tender/' . $tender->tender_number . '/' . $type);
        if (empty($tender->visibility_bid_document)) $tender->visibility_bid_document = 'PRIVATE';
        $arr_data = array_merge($this->defaultViewData($tender, $type), [
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

    private function saveTenderItems($tender, $vcode, $type, Request $request)
    {
        try {
            switch($type){
                case "items": 
                    {   
                        $params = $request->all();
                        if(!empty($params['action']) && $params['action']=='detail-specification'){
                            $result = (new TenderItemSpecificationRepository())->save($tender, $params, $type);
                        }else{
                            $result = (new PoTenderItemsRepository())->save($tender, $vcode, $params);
                        }
                        return response()->json([
                            'status' => 200,
                            'success' => true,
                            'message' => 'data_saved',
                            'data' => $result
                        ]);
                    }
                    break;
                case "po_header": 
                    { 
                        $params = $request->all();
                        $this->repoPo->savedata($tender, $vcode, $type, $params);
                        return response()->json([
                            'status' => 200,
                            'success' => true,
                            'message' => 'data_saved',
                        ]);
                    }
                break;
                case "document_type":{
                    $params = $request->all();
                        $this->repoPo->saveDocumentType($tender, $vcode, $type, $params);
                        return response()->json([
                            'status' => 200,
                            'success' => true,
                            'message' => 'data_saved',
                        ]);
                }
                break;  
                case "delivery_date":{
                    $params = $request->all();
                        $this->repoPo->saveDocumentType($tender, $vcode, $type, $params);
                        return response()->json([
                            'status' => 200,
                            'success' => true,
                            'message' => 'data_saved',
                        ]);
                } 
                break;
                case "document_date":{
                    $params = $request->all();
                        $this->repoPo->saveDocumentType($tender, $vcode, $type, $params);
                        return response()->json([
                            'status' => 200,
                            'success' => true,
                            'message' => 'data_saved',
                        ]);
                } 
                break;
                case "submit_sap":{
                    $params = $request->all();
                    $this->repoPo->submitToSap($params);
                    return response()->json([
                        'status' => 200,
                        'success' => true,
                        'message' => 'data_saved sumbmit sap',
                    ]);
                }
                break;
            default:
                break;
            }
        } catch (Exception $e) {
            Log::error($e);
            return response()->json([
                'status' => 200,
                'success' => false,
                'message' => 'data_not_saved',
            ]);
        }
    }

    function validateVendor($vcode){
        if (Auth::user()->isVendor()){
            if (trim(Auth::user()->userid)!=trim($vcode)){
                abort(404);   
            }
        }
    }

}
/*
PT Abyor International - Voproc Trademark Registration and Copyright Recordal
*/