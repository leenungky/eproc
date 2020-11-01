<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use View;
use DB;
use Schema;

use App\Applicant;
use App\VendorWorkflow;
use App\ApplicantGeneralAdministration;
use App\ApplicantCompanyProfile;
use App\ApplicantDeed;
use App\ApplicantShareholder;
use Illuminate\Database\Eloquent\Collection;

class ProfileController extends Controller {

    public $tables;
    public $tableKeys;

    public function __construct() {
        $this->middleware('auth');
        $this->tables = [
            'general' => 'vendor_profile_generals',
            'deeds' => 'vendor_profile_deeds',
            'shareholders' => 'vendor_profile_deeds',
            'bod-boc' => 'vendor_profile_bodbocs',
            'business-permit' => 'vendor_profile_business_permits',
            'pic' => 'vendor_profile_pics',
            'tool' => 'vendor_profile_tools',
            'expert' => 'vendor_profile_pic',
            'certification' => 'vendor_profile_pic',
            'competency' => 'vendor_profile_pic',
            'experience' => 'vendor_profile_pic',
            'financial' => 'vendor_profile_pic',
            'bank-account' => 'applicant_banks',
            'tax-document' => 'applicant_taxes',
            'financial-statements' => 'applicant_financial_statements',
        ];
        $this->tableKeys = array_keys($this->tables);

    }

    public function index() {
        //        
    }

    public function view_edit_profile(Request $request) {
        $applicantId = Auth::user()->ref_id;
        $arrColumnVal = array();
        $arrColumnVal['id'] = $applicantId;
        $data = $this->specific_find($arrColumnVal);
        $data['applicant'] = $data;
        $data['profiles'] = $this->get_profile_general($applicantId);
        return View::make('applicants/profiles/administration/general', $data);
    }

    public function view_edit_profile_sub(Request $request, $submenu = '') {
        $applicantId = Auth::user()->ref_id;
        $arrColumnVal = array();
        $arrColumnVal['id'] = $applicantId;
        $data = $this->show_changes_profiles($arrColumnVal);
        $data['applicant'] = $data;
        $data['submenu'] = $submenu;
        $data['profiles'] = [];        

        switch ($submenu) {
            case 'general':
                $menu = 'administration';
                $data['profiles'] = $this->get_profile_general($applicantId);
                break;
            case 'deeds':
                $menu = 'administration';
                $data['profiles'] = $this->get_profile_deeds($request);
                break;
            case 'shareholders':
                $menu = 'administration';
                $data['profiles'] = $this->get_profile($request, 'applicant_shareholders');
                break;
            case 'bod-boc':
                $menu = 'administration';
                $data['profiles'] = $this->get_profile_shareholders($request);
                break;
            case 'business-permit':
                $menu = 'administration';
                $data['profiles'] = $this->get_profile_shareholders($request);
                break;
            case 'pic':
                $menu = 'administration';
                $data['profiles'] = $this->get_profile_shareholders($request);
                break;
            case 'bank-account':
            case 'tax-document':
            case 'financial-statements':
                $menu = 'finance';
                $data['profiles'] = $this->get_profile($request, $this->tables[$submenu]);
                break;
            case 'tools':
            case 'expert':
            case 'competency':
            case 'certification':
            case 'work-experience':
                $menu = 'workexperience';
                $function = 'get_profile'.$submenu;
                $data['profiles'] = method_exists($this,$function) ? $this->$function($request) : new Collection();
                break;
            default:
                $menu = 'administration';
                $submenu = 'general';
                $data['profiles'] = $this->get_profile_general($applicantId);
            break;
        }
        $data['type'] = $submenu;
        $data['storage'] = asset('storage/vendor/profiles/'.$applicantId.'/'.$submenu);
        return View::make("applicants/profiles/$menu/" . $submenu, $data);
    }

