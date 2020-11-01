<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\LegacyRepository;
use Log;

class LegacyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function legacy(){
        if(auth()->user()->hasRole('Super Admin')){
            return view('legacy');
        }else{
            return view('home');
        }
    }
    public function testLegacy(Request $request){
        if(auth()->user()->hasRole('Super Admin')){
            $legacyRepo = new LegacyRepository();
            // $result = $legacyRepo->migrateLegacyOld($request->maxData ?? null);

            $vfrom = $request->vendor_from ?? '';
            $vto = $request->vendor_to ?? '';
            $pfrom = $request->purchase_org_code_from ?? '';
            $pto = $request->purchase_org_code_to ?? '';

            $result = $legacyRepo->migrateLegacy([
                'max' => $request->maxData ?? 0,
                'vendor_from' => $vfrom,
                'vendor_to' => $vto,
                'purch_org_from' => $pfrom,
                'purch_org_to' => $pto,
            ]);
            
            Log::debug('====MIGRATION MESSAGE====');
            Log::debug($legacyRepo->migrationMessage);
            return json_encode([
                'success'=>true,
                'message'=>'Finish migrating data. Migrated data: '.$result,
                'data'=>$legacyRepo->migratedVendorData, 
                'sapMessage'=>$legacyRepo->sapMessage,
                'migrationMessage'=>$legacyRepo->migrationMessage
            ],true);
        }else{
            return json_encode([
                'success'=>false,
                'message'=>'Unauthorized', 
            ],true);
        }
    }
}
