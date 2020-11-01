<?php

namespace App\Repositories;

use App\SapConnector;
use App\Vendor;
use App\VendorProfile;
use App\VendorProfileBankAccount;
use App\VendorProfileGeneral;
use App\VendorProfilePic;
use App\VendorProfileTax;
use App\VendorProfileDetailStatus;
use App\VendorWorkflow;
// use App\VendorApproval;
use App\User;
use App\Repositories\VendorRepository;

use App\RefBank;
use App\RefCompanyType;
use App\RefCompanyGroup;
use App\RefPurchaseOrg;
use App\RefCountry;
use App\RefProvince;
use App\RefCity;
use App\RefSubDistrict;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class LegacyRepository{

    protected $general, $deeds, $shareholder, $bodboc, $businesspermit, $pic, $equipment, 
            $expert, $certification, $scopesupply, $experience, $bankaccount, $financial, $tax;

    protected $createdBy = 'migration';
    protected $migratedVendor;
    public $migratedVendorData;
    public $sapMessage;
    public $migrationMessage;

    function __construct(){
        $this->companyTypes = RefCompanyType::pluck('company_type','id');
        $this->banks = RefBank::pluck('id','bank_key');
        $this->purchaseOrgs = RefPurchaseOrg::pluck('id','org_code');
        $this->vendorGroups = array_flip(config('eproc.sap.default_variables.PARTN_GRP'));
        $this->validVendorGroups = config('eproc.sap.default_variables.PARTN_GRP');
        $this->vendorRepo = new VendorRepository();
    }
    /**
     * sync Ref Bank from SAP Data
     *
     * @return array
     *
     * @throws \Exception
     */
    public function migrateLegacy($params){
        $maxExecutionTime = ini_get('max_execution_time');
        ini_set('max_execution_time', 1800);
        $memoryLimit = ini_get('memory_limit');
        ini_set("memory_limit","512M");
        Log::debug('========= START MIGRATION =========');
        Log::debug('PARAMS:');
        Log::debug($params);
        // Log::debug('getting sap data...');
        $sapResult = $this->getSapLegacy($params);
        if($sapResult['status']){
            // Log::debug('reformatting...');
            $vendors = $this->reformatLegacyData($sapResult,$params);
            // Log::debug('sending to db...');
            $dbResult = $this->sendLegacyToDb($vendors,$params['max'],$params);
        }else{
            Log::debug('SAP message: '.$this->sapMessage);
        }
        Log::debug('========= FINISH MIGRATION ['.$this->migratedVendor.' vendor(s)] =========');
        ini_set('max_execution_time', $maxExecutionTime == 0 ? 120 : $maxExecutionTime);
        ini_set("memory_limit",$memoryLimit);
        return $this->migratedVendor;
    }

    public function migrateLegacyOld($max=null){
        $maxExecutionTime = ini_get('max_execution_time');
        ini_set('max_execution_time', 1800);
        $memoryLimit = ini_get('memory_limit');
        ini_set("memory_limit","512M");
        Log::debug('========= START MIGRATION =========');
        // Log::debug('getting sap data...');
        $sapResult = $this->getSapLegacy();
        if($sapResult['status']){
            // Log::debug('reformatting...');
            $vendors = $this->reformatLegacyData($sapResult);
            // Log::debug('sending to db...');
            $dbResult = $this->sendLegacyToDb($vendors,$max);
        }
        Log::debug('========= FINISH MIGRATION ['.$this->migratedVendor.' vendor(s)] =========');
        ini_set('max_execution_time', $maxExecutionTime == 0 ? 120 : $maxExecutionTime);
        ini_set("memory_limit",$memoryLimit);
        return $this->migratedVendor;
    }

    public function sendLegacyToDb($vendors,$max,$params){
        $success = false;
        $this->migrationMessage = "";
        try{
            DB::beginTransaction();

            $i = 0;
            $break = false;
            $savedData = [];
            $maxMigrateInsert = is_null($max) ? config('eproc.default_max_legacy_insert_per_transaction') : $max;
            // Log::debug('Start sending to db');
            $this->migratedVendor = 0;
            $this->migratedVendorData = [];
            while($i<count($vendors) && !$break){
                $data = $vendors[$i];

                $count = Vendor::where('business_partner_code',$data['vendor']['business_partner_code'])
                    ->where('sap_vendor_code',$data['vendor']['sap_vendor_code'])
                    ->count();

                if($count==0 && !is_null($data['vendor']['purchase_org_id'])){
                    Log::debug('Start saving vendor '.$data['vendor']['sap_vendor_code'].', purch org:'.$data['vendor']['purchase_org_id']);
                    //new vendor yg registration_status nya adalah candidate
                    // Log::debug($data['vendor']);
                    $v = new Vendor();
                    $v->fill($data['vendor']);
                    $v->registration_status = 'candidate';
                    $v->is_legacy = true;
                    $v->save();
                    $vendorID = $v->id;
                    // Log::debug('vendor saved: '.$vendorID);

                    $vp = new VendorProfile();
                    $vp->fill($data['profile']);
                    $vp->vendor_id = $v->id;
                    $vp->company_warning = 'GREEN';
                    $vp->save();
                    // Log::debug('vendor profile saved: '.$vp->id);

                    $arr_registration_number = $this->vendorRepo->createRegistrationNumber(null, $v);
                    $registration_number = $arr_registration_number["registration_number"];
                    $last_number = $arr_registration_number["nextNumber"];

                    $v->vendor_code = $registration_number;
                    $v->save();

                    if ($v->vendor_group=="foreign")
                        RefCompanyGroup::where('name', "foreign")->update(['last_number' => $last_number]);                        
                    else
                        RefCompanyGroup::where('name', "local")->update(['last_number' => $last_number]);                        
                        
                    if(count($data['generals'])>0){
                        foreach($data['generals'] as $general){
                            $vg = new VendorProfileGeneral();
                            $vg->fill($general);
                            $vg->vendor_profile_id = $vp->id;
                            $vg->save();
                            // Log::debug('vendor profile general saved: '.$vg->id);
                        }
                    }
    
                    if(count($data['banks'])>0){
                        foreach($data['banks'] as $bank){
                            $vb = new VendorProfileBankAccount();
                            $vb->fill($bank);
                            $vb->vendor_profile_id = $vp->id;
                            $vb->save();
                            // Log::debug('vendor profile bank saved: '.$vb->id);
                        }
                    }
    
                    if(count($data['pics'])>0){
                        foreach($data['pics'] as $pic){
                            $vpic = new VendorProfilePic();
                            $vpic->fill($pic);
                            $vpic->username = $registration_number;
                            $vpic->vendor_profile_id = $vp->id;
                            $vpic->save();
                            // Log::debug('vendor profile pic saved: '.$vpic->id);
                        }
                    }
    
                    if(count($data['taxes'])>0){
                        foreach($data['taxes'] as $tax){
                            $vtax = new VendorProfileTax();
                            $vtax->fill($tax);
                            $vtax->vendor_profile_id = $vp->id;
                            $vtax->save();
                            // Log::debug('vendor profile tax saved: '.$vtax->id);
                        }
                    }

                    //3. Profile detail statuses dg initial value is approve is revise is submite false, warning untuk sesuai kriteria company type
                    $this->set_profile_detail_status($v->vendor_group,$vp->company_type,'warning');
                    $vendorProfileStatus = new VendorProfileDetailStatus([
                        'vendor_profile_id' => $vp->id,
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
                        'created_by' => $this->createdBy,
                    ]);
                    $vendorProfileStatus->save();
                    // Log::debug('vendor profile status saved: '.$vendorProfileStatus->id);
        
                    //4. vendor workflow 1 row aja isinya Initial Submission tapi cari istilah lain aja pak kalo datanya berasal dari syncronize sap
                    $vendorWorkflow = new VendorWorkflow([
                        'vendor_id' => $vendorID,
                        'activity' => 'Legacy Submission',
                        'remarks' => '',
                        'started_at' => now(),
                        'finished_at' => null,
                    ]);
                    $vendorWorkflow->save();
                    // Log::debug('vendor workflow saved: '.$vendorWorkflow->id);
    
                    $savedData[] = $data['bp'];

                    // Insert users ID
                    $setpassword = config('eproc.default_legacy_password');
                    $setpassword = str_replace('[VENDOR_CODE]',$registration_number,$setpassword);
                    // Log::debug('password: '.$setpassword);
                    $users = new User([
                        'name' => $data['username'],
                        'userid' => $registration_number,
                        'user_type' => 'vendor',
                        'ref_id' => $vendorID,
                        'email' => $v->company_email,
                        'email_verified_at' => now(),
                        'password' => Hash::make($setpassword)
                    ]);
                    $users->save();
                    // Log::debug('user saved: '.$users->id.', '.$users->userid.', '.$setpassword);

                    // Set User Role
                    $users->assignRole('vendor');

                    // Log::debug('========Finish saving vendor');
                    Log::debug('Vendor ['.$v->vendor_name.'] saved. '.$registration_number.' / '.$setpassword);

                    $this->migratedVendor++;
                    $this->migratedVendorData[] = ['vendor'=>$v,'profile'=>$vp];

                    if($maxMigrateInsert > 0){
                        $break =  $this->migratedVendor >= $maxMigrateInsert ; 
                    }
                }else{
                    $this->migrationMessage .= $count>0 ? "<br>Vendor ".$data['vendor']['sap_vendor_code']." already exist in DB \n" : "";
                    $this->migrationMessage .= is_null($data['vendor']['purchase_org_id']) ? "<br>Vendor ".$data['vendor']['sap_vendor_code']." have no purchase organization. Cannot insert to DB \n" : "";
                }
                $i++;
            }
            // Log::debug("===========Finish saving all vendors");
            $success = true;
    
            DB::commit();
        }catch(Exception $e){
            DB::rollback();
            Log::error($e->getMessage());
        }

        return $success;
    }

    public function getSapLegacy($params){
        try {
            $sapMessage = "";
            $sap = new SapConnector();
            $result = $sap->call('legacy_list', [
                'I_PURCHORG_CODE' => [
                    'EKORG_LOW' => $params['purch_org_from'],
                    'EKORG_HIGH' => $params['purch_org_to'],
                ],
                'I_VENDOR_NO' =>[
                    'VENDOR_LOW' => $params['vendor_from'],
                    'VENDOR_HIGH' => $params['vendor_to'],
                ]
            ]);
            Log::debug($sap->requestMessage);
            // Log::debug($result);
            if($result!==false){
                if(isset($result['RETURN'])){
                    $type = $result['RETURN']['TYPE'];
                    switch($type){
                        case 'S' : $status = true; break; //success
                        case 'E' : $status = false; break; //error
                        case 'W' : $status = false; break; //warning
                        case 'I' : $status = false; break; //info
                        case 'A' : $status = false; break; //abort
                    }
                    $message = $result['RETURN']['MESSAGE'];
                    $this->sapMessage = $result['RETURN']['MESSAGE'];
                    
                    return ['status'=>$status,'data'=>$result,'message'=>$message];
               }else{
                    //something wrong
                    return ['status'=>false,'data'=>$result,'message'=>"Fail synchronize to SAP. Please contact administrator."];
                }
            }else{
                Log::error($sap->debugMessage);
                return ['status'=>false,'data'=>$result,'message'=>"Fail synchronize to SAP. Please contact administrator."];
            }

        } catch (Exception $e) {
            Log::error('Error getting migration data: '.$e->getMessage());
            return ['status'=>false,'data'=>$result,'message'=>"Fail synchronize to SAP. Please contact administrator."];
        }
    }
    
    public function reformatLegacyData($sapData, $params){
        $data = $sapData['data'];
        $currentVendors = Vendor::where('registration_status','vendor')->pluck('business_partner_code')->toArray();
        $legacies = [];
        $creator = $this->createdBy;

        if(!isset($data['I_DATA']['ITEM'][0])){
            //item only 1.
            $item = $data['I_DATA']['ITEM'];
            $data['I_DATA']['ITEM'] = array($item);
        }
        foreach($data['I_DATA']['ITEM'] as $item){

            $canProcessVendor = true;
            if($params['vendor_from'] != '') $canProcessVendor = $canProcessVendor && $params['vendor_from'] <= $item['PARTNER_NO'];
            if($params['vendor_to'] != '') $canProcessVendor = $canProcessVendor && $item['PARTNER_NO'] <= $params['vendor_to'];

            if(!in_array($item['PARTNER_NO'], $currentVendors) 
                && in_array($item['PARTN_GRP'],$this->validVendorGroups)
                && $canProcessVendor
            ){
                $companyType = $this->getCompanyType($item['NAME1'],$item['COUNTRY']);
                $vendorProfile = [
                    'vendor_id' => 0, //
                    'company_name' => $item['NAME1'],
                    'company_type' => $companyType['desc'],
                    'company_status' => 'ACTIVE',
                    'company_warning' => 'GREEN',
                    'created_by' => $creator,
                ];
                $vendorGroup = $this->vendorGroups[$item['PARTN_GRP']];
                $vendor = [
                    'vendor_group'          => $vendorGroup,
                    'vendor_name'           => $item['NAME1'],
                    'company_type_id'       => $companyType['type'],
                    'purchase_org_id'       => null,
                    'purchase_org_id_1'     => null,
                    'president_director'    => $item['NAME1'],
                    'country'               => $item['COUNTRY'],
                    'postal_code'           => $item['POST_CODE1'],
                    'phone_number'          => $item['TEL_NUMBER'],
                    'fax_number'            => $item['FAX'],
                    'company_email'         => $item['EMAIL'],
                    'pic_full_name'         => $item['NAME1'], //
                    'pic_mobile_number'     => $item['TEL_NUMBER'], //
                    'pic_email'             => $item['EMAIL'], //
                    'business_partner_code' => $item['PARTNER_NO'],
                    'sap_vendor_code'       => $item['VENDOR_NO'],
                    'already_exist_sap'     => true,
                    'created_by'            => $creator,
                ];
                if($vendorGroup=='local'){
                    $province = $item['REGION']=='' ? null : RefProvince::where('region_description', 'ilike', '%' . $item['REGION'] . '%')->first();
                    $city = $item['CITY']=='' ? null : RefCity::where('city_description', 'ilike', '%' . $item['CITY'] . '%')->first();
                    $sub_district = $item['DISTRICT']=='' ? null : RefSubDistrict::where('district_description', 'ilike', '%' . $item['DISTRICT'] . '%')->first();
                    $vendor = array_merge($vendor, [
                        'street'                => $item['STREET'],
                        'building_name'         => $item['STR_SUPPL1'],
                        'kavling_floor_number'  => $item['STR_SUPPL2'],                        
                        'rt'                    => $item['STR_SUPPL3'],                        
                        'rw'                    => $item['STR_SUPPL4'],                        
                        'village'               => $item['LOCATION'],
                        'province'              => is_null($province) ? $item['REGION'] : $province->region_code,
                        'city'                  => is_null($city) ? $item['CITY'] : $city->city_code, 
                        'sub_district'          => is_null($sub_district) ? $item['DISTRICT'] : $sub_district->district_code, 
                        'house_number'          => $item['HOUSE_NO'],
                    ]);
                }else{
                    $vendor = array_merge($vendor, [
                        'address_1' => $item['STREET'],
                        'address_2' => $item['STR_SUPPL1'],
                        'address_3' => $item['STR_SUPPL2'],                        
                    ]);
                }

                //VENDOR GENERAL
                $vendorGeneral = [
                    'vendor_profile_id'     => 0, //will be changed after insert vp
                    'company_name'          => $item['NAME1'],
                    'company_type_id'       => $companyType['type'], //
                    'location_category'     => null,
                    'country'               => $item['COUNTRY'],
                    'postal_code'           => $item['POST_CODE1'],
                    'phone_number'          => $item['TEL_NUMBER'],
                    'fax_number'            => $item['FAX'],
                    'website'               => null,
                    'company_email'         => $item['EMAIL'],
                    'parent_id'             => 0,
                    'primary_data'          => true,
                    'is_current_data'       => true,
                    'created_by'            => $creator,
                ];
                if($vendorGroup=='local'){
                    $vendorGeneral = array_merge($vendorGeneral, [
                        'street'                => $item['STREET'],
                        'building_name'         => $item['STR_SUPPL1'],
                        'kavling_floor_number'  => $item['STR_SUPPL2'],                        
                        'rt'                    => $item['STR_SUPPL3'],                        
                        'rw'                    => $item['STR_SUPPL4'],                        
                        'village'               => $item['LOCATION'],
                        'province'              => is_null($province) ? $item['REGION'] : $province->region_code,
                        'city'                  => is_null($city) ? $item['CITY'] : $city->city_code, 
                        'sub_district'          => is_null($sub_district) ? $item['DISTRICT'] : $sub_district->district_code, 
                        'house_number'          => $item['HOUSE_NO'],
                    ]);
                }else{
                    $vendorGeneral = array_merge($vendorGeneral, [
                        'address_1' => $item['STREET'],
                        'address_2' => $item['STR_SUPPL1'],
                        'address_3' => $item['STR_SUPPL2'],                        
                    ]);
                }
                $legacies[] = [
                    'bp' => $item['PARTNER_NO'],
                    'username' => $item['NAME1'],
                    'vendor' => $vendor,
                    'profile' => $vendorProfile,
                    'generals' => [$vendorGeneral],
                    'banks' => [],
                    'pics' => [],
                    'taxes' => [],
                ];
            }
        }

        if(isset($data['T_BANK']['ITEM'])){
            if(!isset($data['T_BANK']['ITEM'][0])){
                //item only 1.
                $item = $data['T_BANK']['ITEM'];
                $data['T_BANK']['ITEM'] = array($item);
            }
            foreach($data['T_BANK']['ITEM'] as $item){

                $canProcessVendor = true;
                if($params['vendor_from'] != '') $canProcessVendor = $canProcessVendor && $params['vendor_from'] <= $item['PARTNER_NO'];
                if($params['vendor_to'] != '') $canProcessVendor = $canProcessVendor && $item['PARTNER_NO'] <= $params['vendor_to'];

                if(!in_array($item['PARTNER_NO'], $currentVendors) && $canProcessVendor){
                    $index = $this->searchIndex($item['PARTNER_NO'], $legacies);
                    if($index!==false){
                        $legacies[$index]['banks'][] = [
                            'vendor_profile_id' => 0, //will be changed after insert vp
                            'account_holder_name' => $item['KOINH'],
                            'account_number' => $item['BANKN'],
                            'currency' => config('eproc.default_currency'),
                            'country_code' => $item['BANKS'], //ok
                            'bank_key' => $item['BANKL'], //ref_bank
                            'bank_name' => $this->banks[$item['BANKL']], //ref bank::bank_id
                            'bank_address' => 'please fill bank address', //dummy address
                            'parent_id' => 0,
                            'is_current_data' => true,
                            'created_by' => $creator,
                        ];
                    }
                }
            }
        }

        if(isset($data['T_PURCHASING']['ITEM'])){
            if(!isset($data['T_PURCHASING']['ITEM'][0])){
                //item only 1.
                $item = $data['T_PURCHASING']['ITEM'];
                $data['T_PURCHASING']['ITEM'] = array($item);
            }
            foreach($data['T_PURCHASING']['ITEM'] as $item){

                $canProcessVendor = true;
                if($params['vendor_from'] != '') $canProcessVendor = $canProcessVendor && $params['vendor_from'] <= $item['PARTNER_NO'];
                if($params['vendor_to'] != '') $canProcessVendor = $canProcessVendor && $item['PARTNER_NO'] <= $params['vendor_to'];

                $canProcessPurchOrg = true;
                if($params['purch_org_from'] != '') $canProcessPurchOrg = $canProcessPurchOrg && $params['purch_org_from'] <= $item['EKORG'];
                if($params['purch_org_to'] != '') $canProcessPurchOrg = $canProcessPurchOrg && $item['EKORG'] <= $params['vendor_to'];

                if(!in_array($item['PARTNER_NO'], $currentVendors) && $canProcessVendor && $canProcessPurchOrg){
                    $index = $this->searchIndex($item['PARTNER_NO'], $legacies);
                    if($index!==false){
                        $legacies[$index]['pics'][] = [
                            'vendor_profile_id' => 0, //will be changed after insert vp
                            'username' => 'user', //will be changed after insert vendor eproc-vendor-code 
                            'full_name'=> $item['VERKF'],
                            'email' => $item['EMAIL'], //?
                            'phone' => $item['TELF1'],
                            'primary_data' => false,
                            'parent_id' => 0,
                            'is_current_data' => true,
                            'created_by' => $creator,
                        ];

                        if(count($legacies[$index]['pics'])==1){
                            $legacies[$index]['pics'][0]['primary_data']=true;
                            $legacies[$index]['username'] = $item['VERKF'];
                        }
                        if($legacies[$index]['vendor']['purchase_org_id'] == null){
                            $legacies[$index]['vendor']['purchase_org_id'] = $this->purchaseOrgs[$item['EKORG']];
                            $legacies[$index]['vendor']['pic_full_name'] = $item['VERKF'];
                            $legacies[$index]['vendor']['pic_mobile_number'] = $item['TELF1'];
                            $legacies[$index]['vendor']['pic_email'] = $item['EMAIL'];
                        }else{
                            $legacies[$index]['vendor']['purchase_org_id_1'] = $this->purchaseOrgs[$item['EKORG']];
                        }
                        foreach($legacies[$index]['banks'] as $key=>$val){
                            $legacies[$index]['banks'][$key]['currency'] = $item['WAERS'];
                        }
                    }
                }
            }
        }

        if(isset($data['T_TAX']['ITEM'])){
            if(!isset($data['T_TAX']['ITEM'][0])){
                //item only 1.
                $item = $data['T_TAX']['ITEM'];
                $data['T_TAX']['ITEM'] = array($item);
            }
            foreach($data['T_TAX']['ITEM'] as $item){

                $canProcessVendor = true;
                if($params['vendor_from'] != '') $canProcessVendor = $canProcessVendor && $params['vendor_from'] <= $item['PARTNER_NO'];
                if($params['vendor_to'] != '') $canProcessVendor = $canProcessVendor && $item['PARTNER_NO'] <= $params['vendor_to'];

                if(!in_array($item['PARTNER_NO'], $currentVendors) && $canProcessVendor){
                    $index = $this->searchIndex($item['PARTNER_NO'], $legacies);
                    if($index!==false){
                        $legacies[$index]['taxes'][] = [
                            'vendor_profile_id' => 0, //will be changed after insert vp
                            'tax_document_type' => $item['TAX_TYPE'],
                            'tax_document_number' => $item['TAX_NUMBER'],
                            'issued_date' => null,
                            'tax_document_attachment' => null,
                            'parent_id' => 0,
                            'is_current_data' => true,
                            'created_by' => $creator,
                        ];

                        if($item['TAX_TYPE']=='ID1'||$item['TAX_TYPE']=='ZZ1'){
                            //tin
                            $legacies[$index]['vendor']['identification_type'] = 'tin';
                            $legacies[$index]['vendor']['tin_number'] = $item['TAX_NUMBER'];
                        }else if($item['TAX_TYPE']=='ID2'){
                            //non pkp
                            $legacies[$index]['vendor']['pkp_type'] = 'non-pkp';
                            $legacies[$index]['vendor']['non_pkp_number'] = $item['TAX_NUMBER'];
                        }else if($item['TAX_TYPE']=='ID3'){
                            //pkp
                            $legacies[$index]['vendor']['pkp_type'] = 'pkp';
                            $legacies[$index]['vendor']['pkp_number'] = $item['TAX_NUMBER'];
                        }else if($item['TAX_TYPE']=='ID4'){
                            //id_card
                            $legacies[$index]['vendor']['identification_type'] = 'id-card';
                            $legacies[$index]['vendor']['idcard_number'] = $item['TAX_NUMBER'];
                        }
                    }
                }
            }
        }

        return $legacies;
    }

    public function reformatLegacyDataOld($sapData){
        $data = $sapData['data'];
        $currentVendors = Vendor::where('registration_status','vendor')->pluck('business_partner_code')->toArray();
        $legacies = [];
        $creator = $this->createdBy;
 
        foreach($data['I_DATA']['ITEM'] as $item){
            if(!in_array($item['PARTNER_NO'], $currentVendors)){
                $province = $item['CITY']=='' ? null : RefProvince::where('region_description', 'ilike', $item['CITY'] . '%')->first();
                $city = $item['LOCATION']=='' ? null : RefCity::where('city_description', 'ilike', $item['LOCATION'] . '%')->first();
                $companyType = $this->getCompanyType($item['NAME1'],$item['COUNTRY']);
                $vendorProfile = [
                    'vendor_id' => 0, //
                    'company_name' => $item['NAME1'],
                    'company_type' => $companyType['desc'],
                    'company_status' => 'ACTIVE',
                    'created_by' => $creator,
                ];
                $vendor = [
                    'vendor_group' => $companyType['group'],
                    'vendor_name' => $item['NAME1'],
                    'company_type_id' => $companyType['type'], //
                    'purchase_org_id' => 0, // changed below from pic.
                    'president_director' => $item['NAME1'], //
                    'address_1' => $item['STREET'],
                    'address_2' => $item['STR_SUPPL1'],
                    'address_3' => $item['STR_SUPPL2'],
                    'address_4' => $item['STR_SUPPL3'],
                    'address_5' => $item['DISTRICT'],
                    'country' => $item['COUNTRY'], //ok
                    'province' => is_null($province) ? $item['CITY'] : $province->region_code, //ref province
                    'city' => is_null($city) ? $item['LOCATION'] : $city->city_code, //ref city
                    'postal_code' => $item['POST_CODE1'],
                    'phone_number' => $item['TEL_NUMBER'],
                    'fax_number' => $item['FAX'],
                    'company_email' => $item['EMAIL'],
                    'pic_full_name' => $item['NAME1'], //
                    'pic_mobile_number' => $item['TEL_NUMBER'], //
                    'pic_email' => $item['EMAIL'], //
                    'business_partner_code' => $item['PARTNER_NO'],
                    'sap_vendor_code' => $item['VENDOR_NO'],
                    'already_exist_sap' => true,
                    'created_by' => $creator,
                ];
                $vendorGeneral = [
                    'vendor_profile_id' => 0, //will be changed after insert vp
                    'company_name' => $item['NAME1'],
                    'company_type_id' => $companyType['type'], //
                    'location_category' => null,
                    'country' => $item['COUNTRY'], //ok
                    'province' => is_null($province) ? $item['CITY'] : $province->region_code, //ref province
                    'city' => is_null($city) ? $item['LOCATION'] : $city->city_code, //ref city
                    'sub_district' => null,
                    'postal_code' => $item['POST_CODE1'],
                    'address_1' => $item['STREET'],
                    'address_2' => $item['STR_SUPPL1'],
                    'address_3' => $item['STR_SUPPL2'],
                    'address_4' => $item['STR_SUPPL3'],
                    'address_5' => $item['DISTRICT'],
                    'phone_number' => $item['TEL_NUMBER'],
                    'fax_number' => $item['FAX'],
                    'website' => null,
                    'company_email' => $item['EMAIL'],
                    'parent_id' => 0,
                    'primary_data' => true,
                    'is_current_data' => true,
                    'created_by' => $creator,
                ];
                $legacies[] = [
                    'bp' => $item['PARTNER_NO'],
                    'username' => $item['NAME1'],
                    'vendor' => $vendor,
                    'profile' => $vendorProfile,
                    'generals' => [$vendorGeneral],
                    'banks' => [],
                    'pics' => [],
                    'taxes' => [],
                ];
            }
        }
        foreach($data['T_BANK']['ITEM'] as $item){
            if(!in_array($item['PARTNER_NO'], $currentVendors)){
                $index = $this->searchIndex($item['PARTNER_NO'], $legacies);
                if($index!==false){
                    $legacies[$index]['banks'][] = [
                        'vendor_profile_id' => 0, //will be changed after insert vp
                        'account_holder_name' => $item['KOINH'],
                        'account_number' => $item['BANKN'],
                        'currency' => config('eproc.default_currency'),
                        'country_code' => $item['BANKS'], //ok
                        'bank_key' => $item['BANKL'], //ref_bank
                        'bank_name' => $this->banks[$item['BANKL']], //ref bank::bank_id
                        'bank_address' => 'please fill bank address', //dummy address
                        'parent_id' => 0,
                        'is_current_data' => true,
                        'created_by' => $creator,
                    ];
                }
            }
        }
        foreach($data['T_PURCHASING']['ITEM'] as $item){
            if(!in_array($item['PARTNER_NO'], $currentVendors)){
                $index = $this->searchIndex($item['PARTNER_NO'], $legacies);
                if($index!==false){
                    $legacies[$index]['pics'][] = [
                        'vendor_profile_id' => 0, //will be changed after insert vp
                        'username' => 'user', //will be changed after insert vendor eproc-vendor-code 
                        'full_name'=> $item['VERKF'],
                        'email' => $item['EMAIL'], //?
                        'phone' => $item['TELF1'],
                        // 'primary_data'=> $item['SPERM']=='X',
                        'primary_data' => false,
                        'parent_id' => 0,
                        'is_current_data' => true,
                        'created_by' => $creator,
                    ];

                    // if($item['SPERM']=='X') $legacies[$index]['username'] = $item['VERKF'];
                    if(count($legacies[$index]['pics'])==1){
                        $legacies[$index]['pics'][0]['primary_data']=true;
                        $legacies[$index]['username'] = $item['VERKF'];
                    }
                    $legacies[$index]['vendor']['purchase_org_id'] = $this->purchaseOrgs[$item['EKORG']];
                    $legacies[$index]['vendor']['pic_full_name'] = $item['VERKF'];
                    $legacies[$index]['vendor']['pic_mobile_number'] = $item['TELF1'];
                    $legacies[$index]['vendor']['pic_email'] = $item['EMAIL'];
                    foreach($legacies[$index]['banks'] as $key=>$val){
                        $legacies[$index]['banks'][$key]['currency'] = $item['WAERS'];
                    }
                }
            }
        }
        foreach($data['T_TAX']['ITEM'] as $item){
            if(!in_array($item['PARTNER_NO'], $currentVendors)){
                $index = $this->searchIndex($item['PARTNER_NO'], $legacies);
                if($index!==false){
                    $legacies[$index]['taxes'][] = [
                        'vendor_profile_id' => 0, //will be changed after insert vp
                        'tax_document_type' => $item['TAX_TYPE'],
                        'tax_document_number' => $item['TAX_NUMBER'],
                        'issued_date' => null,
                        'tax_document_attachment' => null,
                        'parent_id' => 0,
                        'is_current_data' => true,
                        'created_by' => $creator,
                    ];
                    if($companyType['group']=='local'){
                        if($item['TAX_TYPE']=='ID1'){
                            //tin
                            $legacies[$index]['vendor']['tin_number'] = $item['TAX_NUMBER'];
                        }else if($item['TAX_TYPE']=='ID2'){
                            //pkp
                            $legacies[$index]['vendor']['pkp_type'] = 'pkp';
                            $legacies[$index]['vendor']['pkp_number'] = $item['TAX_NUMBER'];
                        }
                    }else{
                        $legacies[$index]['vendor']['tin_number'] = $item['TAX_NUMBER'];
                    }
                }
            }
        }
        return $legacies;
    }

    function searchIndex($bpCode, $haystack){
        $i = 0;
        $found = false;
        while($i<count($haystack) && !$found){
            if($haystack[$i]['bp'] == $bpCode){
                $found = true;
            }else{
                $i++;
            }
        }
        if($found){
            return $i;
        }
        return false;
    }

    function define_profiles_required($general, $deeds, $shareholder, $bodboc, $businesspermit, $pic, $equipment, $expert, $certification, $scopesupply, $experience, $bankaccount, $financial, $tax){
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

    function set_profile_detail_status($vendorGroup, $companyType, $profileStatus){
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

    function getCompanyType($name, $country){
        if($country == 'ID'){
            $vendorGroup = 'local';
            $found = false;
            foreach($this->companyTypes as $typeId=>$typeName){
                if(stripos($name,$typeName)!==false){
                    $type = $typeId;
                    $description = $typeName;
                    $found = true;
                }
            }
            if(!$found){
                $type = 8;
                $description = 'Others';
            }
        }else{
            $vendorGroup = 'foreign';
            $type = 7;
            $description = 'Company';
        }
        return ['group'=>$vendorGroup, 'type'=>$type, 'desc'=>$description];
    }
}