    public function view_show_profile() {
        $applicantId = Auth::user()->ref_id;
        $arrColumnVal = array();
        $arrColumnVal['id'] = $applicantId;
        $data = $this->specific_find($arrColumnVal);
        $data['applicant'] = $data;
        $data['checklist'] = $this->get_profile_checklist($applicantId);
        return View::make('applicants/profiles/show', $data);
    }
    
    public function get_profile_checklist($id){
        return DB::table('v_profile_checklist')->select('*')->where('applicant_id', $id)->first();
    }
    
    public function show_changes_profiles($column){
        try {
            if (isset($column['id'])) {
                $column['applicants.id'] = $column['id'];
                // remove ambiguos
                unset($column['id']);
            }

            $registerNumber = Applicant::select(
                            'applicants.id as applicant_id',
                            DB::raw('ROW_NUMBER () OVER (ORDER BY applicants.id) as register_number')
                    )->join('applicant_statuss', function ($join) {
                $join->on('applicant_statuss.applicant_id', '=', 'applicants.id')
                        ->where('applicant_statuss.statusflg', '=', 1);
            });

            return Applicant::select(
                                    'applicants.id',
                                    'registered.register_number as row_number',
                                    DB::raw('LPAD(registered.register_number::varchar, 8, \'0\') as register_number'),
                                    'applicants.partner_name',
                                    'applicants.company_type_id',
                                    'ref_company_types.company_type',
                                    'applicants.purchase_org_id',
                                    'ref_purchase_orgs.org_code',
                                    'ref_purchase_orgs.description',
                                    'applicants.president_director',
                                    'applicants.address_1',
                                    'applicants.address_2',
                                    'applicants.address_3',
                                    'applicants.address_4',
                                    'applicants.address_5',
                                    'applicants.country',
                                    'applicants.province',
                                    'applicants.city',
                                    'applicants.sub_district',
                                    'applicants.house_number',
                                    'applicants.postal_code',
                                    'applicants.phone_number',
                                    'applicants.fax_number',
                                    'applicants.company_email',
                                    'applicants.company_site',
                                    'applicants.pic_full_name',
                                    'applicants.pic_mobile_number',
                                    'applicants.pic_email',
                                    'applicants.tender_ref_number',
                                    'applicants.pkp_number',
                                    'applicants.pkp_attachment',
                                    'applicants.npwp_tin_number',
                                    'applicants.npwp_tin_attachment',
                                    'applicants.deleteflg',
                                    DB::raw('TO_CHAR(applicants.created_at, \'DD.MM.YYYY HH24:MI\') as created_at'),
                                    'applicants.created_by',
                                    'applicants.updated_at',
                                    'applicants.updated_by',
                                    'ref_statuss.status as applicant_status'
                            )
                            ->join('applicant_statuss', function ($join) {
                                $join->on('applicant_statuss.applicant_id', '=', 'applicants.id')
                                ->where('applicant_statuss.statusflg', '=', 1);
                            })
                            ->join('ref_statuss', function ($join) {
                                $join->on('ref_statuss.id', '=', 'applicant_statuss.status_id')
                                ->where('ref_statuss.deleteflg', '=', 0);
                            })
                            ->join('ref_company_types', function ($join) {
                                $join->on('ref_company_types.id', '=', 'applicants.company_type_id')
                                ->where('ref_company_types.deleteflg', '=', 0);
                            })
                            ->join('ref_purchase_orgs', function ($join) {
                                $join->on('ref_purchase_orgs.id', '=', 'applicants.purchase_org_id')
                                ->where('ref_purchase_orgs.deleteflg', '=', 0);
                            })
                            ->joinSub($registerNumber, 'registered', function ($join) {
                                $join->on('applicants.id', '=', 'registered.applicant_id');
                            })
                            ->leftJoin('applicant_general_administrations', function ($join) {
                                $join->on('applicant_general_administrations.applicant_id', '=', 'applicants.id')
                                ->whereNull('applicant_general_administrations.deleted_at');
                            })
                            ->where($column)->orderby('applicants.id', 'DESC')->first();
        } catch (QueryException $e) {
            Log::error($e->getMessage());
        }
    }

