<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;
use App\Mail\TestMail;
use App\RefListOption;
use App\RefPurchaseOrg;
use App\Vendor;
use App\VendorSanction;
use App\VendorSanctionHistory;
use App\VendorSanctionWorkflow;
use App\VendorProfilePic;
use App\VendorProfileGeneral;
use App\VendorProfile;
use App\SapConnector;
use App\User;
use App\Buyer;
use App\Repositories\VendorSanctionRepository;
use App\Repositories\VendorRepository;
use App\Repositories\BuyerRepository;
use View;
use DB;
use DataTables;
use Auth;
use App\Traits\AccessLog;
use App\Jobs\ProcessEmail;

class VendorSanctionController extends Controller
{
    use AccessLog;
    public $sanctionStatuses = [];
    public $workflow = [];
    public $emailConfig = [];
    public $sanctionTypes = [];

    public function __construct(){
        $this->middleware('auth');
        $this->sanctionTypes = RefListOption::where('type','sanction_types')->where('deleteflg',false)->pluck('value','key')->toArray();
        $this->vendorRepo = new VendorRepository();
        $this->getVendor = $this->vendorRepo->getListVendorByType('vendor');
        $this->buyerRepo = new BuyerRepository();
        $this->sanctionRepo = new VendorSanctionRepository();

        $config = config('eproc.vendor_sanction');
        $this->sanctionStatuses = $config['sanction_statuses'];
        $this->workflow = $config['workflow'];
        $this->emailConfig = $config['email_config'];
    }

    public function vendor_option_list(Request $request){
        $query = $this->getVendor;

        //find same purchase organization
        $purchOrgs = $this->buyerRepo->getUserPurchaseOrganization(auth()->user(),false);
        $query->whereIn('vendors.purchase_org_id',$purchOrgs);

        $term = $request->term ?? '';
        if($term!='') $query->where(function($subquery) use ($term){
            $subquery->where('vendor_profile_generals.company_name','~*',$term)
                     ->orWhere('vendors.vendor_code','~*',$term);
        });
        // Log::debug($query->toSql());
        $profiles = $query->get();
        $output = [];
        foreach($profiles as $profile){
            $output[] = [
                'id' => $profile->id,
                'text' => "{$profile->vendor_code} - {$profile->company_name}",
            ];
        }
        return response()->json($output);
    }
    
