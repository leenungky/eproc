<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \PDF;
use DB;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Support\Facades\Auth;
use App\VendorHistoryStatus;
use App\VendorProfile;
use App\VendorProfileBodboc;
use App\VendorProfileGeneral;
use App\VendorProfileCompetency;
use App\VendorSanction;
use App\VendorEvaluationGeneral;
use App\RefCity;
use App\RefSubDistrict;
use App\RefProvince;
use App\RefCountry;
use App\Vendor;
use App\RefPurchaseOrg;
use App\RefCompanyType;
use App\Repositories\VendorRepository;

//use View;
class ReportController extends Controller  {
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct() {
        $this->middleware('auth');
        $this->vendorRepo = new VendorRepository();
    }

    public function pdf_profile($id = null)
    {
        $status = "approved";//"submit";
        $vendorId = $id??Auth::user()->ref_id;
        $params = $vendorId;
        $vendor_history_status = VendorHistoryStatus::where('vendor_id', $params)
            ->orderBy('created_at', 'desc')
            ->first();
        $vendor = Vendor::select("vendor_code","purchase_org_id","vendor_group")->where('id', $params)->first();
        $vendor_profiles = VendorProfile::where('vendor_id', $params)->first();
        $vp_id = isset($vendor_profiles)? $vendor_profiles->id : "";
        $vendor_purchase_org = RefPurchaseOrg::where('id', $vendor->purchase_org_id)->first();
        $vendor_profiles_bodbocs = VendorProfileBodboc::where('vendor_profile_id', $vp_id)
            ->where('company_head',true)
            ->where('is_current_data',true)
            ->first();
        $vendor_profiles_general = VendorProfileGeneral::where('vendor_profile_id', $vp_id)
            ->where('primary_data',true)
            ->where('is_current_data',true)
            ->first();
        $vendor_profiles_competency = VendorProfileCompetency::select("classification","detail_competency")
            ->where('vendor_profile_id', $vp_id)
            ->where('is_current_data',true)
            ->get();
        $city = "";
        $district = "";
        $province = "";
        $country = "";        
        if (isset($vendor_profiles_general)){
            if ($vendor_profiles_general->primary_data){
                $db_country = RefCountry::select("country_description")->where("country_code", $vendor_profiles_general->country)->first();
                $country =  isset($db_country) ? $db_country->country_description : "";
                $db_province = RefProvince::select("region_description")
                    ->where("country_code", $vendor_profiles_general->country)
                    ->where("region_code", $vendor_profiles_general->province)
                    ->first();
                $province =  isset($db_province) ? $db_province->region_description : "";
                $dbcity = RefCity::select("city_description")->where("city_code", $vendor_profiles_general->city)->first();
                $city =  isset($dbcity) ? $dbcity->city_description : "";
                $db_sub_district = RefSubDistrict::select("district_description")->where("district_code", $vendor_profiles_general->sub_district)->first();
                $sub_district =  isset($db_sub_district) ? $db_sub_district->district_description : "";
                $companyType = RefCompanyType::where('id',$vendor_profiles_general->company_type_id)->first();
                    
            }
        } 
        //dd($country, $province, $sub_district, $city, $vendor_history_status, $vendor_profiles, $vendor_profiles_bodbocs, $vendor_profiles_general, $vendor_profiles_competency)->all();
        $data = [
            "V" => $vendor,
            "VPO" => $vendor_purchase_org,
            'VH' => $vendor_history_status,
            'VP' => $vendor_profiles,
            "VPB" => $vendor_profiles_bodbocs,
            "VPG" => $vendor_profiles_general,
            "VPC" => $vendor_profiles_competency,
            "city" => $city,
            "sub_district" => $sub_district,
            "province" => $province,
            "country" => $country,
            "company_type" => !is_null($companyType) ? $companyType->company_type : $vendor_profiles->company_type
        ];
          
        $pdf = PDF::loadView('report/pdf_avl_report', $data);
        // return $pdf->download('pdf_avl_report.pdf');
        return $pdf->stream($vendor->vendor_code.'avl_report.pdf');
        // return View("report/pdf_avl_report", $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function excel()
    { 
        return (new UserReport)->download('medium.xlsx');    
    }

    public function applicants_excel(){
        $title="List of Applicants";
        $user=auth()->user()->name;
        $fields=explode(",","status,partner_name,vendor_group,company_type,purchasing_organization,province,created_date,email,id_card_number,npwp_tin_number,pkp_number,non_pkp_number");

        $vendor = $this->vendorRepo->getListVendorByType('applicant')
        ->select(
            DB::raw("case vendor_history_statuses.status when 'rejected' then 'rejected' else vendor_workflows.activity end as status"),
            DB::raw("vendors.vendor_name as partner_name"),
            'vendors.vendor_group',
            'ref_company_types.company_type',
            DB::raw("concat(ref_purchase_orgs.org_code,' - ',ref_purchase_orgs.description) as purchasing_organization"),
            DB::raw('ref_provinces.region_description as province'),
            DB::raw('TO_CHAR(vendors.created_at, \'DD.MM.YYYY HH24:MI\') as created_date'),
            DB::raw('vendors.company_email as email'),
            DB::raw('vendors.idcard_number as id_card_number'),
            DB::raw('vendors.tin_number as npwp_tin_number'),
            DB::raw('vendors.pkp_number'),
            DB::raw('vendors.non_pkp_number')
        )
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

        $report = new ExcelReport([
            'title'=>$title,
            'user'=>$user,
            'fields'=>$fields,
            'rows'=>$vendor,
        ]);
        return $report->download('ApplicantsReport_'.date('Ymd_His').'.xlsx');    
    }
    public function candidates_excel(){
        $title="List of Candidates";
        $user=auth()->user()->name;
        $fields=explode(",","status,provider_name,company_type,province,register_number,created_date,updated_date");

        $query = $this->vendorRepo->getListVendorByType('candidate')
        ->select(
            DB::raw("vendor_workflows.activity as status"),
            DB::raw("vendors.vendor_name as provider_name"),
            'ref_company_types.company_type',
            DB::raw('ref_provinces.region_description as province'),
            DB::raw("vendors.vendor_code as register_number"),
            DB::raw('TO_CHAR(vendors.created_at, \'DD.MM.YYYY HH24:MI\') as created_date'),
            DB::raw('TO_CHAR(vendors.updated_at, \'DD.MM.YYYY HH24:MI\') as updated_date')
        )
        ->join('vendor_workflows', function ($join) {
            $join->on('vendor_workflows.vendor_id', '=', 'vendors.id')
            ->whereNotNull('vendor_workflows.started_at')
            ->whereNull('vendor_workflows.finished_at')
            ->whereNull('vendor_workflows.deleted_at');
        });
        $query->orderBy('vendor_profiles.id','desc');
        $vendor = $query->get();
    
        $report = new ExcelReport([
            'title'=>$title,
            'user'=>$user,
            'fields'=>$fields,
            'rows'=>$vendor,
        ]);
        return $report->download('CandidatesReport_'.date('Ymd_His').'.xlsx');    
    }
    public function vendors_excel(){
        $title="List of Vendors";
        $user=auth()->user()->name;
        $fields=explode(",","provider_name,company_type,province,register_number,created_date,updated_date");

        $query = $this->vendorRepo->getListVendorByType('vendor')
        ->select(
            DB::raw("vendors.vendor_name as provider_name"),
            'ref_company_types.company_type',
            DB::raw('ref_provinces.region_description as province'),
            DB::raw("vendors.vendor_code as register_number"),
            DB::raw('TO_CHAR(vendors.created_at, \'DD.MM.YYYY HH24:MI\') as created_date'),
            DB::raw('TO_CHAR(vendors.updated_at, \'DD.MM.YYYY HH24:MI\') as updated_date')
        )
        ->leftJoin('vendor_workflows', function ($join) {
            $join->on('vendor_workflows.vendor_id', '=', 'vendors.id')
            ->whereNotNull('vendor_workflows.started_at')
            ->whereNull('vendor_workflows.finished_at')
            ->whereNull('vendor_workflows.deleted_at');
        });
        $query->orderBy('vendor_profiles.id','desc');
        $vendor = $query->get();
    
        $report = new ExcelReport([
            'title'=>$title,
            'user'=>$user,
            'fields'=>$fields,
            'rows'=>$vendor,
        ]);
        return $report->download('VendorsReport_'.date('Ymd_His').'.xlsx');    
    }

    public function sanctions_excel(){
        $title="List of Vendor Sanctions";
        $user=auth()->user()->name;
        $fields=['vendor_name','vendor_code','sanction_type','valid_from_date','valid_thru_date','status','letter_number','description'];

        $data = VendorSanction::distinct('vendor_profile_id')
        ->select(
            '*',
            DB::raw("concat(ref_list_options.key,' (',ref_list_options.value,')') as sanction_type"),
            DB::raw('TO_CHAR(valid_from_date, \'DD.MM.YYYY\') as valid_from_date'),
            DB::raw('TO_CHAR(valid_thru_date, \'DD.MM.YYYY\') as valid_thru_date'),
            'status',
            'letter_number',
            'description'
        )
        ->leftJoin('ref_list_options', function ($join) {
            $join->on('vendor_sanctions.sanction_type', '=', 'ref_list_options.key')
            ->where('ref_list_options.type','sanction_types');
        })
        ->orderBy('vendor_profile_id')
        ->orderBy('vendor_sanctions.id','desc')
        ->get();

        $report = new ExcelReport([
            'title'=>$title,
            'user'=>$user,
            'fields'=>$fields,
            'rows'=>$data,
        ]);
        return $report->download('SanctionsReport_'.date('Ymd_His').'.xlsx');    
    }
    public function evaluationlist_excel(){
        $title="Evaluation List Report";
        $user=auth()->user()->name;
        $fields=['name','description','category_name','status','start_date','end_date'];
        $data = VendorEvaluationGeneral::orderBy('id','desc')->get();
        
        $report = new ExcelReport([
            'title'=>$title,
            'user'=>$user,
            'fields'=>$fields,
            'rows'=>$data,
        ]);
        return $report->download('EvaluationListReport_'.date('Ymd_His').'.xlsx');    
    }
    public function evaluationform_excel($id){
        $general = VendorEvaluationGeneral::find($id);
        $title="Evaluation Form Report (".$general->name.")";
        $user=auth()->user()->name;
        $fields = ['vendor_code','company_name','total_score','score_categories_name','city','province','country','purchasing_organization',
            'sanction_type','company_status','year','project_code','start_date','end_date','evaluated_by'];
        $closure = Vendor::getCurrent();
        $query = $closure->select(
            DB::raw('ef.id as id'),
            DB::raw('eg.id as evaluation_id'),
            DB::raw('vendors.id as vendor_id'),
            'vendors.vendor_code',
            'g.company_name',
            DB::raw('coalesce(ref_cities.city_description, g.city) as city'),
            DB::raw('coalesce(ref_provinces.region_description, g.province) as province'),
            DB::raw('coalesce(ref_countries.country_description, g.country) as country'),
            DB::raw('concat(po.org_code,\' - \',po.description) as purchasing_organization'),
            DB::raw("concat(ref_list_options.key,' (',ref_list_options.value,')') as sanction_type"),
            DB::raw('case p.company_warning when \'RED\' then \'Inactive\' else \'Active\' end as company_status'),
            'eg.year',
            'eg.project_code',
            'ef.total_po_document',
            'ef.total_po_value',
            DB::raw('TO_CHAR(eg.start_date, \'DD.MM.YYYY\') as start_date'),
            DB::raw('TO_CHAR(eg.end_date, \'DD.MM.YYYY\') as end_date'),
            'ef.total_score',
            'ef.evaluated_by',
            'eg.status',
            DB::raw('es.name as score_categories_name')
        )
        ->join('vendor_evaluation_forms as ef', function($join){
            $join->on('ef.vendor_id','=','vendors.id');
        })
        ->join('vendor_evaluation_generals as eg', function($join){
            $join->on('ef.vendor_evaluation_id','=','eg.id');
        })
        ->join('ref_purchase_orgs as po', function($join){
            $join->on('vendors.purchase_org_id','=','po.id');
        })
        ->join('vendor_evaluation_score_categories as esc', function($join){
            $join->on('esc.id','=','eg.category_id');
        })
        ->leftJoin('vendor_evaluation_scores as es', function($join){
            $join->on('esc.id','=','es.category_id')
            ->whereRaw("CASE WHEN es.lowest_score_operator = '>=' THEN es.lowest_score <= ef.total_score ELSE es.lowest_score < ef.total_score END")
            ->whereRaw("CASE WHEN es.highest_score_operator = '<=' THEN es.highest_score >= ef.total_score ELSE es.highest_score > ef.total_score END");
        })
        ->join('ref_countries', "g.country", '=', "ref_countries.country_code")
        ->leftJoin('ref_provinces', function ($join) {
            $join->on('ref_provinces.country_code', 'g.country');
            $join->on('ref_provinces.region_code', 'g.province');
        })
        ->leftJoin('ref_cities', function ($join) {
            $join->on('ref_cities.city_code', '=', 'g.city');
            $join->on('ref_cities.country_code', 'g.country');
            $join->on('ref_cities.region_code', 'g.province');
        })
        ->leftJoin('ref_list_options', function ($join) {
            $join->on('p.company_warning', '=', 'ref_list_options.key')
            ->where('ref_list_options.type','sanction_types');
        })
        ->where('eg.id',$id);
        $data = $query->get();

        $report = new ExcelReport([
            'title'=>$title,
            'user'=>$user,
            'fields'=>$fields,
            'rows'=>$data,
        ]);
        return $report->download('EvaluationFormReport_'.date('Ymd_His').'.xlsx');    
    }
}


class UserReport implements FromView
{
    use Exportable;
    
    public function view(): View
    {
        $data = [
            'title' => 'First PDF for Medium',
            'heading' => 'Hello from 99Points.info',
            'content' => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged."      
              ];
     
        return view('report.excel_vendor', $data);
    }
}

class ExcelReport implements FromView
{
    use Exportable;

    protected $data=[];
    //title
    //username
    //fields
    //rows

    function __construct($data){
        $this->data = $data;
    }

    public function view(): View
    {
        return view('report.excel_vendor_management', $this->data);
    }
}