    public function specific_find($column) {
        try {
            if (isset($column['id'])) {
                $column['applicants.id'] = $column['id'];
                // remove ambiguos
                unset($column['id']);
            }

            $registerNumber = Applicant::select(
                            'applicants.id as applicant_id',
                            DB::raw('ROW_NUMBER () OVER (ORDER BY applicants.id) as register_number')
                    )->join('applicant_statuss', function ($join) {
                $join->on('applicant_statuss.applicant_id', '=', 'applicants.id')
                        ->where('applicant_statuss.statusflg', '=', 1);
            });

            return Applicant::select(
                                    'applicants.id',
                                    'registered.register_number as row_number',
                                    DB::raw('LPAD(registered.register_number::varchar, 8, \'0\') as register_number'),
                                    'applicants.partner_name',
                                    'applicants.company_type_id',
                                    'ref_company_types.company_type',
                                    'applicants.purchase_org_id',
                                    'ref_purchase_orgs.org_code',
                                    'ref_purchase_orgs.description',
                                    'applicants.president_director',
                                    'applicants.address_1',
                                    'applicants.address_2',
                                    'applicants.address_3',
                                    'applicants.address_4',
                                    'applicants.address_5',
                                    'applicants.country',
                                    'applicants.province',
                                    'applicants.city',
                                    'applicants.sub_district',
                                    'applicants.house_number',
                                    'applicants.postal_code',
                                    'applicants.phone_number',
                                    'applicants.fax_number',
                                    'applicants.company_email',
                                    'applicants.company_site',
                                    'applicants.pic_full_name',
                                    'applicants.pic_mobile_number',
                                    'applicants.pic_email',
                                    'applicants.tender_ref_number',
                                    'applicants.pkp_number',
                                    'applicants.pkp_attachment',
                                    'applicants.npwp_tin_number',
                                    'applicants.npwp_tin_attachment',
                                    'applicants.deleteflg',
                                    DB::raw('TO_CHAR(applicants.created_at, \'DD.MM.YYYY HH24:MI\') as created_at'),
                                    'applicants.created_by',
                                    'applicants.updated_at',
                                    'applicants.updated_by',
                                    'ref_statuss.status as applicant_status'
                            )
                            ->join('applicant_statuss', function ($join) {
                                $join->on('applicant_statuss.applicant_id', '=', 'applicants.id')
                                ->where('applicant_statuss.statusflg', '=', 1);
                            })
                            ->join('ref_statuss', function ($join) {
                                $join->on('ref_statuss.id', '=', 'applicant_statuss.status_id')
                                ->where('ref_statuss.deleteflg', '=', 0);
                            })
                            ->join('ref_company_types', function ($join) {
                                $join->on('ref_company_types.id', '=', 'applicants.company_type_id')
                                ->where('ref_company_types.deleteflg', '=', 0);
                            })
                            ->join('ref_purchase_orgs', function ($join) {
                                $join->on('ref_purchase_orgs.id', '=', 'applicants.purchase_org_id')
                                ->where('ref_purchase_orgs.deleteflg', '=', 0);
                            })
                            ->joinSub($registerNumber, 'registered', function ($join) {
                                $join->on('applicants.id', '=', 'registered.applicant_id');
                            })
                            ->where($column)->orderby('applicants.id', 'DESC')->first();
        } catch (QueryException $e) {
            Log::error($e->getMessage());
        }
    }
    
