<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\TestMail;
use App\RefListOption;
use App\RefPurchaseOrg;
use App\VendorSanctionExpiration;
use App\VendorSanctionWorkflow;
use App\VendorSanctionHistory;
use App\VendorSanction;
use App\VendorProfilePic;
use App\VendorProfile;
use App\Vendor;
use App\SapConnector;
use App\User;
use App\Repositories\VendorSanctionRepository;
use App\Jobs\ProcessEmail;
use Carbon\Carbon;
use \DB;


class ExpirySanction extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sanction:expiry';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Vendor Sanction Refresh';

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
        Log::debug("begin expiry sanction ");
     
        $now = Carbon::today();
        $expiring = $now->addDays(5);
        Log::debug($now);
        Log::debug("=============================== sanction expiring at ".date_format($expiring, 'Y-m-d')." =======================");
        $dataExpiring = VendorSanction::where('status','APPROVED')
                ->where('valid_thru_date',date_format($expiring, 'Y-m-d'))
                ->whereIn('sanction_type',['YELLOW','RED']) //green tidak perlu dicek
                ->orderBy('vendor_profile_id','asc')
                ->orderBy('id','asc')
                ->get();
        $this->logicNotif($dataExpiring,'expiring');

        $now = Carbon::today();
        $expired = $now->subDays(1);
        Log::debug("=============================== sanction expired at ".date_format($expired, 'Y-m-d')." and before =======================");
        $dataExpired = VendorSanction::where('status','APPROVED')
                ->where('valid_thru_date','<=',date_format($expired, 'Y-m-d'))
                ->whereIn('sanction_type',['YELLOW','RED']) //green tidak perlu dicek
                ->orderBy('vendor_profile_id','asc')
                ->orderBy('id','asc')
                ->get();
        $this->logicNotif($dataExpired,'expired');

        Log::debug("=============================== end expiry sanction =======================");
    }

    public function logicNotif($data, $type){
        $ccs = User::role(['Procurement Manager','Admin Vendor'])->join("user_extensions","user_extensions.user_id","=","users.id")->where("user_extensions.status","=",1)->whereNotNull("users.email")->pluck('email')->toArray();
        $sanctionTypes = RefListOption::where('type','sanction_types')->where('deleteflg',false)->pluck('value','key')->toArray();
        $repo = new VendorSanctionRepository();
        foreach($data as $sanction){
            //save to sanctionexpiration
            VendorSanctionExpiration::updateOrCreate(
            [
                'vendor_sanction_id' => $sanction->id,
                'vendor_profile_id' => $sanction->vendor_profile_id,
                'expiration_status' => $type
            ],
            [
                'vendor_sanction_id' => $sanction->id,
                'vendor_profile_id' => $sanction->vendor_profile_id,
                'sanction_type' => $sanction->sanction_type,
                'valid_from_date' => $sanction->valid_from_date,
                'valid_thru_date' => $sanction->valid_thru_date,
                'status' => $sanction->status,
                'expiration_status' => $type
            ]);
            //

            Log::debug("Sanction Id: ".$sanction->id.", Vendor Profile Id: ".$sanction->vendor_profile_id);
            $vp = VendorProfile::find($sanction->vendor_profile_id);
            $vendor = Vendor::find($vp->vendor_id);
            $purch_org = RefPurchaseOrg::find($vendor->purchase_org_id);
            $pic = VendorProfilePic::where('vendor_profile_id',$sanction->vendor_profile_id)
                ->where('is_current_data',true)
                ->where('primary_data',true)
                ->first();
            $lastHistory = VendorSanctionHistory::where('vendor_profile_id',$sanction->vendor_profile_id)
                ->where('vendor_sanction_id',$sanction->id)
                ->orderBy('id','desc')->first();

            $recipients = [$pic->full_name => $pic->email];
            $arrdata = [];
            $arrdata['sanction_valid_thru_date'] = $sanction->valid_thru_date;
            if($type=='expiring'){
                if($sanction->sanction_type=='YELLOW'){
                    $arrdata['mailtype'] = "sanction_expiring_yellow";
                    $arrdata['subject'] = 'WARNING: Vendor Sanction '.$vp->company_name;
                }else if($sanction->sanction_type=='RED'){
                    $arrdata['mailtype'] = "sanction_expiring_red";
                    $arrdata['subject'] = 'BLACKLISTED: Vendor Sanction '.$vp->company_name;
                }
                $arrdata['sanction_type'] = $sanction->sanction_type;
                $arrdata['sanction_type_description'] = $sanctionTypes[$sanction->sanction_type];
            }else if($type=='expired'){
                //if was blacklisted, send to SAP//
                if($sanction->sanction_type=='RED'){
                    Log::debug("Sending Unblacklist information to SAP...");
                    $return = $repo->unblock([
                        'business_partner_code' => $vendor->business_partner_code,
                        'sap_vendor_code' => $vendor->sap_vendor_code,
                        'org_code' => $purch_org->org_code
                    ]);
                    Log::debug($return);
                }
                //delete current sanction
                $sanction->delete();

                //new sanction 
                $sanctionType = 'GREEN';
                $newSanction = VendorSanction::create([
                    'vendor_profile_id' => $sanction->vendor_profile_id,
                    'sanction_type' => $sanctionType,
                    'valid_from_date' => date('Y-m-d'),
                    'valid_thru_date' => date('Y-12-31'),
                    'letter_number' => '',
                    'description' => '',
                    'attachment' => '',
                    'status' => 'APPROVED',
                    'created_by' => -1,
                    'updated_by' => -1
                ]);

                //change vp to green
                $vp->company_warning=$sanctionType;
                $vp->save();

                //add workflow comment history
                $vendorSanctionWorkflow = new VendorSanctionWorkflow([
                    'vendor_id' => $vendor->id,
                    'vendor_sanction_id' => $newSanction->id,
                    'activity' => 'Sanction Expiration has changed the Sanction Type become GREEN (No Warning)',
                    'remarks' => '',
                    'started_at' => now(),
                    'finished_at' => now(),
                    'created_by' => 'schedule job',
                    'updated_by' => 'schedule job'
                ]);
                $vendorSanctionWorkflow->save();

                $arrdata['mailtype'] = "sanction_expired_unblacklist";
                $arrdata['sanction_type'] = $sanctionType;
                $arrdata['sanction_type_description'] = $sanctionTypes[$sanctionType];
                $arrdata['subject'] = 'UNBLACKLIST: Vendor Sanction '.$vp->company_name;
            }

            $arrdata['vendor_name'] = $vp->company_name;
            $arrdata['vendor_type'] = $vp->company_type;
            $arrdata['vendor_code'] = $vendor->vendor_code;
            $arrdata['purchasing_org'] = $purch_org->org_code;
            $arrdata['purchasing_org_description'] = $purch_org->description;
            $arrdata['valid_from_date'] = $sanction->valid_from_date;
            $arrdata['valid_thru_date'] = $sanction->valid_thru_date;
            $arrdata['pic_name'] = $pic->full_name;
            $arrdata['pic_email'] = $pic->email;
            $arrdata['remarks'] = $sanction->description;
            $arrdata['recipient_name'] = $pic->full_name;

            Log::debug("Email to: ".$pic->email."(".$pic->full_name."), Subject: ".$arrdata['subject']);
            ProcessEmail::dispatch($recipients, $ccs, (object)$arrdata);
        }

    }

}