    public function sanction_view(){
        $user = auth()->user();
        if($user && $user->user_type=='vendor'){
            return $this->sanction_input();
        }else{
            $vendors = $this->getVendor->select(
                'vendors.id',
                DB::raw('vendor_profile_generals.company_name as vendor_name'),
                DB::raw('count(s.id) as sanction_count')
            )
            ->leftJoin('vendor_sanctions as s', function ($join) {
                $join->on('s.vendor_profile_id', '=', 'vendor_profiles.id');
            })
            ->groupBy('vendors.id','vendor_profile_generals.company_name')
            ->get();
    
            $data = [
                'type'=>auth()->user()->user_type=='vendor',
                'sanctionTypes'=>$this->sanctionTypes,
                'vendors'=>$vendors,
                'storage' => asset('storage/vendor/sanctions'),
                'fields'=>['vendor_name','vendor_code','sanction_type','valid_from_date','valid_thru_date','status','letter_number','description'],
                'fieldSizes'=>['300','50','100','100','100','80','120','300'],
            ];
            return view('vendor.sanction.list',$data);
        }
    }
    public function sanction_data_list(){
        if (request()->ajax()) {
            // $data = VendorSanction::where('status','=',$this->sanctionStatuses['SUBMITTED'])->get();
            // $data = VendorSanction::withTrashed()
            $data = VendorSanction::distinct('vendor_profile_id')
                ->orderBy('vendor_profile_id')
                ->orderBy('id','desc')
                ->get();
            return DataTables::of($data)
            ->make(true);
        }
    }
    public function sanction_input(){
        if(!Auth::check()) return redirect()->route('login');
        $data = [
            'storage' => asset('storage/vendor/sanctions'),
            'sanctionTypes'=>$this->sanctionTypes,
            'fields'=>['sanction_type','valid_from_date','valid_thru_date','status','letter_number','description'],
            'historyFields'=>['activity_date','username','role','activity','status','comments','pic','sanction_detail'],
            'commentHistoryFields'=>['userid','name','activity','started_at','finished_at','remarks'],
        ];
        if(auth()->user()->user_type=='vendor'){
            $data['vendor'] = $this->vendorRepo->getVendorById(auth()->user()->ref_id);
            $data['isBuyerActive'] = false;
        }else{
            $data['isBuyerActive'] = $this->buyerRepo->userIsBuyer(auth()->user());
        }
        return view('vendor.sanction.sanction_input',$data);
    }
    public function sanction_data($id){
        $data = $this->getVendor->select(
                    'vendors.id',
                    DB::raw('ref_purchase_orgs.description as purchase_org_description'),
                    'vendors.vendor_name',
                    DB::raw('count(s.id) as sanction_count'),
                    'vendors.vendor_code'
                )
                ->leftJoin('vendor_sanctions as s', function ($join) {
                    $join->on('s.vendor_profile_id', '=', 'vendor_profiles.id');
                })
                ->where('vendors.id',$id)
                ->groupBy('vendors.id','ref_purchase_orgs.description')
                ->first();
        $profile = VendorProfile::where('vendor_id',$id)->first();
        $pic = VendorProfilePic::where('vendor_profile_id',$profile->id)
                ->where('primary_data',true)
                ->where('is_current_data',true)
                ->first();
        $output = [
            'id' => $data->id,
            'vendor_name' => $data->vendor_name,
            'sanction_count' => $data->sanction_count,
            'vendor_code' => $data->vendor_code,
            'purchase_org_description' => $data->purchase_org_description,
            'pic_id' => $pic->id ?? '',
            'pic' => $pic->full_name ?? '',
        ];
        return response()->json($output);
    }
    public function sanction_current_list(Request $request, $id=null){
        $sanctionStatuses = $this->sanctionStatuses;
        if (request()->ajax()) {
            $query = VendorSanction::select('*');
            if(auth()->user()->user_type=='vendor'){
                $vendorId = auth()->user()->ref_id;
            }else if(!is_null($id)){
                $vendorId = $id;
            }
            $vp = VendorProfile::where('vendor_id',$vendorId)->first();
            $pic = $this->vendorRepo->getPicDetailsByProfileId($vp->id,true,true)->first();
            $query->where('vendor_profile_id',$vp->id)
            ->addSelect(DB::raw("'".($pic->full_name ?? '')."' as pic"))
            ->where(function($q) use($sanctionStatuses) {
                $q->where('status',$sanctionStatuses['APPROVED']);
                if(auth()->user()->user_type!='vendor') $q->orWhere('status',$sanctionStatuses['SUBMITTED']);
                // $q->orWhere('status','SUBMITTED');
            })
            ->orderBy('status');
            $data = $query->get();
            // dd($query->toSql());
            if(count($data)==0){
                $data[] = [
                    "id"=>$vendorId,
                    "vendor_profile_id"=>$vp->id,
                    "sanction_type"=>"GREEN",
                    "valid_from_date"=>date('Y-m-d',strtotime($vp->created_at)),
                    "valid_thru_date"=>null,
                    "letter_number"=>"",
                    "description"=>"",
                    "status"=>"",
                    "pic"=>($pic ? $pic->full_name : ''),
                ];
            }
            return DataTables::of($data)
            ->make(true);
        }
    }
    public function sanction_history_list(Request $request, $id=null){
        if (request()->ajax()) {
            $query = VendorSanctionHistory::select('*');
            if(auth()->user()->user_type=='vendor'){
                $vendorId = auth()->user()->ref_id;
            }else if(!is_null($id)){
                $vendorId = $id;
            }
            $vp = VendorProfile::where('vendor_id',$vendorId)->first();
            $query->where('vendor_profile_id',$vp->id)
            ->orderBy('id','desc');
            $data = $query->get();
            return DataTables::of($data)
            ->make(true);
        }
    }    
    public function sanction_comment_history_list(Request $request, $id=null){
        if (request()->ajax()) {
            $query = VendorSanctionWorkflow::select(
                        'vendor_sanction_workflows.*',
                        DB::raw('COALESCE(users.userid, vendor_sanction_workflows.created_by) as userid'),
                        DB::raw('COALESCE(users.name, vendor_sanction_workflows.created_by) as name'),
                        'vendor_sanctions.sanction_type'
                    )
                    ->leftJoin('users', function($join){
                        $join->on('users.userid', '=', 'vendor_sanction_workflows.created_by');
                    })
                    ->leftJoin('vendor_sanctions', function($join){
                        $join->on('vendor_sanctions.id', '=', 'vendor_sanction_workflows.vendor_sanction_id');
                    });
            if(auth()->user()->user_type=='vendor'){
                $vendorId = auth()->user()->ref_id;
            }else if(!is_null($id)){
                $vendorId = $id;
            }
            $query->where('vendor_id',$vendorId)
            ->orderBy('id','desc');
            $data = $query->get();
            return DataTables::of($data)
            ->make(true);
        }
    }
    public function sanction_store(Request $request){
        if($request->ajax()){
            //validation

            $success = false;
            $vendorID = $request->vendor_id;
            $userid = auth()->user()->userid;
            try{
                DB::beginTransaction();
                $vp = VendorProfile::where('vendor_id',$request->vendor_id)->first();

                //softdelete previous if exists
                VendorSanction::where('vendor_profile_id',$vp->id)->where('status','REVISE')->delete();

                $data = new VendorSanction();

                $data->created_by = auth()->user()->id;
                $data->sanction_type = $request->sanction_type;
                $data->valid_from_date = $request->valid_from_date;
                $data->valid_thru_date = $request->valid_thru_date;
                $data->letter_number = $request->letter_number;
                $data->description = $request->description;
                $data->status = $this->sanctionStatuses['SUBMITTED'];

                $continue = true;

                $data->vendor_profile_id = $vp->id;
                foreach($request->file() as $key=>$file){
                    $filename = 'public/vendor/sanctions/'.$vp->vendor_id.'/'.$file->getClientOriginalName();
                    if(Storage::exists($filename)){
                        $continue = false;
                        $message = "duplicate_file: ".$filename;
                    }
                }

                if($continue){
                    foreach($request->file() as $key=>$file){
                        $path = Storage::putFileAs('public/vendor/sanctions/'.$vp->vendor_id , $file, $file->getClientOriginalName() );
                        $data[$key] = $file->getClientOriginalName();
                    }
                }
                $data->save();
                $this->sendEmail($data,'SUBMISSION',null,$request);

                $resubmission = VendorSanctionWorkflow::where('vendor_id',$vendorID)->count() > 0;
                if($resubmission){
                    VendorSanction::where('status','REVISE')
                        ->where('vendor_profile_id',$vp->id)
                        ->delete();
                }
                //create history
                $history['vendor_profile_id'] = $vp->id;
                $history['vendor_sanction_id'] = $data->id;
                $history['username'] = auth()->user()->name;
                $history['comments'] = $resubmission ? "Resubmission" : "Submission";
                $history['pic'] = $request->pic;
                $history['activity_date'] = now();
                $history['status'] = 'created';
                $this->finishActivity($history);

                // Submission and Approval generate
                $workflow = config('workflow.sanction-submission.tasks');
                $i = 0;
                foreach($workflow as $task){
                    $vendorSanctionWorkflow = new VendorSanctionWorkflow([
                        'vendor_id' => $vendorID,
                        'vendor_sanction_id' => $data->id,
                        'activity' => ($i==0 && $resubmission) ? 'Resubmission' : $task['activity'],
                        'remarks' => ($i==0 && $resubmission) ? 'Resubmission' : $task['remarks'],
                        'started_at' => $task['started_at']=='now' ? now() : null,
                        'finished_at' => $task['finished_at']=='now' ? now() : null,
                        'created_by' => $i==0 ? $userid : 'system'
                        // 'created_by' => ''
                    ]);
                    $vendorSanctionWorkflow->save();
                    $i++;
                }
                
                if($continue){
                    $message = 'Data saved'.
                    DB::commit();
                }else{
                    DB::rollback();
                }
                $success=$continue;
            }catch(Exception $e){
                DB::rollback();
            }

            return response()->json([
                'success'=> $success,
                'message' => $message,
                'data' => ['id'=> $data->id],
            ]);
        }
    }
    public function sanction_detail($id){
        $vendor = $this->vendorRepo->getVendorById($id);
        $vp = VendorProfile::where('vendor_id',$id)->first();
        $pic = $this->vendorRepo->getPicDetailsByProfileId($vp->id,true,true)->first();
        $sanctions = VendorSanction::where('vendor_profile_id',$vp->id)
                ->get();
        $user = auth()->user();
        $data = [
            'sanctionTypes'=>$this->sanctionTypes,
            'vendor'=>$vendor,
            'storage' => asset('storage/vendor/sanctions'),
            'sanctions'=>$sanctions,
            'pic'=>$pic,
            'fields'=>['activity_date','username','role','activity','status','comments','pic','sanction_detail'],
            'commentHistoryFields'=>['userid','name','activity','started_at','finished_at','sanction_type','remarks'],
            'chFieldSizes'=>['120','120','200','100','100','150','200'],
            'isBuyerActive' => $this->buyerRepo->userIsBuyer($user),
            'samePurchOrg' => $this->buyerRepo->userHavePurchaseOrganization($user,$vendor->purchase_org_id)
        ];
        return view('vendor.sanction.detail',$data);
    }
    public function sanction_patch(Request $request){
        if($request->ajax()){
            //validation
            $success = false;
            $userid = auth()->user()->userid;
            try{
                DB::beginTransaction();
                $data = VendorSanction::find($request->id);
                $data->updated_by = auth()->user()->id;
                $continue = true;
                if($request->approved=='true'){
                    $data->status = $this->sanctionStatuses['APPROVED'];
                    $old = VendorSanction::find($request->current);
                    $vp = VendorProfile::find($data->vendor_profile_id);
                    $currentProfileStatus = $vp->company_warning;

                    $now = date('Y-m-d');

                    //20200823 tetap harus menunggu scheduler untuk apply sanction meskipun sudah lewat.
                    //20200826 additional.. hanya block dan unblock serta company_warning yang diskip. proses sanctionnya tetap jalan.
                    $sanctionCanApply = ($now >= $data->valid_from_date) && ($now <= $data->valid_thru_date);
                    // $sanctionCanApply = false;
                    // $sanctionCanApply = true;
                    // Log::debug(['Now' => $now, 'From' => $data->valid_from_date, 'To' => $data->valid_thru_date, 'CanApply' => $sanctionCanApply]);
                    $continue = true;
                    // if($sanctionCanApply && ($data->sanction_type=='RED'||$old->sanction_type=='RED')){
                    if($sanctionCanApply && ($data->sanction_type=='RED'||$currentProfileStatus=='RED')){
                        //send block/unblock to SAP
                        if($data->sanction_type!=$currentProfileStatus){
                            Log::debug('sending to sap...');
                            $res = $this->SapBlock($data,$old,$currentProfileStatus);
                            $message = $res['message'];
                            $continue = $res['status'];
                        }
                    }

                    if($continue){
                        $vendorID = $vp->vendor_id;
   
                        //apply sanction
                        if(!is_null($old)){
                            $old->updated_by = auth()->user()->id;
                            $old->deleted_at = now();
                            $old->save();
                        }
                        $data->save();

                        //save profile
                        if($sanctionCanApply){
                            $vp->company_warning = $data->sanction_type;
                            $vp->save();
                        }
                        
                        // TODO: Finished Workflow
                        VendorSanctionWorkflow::where('vendor_id', $vendorID)
                            ->where('vendor_sanction_id', $data->id)
                            ->whereNotNull('started_at')
                            ->whereNull('finished_at')
                            ->update([
                                'finished_at' => now(), 
                                'remarks' => $request->comment, 
                                'created_by' => $userid
                            ]);
                        
                        //sendMail notification
                        $this->sendEmail($data,'APPROVAL',$old, $request);
                    }
                }else{
                    $vp = VendorProfile::find($data->vendor_profile_id);
                    $vendorID = $vp->vendor_id;
                    $data->status = 'REVISE';
                    $data->updated_at = now();
                    $data->save();
                    VendorSanctionWorkflow::where('vendor_id', $vendorID)
                        ->where('vendor_sanction_id', $data->id)
                        ->whereNotNull('started_at')
                        ->whereNull('finished_at')
                        ->update([
                            'finished_at' => now(), 
                            'remarks' => $request->comment, 
                            'created_by' => $userid
                        ]);

                    $this->sendEmail($data,'REVISE', null, $request);
                }

                if($continue){
                    //TODO: create history//
                    $lastHistory = VendorSanctionHistory::where('vendor_profile_id',$data->vendor_profile_id)
                        ->where('vendor_sanction_id',$data->id)
                        ->orderBy('id','desc')->first();
                    $history['vendor_profile_id'] = $lastHistory->vendor_profile_id;
                    $history['vendor_sanction_id'] = $lastHistory->vendor_sanction_id;
                    $history['username'] = auth()->user()->name;
                    $history['comments'] = $request->comment;
                    $history['pic'] = $lastHistory->pic;
                    $history['activity_date'] = now();
                    $history['status'] = $data->status;
                    $this->finishActivity($history,'manual',$lastHistory->activity);

                    $message = 'Data saved'.
                    DB::commit();
                    $success=true;
                }else{
                    DB::rollback();
                    $success=false;
                }
            }catch(Exception $e){
                DB::rollback();
            }

            return response()->json([
                'success'=> $success,
                'message' => $message,
                'data' => ['id'=> $data->id],
            ]);
        }
    }
    