    public function create_profile_general(Request $request){
        $name = Auth::user()->name;
        $generalAdministration = new ApplicantGeneralAdministration([
            'applicant_id' => $request->input('applicant_id'),
            'company_name' => $request->input('company_name'),
            'company_type_id' => $request->input('company_type_id'),
            'location_category' => $request->input('location_category'),
            'country' => $request->input('country'),
            'province' => $request->input('province'),
            'city' => $request->input('city'),
            'sub_district' => $request->input('sub_district'),
            'postal_code' => $request->input('postal_code'),
            'address' => $request->input('address'),
            'phone_number' => $request->input('phone_number'),
            'fax_number' => $request->input('fax_number'),
            'website' => $request->input('company_site'),
            'company_email' => $request->input('company_email'),
            'created_by' => $name,
            'created_at' => now(),
        ]);

        $success = false;
        try{
            DB::beginTransaction();            
            $generalAdministration->save();            
            DB::commit();
            $success=true;
        }catch(Exception $e){
            DB::rollback();
        }
        
        return response()->json([
            'status' => 200,
            'success' => $success,
            'message' => $success ? "data_saved" : "data_not_saved",
            'data' => ['id'=>$generalAdministration->id],
        ]);
    }
    
    public function update_profile_general(Request $request){
        // var_dump($request->input());
        $name = Auth::user()->name;
        $generalAdministration = new ApplicantGeneralAdministration([
            'applicant_id' => $request->input('applicant_id'),
            'company_name' => $request->input('company_name'),
            'company_type_id' => $request->input('company_type_id'),
            'location_category' => $request->input('location_category'),
            'country' => $request->input('country'),
            'province' => $request->input('province'),
            'city' => $request->input('city'),
            'sub_district' => $request->input('sub_district'),
            'postal_code' => $request->input('postal_code'),
            'address' => $request->input('address'),
            'phone_number' => $request->input('phone_number'),
            'fax_number' => $request->input('fax_number'),
            'website' => $request->input('company_site'),
            'company_email' => $request->input('company_email'),
            'created_by' => $name,
            'created_at' => now(),
            'parent_id' => $request->input('id')
        ]);

        $success = false;
        try{
            DB::beginTransaction();
                        
            if($request->input('edit_type') == 'current'){
                $generalAdministration->save();                
            } else {
                ApplicantGeneralAdministration::where('id', $request->input('id'))->update([
                    'applicant_id' => $request->input('applicant_id'),
                    'company_name' => $request->input('company_name'),
                    'location_category' => $request->input('location_category'),
                    'country' => $request->input('country'),
                    'province' => $request->input('province'),
                    'city' => $request->input('city'),
                    'sub_district' => $request->input('sub_district'),
                    'postal_code' => $request->input('postal_code'),
                    'address' => $request->input('address'),
                    'phone_number' => $request->input('phone_number'),
                    'fax_number' => $request->input('fax_number'),
                    'website' => $request->input('company_site'),
                    'company_email' => $request->input('company_email'),
                    'created_by' => $name,
                    'created_at' => now(),
                    'parent_id' => 0
                ]);
            }
            
            DB::commit();
            $success=true;
        }catch(Exception $e){
            DB::rollback();
        }
        
        return response()->json([
            'status' => 200,
            'success' => $success,
            'message' => $success ? "data_saved" : "data_not_saved",
            'data' => ['id'=>$generalAdministration->id],
        ]);
    }
    
