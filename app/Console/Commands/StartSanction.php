<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\RefListOption;
use App\RefPurchaseOrg;
use App\VendorSanction;
use App\VendorProfile;
use App\Vendor;
use App\Repositories\VendorSanctionRepository;
use Carbon\Carbon;
use \DB;


class StartSanction extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sanction:start';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Vendor Sanction Start';

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
        /**
         * Logic:
         * 1. Ambil sanction terakhir seluruh vendor yang valid_from_date <= now dan valid_thru_date >= now
         * 2. Lakukan Looping tiap vendor, Cek current sanction dan next sanction
         * 3. Jika current sanction = RED, maka lakukan unblock sap.
         * 4. Jika next sanction = RED, maka lakukan block sap.
         * 5. Simpan Perubahan Status ke tabel Vendor Profile
         */
        Log::debug("begin check sanction that needs to be started.. ");
     
        $now = Carbon::today();
        Log::debug($now);
        $dt = date_format($now, 'Y-m-d');
        Log::debug("=============================== sanction started at ".$dt." =======================");
        $dataStart = VendorSanction::whereIn('sanction_type',['YELLOW','RED','GREEN'])
                ->where('status','APPROVED')
                ->where('valid_from_date','<=',$dt)
                ->where('valid_thru_date','>=',$dt)
                ->orderBy('vendor_profile_id','asc')
                ->orderBy('valid_from_date','desc')
                ->orderBy('id','desc')
                ->get();
        //maksimum per vendor punya 2 line. pilih yang terbaru, dan hapus yang lama.
        $this->logicNotif($dataStart);

    }

    public function logicNotif($data){
        $sanctionTypes = RefListOption::where('type','sanction_types')->where('deleteflg',false)->pluck('value','key')->toArray();
        $repo = new VendorSanctionRepository();
        $currentProfileId = 0;
        $isCurrentProfileProcessed = true;

        foreach($data as $sanction){
            //1. proses id yg paling besar, karena diinput terakhir
            //   kalau id sama, hanya proses jika row sebelumnya tidak diproses
            if($currentProfileId!=$sanction->vendor_profile_id || ($currentProfileId==$sanction->vendor_profile_id && !$isCurrentProfileProcessed)){
                $currentProfileId = $sanction->vendor_profile_id;
                $isCurrentProfileProcessed = false;

                //2. hanya jika now masuk dalam range.
                $now = date('Y-m-d');
                if($now >= $sanction->valid_from_date && $now <= $sanction->valid_thru_date){
                    $vp = VendorProfile::find($sanction->vendor_profile_id);
                    $vendor = Vendor::find($vp->vendor_id);
                    $purch_org = RefPurchaseOrg::find($vendor->purchase_org_id);

                    // 3. hanya jika statusnya beda.
                    Log::debug("Sanction Id: ".$sanction->id.", Vendor Profile Id: ".$sanction->vendor_profile_id.", Status (old->new): ".$vp->company_warning." -> ".$sanction->sanction_type);
                    if($vp->company_warning != $sanction->sanction_type){

                        //kirim ke sap jika block/unblock//
                        if($sanction->sanction_type=='RED'){
                            Log::debug("Sending Blacklist information to SAP...");
                            $return = $repo->block([
                                'business_partner_code' => $vendor->business_partner_code,
                                'sap_vendor_code' => $vendor->sap_vendor_code,
                                'org_code' => $purch_org->org_code
                            ]);
                            Log::debug($return);
                        }else{
                            if($vp->company_warning=='RED'){
                                Log::debug("Sending Unblacklist information to SAP...");
                                $return = $repo->unblock([
                                    'business_partner_code' => $vendor->business_partner_code,
                                    'sap_vendor_code' => $vendor->sap_vendor_code,
                                    'org_code' => $purch_org->org_code
                                ]);
                                Log::debug($return);
                            }
                        }
        
                        //change vp status
                        $vp->company_warning=$sanction->sanction_type;
                        $vp->save();

                        //set diproses = true agar yang lain dihapus
                        Log::debug("diproses");
                        $isCurrentProfileProcessed = true;
                    }
                }else{
                    if($now > $sanction->vendor_thru_date){
                        //hapus karena sudah lewat. cek yg next.
                        Log::debug("dihapus karena sudah lewat");
                        $sanction->delete();
                    }
                }

            }else{
                //hapus yang lain, karena sangsi untuk vendor ini sudah ada yg dijalankan.
                Log::debug("hapus yang lain");
                $sanction->delete();
            }

        }

    }

}
