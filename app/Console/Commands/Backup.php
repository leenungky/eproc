<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\TestMail;
use Carbon\Carbon;
use \DB;
use App\VendorProfileGeneral;
use App\VendorProfilePic;
use App\VendorProfile;
use App\Vendor;
use App\VendorProfileCertification;
use App\VendorProfileBusinessPermit;
use App\User;
use App\DocumentExpiry;


class Backup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tender Workflow Refresh';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Log::debug("begin expiry status ");
     
        
        // $first = VendorProfileBusinessPermit::where(DB::raw("(valid_thru_date - INTERVAL '30 DAYS')::timestamp::date"), '=', $now)->get();
        // $second = VendorProfileBusinessPermit::where(DB::raw("(valid_thru_date + INTERVAL '14 DAYS')::timestamp::date"), '=', $now)->get();
        // $third = VendorProfileBusinessPermit::where(DB::raw("(valid_thru_date + INTERVAL '30 DAYS')::timestamp::date"), '=', $now)->get();
        
        // $this->logicNotif($first, "first");
        // $this->logicNotif($second, "expired");
        // $this->logicNotif($third, "expired");


        // $valid = VendorProfileBusinessPermit::where("valid_thru_date", '>=', $now)
        //     ->where('valid_from_date', '>=', $now)
        //     ->get();        
        // $expiring = VendorProfileBusinessPermit::where("valid_thru_date",'>', $now)            
        //     ->get();
        Log::debug(" ============ business_permit ============== ");
        $data =  VendorProfileBusinessPermit::all();
        $this->logicData($data, "Business Permit");
        Log::debug(" ============ certification ============== ");
        $data =  VendorProfileCertification::all();
        $this->logicData($data, "Certification");
        Log::debug("=============================== end expiry status =======================");
    }

    public function logicData($data, $type){
        $now = Carbon::now();   
        $now =date_format($now, 'Y-m-d');
        foreach($data as $value){
            Log::info("========================== foreach :".$value->id);
            $expires_date = $this->getNewDateLessThanEndDate($value->valid_thru_date, "30 days");
            $expired_date1 = $this->getNewDateMoreThanEndDate($value->valid_thru_date, "14 days");
            $expired_date2 = $this->getNewDateMoreThanEndDate($value->valid_thru_date, "30 days");
            Log::info("now: ".$now.", expires_date :".$expires_date." , expired_date1 : ".$expired_date1.", expired_date2:".$expired_date2);           
            if ($value->valid_from_date <= $now && $now <= $value->valid_thru_date ){
                Log::info("value->valid_from_date <= now && now <= value->valid_thru_date ");
                if ($now < $expires_date ){
                    Log::info("now < expires_date");
                    $this->updateStatus($value, $type, "valid");
                }else{
                    $this->updateStatus($value, $type, "expiring"); 
                    if ($now == $expires_date){
                        Log::info("now == expires_date");
                        $this->logicNotif($value, $type, "expiring");
                    }
                }                
            }else if ($now > $value->valid_thru_date) {
                Log::info("now > value->valid_thru_date");
                $this->updateStatus($value, $type, "expired");
                if ($now == $expired_date1){
                    Log::info(" now == expired_date1 ");                    
                    $this->logicNotif($value, $type, "expired");
                }else if ($now == $expired_date2){
                    Log::info(" now == expired_date2 ");
                    $this->logicNotif($value, $type, "expired");
                }                
            }
        }
    }

    public function getNewDateLessThanEndDate($date, $param){
        $date = date_create($date);
        date_sub($date,date_interval_create_from_date_string($param));
        return date_format($date,"Y-m-d");
    }

    public function getNewDateMoreThanEndDate($date, $param){
        $date = date_create($date);
        date_add($date,date_interval_create_from_date_string($param));
        return date_format($date,"Y-m-d");
    }

    public function getDateLessThanEndDate($value, $param){
        $date=date_create($value->valid_thru_date);
        date_sub($date,date_interval_create_from_date_string($param));
        return date_format($date,"Y-m-d");
    }

    public function getDateMoreThanEndDate($value, $param){
        $date=date_create($value->valid_thru_date);
        date_add($date,date_interval_create_from_date_string($param));
        return date_format($date,"Y-m-d");
    }

    public function updateStatus($value, $type,  $status){
       
        $VPBPS = new DocumentExpiry;
        $VPBPS->vendor_business_permits_id  = $value->id;
        $VPBPS->vendor_profile_id           = $value->vendor_profile_id;
        $VPBPS->valid_from_date             = $value->valid_from_date;
        $VPBPS->valid_thru_date             = $value->valid_thru_date;   
        $VPBPS->current_date                = date("Y-m-d");     
        $VPBPS->status                      = $status;
        $VPBPS->type                        = $type;
        $VPBPS->created_by                  = "schedule job";
        $VPBPS->updated_by                  = "schedule job";
        if ($type=="Certification"){
            $VPBPS->document_type               = $value->certification_type." ".$value->description;
        }else{
            $VPBPS->document_type               = $value->business_permit_type;
        }
       
        $VPBPS->save();
    }

    public function logicNotif($value, $type, $status){
        $VP =  VendorProfile::select("vendor_id")->where("id", $value->vendor_profile_id)->first();
        $vendor = Vendor::where("id", $VP->vendor_id)->first();
        $pic = VendorProfilePic::where('vendor_profile_id',$value->vendor_profile_id)
            ->where('is_current_data',true)
            ->where('primary_data',true)
            ->first();
        $VG = VendorProfileGeneral::where('vendor_profile_id',$value->vendor_profile_id)
            ->where('is_current_data',true)
            ->where('primary_data',true)
            ->first();

        if (isset($vendor)){
            $arrdata["recipient"] = $pic->email ?? $vendor->pic_email;
            $arrdata["cc"] = User::role(['Admin Vendor'])->join("user_extensions","user_extensions.user_id","=","users.id")->where("user_extensions.status","=",1)->whereNotNull("users.email")->pluck('email')->toArray();
            // $arrdata["cc"] = "lee.nungky@gmail.com";
            // $arrdata["bcc"] = $arrdata["recipient"];        
            $arrdata['mailtype'] = "expiry_status";
            $arrdata['status'] = $status;
            $arrdata['company_name'] = $VG->company_name ?? $vendor->vendor_name;
            $arrdata["document_validityend"] = $value->valid_thru_date;
            $arrdata["companycode_description"] = $vendor->vendor_code;
            $arrdata['subject'] = "DOCUMENT EXPIRATIONS: expires on ".$value->valid_thru_date;
            if ($type=="Business Permit"){
                $arrdata["document_name"] = $value->business_permit_type;
                $arrdata['subject'] = "DOCUMENT EXPIRATIONS: ".$value->business_permit_type." expires on ".$value->valid_thru_date;
            }else{
                $arrdata["document_name"] = $value->certification_type." ".$value->description;
                $arrdata['subject'] = "DOCUMENT EXPIRATIONS: ".$value->certification_type." expires on ".$value->valid_thru_date;
            }
            
            if ($status=="expiring"){
                $arrdata["description"] = "will expire";
            }else if ($status=="expired"){
                $arrdata["description"] = "has expired";
            }
            
            $arrdata = (object) $arrdata;
            $this->kirim($arrdata);
        }
    }

    public function kirim($arrdata){
        Mail::to($arrdata->recipient)
        ->cc($arrdata->cc)
        //->bcc($arrdata->bcc)
        ->send(new TestMail($arrdata));
        Log::debug("Finish expiry document");
        echo PHP_EOL;
    }
}