    public function sanction_patch_old(Request $request){
        if($request->ajax()){
            //validation
            $success = false;
            $userid = auth()->user()->userid;
            try{
                DB::beginTransaction();
                $data = VendorSanction::find($request->id);
                $data->updated_by = auth()->user()->id;
                $continue = true;
                if($request->approved=='true'){
                    $data->status = $this->sanctionStatuses['APPROVED'];
                    $old = VendorSanction::find($request->current);
                    //send to SAP
                    Log::debug('sending to sap...');
                    $res = $this->SapBlock($data,$old);
                    $message = $res['message'];
                    $continue = $res['status'];
                    if($continue){
                        if(!is_null($old)){
                            $old->updated_by = auth()->user()->id;
                            $old->deleted_at = now();
                            $old->save();
                        }
                        $data->save();
                        //save profile
                        $vp = VendorProfile::find($data->vendor_profile_id);
                        $vendorID = $vp->vendor_id;
                        $vp->company_warning = $data->sanction_type;
                        $vp->save();
                        
                        // TODO: Finished Workflow
                        VendorSanctionWorkflow::where('vendor_id', $vendorID)
                            ->where('vendor_sanction_id', $data->id)
                            ->whereNotNull('started_at')
                            ->whereNull('finished_at')
                            ->update([
                                'finished_at' => now(), 
                                'remarks' => $request->comment, 
                                'created_by' => $userid
                            ]);
                        
                        //sendMail
                        $this->sendEmail($data,'APPROVAL',$old, $request);
                    }
                }else{
                    $vp = VendorProfile::find($data->vendor_profile_id);
                    $vendorID = $vp->vendor_id;
                    $data->status = 'REVISE';
                    $data->updated_at = now();
                    $data->save();
                    VendorSanctionWorkflow::where('vendor_id', $vendorID)
                        ->where('vendor_sanction_id', $data->id)
                        ->whereNotNull('started_at')
                        ->whereNull('finished_at')
                        ->update([
                            'finished_at' => now(), 
                            'remarks' => $request->comment, 
                            'created_by' => $userid
                        ]);

                    $this->sendEmail($data,'REVISE', null, $request);
                }

                if($continue){
                    //TODO: create history//
                    $lastHistory = VendorSanctionHistory::where('vendor_profile_id',$data->vendor_profile_id)
                        ->where('vendor_sanction_id',$data->id)
                        ->orderBy('id','desc')->first();
                    $history['vendor_profile_id'] = $lastHistory->vendor_profile_id;
                    $history['vendor_sanction_id'] = $lastHistory->vendor_sanction_id;
                    $history['username'] = auth()->user()->name;
                    $history['comments'] = $request->comment;
                    $history['pic'] = $lastHistory->pic;
                    $history['activity_date'] = now();
                    $history['status'] = $data->status;
                    $this->finishActivity($history,'manual',$lastHistory->activity);

                    $message = 'Data saved'.
                    DB::commit();
                    $success=true;
                }else{
                    DB::rollback();
                    $success=false;
                }
            }catch(Exception $e){
                DB::rollback();
            }

            return response()->json([
                'success'=> $success,
                'message' => $message,
                'data' => ['id'=> $data->id],
            ]);
        }
    }

