<?php

namespace App\Repositories;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

use App\Vendor;
use App\VendorWorkflow;
use App\VendorApproval;
use App\VendorProfile;
use App\VendorProfileDetailStatus;
use App\VendorProfileBankAccount;
use App\VendorProfileBodboc;
use App\VendorProfileBusinessPermit;
use App\VendorProfileCertification;
use App\VendorProfileCompetency;
use App\VendorProfileDeed;
use App\VendorProfileExperience;
use App\VendorProfileExpert;
use App\VendorProfileFinancial;
use App\VendorProfileGeneral;
use App\VendorProfilePic;
use App\VendorProfileShareholder;
use App\VendorProfileTax;
use App\VendorProfileTool;
use App\VendorEvaluationForm;
use App\VendorEvaluationGeneral;
use App\SapConnector;
use App\DocumentExpiry;
use App\RefCompanyGroup;
use App\Models\Ref\RefSysParam;

class VendorRepository extends BaseRepository
{
    private $logName = 'VendorRepository';

    public function __construct(){
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
            'tax-document' => with(new VendorProfileTax)->getTable()
        ];
        $this->tableKeys = array_keys($this->tables);
        $this->tables['vendorprofile'] = with(new VendorProfile)->getTable();
        $this->tables['vendorprofiledetailstatus'] = with(new VendorProfileDetailStatus)->getTable();
    }

    public function getListVendorByType($type){
        $vendor = $type=='applicant' ? $this->getQueryApplicant() : $this->getQueryVendor();
        return $vendor->where('vendors.registration_status', $type);
    }

    public function getVendorById($vendorId){
        return $this->getQueryVendorById($vendorId)->first();
    }
    
    public function getQueryApplicant(){
        $query = Vendor::select("vendors.*",
            'ref_company_types.company_type',
            'ref_purchase_orgs.org_code',
            'ref_purchase_orgs.description',
            DB::raw('TO_CHAR(vendors.created_at, \'DD.MM.YYYY HH24:MI\') as created_at'),
            DB::raw('ref_countries.country_description as country'),
            DB::raw('ref_provinces.region_description as province'),
            DB::raw('ref_cities.city_description as city'),
            DB::raw('ref_sub_districts.district_description as sub_district')
        )
        ->join('ref_company_types', "ref_company_types.id", '=', "vendors.company_type_id")
        ->join('ref_purchase_orgs', "ref_purchase_orgs.id", '=', "vendors.purchase_org_id")
        ->join('ref_countries', "vendors.country", '=', "ref_countries.country_code")
        ->leftJoin('ref_provinces', function ($join) {
            $join->on('ref_provinces.country_code', 'vendors.country');
            $join->on('ref_provinces.region_code', 'vendors.province');
        })
        ->leftJoin('ref_cities', function ($join) {
            $join->on('ref_cities.city_code', '=', 'vendors.city');
            $join->on('ref_cities.country_code', 'vendors.country');
            $join->on('ref_cities.region_code', 'vendors.province');
        })
        ->leftJoin('ref_sub_districts', function ($join) {
            $join->on('ref_sub_districts.district_code', '=', 'vendors.sub_district');
            $join->on('ref_sub_districts.country_code', 'vendors.country');
            $join->on('ref_sub_districts.region_code', 'vendors.province');
            $join->on('ref_sub_districts.city_code', 'vendors.city');
        });
        return $query;
    }
    public function getQueryApplicantById($vendorId){
        return $this->getQueryApplicant()->where("vendors.id",$vendorId);
    }
    public function getQueryVendor(){
        $query = Vendor::select(
                "vendors.*",
                'vendor_profile_generals.company_name',
                'ref_company_types.company_type',
                DB::raw('ref_company_types.description as company_type_description'),
                DB::raw('ref_purchase_orgs.org_code as purchase_org_code'),
                DB::raw('ref_purchase_orgs.description as purchase_org_description'),
                DB::raw('ref_purchase_orgs_1.org_code as purchase_org_code_1'),
                DB::raw('ref_purchase_orgs_1.description as purchase_org_description_1'),
                DB::raw('TO_CHAR(vendors.created_at, \'DD.MM.YYYY HH24:MI\') as created_at'),
                DB::raw('ref_countries.country_description as country'),
                DB::raw('ref_provinces.region_description as province'),
                DB::raw('ref_cities.city_description as city'),
                DB::raw('ref_sub_districts.district_description as sub_district'),
                DB::raw("CASE WHEN vendors.registration_status = 'vendor' THEN COALESCE(vendor_profile_detail_statuses.update_vendor_data_status, 'To Be Updated') ELSE '-' END as update_vendor_data_status"),
                DB::raw("COALESCE(vendor_profiles.company_warning, 'GREEN') as vendor_sanction_status"),
                DB::raw("CASE vendor_profiles.company_warning WHEN 'RED' THEN 'inactive' ELSE 'active' END as vendor_status")
        )
        ->join('ref_purchase_orgs', "ref_purchase_orgs.id", '=', "vendors.purchase_org_id")
        ->leftJoin('ref_purchase_orgs as ref_purchase_orgs_1', "ref_purchase_orgs_1.id", '=', "vendors.purchase_org_id_1")
        ->join('vendor_profiles', function($join){
            $join->on("vendor_profiles.vendor_id", "=", "vendors.id")
                    ->whereNull("vendor_profiles.deleted_at");
        })
        ->join('vendor_profile_generals', function($join){
            $join->on("vendor_profile_generals.vendor_profile_id", "=", "vendor_profiles.id")
                    ->where("vendor_profile_generals.is_current_data", true)
                    ->where("vendor_profile_generals.primary_data", true)
                    ->whereNull("vendor_profile_generals.deleted_at");
        })
        ->join('ref_company_types', "ref_company_types.id", '=', "vendor_profile_generals.company_type_id")
        ->join('ref_countries', "vendor_profile_generals.country", '=', "ref_countries.country_code")
        ->leftJoin('ref_provinces', function ($join) {
            $join->on('ref_provinces.country_code', 'vendor_profile_generals.country');
            $join->on('ref_provinces.region_code', 'vendor_profile_generals.province');
        })
        ->leftJoin('ref_cities', function ($join) {
            $join->on('ref_cities.city_code', '=', 'vendor_profile_generals.city');
            $join->on('ref_cities.country_code', 'vendor_profile_generals.country');
            $join->on('ref_cities.region_code', 'vendor_profile_generals.province');
        })
        ->leftJoin('ref_sub_districts', function ($join) {
            $join->on('ref_sub_districts.district_code', '=', 'vendor_profile_generals.sub_district');
            $join->on('ref_sub_districts.country_code', 'vendor_profile_generals.country');
            $join->on('ref_sub_districts.region_code', 'vendor_profile_generals.province');
            $join->on('ref_sub_districts.city_code', 'vendor_profile_generals.city');
        })
        ->join('vendor_profile_detail_statuses', function($join){
            $join->on("vendor_profile_detail_statuses.vendor_profile_id", "=", "vendor_profiles.id")
                    ->whereNull("vendor_profile_detail_statuses.deleted_at");
        });
        return $query;
    }
    public function getQueryVendorById($vendorId){
        return $this->getQueryVendor()->where("vendors.id",$vendorId);
    }

    public function getVendorByProfileId($profileId){
        $profile = VendorProfile::find($profileId);
        return $this->getVendorById($profile->vendor_id);
    }

    public function getProfileByVendorId($vendorId){
        return VendorProfile::select(
                    'vendor_profiles.*', 
                    'vendors.vendor_group', 
                    'vendors.registration_status', 
                    DB::raw('ref_purchase_orgs.org_code as purc_org_code'), 
                    DB::raw('ref_purchase_orgs.description as purc_org_description')
                )
                ->join('vendors', function($join){
                    $join->on('vendors.id', '=', 'vendor_profiles.vendor_id')
                            ->whereNull('vendors.deleted_at');
                })
                ->join('ref_purchase_orgs', function ($join) {
                    $join->on('ref_purchase_orgs.id', '=', 'vendors.purchase_org_id')
                    ->whereNull('ref_purchase_orgs.deleted_at');
                })
                ->where('vendor_profiles.vendor_id',$vendorId)
                ->first();
    }

    public function getProfileDetailsByVendorId($vendorId, $type='general', $activeOnly=false){
        $vp = $this->getProfileByVendorId($vendorId);
        return $this->getProfileDetailsById($vp->id, $type, $activeOnly);
    }

    public function getProfileDetailsById($profileId, $submenu='general', $activeOnly=false){
        switch($submenu){
            case 'general': return $this->getGeneralDetailsByProfileId($profileId, $activeOnly); break;
            case 'bod-boc': return $this->getBodBocDetailsByProfileId($profileId, $activeOnly); break;
            case 'pic': return $this->getPicDetailsByProfileId($profileId, $activeOnly); break;
            case 'bank-account': return $this->getBankDetailsByProfileId($profileId, $activeOnly); break;
            case 'tax-document': return $this->getTaxDetailsByProfileId($profileId, $activeOnly); break;
            case 'competency': return $this->getCompetencyDetailsByProfileId($profileId, $activeOnly); break;
            case 'work-experience': return $this->getExperienceDetailsByProfileId($profileId, $activeOnly); break;
            default : return $this->getCommonDetailsByProfileId($profileId, $submenu, $activeOnly); //to be deleted??
            break;
        }
    }

    public function getCommonDetailsByProfileId($profileId, $submenu, $activeOnly=false, $primaryOnly=false){
        $table = $this->tables[$submenu];
        $query = DB::table($table)->select("$table.*")
            ->join('vendor_profiles', 'vendor_profiles.id', '=', "$table.vendor_profile_id")
            ->whereNull('vendor_profiles.deleted_at')
            ->where("$table.vendor_profile_id", $profileId)
            ->whereNull("$table.deleted_at")
            ->orderBy("$table.id", 'DESC');
            
        if($activeOnly){
            $query->where("$table.is_current_data",true);
        }
        if($primaryOnly){
            $query->where("$table.primary_data",true);
        }
        return $query;
    }

    public function getGeneralDetailsByProfileId($profileId, $activeOnly=false, $primaryOnly=false){
        $table = 'vendor_profile_generals';
        $query = VendorProfileGeneral::select(
            "$table.*", 
            'ref_countries.country_code',
            DB::raw('ref_countries.country_description as country'),
            'ref_provinces.region_code',
            DB::raw('ref_provinces.region_description as province'),
            'ref_cities.city_code',
            DB::raw('ref_cities.city_description as city'),
            'ref_sub_districts.district_code',
            DB::raw('ref_sub_districts.district_description as sub_district')
        )
        ->join('vendor_profiles', function ($join) {
            $join->on('vendor_profiles.id', '=', "vendor_profile_generals.vendor_profile_id")
            ->whereNull('vendor_profiles.deleted_at');
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
        ->where("$table.vendor_profile_id", $profileId)
        ->orderBy("$table.id", 'DESC');

        if($activeOnly){
            $query->where("$table.is_current_data",true);
        }
        if($primaryOnly){
            $query->where("$table.primary_data",true);
        }
        return $query;
    }

    public function getBodBocDetailsByProfileId($profileId, $activeOnly=false, $primaryOnly=false){
        $table = 'vendor_profile_bodbocs';
        $query = DB::table($table)->select(
                'vendor_profile_bodbocs.id',
                'vendor_profile_bodbocs.vendor_profile_id',
                'vendor_profile_bodbocs.board_type',
                DB::raw("CASE WHEN vendor_profile_bodbocs.is_person_company_shareholder::text = 'true' THEN 'Yes' ELSE 'No' END as is_person_company_shareholder"),
                'vendor_profile_bodbocs.full_name',
                'vendor_profile_bodbocs.nationality',
                'vendor_profile_bodbocs.position',
                'vendor_profile_bodbocs.email',
                'vendor_profile_bodbocs.phone_number',
                DB::raw("CASE WHEN vendor_profile_bodbocs.company_head::text = 'true' THEN 'Yes' ELSE 'No' END as company_head"),
                'vendor_profile_bodbocs.parent_id',
                'vendor_profile_bodbocs.is_current_data'
            )
            ->join('vendor_profiles', 'vendor_profiles.id', '=', "$table.vendor_profile_id")
            ->whereNull('vendor_profiles.deleted_at')
            ->where("$table.vendor_profile_id", $profileId)
            ->whereNull("$table.deleted_at")
            ->orderBy("$table.id", 'DESC');
            
        if($activeOnly){
            $query->where("$table.is_current_data",true);
        }
        if($primaryOnly){
            $query->where("$table.primary_data",true);
        }
        return $query;
    }
    
    public function getPicDetailsByProfileId($profileId, $activeOnly=false, $primaryOnly=false){
        $table = 'vendor_profile_pics';
        $query = DB::table($table)->select(
                'vendor_profile_pics.id',
                'vendor_profile_pics.vendor_profile_id',
                'vendor_profile_pics.username',
                'vendor_profile_pics.full_name',
                'vendor_profile_pics.email',
                'vendor_profile_pics.phone',
                DB::raw("CASE WHEN vendor_profile_pics.primary_data::text = 'true' THEN 'Yes' ELSE 'No' END as primary_data"),
                'vendor_profile_pics.parent_id',
                'vendor_profile_pics.is_current_data'
            )
            ->join('vendor_profiles', 'vendor_profiles.id', '=', "$table.vendor_profile_id")
            ->whereNull('vendor_profiles.deleted_at')
            ->where("$table.vendor_profile_id", $profileId)
            ->whereNull("$table.deleted_at")
            ->orderBy("$table.id", 'DESC');
            
        if($activeOnly){
            $query->where("$table.is_current_data",true);
        }
        if($primaryOnly){
            $query->where("$table.primary_data",true);
        }
        return $query;
    }
    
    public function getBankDetailsByProfileId($profileId, $activeOnly=false){
        $table = 'vendor_profile_bank_accounts';
        $query = VendorProfileBankAccount::select(
            "$table.*",
            'ref_banks.country_code',
            'ref_banks.bank_key',
            DB::raw('ref_banks.description as bank_name_description')
        )
        ->leftJoin('ref_banks', function ($join) use ($table) {
            $join->on('ref_banks.id', '=',  DB::raw("CAST($table.bank_name as BIGINT)"))
            ->where('ref_banks.deleteflg','');
        })
        ->where("$table.vendor_profile_id", $profileId)
        ->orderBy("$table.id", 'DESC');
        if($activeOnly){
            $query->where('is_current_data',true);
        }
        return $query;
    }

    public function getCompetencyDetailsByProfileId($profileId, $activeOnly=false){
        $table = 'vendor_profile_competencies';
        $query = VendorProfileCompetency::select(
            "$table.*",
            DB::raw('concat(ref_scope_of_supplies.id, \' - \', ref_scope_of_supplies.description) as classification_description')
        )
        ->leftJoin('ref_scope_of_supplies', 'ref_scope_of_supplies.id', $table.'.classification') 
        ->where("$table.vendor_profile_id", $profileId)
        ->orderBy("$table.id", 'DESC');
        if($activeOnly){
            $query->where('is_current_data',true);
        }
        return $query;
    }
    public function getExperienceDetailsByProfileId($profileId, $activeOnly=false){
        $table = 'vendor_profile_experience';
        $query = VendorProfileExperience::select(
            "$table.*",
            DB::raw('concat(ref_scope_of_supplies.id, \' - \', ref_scope_of_supplies.description) as classification_description'),
            DB::raw('ref_countries.country_description as country'),
            DB::raw('ref_provinces.region_description as province'),
            DB::raw('ref_cities.city_description as city'),
            DB::raw('ref_sub_districts.district_description as sub_district')
        )
        ->join('ref_countries', function ($join) use ($table) {
            $join->on('ref_countries.country_code', '=', $table.'.country')
            ->whereNull('ref_countries.deleted_at');
        })
        ->leftJoin('ref_scope_of_supplies', 'ref_scope_of_supplies.id', $table.'.classification') 
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
        ->where("$table.vendor_profile_id", $profileId)
        ->orderBy("$table.id", 'DESC');
        if($activeOnly){
            $query->where('is_current_data',true);
        }
        return $query;

    }

    public function getTaxDetailsByProfileId($profileId, $activeOnly=false){
        $table = 'vendor_profile_taxes';
        $query = VendorProfileTax::select(
            "$table.*", 
            "ref_list_options.value as tax_document_type_description"
        )
        ->join('ref_list_options', function ($join) use ($table) {
            $join->on('ref_list_options.key', '=', $table.'.tax_document_type')
                ->where('ref_list_options.type', '=', 'vendor_tax_codes')
                ->where('ref_list_options.deleteflg',false);
        })
        ->where("$table.vendor_profile_id", $profileId)
        ->orderBy("$table.id", 'DESC');
        if($activeOnly){
            $query->where('is_current_data',true);
        }
        return $query;
    }

    public function getBusinessPermitsStatus($vendorId){
        
        $VP = VendorProfile::select("id")->where("vendor_id",$vendorId)->first();
        if($VP) {
            $dt = DB::select(DB::raw("select distinct A.document_type, A.vendor_profile_id, A.created_at, A.type, A.valid_from_date, current_date, A.valid_thru_date, A.status from vendor_document_expiration A inner join 
            (select max(created_at) created_at, vendor_profile_id, document_type from vendor_document_expiration group by vendor_profile_id, document_type) B
            on   A.created_at = B.created_at AND A.vendor_profile_id=B.vendor_profile_id AND A.document_type=B.document_type where A.vendor_profile_id=".$VP->id." order by A.type"));
            return $dt;
        }else{
            return null;
        }
    }

    public function getCommentHistoryByVendorId($vendorId){
        try {
            return 
                VendorWorkflow::select(
                    'vendor_workflows.vendor_id',
                    'vendor_workflows.activity',
                    'vendor_workflows.remarks',
                    'vendor_workflows.started_at',
                    'vendor_workflows.finished_at',
                    'vendor_workflows.created_by',
                    DB::raw("CASE WHEN vendor_workflows.created_by IS NOT NULL THEN COALESCE(users.name, vendors.vendor_name) END as name")
                )
                ->join('vendors', function ($join) {
                    $join->on('vendor_workflows.vendor_id', '=', 'vendors.id')
                    ->whereNull('vendors.deleted_at');
                })
                ->leftJoin('users', function ($join) {
                    $join->on('users.userid', '=', 'vendor_workflows.created_by');
                })
                ->where('vendors.id', $vendorId)->orderby('vendor_workflows.id', 'DESC')->get();
        } catch (QueryException $e) {
            dd($e->getMessage());
        }
    }
    
    public function getProfileSubmission($vendorId){
        try {
            return VendorProfileDetailStatus::select($this->tables['vendorprofiledetailstatus'].'.is_submitted')
                    ->join($this->tables['vendorprofile'], function($join){
                        $join->on($this->tables['vendorprofile'].'.id', '=', $this->tables['vendorprofiledetailstatus'].'.vendor_profile_id')
                        ->whereNull($this->tables['vendorprofile'].'.deleted_at');
                    })
                    ->where($this->tables['vendorprofile'].'.vendor_id', $vendorId)
                    ->where($this->tables['vendorprofiledetailstatus'].'.is_submitted', true)
                    ->count();
            
        } catch (QueryException $ex) {
            
        }
    }
    
    public function getProfileApproval($vendorId, $asPosition){
        try {
            return VendorApproval::select('vendor_approvals.*')->join('vendors', function($join){
                        $join->on('vendors.id', '=', 'vendor_approvals.vendor_id')
                                ->whereNull('vendors.deleted_at');
                    })
                    ->where('vendor_approvals.vendor_id', $vendorId)
                    ->where('vendor_approvals.as_position', $asPosition)
                    ->where('vendor_approvals.is_done', false)
                    ->whereNull('vendor_approvals.deleted_at')
                    ->orderby('vendor_approvals.sequence_level', 'ASC')
                    ->first();
                    
        } catch (QueryException $ex) {
            
        }
    }

    public function generateAvlNo($id){
        $vend = VendorProfile::select("avl_no")->where("vendor_id", $id)->first();
        if ($vend->avl_no==null){
            $max = VendorProfile::select(DB::raw("max(avl_no) max_avl_no, current_date"))->first();
            $avl_no = "00001";
            $date = $max->current_date;
            if (isset($max->max_avl_no)){
                $db_avl_no = $max->max_avl_no;
                $inv_avl_no = intval($db_avl_no)+1;
                $inv_avl_no = "0000000000000".$inv_avl_no;
                $avl_no = substr($inv_avl_no, -5);
            }        
            $data = ['avl_no' => $avl_no, 'avl_date' => $date];
            VendorProfile::where('vendor_id', $id)->update($data);
            return $data;
        }
    }
    
    public function sap_create_change_vendor($id){
        $vendor = $this->getVendorById($id);
        $profile = $this->getProfileByVendorId($id);
        $general = $this->getGeneralDetailsByProfileId($profile->id, true, true)->first();
        $banks = $this->getBankDetailsByProfileId($profile->id, true)->get();
        $pic = $this->getCommonDetailsByProfileId($profile->id, 'pic', true, true)->first(); //!!HARDCODE!!//
        $tax = $this->getCommonDetailsByProfileId($profile->id, 'tax-document', true)
            ->whereNull('vendor_profile_taxes.deleted_at')
            // ->whereNull('vendor_profile_taxes.tax_document_type','ID1') //!!HARDCODE!!//
            ->get();

        if(isset($vendor->business_partner_code)){
            if(isset($vendor->sap_vendor_code)){
                $processType = 3;
            }else{
                $processType = 2;
            }
        }else{
            $processType = 1;
        }

        //fill data//        
        if($vendor->vendor_group === 'local'){
            $inputData = [
                'I_DATA'=>[
                    'PROC_TYPE'=>$processType,
                    'PARTNER_NO'=>$vendor->business_partner_code ?? '',
                    'VENDOR_NO'=>$vendor->sap_vendor_code ?? '',
                    'PARTN_CAT'=>config('eproc.sap.default_variables.PARTN_CAT'), 
                    'PARTN_GRP'=>$vendor->vendor_group=="foreign"? "Z002" : "Z001" , //??
                    'SEARCHTERM1'=>$general->company_name,
                    'SEARCHTERM2'=>'',
                    'NAME1'=>$general->company_name,
                    'NAME2'=>'',
                    'NAME3'=>'',
                    'NAME4'=>'',
                    'EMAIL'=>$general->company_email,
                    'FAX'=>$general->fax_number,
                    'POST_CODE1'=>$general->postal_code,
                    'PO_BOX'=>'',
                    'TEL_NUMBER'=>$general->phone_number,
                    'STREET'=>substr($general->street, 0, 47),
                    'HOUSE_NO'=>$general->house_number, //bakal masuk ke generals.
                    'STR_SUPPL1'=>$general->building_name,
                    'STR_SUPPL2'=>$general->kavling_floor_number,
                    'STR_SUPPL3'=>$general->rt,
                    'STR_SUPPL4'=>$general->rw,
                    'LOCATION'=>$general->village,
                    'DISTRICT'=>$general->sub_district,
                    'BUILDING'=>'',
                    'FLOOR'=>'',
                    'COUNTRY'=>$general->country_code,
                    'REGION'=>$general->region_code,
                    'CITY'=>$general->city,
                    'PARTNERLANGUAGE'=>'',
                    'BUKRS'=>config('eproc.company_code'), 
                    'AKONT'=>'',
                    'FDGRV'=>'',
                    'ZTERM'=>'',
                    'REPRF'=>'',
                    'ZWELS'=>'',
                    'HBKID'=>'',
                ],
            ];
        } else {
            $inputData = [
                'I_DATA'=>[
                    'PROC_TYPE'=>$processType,
                    'PARTNER_NO'=>$vendor->business_partner_code ?? '',
                    'VENDOR_NO'=>$vendor->sap_vendor_code ?? '',
                    'PARTN_CAT'=>config('eproc.sap.default_variables.PARTN_CAT'), 
                    'PARTN_GRP'=>config('eproc.sap.default_variables.PARTN_GRP.foreign'), //??
                    'SEARCHTERM1'=>$general->company_name,
                    'SEARCHTERM2'=>'',
                    'NAME1'=>$general->company_name,
                    'NAME2'=>'',
                    'NAME3'=>'',
                    'NAME4'=>'',
                    'EMAIL'=>$general->company_email,
                    'FAX'=> $general->fax_number ?? '',
                    'POST_CODE1'=>substr($general->postal_code, 0, 10),
                    'PO_BOX'=>'',
                    'TEL_NUMBER'=>$general->phone_number,
                    'STREET'=>substr($general->address_1, 0, 47),
                    'STR_SUPPL1'=>$general->address_2,
                    'STR_SUPPL2'=>$general->address_3,
                    'BUILDING'=>'',
                    'FLOOR'=>'',
                    'COUNTRY'=>$general->country_code,
                    'PARTNERLANGUAGE'=>'',
                    'BUKRS'=>config('eproc.company_code'), 
                    'AKONT'=>'',
                    'FDGRV'=>'',
                    'ZTERM'=>'',
                    'REPRF'=>'',
                    'ZWELS'=>'',
                    'HBKID'=>'',
                ],
            ];
        }
        
        $inputBanks = [];
        if(count($banks)>0){
            $inputBanks = [
                'T_BANK'=>[
                    'item'=>[       //bisa lebih dari satu items
                    ],
                ],
            ];
            $i = 0;
            foreach($banks as $bank){
                $i++;
                $bankData = [
                    'PARTNER_NO'=>$vendor->business_partner_code ?? '',
                    'VENDOR_NO'=>$vendor->sap_vendor_code ?? '',
                    'BKVID'=>str_pad($i,4,"0",STR_PAD_LEFT),
                    'BANKS'=>$bank->country_code,
                    'BANKL'=>$bank->bank_key,
                    'BANKN'=>$bank->account_number,
                    'KOINH'=>$bank->account_holder_name,
                ];
                $inputBanks['T_BANK']['item'][] = $bankData;
            }
        }
        $inputPurchasings = [
            'T_PURCHASING'=>[
                'item'=>[       //bisa lebih dari satu items
                    'PARTNER_NO'=>$vendor->business_partner_code ?? '',
                    'VENDOR_NO'=>$vendor->sap_vendor_code ?? '',
                    'EKORG'=>$vendor->purchase_org_code,
                    'WAERS'=> count($banks)>0 ? $banks[0]->currency : config('eproc.default_currency'),
                    'ZTERM'=>'',
                    'VERKF'=>$pic->full_name,
                    'TELF1'=>$pic->phone,
                    'EMAIL'=>$pic->email,
                    'WEBRE'=>'',
                    'LEBRE'=>'',
                ],
            ],
        ];
        $inputTaxes = [];
        if(count($tax)>0){
            $items = [];
            foreach($tax as $t){
                if (in_array($t->tax_document_type, array('ID1','ID2','ID3','ID4','ZZ1') ) ){
                    $items[] = [
                        'PARTNER_NO'=>$vendor->business_partner_code ?? '',
                        'VENDOR_NO'=>$vendor->sap_vendor_code ?? '',
                        'TAX_TYPE'=> $t->tax_document_type,
                        'TAX_NUMBER'=> $t->tax_document_number
                    ];
                }
            }

            $inputTaxes = [
                'T_TAX'=>[
                    'item'=> $items,
                ],
            ];
        }
        // $withts = [  
        //         'T_WITHT'=>[
        //         'item'=>[       //bisa lebih dari satu items
        //             'PARTNER_NO'=>'',
        //             'VENDOR_NO'=>'',
        //             'WT_WITHT'=>'',
        //             'WT_SUBJCT'=>'',
        //         ],
        //     ],
        // ];

        $data = array_merge($inputData, $inputBanks, $inputPurchasings, $inputTaxes);

        try {
            $sap = new SapConnector();
            // Log::debug($data);

            $result = $sap->call('create_update_bp',$data);
            Log::debug("============== REQUEST TO SAP (Create Update BP) ===============");
            Log::debug($sap->requestMessage);
            Log::debug("============== SAP RESPONSE (Create Update BP) ===============");
            Log::debug($sap->responseMessage);
            if($result!==false){
                if(isset($result['RETURN'])){
                    // $type = $result['RETURN']['ITEM']['TYPE'] ?? $result['RETURN']['ITEM'][0]['TYPE'];
                    // switch($type){
                    //     case 'S' : $status = true; break; //success
                    //     case 'E' : $status = true; break; //error
                    //     case 'W' : $status = true; break; //warning
                    //     case 'I' : $status = true; break; //info
                    //     case 'A' : $status = false; break; //abort
                    // }
                    // $message = $result['RETURN']['ITEM']['MESSAGE'] ?? $result['RETURN']['ITEM'][0]['MESSAGE'];
                    // if(!isset($result['RETURN']['ITEM']['MESSAGE'])){
                    //     foreach($result['RETURN']['ITEM'] as $item){
                    //         $message.="\n".$item['MESSAGE'];
                    //     }
                    // }
                    // return ['status'=>$status,'data'=>$result,'message'=>$message];

                    $status = true;
                    if(!isset($result['RETURN']['ITEM']['TYPE'])){
                        foreach($result['RETURN']['ITEM'] as $item){
                            switch($item['TYPE']){
                                case 'S' : $status = $status && true; break; //success
                                case 'E' : $status = $status && false; break; //error
                                case 'W' : $status = $status && true; break; //warning
                                case 'I' : $status = $status && true; break; //info
                                case 'A' : $status = $status && false; break; //abort
                            }
                        }
                    }else{
                        switch($result['RETURN']['ITEM']['TYPE']){
                            case 'S' : $status = $status && true; break; //success
                            case 'E' : $status = $status && false; break; //error
                            case 'W' : $status = $status && true; break; //warning
                            case 'I' : $status = $status && true; break; //info
                            case 'A' : $status = $status && false; break; //abort
                        }
                    }
                    $message = "";
                    if(!isset($result['RETURN']['ITEM']['MESSAGE'])){
                        foreach($result['RETURN']['ITEM'] as $item){
                            $message.=$item['MESSAGE']."\n";
                        }
                    }else{
                        $message = $result['RETURN']['ITEM']['MESSAGE'];
                    }
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
            Log::error($e->getMessage());
            return ['status'=>false,'message'=>"Network Connection Error. Please contact administrator."];
        }

    }

    public function getNextAvlNo(){
        $config = config('eproc.avl_number');
        $format = $config['format'];
        $pad = $config['pad'];
        $max = $config['max'];

        $param = RefSysParam::where('name','last_avl_number')->first();

        $number = $param->value==$max ? 1 : intval($param->value1) + 1;
        $param->value1 = $number;
        $param->save();

        $value = str_replace('[NUMBER]',str_pad($number,$pad,'0',STR_PAD_LEFT),$format);
        $value = str_replace('[MM]',date('m'),$value);
        $value = str_replace('[YYYY]',date('Y'),$value);

        return $value;

    }

    public function getMessage($validator){
        return $validator->errors()->first();
    }

    public function isValidEmail($emails) {
        $is_valid = true;
        if (is_array($emails)){
            foreach($emails as $email){
                if (!$this->isCekValidEmail($email)){
                    $is_valid = false;
                    break;
                }
            }
        }else{
            $is_valid = $this->isCekValidEmail($emails);
        }
        return $is_valid;
    }

    public function isCekValidEmail($emails){
        return filter_var($emails, FILTER_VALIDATE_EMAIL) 
            && preg_match('/@.+\./', $emails);
    }

    public function getLastEvaluationScore($vendorId){
        try {
            $result = VendorEvaluationForm::select(
                    'vendor_evaluation_id',
                    'total_score'
                )->join('vendor_evaluation_generals', function ($join) {
                    $join->on('vendor_evaluation_forms.vendor_evaluation_id', '=', 'vendor_evaluation_generals.id')
                    ->where('vendor_evaluation_generals.status','APPROVED');
                })
                ->where('vendor_evaluation_forms.vendor_id', $vendorId)
                ->orderby('vendor_evaluation_generals.updated_at', 'DESC')
                ->first();
        } catch (QueryException $ex) {
            Log::debug($ex->getMessage());
            $result = null;
        }
        return $result;
    }

    public function isDuplicateVendor($vendorType, $identification_type, $identity_number, $city, $vendorId=null){
        Log::debug("Check Duplicate For: VendorType[$vendorType], IdentificationType[$identification_type], IdentityNumber[$identity_number], City[$city], VendorId[$vendorId]");
        $vendorQueryData = DB::table('view_vendor_duplicate_check')
        // check case when registration_status is applicant, then check compare validation value from vendors table 
        // and case when status is candidate or vendor then check compare value validation from table profiles tax or general
        ->whereRaw("CASE WHEN tax_id is null THEN "
                . "     CASE WHEN ? = 'tin' THEN "
                . "         tin_number = ? "
                . "     ELSE"
                . "         idcard_number = ? "
                . "     END "
                . "ELSE "
                . "     tax_document_number = ? "
                . "END", [$identification_type, $identity_number, $identity_number, $identity_number]);

        if($vendorId){
            $vendorData = $vendorQueryData->where('id', '<>', $vendorId);
        }

        $vendorData = [];
        DB::enableQueryLog();
        if ($vendorType == "local"){
            // $vendorData = $vendorQueryData->whereRaw(("case when vendor_profile_generals.id is null then vendors.city = '" . $request->city . "' else vendor_profile_generals.city = '" . $request->city . "' end"))->first();
            $vendorData = $vendorQueryData
            ->whereRaw("CASE WHEN general_id is null THEN "
                    . "     city = ? "
                    . "ELSE "
                    . "     city_general = ? "
                    . "END", [$city, $city])
            ->get();
        } else {
            $vendorData = $vendorQueryData->get();
        }
        $query = DB::getQueryLog();
        // Log::debug(end($query));
        
        Log::debug("Check Duplicate Result: ".(count($vendorData) > 0 ? "Data is Duplicated" : "Data Not Duplicated"));
        return count($vendorData) > 0;

    }
    public function isDuplicateVendorUnused($vendorType, $identification_type, $identity_number, $city, $vendorId=null){
        Log::debug("Check Duplicate For: VendorType[$vendorType], IdentificationType[$identification_type], IdentityNumber[$identity_number], VendorId[$vendorId]");
        $vendorQueryData = Vendor::select(
            'vendors.id',
            'vendors.tin_number',
            'vendors.idcard_number',
            'vendors.city',
            'vendors.vendor_group',
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
                . "END", [$identification_type, $identity_number, $identity_number, $identity_number]);

        if($vendorId){
            $vendorData = $vendorQueryData->where('vendors.id', '<>', $vendorId);
        }

        $vendorData = [];
        DB::enableQueryLog();
        if ($vendorType == "local"){
            // $vendorData = $vendorQueryData->whereRaw(("case when vendor_profile_generals.id is null then vendors.city = '" . $request->city . "' else vendor_profile_generals.city = '" . $request->city . "' end"))->first();
            $vendorData = $vendorQueryData
            ->whereRaw("CASE WHEN vendor_profile_generals.id is null THEN "
                    . "     vendors.city = ? "
                    . "ELSE "
                    . "     vendor_profile_generals.city = ? "
                    . "END", [$city, $city])
            ->get();
        } else {
            $vendorData = $vendorQueryData->get();
        }
        $query = DB::getQueryLog();
        Log::debug(end($query));
        
        Log::debug("Check Duplicate Result: ".(count($vendorData) > 0 ? "Data is Duplicated" : "Data Not Duplicated"));
        return count($vendorData) > 0;
    }

    public function createRegistrationNumber($reqData, $vendorInfo){
        //ambil last number
        //last number +1
        //update last number
        //samakan kode 557 - 561, ganti $vendorID dengan last number.
        
        if ($vendorInfo->vendor_group=="foreign"){
            $foreign = RefCompanyGroup::where("name","=","foreign")->first();
            $last_number = $foreign->last_number;  
            $registration_number =  'L'. str_pad($last_number, 5, "0", STR_PAD_LEFT);
            $last_new_number = intval($last_number)+1;
        }elseif ($vendorInfo->vendor_group=="local"){
            $local = RefCompanyGroup::where("name","=","local")->first();
            $last_number = $local->last_number;  
            $registration_number =  'D'. str_pad($last_number, 5, "0", STR_PAD_LEFT);
            $last_new_number = intval($last_number)+1;
        }       
       
        return array("registration_number" => $registration_number, "nextNumber" => $last_new_number);


    }

}