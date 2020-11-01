<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Mail\TestMail;
use App\RefListOption;
use App\Vendor;
use App\VendorProfile;
use App\VendorSanction;
use App\VendorWorkflow;
use App\VendorHistoryStatus;
use App\VendorApproval;
use App\Repositories\VendorRepository;
use View;
use DB;
use DataTables;
use App\Traits\AccessLog;
//use Illuminate\Http\Request;

class VendorController extends Controller
{
    use AccessLog;
    public function __construct()
    {
        //
        $this->vendorRepo = new VendorRepository();
    }
    //
    public function index(){
        $this->middleware('auth');
        return view('vendor/main');
    }
    
    public function view_user_management(){
        $this->middleware('auth');
        $blacklisted = false;
        $user = auth()->user();
        $data = [];
        if($user->user_type=='vendor'){
            $profile = VendorProfile::where('vendor_id',$user->ref_id)->first();
            $blacklisted = $profile->is_blacklisted;
            $data['blacklisted'] = $blacklisted;
        }else{
            $data['blacklisted'] = false;
        }
        return View::make('auth.passwords.change', $data);
    }
    
    public function view_sanction(){
        $this->middleware('auth');
        $vendors = Vendor::select(
            'vendors.id',
            'vendors.vendor_name',
            DB::raw('count(vendor_sanctions.id) as sanction_count'),
            'vendors.president_director'
        )
        ->leftJoin('vendor_sanctions', function ($join) {
            $join->on('vendor_sanctions.vendor_profile_id', '=', 'vendors.id');
        })
        ->groupBy('vendors.id')
        ->get();

        $data = [
            'type'=>auth()->user()->user_type=='vendor',
            'sanctionTypes'=>RefListOption::where('type','sanction_types')->where('deleteflg',false)->pluck('value','key'),
            'vendors'=>$vendors,
            'storage' => asset('storage/vendor/sanctions'),
            'fields'=>['vendor_name','vendor_code','sanction_type','valid_from_date','valid_thru_date','letter_number','description'],
        ];
        return view('vendor.admin.sanction',$data);
    }
    public function sanction_data(){
        $this->middleware('auth');
        if (request()->ajax()) {
            $query = VendorSanction::select('*');
            if(auth()->user()->user_type=='vendor'){
                $vendorId = auth()->user()->ref_id;
                $profile = VendorProfile::where('vendor_id',$vendorId)->first();
                $query->where('vendor_profile_id',$profile->id);
            }

            $data = $query->get();
            return DataTables::of($data)
            ->make(true);
        }
    }
    public function store_sanction(Request $request){
        $this->middleware('auth');
        if($request->ajax()){
            //validation

            $success = false;
            try{
                DB::beginTransaction();
                if(is_null($request->id)){
                    $data = new VendorSanction();
                    $data->created_by = auth()->user()->name;
                }else{
                    $data = VendorSanction::find($request->id);
                    $data->updated_by = auth()->user()->name;
                }
                $data->vendor_profile_id = $request->vendor_profile_id;
                $data->sanction_type = $request->sanction_type;
                $data->valid_from_date = $request->valid_from_date;
                $data->valid_thru_date = $request->valid_thru_date;
                $data->letter_number = $request->letter_number;
                $data->description = $request->description;
                $data->status = '';

                if($request->file()>0){
                    if(!is_null($request->id)){
                        $old = VendorSanction::where('id',$request->id)->get();
                        if(count($old)>0){
                            foreach($request->file() as $key=>$file){
                                $filename = 'public/vendor/sanctions/'.$request->vendor_profile_id.'/'.$old[0]->$key;
                                Storage::delete($filename);
                            }
                        }
                    }
                }
                $continue = true;
                foreach($request->file() as $key=>$file){
                    $filename = 'public/vendor/sanctions/'.$request->vendor_profile_id.'/'.$file->getClientOriginalName();
                    if(Storage::exists($filename)){
                        $continue = false;
                        $message = "duplicate_file: ".$filename;
                    }
                }
                if($continue){
                    foreach($request->file() as $key=>$file){
                        $path = Storage::putFileAs('public/vendor/sanctions/'.$request->vendor_profile_id , $file, $file->getClientOriginalName() );
                        $data[$key] = $file->getClientOriginalName();
                    }
                }
                $data->save();

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
    
    // View Applicants
    public function view_vendors(Request $request){
        return View::make('vendor.list');
    }
    
    // Datatables ServerSide
    public function datatable_list_vendors(Request $request) {
        if (request()->ajax()) {
            // $vendor = DB::table('v_vendors')->skip($request->start)->take($request->length)->get();
            $query = $this->vendorRepo->getListVendorByType('vendor')
                    ->addSelect('vendor_workflows.activity')
                ->leftJoin('vendor_workflows', function ($join) {
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

}