    public function getNextActivity($key=null){
        if(is_null($key)){
            $k = key($this->workflow);
            return ['key' => $k, 'val' => $this->workflow[$k]];
        }else{
            $keys = array_keys($this->workflow);
            $num = array_search($key,$keys) + 1;
            if($num==count($keys)){
                return null; //finish
            }else{
                $k = $keys[$num];
                return ['key' => $k, 'val' => $this->workflow[$k]];
            }
        }
    }
    public function finishActivity($data,$type=null,$key=null){
        $activity = $this->getNextActivity($key);
        if($activity==null){
            //already finish.
        }else if($activity['val'][1]=='auto'){
            //saving, and do finish again.
            $data['activity'] = $activity['key'];
            $data['role'] = $activity['val'][0];
            VendorSanctionHistory::insert($data);
            $this->finishActivity($data,'auto',$activity['key']);
        }else if($type=='manual'){
            //only run if activity manual, and finish activity called manually.
            $data['activity'] = $activity['key'];
            $data['role'] = $activity['val'][0];
            VendorSanctionHistory::insert($data);
            $this->finishActivity($data,'auto',$activity['key']);
        }
    }

    public function SapBlock($current,$old,$currentProfileStatus){
        $oldStatus='GREEN';
        $newStatus=$current->sanction_type;
        if(!is_null($old)){
            $oldStatus=$old->sanction_type;
        }
        $oldStatus = $currentProfileStatus ?? 'GREEN';

        if($oldStatus=='RED'){
            $vp = VendorProfile::find($current->vendor_profile_id);
            $vd = Vendor::find($vp->vendor_id);
            $refPOrg = RefPurchaseOrg::find($vd->purchase_org_id);
            $data = [
                'business_partner_code' => $vd->business_partner_code,
                'sap_vendor_code' => $vd->sap_vendor_code,
                'org_code' => $refPOrg->org_code
            ];
            return $this->sanctionRepo->unblock($data);
        } 

        if($newStatus=='RED'){
            $vp = VendorProfile::find($current->vendor_profile_id);
            $vd = Vendor::find($vp->vendor_id);
            $refPOrg = RefPurchaseOrg::find($vd->purchase_org_id);
            $data = [
                'business_partner_code' => $vd->business_partner_code,
                'sap_vendor_code' => $vd->sap_vendor_code,
                'org_code' => $refPOrg->org_code
            ];
            return $this->sanctionRepo->block($data);
        } 
        
    }