    public function revert_profile_general(Request $request){
        $success = false;
        try{
            
            // Update Applicant Status statusflg = 0
            $general = ApplicantGeneralAdministration::where(['id'=>$request->input('id'), 'is_current_data'=>false, 'is_submitted'=>false])->forceDelete();
            
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
    }
    
    public function revertall_profile_general($id){
        $success = false;
        try{
            DB::beginTransaction();
            
            // Update Applicant Status statusflg = 0
            $general = ApplicantGeneralAdministration::where(['applicant_id'=>$id, 'is_current_data'=>false, 'is_submitted'=>false])->forceDelete();
            
            DB::commit();
            $success=true;
        }catch(Exception $e){
            DB::rollback();
        }
        
        return response()->json([
            'status' => 200,
            'success' => $success,
            'message' => $success ? "data_saved" : "data_not_saved",
            'data' => [],
        ]);
    }
    
    public function finishall_profile_general($id){
        $success = false;
        try{
            DB::beginTransaction();
            
            // delete data before
        //    ApplicantGeneralAdministration::where(['applicant_id'=>$id, 'is_current_data'=>true])->where('parent_id', 0)->forceDelete();
            
            ApplicantGeneralAdministration::where(['applicant_id'=>$id])->update([
                'is_finished' => true
            ]);
            
            DB::commit();
            $success=true;
        }catch(Exception $e){
            DB::rollback();
        }
        
        return response()->json([
            'status' => 200,
            'success' => $success,
            'message' => $success ? "data_saved" : "data_not_saved",
            'data' => [],
        ]);
    }
    
    public function get_profile_general($applicantId){
        return ApplicantGeneralAdministration::select('applicant_general_administrations.*', 'ref_company_types.company_type')->withTrashed(false)
                ->join('ref_company_types', 'ref_company_types.id', '=', 'applicant_general_administrations.company_type_id')
                ->where('applicant_id', $applicantId)->orderBy('id', 'DESC')->get();
    }
    
    public function find_profile_general(Request $request){
        return ApplicantGeneralAdministration::withTrashed(false)->where('id', $request->get('id'))->orderBy('id', 'DESC')->first();
    }

    public function create_profile_company(Request $request){
        $name = Auth::user()->name;
        $companyProfile = new ApplicantCompanyProfile([
            'applicant_id' => $request->input('applicant_id'),
            'company_name' => $request->input('company_name'),
            'company_type_id' => $request->input('company_type_id'),
            'created_by' => $name,
            'created_at' => now(),
        ]);
        
        $success = false;
        try{
            DB::beginTransaction();
            $companyProfile->save();            
            DB::commit();
            $success=true;
        }catch(Exception $e){
            DB::rollback();
        }
        
        return response()->json([
            'status' => 200,
            'success' => $success,
            'message' => $success ? "data_saved" : "data_not_saved",
            'data' => ['id'=>$companyProfile->id],
        ]);
    }
    
    public function update_profile_company(Request $request){
        $name = Auth::user()->name;
        $companyProfile = new ApplicantCompanyProfile([
            'applicant_id' => $request->input('applicant_id'),
            'company_name' => $request->input('company_name'),
            'company_type_id' => $request->input('company_type_id'),
            'created_by' => $name,
            'created_at' => now(),
        ]);

        // var_dump($draft);die();

        $success = false;
        try{
            DB::beginTransaction();
            
            // Update Applicant Status statusflg = 0
            ApplicantCompanyProfile::where('applicant_id', $request->input('applicant_id'))
                    ->update(['deleted_at' => now()]);

            $companyProfile->save();
            
            DB::commit();
            $success=true;
        }catch(Exception $e){
            DB::rollback();
        }
        
        return response()->json([
            'status' => 200,
            'success' => $success,
            'message' => $success ? "data_saved" : "data_not_saved",
            'data' => ['id'=>$companyProfile->id],
        ]);
    }
    
    public function repeat_profile_company($id){
        $success = false;
        try{
            DB::beginTransaction();
            
            // Update Applicant Status statusflg = 0
            $companyProfile = ApplicantCompanyProfile::where(['applicant_id'=>$id,'is_finished'=>false])->forceDelete();
            
            DB::commit();
            $success=true;
        }catch(Exception $e){
            DB::rollback();
        }
        
        return response()->json([
            'status' => 200,
            'success' => $success,
            'message' => $success ? "data_saved" : "data_not_saved",
            'data' => [],
        ]);
    }
    
    public function get_profile_company(Request $request){
        return ApplicantCompanyProfile::select(
                    'applicant_company_profiles.*', 
                    'ref_company_types.company_type'
                )->withTrashed(false)
                ->join('ref_company_types', 'ref_company_types.id', '=', 'applicant_company_profiles.company_type_id')
                ->where('ref_company_types.deleteflg',false)
                ->orderBy('applicant_company_profiles.id', 'DESC')
                ->get();
    }
    
    public function create_profile_deeds(Request $request){
        $name = Auth::user()->name;
        var_dump($request->file('attachment'));
        exit;
        $deeds = new ApplicantDeed([
            'applicant_id' => $request->input('applicant_id'),
            'deed_type' => $request->input('deed_type'),
            'deed_number' => $request->input('deed_number'),
            'deed_date' => $request->input('deed_date'),
            'notary_name' => $request->input('notary_name'),
            'sk_menkumham_number' => $request->input('sk_menkumham_number'),
            'sk_menkumham_date' => $request->input('sk_menkumham_date'),
            'attachment' => null !== $request->file('attachment') ? $request->file('attachment')->getClientOriginalName() : '',
            'created_by' => $name,
            'created_at' => now(),
        ]);


        $success = false;
        try{
            DB::beginTransaction();
            $deeds->save();            
            DB::commit();
            $success=true;
        }catch(Exception $e){
            DB::rollback();
        }
        
        return response()->json([
            'status' => 200,
            'success' => $success,
            'message' => $success ? "data_saved" : "data_not_saved",
            'data' => ['id'=>$deeds->id],
        ]);
    }
    
    public function update_profile_deeds(Request $request){
        $name = Auth::user()->name;
        var_dump($request->file('attachment'));
        exit;
        $deeds = new ApplicantDeed([
            'applicant_id' => $request->input('applicant_id'),
            'deed_type' => $request->input('deed_type'),
            'deed_number' => $request->input('deed_number'),
            'deed_date' => $request->input('deed_date'),
            'notary_name' => $request->input('notary_name'),
            'sk_menkumham_number' => $request->input('sk_menkumham_number'),
            'sk_menkumham_date' => $request->input('sk_menkumham_date'),
            'attachment' => null !== $request->file('attachment') ? $request->file('attachment')->getClientOriginalName() : '',
            'created_by' => $name,
            'created_at' => now(),
        ]);

        $success = false;
        try{
            DB::beginTransaction();
            
            // Update Applicant Status statusflg = 0
            $applicantDeed = ApplicantDeed::where('applicant_id', $request->input('applicant_id'))
                    ->update(['deleted_at' => now()]);

            $deeds->save();
            $lastID = $applicantDeed->id;
            
            DB::commit();
            
            $path1 = Storage::putFileAs(
                'public/vendor/'.$lastID.'/profiles/deeds/',
                $request->file('pkp_attachment'), $request->file('pkp_attachment')->getClientOriginalName()
            );
            $success=true;
        }catch(Exception $e){
            DB::rollback();
        }
        
        return response()->json([
            'status' => 200,
            'success' => $success,
            'message' => $success ? "data_saved" : "data_not_saved",
            'data' => ['id'=>$deeds->id],
        ]);
    }
    
    public function get_profile_deeds(Request $request){
        return ApplicantDeed::withTrashed(false)->orderBy('id', 'DESC')->get();
    }
    
    public function create_profile_shareholders(Request $request){
        $name = Auth::user()->name;
        $shareholders = new ApplicantShareholder([
            'applicant_id' => $request->input('applicant_id'),
            'full_name' => $request->input('full_name'),
            'nationality' => $request->input('nationality'),
            'share_percentage' => $request->input('share_percentage'),
            'email' => $request->input('email'),
            'identity_number' => $request->input('ktp_number'),
            'identity_attachment' => $request->input('ktp_attachment'),
            'created_by' => $name,
            'created_at' => now(),
        ]);

        $success = false;
        try{
            DB::beginTransaction();
            
            $shareholders->save();
            
            DB::commit();
            $success=true;
        }catch(Exception $e){
            DB::rollback();
        }
        
        return response()->json([
            'status' => 200,
            'success' => $success,
            'message' => $success ? "data_saved" : "data_not_saved",
            'data' => ['id'=>$shareholders->id],
        ]);
    }
    
    public function update_profile_shareholders(Request $request){
        $name = Auth::user()->name;
        $shareholders = new ApplicantShareholder([
            'applicant_id' => $request->input('applicant_id'),
            'full_name' => $request->input('full_name'),
            'nationality' => $request->input('nationality'),
            'share_percentage' => $request->input('share_percentage'),
            'email' => $request->input('email'),
            'identity_number' => $request->input('ktp_number'),
            'identity_attachment' => $request->input('ktp_attachment'),
            'created_by' => $name,
            'created_at' => now(),
        ]);

        $success = false;
        try{
            DB::beginTransaction();
            
            // Update Applicant Status statusflg = 0
            ApplicantShareholder::where('applicant_id', $request->input('applicant_id'))
                    ->update(['deleted_at' => now()]);

            $shareholders->save();
            
            DB::commit();
            $success=true;
        }catch(Exception $e){
            DB::rollback();
        }
        
        return response()->json([
            'status' => 200,
            'success' => $success,
            'message' => $success ? "data_saved" : "data_not_saved",
            'data' => ['id'=>$shareholders->id],
        ]);
    }
    
    public function get_profile_shareholders(Request $request){
        return ApplicantShareholder::withTrashed(false)->orderBy('id', 'DESC')->get();
    }
    
    public function send_submission(Request $request){
        $success = false;
        try{
            DB::beginTransaction();
            ApplicantGeneralAdministration::where(['applicant_id'=>$request->input('id'), 'is_finished'=>true])->update(['is_submitted'=>true]);
            
            // Update Applicant Status statusflg = 0
            VendorWorkflow::where('applicant_id', $request->input('id'))
                    ->update(['end_date' => now(), 'deleted_at' => now()]);
            // Run Workflow
            $vendorWorkflow = new VendorWorkflow([
                'applicant_id' => $request->input('id'),
                'activity' => 'Form Submission',
                'remarks' => $request->input('id'),
                'start_date' => now(),
                'created_by' => Auth::user()->id,
            ]);
            $vendorWorkflow->save();
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
    }

    //FATAH//
    public function get_profile(Request $request, $table){
        return DB::table($table)->whereNull('deleted_at')->orderBy('id', 'DESC')->get();
    }

    public function create_profile(Request $request, $tableInput){
        if(in_array($tableInput, $this->tableKeys)){
            $name = Auth::user()->name;
            $input = $request->input();
            $table = $this->tables[$tableInput];
            try{
                DB::beginTransaction();

                $fields = Schema::getColumnListing($table);

                foreach($fields as $field){
                    if(array_key_exists($field, $input)){
                        $data[$field] = $input[$field];
                    }
                }

                //SAVE FILE IF EXISTS
                if($request->file()>0){
                    foreach($request->file() as $key=>$file){
                        $path = Storage::putFileAs('public/vendor/profiles/'.$input['applicant_id'].'/'.$tableInput , $file, $file->getClientOriginalName() );
                        $data[$key] = $file->getClientOriginalName();
                    }
                }

                //INSERT QUERY
                unset($data['id']);
                $data['created_at'] = now();
                $data['created_by'] = $name;

                $affected = DB::table($table)->insert($data);
                $returnId = DB::getPdo()->lastInsertId();
            
                DB::commit();
                $success=true;
            }catch(Exception $e){
                DB::rollback();
            }
            return response()->json([
                'status' => 200,
                'success' => $success,
                'message' => $success ? "data_saved" : "data_not_saved",
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
            $tmp = DB::table($table)
                ->whereNull('deleted_at')
                ->where('id', $request->get('id'))
                ->orderBy('id', 'DESC')
                ->first();
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
            $success = false;
            $continue = true;
            $returnId = $input['id'];
            $message = "";
            try{
                DB::beginTransaction();

                $fields = Schema::getColumnListing($table);

                foreach($fields as $field){
                    if(array_key_exists($field, $input)){
                        $data[$field] = $input[$field];
                    }
                }
    
                //SAVE FILE IF EXISTS
                if($request->file()>0){
                    if($request->input('edit_type') != 'current'){
                        $old = DB::table($table)->where('id',$input['id'])->get();
                        if(count($old)>0){
                            foreach($request->file() as $key=>$file){
                                $filename = 'public/vendor/profiles/'.$input['applicant_id'].'/'.$tableInput.'/'.$old[0]->$key;
                                Storage::delete($filename);
                            }
                        }
                    }
                    foreach($request->file() as $key=>$file){
                        $filename = 'public/vendor/profiles/'.$input['applicant_id'].'/'.$tableInput.'/'.$file->getClientOriginalName();
                        if(!Storage::exists($filename)){
                            $continue = false;
                            $message = "duplicate_file: ".$file->getClientOriginalName();
                        }
                    }
                    if($continue){
                        foreach($request->file() as $key=>$file){
                            $path = Storage::putFileAs('public/vendor/profiles/'.$input['applicant_id'].'/'.$tableInput , $file, $file->getClientOriginalName() );
                            $data[$key] = $file->getClientOriginalName();
                        }
                    }
                }

                if($continue){
                    if($request->input('edit_type') == 'current'){
                        //insert
                        $data['created_at'] = now();
                        $data['created_by'] = $name;
                        $data['parent_id'] = $input['id'];
                        $affected = DB::table($table)->insert($data);
                        $returnId = DB::getPdo()->lastInsertId();
                    } else {
                        //update
                        $data['updated_at'] = now();
                        $data['parent_id'] = 0;
                        $affected = DB::table($table)->where('id', $input['id'])->update($data);
                        $returnId = $input['id'];
                    }
                }

                if($continue){
                    DB::commit();
                }else{
                    DB::rollback();
                }
                $success=$continue;
            }catch(Exception $e){
                DB::rollback();
            }
            return response()->json([
                'status' => 200,
                'success' => $success,
                'message' => $success ? "data_saved" : "data_not_saved. ".$message,
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

    public function revert_profile(Request $request, $tableInput){
        if(in_array($tableInput, $this->tableKeys)){
            $table = $this->tables[$tableInput];
            $success = false;
            try{
                
                // delete file if exists
                $old = DB::table($table)->where('id',$request->input('id'))->get();
                foreach($old as $row){
                    foreach($row as $key=>$value){
                        if(strpos($key,'attachment') !== false || strpos($key,'letter') !== false){
                            $filename = 'public/vendor/profiles/'.$request->input('applicant_id').'/'.$tableInput.'/'.$row->$key;
                            if(Storage::exists($filename)){
                                Storage::delete($filename);
                            }
                        }
                    }
                }

                DB::table($table)->where(['id'=>$request->input('id'), 'is_current_data'=>false, 'is_submitted'=>false])->delete();
                
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
        if(in_array($tableInput, $this->tableKeys)){
            $table = $this->tables[$tableInput];
            $success = false;
            try{
                DB::beginTransaction();
                
                // delete file if exists
                $old = DB::table($table)->where('applicant_id',$id)->get();
                foreach($old as $row){
                    foreach($row as $key=>$value){
                        if(strpos($key,'attachment') !== false || strpos($key,'letter') !== false){
                            $filename = 'public/vendor/profiles/'.$request->input('applicant_id').'/'.$tableInput.'/'.$row->$key;
                            if(Storage::exists($filename)){
                                Storage::delete($filename);
                            }
                        }
                    }
                }

                $general = DB::table($table)
                        ->where(['applicant_id'=>$id, 'is_current_data'=>false, 'is_submitted'=>false])
                        ->delete();
                
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
            $success = false;
            try{
                DB::beginTransaction();
                            
                $affected = DB::table($table)->where(['applicant_id'=>$id])->update([
                    'is_finished' => true
                ]);
                
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
    
}