    public function sendEmail($sanction,$emailType,$old=null,$request){
        
        $vp = VendorProfile::find($sanction->vendor_profile_id);
        $vendor = Vendor::find($vp->vendor_id);
        $purch_org = RefPurchaseOrg::find($vendor->purchase_org_id);
        $pic = VendorProfilePic::where('vendor_profile_id',$sanction->vendor_profile_id)
                ->where('is_current_data',true)
                ->where('primary_data',true)
                ->pluck('email','full_name')->toArray();
        $to = $this->emailConfig[$emailType]['to'];
        $cc = $this->emailConfig[$emailType]['cc'];
        
        $recipients = $to!='vendor' ? User::role($to)->join("user_extensions","user_extensions.user_id","=","users.id")->where("user_extensions.status","=",1)->whereNotNull("users.email")->pluck('email', 'name')->toArray() : $pic;
        $ccs = $cc!='vendor' ? User::role($cc)->join("user_extensions","user_extensions.user_id","=","users.id")->where("user_extensions.status","=",1)->whereNotNull("users.email")->pluck('email')->toArray() : $pic;
    
        Log::debug('====== Vendor Sanction Profile ID ['.$sanction->vendor_profile_id.'] send email: '.$emailType);
        Log::debug('Role To: '.json_encode($to));
        Log::debug('Role Cc: '.json_encode($cc));
        Log::debug('Email To: '.json_encode($recipients));
        Log::debug('Email Cc: '.json_encode($ccs));
        if($emailType=='SUBMISSION'){
            //email to procurement manager
            //cc admin vendor
            $subject = str_replace('[NAME]',$vp->company_name,$this->emailConfig[$emailType]['subject']);
            // foreach ($recipients as $name=>$email) {
                $arrdata = [];
                $arrdata['mailtype'] = $this->emailConfig[$emailType]['mailtype'];
                $arrdata['vendor_name'] = $vp->company_name;
                $arrdata['vendor_type'] = $vp->company_type;
                $arrdata['purchasing_org'] = $purch_org->org_code;
                $arrdata['purchasing_org_description'] = $purch_org->description;
                // $arrdata['recipient_name'] = $name;
                $arrdata['subject'] = $subject;
                $arrdata = (object) $arrdata;
                if ($this->vendorRepo->isValidEmail($recipients) && $this->vendorRepo->isValidEmail($ccs)){
                    ProcessEmail::dispatch($recipients, $ccs, $arrdata);
                    // Mail::to($recipients)->cc($ccs)->send(new TestMail($arrdata));
                }else{
                    $this->maillog("===========email failed==============. email :".json_encode($recipients).", cc:" .json_encode($ccs).", obj: ".json_encode($arrdata));
                }
            // }
        }
        if($emailType=='REVISE'){
            $subject = str_replace('[NAME]',$vp->company_name,$this->emailConfig[$emailType]['subject']);
            // foreach ($recipients as $name=>$email) {
                $arrdata = [];
                $arrdata['mailtype'] = $this->emailConfig[$emailType]['mailtype'];
                $arrdata['vendor_name'] = $vp->company_name;
                $arrdata['vendor_type'] = $vp->company_type;
                $arrdata['purchasing_org'] = $purch_org->org_code;
                $arrdata['purchasing_org_description'] = $purch_org->description;
                // $arrdata['recipient_name'] = $name;
                $arrdata['subject'] = $subject;
                $arrdata = (object) $arrdata;
                if ($this->vendorRepo->isValidEmail($recipients) && $this->vendorRepo->isValidEmail($ccs)){
                    ProcessEmail::dispatch($recipients, $ccs, $arrdata);
                    //Mail::to($recipients)->cc($ccs)->send(new TestMail($arrdata));
                }else{
                    $this->maillog("===========email failed==============. email :".json_encode($recipients).", cc:" .json_encode($ccs).", obj: ".json_encode($arrdata));
                }
            // }
        }
        if($emailType=='APPROVAL'){
            // $current = VendorSanction::where('vendor_profile_id',$sanction->vendor_profile_id)
            //             ->where('status','APPROVED')->first();
            // $sanctionType = ($current && $current->sanction_type=='RED') ? 'UNBLACKLIST' : $sanction->sanction_type;
            if(!is_null($old)){
                $sanctionType = ($old->sanction_type=='RED') ? 'UNBLACKLIST' : $sanction->sanction_type;
            }else{
                $sanctionType = $sanction->sanction_type;
            }
            $subject = str_replace('[NAME]',$vp->company_name,$this->emailConfig[$emailType]['subject'][$sanctionType]);
            foreach ($recipients as $name=>$email) {
                $arrdata = [];
                $arrdata['mailtype'] = $this->emailConfig[$emailType]['mailtype'][$sanctionType];
                $arrdata['vendor_name'] = $vp->company_name;
                $arrdata['vendor_type'] = $vp->company_type;
                $arrdata['vendor_code'] = $vendor->vendor_code;
                $arrdata['purchasing_org'] = $purch_org->org_code;
                $arrdata['purchasing_org_description'] = $purch_org->description;
                $arrdata['pic_name'] = $name;
                $arrdata['pic_email'] = $email;
                $arrdata['sanction_type'] = $sanction->sanction_type;
                $arrdata['valid_from_date'] = $sanction->valid_from_date;
                $arrdata['valid_thru_date'] = $sanction->valid_thru_date;
                $arrdata['sanction_type_description'] = $this->sanctionTypes[$sanction->sanction_type];
                // $arrdata['remarks'] = $request->comment ?? "";
                $arrdata['remarks'] = $sanction->description;
                $arrdata['recipient_name'] = $name;
                $arrdata['subject'] = $subject;
                $arrdata = (object) $arrdata;
                if ($this->vendorRepo->isValidEmail($email) && $this->vendorRepo->isValidEmail($ccs)){
                    ProcessEmail::dispatch($email, $ccs, $arrdata);
                }else{
                    $this->maillog("===========email failed==============. email :".json_encode($email).", cc:" .json_encode($ccs).", obj: ".json_encode($arrdata));
                }                
            }
        }

    }
}
