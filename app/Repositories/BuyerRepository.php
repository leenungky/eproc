<?php

namespace App\Repositories;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

use App\Buyer;
use App\RefPurchaseOrg;
use App\RefPurchaseGroup;
use App\Models\Ref\RefBuyer;
use App\Models\Ref\RefBuyerPurchOrg;
use App\Models\Ref\RefBuyerPurchGroup;

class BuyerRepository extends BaseRepository
{
    private $logName = 'BuyerRepository';

    public function userIsBuyer($user){
        //pengecekan apakah user ini masih merupakan buyer atau bukan.
        return(count($this->getUserPurchaseOrganization($user)) > 0);
    }

    public function userHavePurchaseOrganization($user, $purchase_org_id){
        $result = false;
        if(is_null($user)) return false;
        if($user->hasRole("Super Admin")){
            $result = true;
        }else{
            $result = RefBuyerPurchOrg::where('purch_org_id',$purchase_org_id)
            ->where('ref_buyer_purch_orgs.user_id',$user->id)
            ->join('ref_buyers', function($query){
                $query->on('ref_buyers.user_id','=','ref_buyer_purch_orgs.user_id')
                ->where('ref_buyers.valid_from_date','<=',now())
                ->where('ref_buyers.valid_thru_date','>=',now());
            })
            ->count() > 0;
        }
        return $result;
    }
    public function getUserPurchaseOrganization($user, $withValid=true){
        if(is_null($user)) return collect(null);
        if($user->hasRole("Super Admin")){
            $result = RefPurchaseOrg::pluck('id');
        }else{
            $result = RefBuyerPurchOrg::where('ref_buyer_purch_orgs.user_id',$user->id)
            ->join('ref_buyers', function($query) use ($withValid){
                $query->on('ref_buyers.user_id','=','ref_buyer_purch_orgs.user_id');
                if($withValid){
                    $query
                    ->where('ref_buyers.valid_from_date','<=',now())
                    ->where('ref_buyers.valid_thru_date','>=',now());
                }
            })
            ->pluck('purch_org_id');
        }
        // Log::debug(__CLASS__.":: Purch org Id for user ".$user->userid."(".$user->name."): ".$result->toJson());
        return $result;
    }

    public function getAll(){
        return RefBuyer::all();
    }

    /**
     * find data
     *
     * @return \Illuminate\Database\Eloquent\Builder
     *
     * @throws \Exception
     */
    public function getAllByPurchaseOrg($orgId = ""){
        $query = RefBuyer::join('ref_buyer_purch_orgs','ref_buyer_purch_orgs.user_id','ref_buyers.user_id')
                    ->where('ref_buyers.valid_from_date','<=',now())
                    ->where('ref_buyers.valid_thru_date','>=',now());
        if(!empty($orgId)){
            $query = $query->where('ref_buyer_purch_orgs.purch_org_id', $orgId);
        }
        return $query;
    }

    public function store($input){
        $success = false;

        try {
            DB::beginTransaction();
            $bdata = [
                'user_id'=>$input->user_id,
                'buyer_name'=>$input->buyer_name,
                'valid_from_date'=>$input->valid_from_date,
                'valid_thru_date'=>$input->valid_thru_date,
            ];
            $exist = RefBuyer::where('user_id',$input->user_id)->count() > 0;
            if($exist){
                //update
                $buyer = RefBuyer::where('user_id',$input->user_id)->first();
                $bdata['updated_by'] = auth()->user()->id;
            }else{
                //create
                $buyer = new RefBuyer();
                $bdata['created_by'] = auth()->user()->id;
            }

            $buyer->fill($bdata);
            $buyer->save();


            //purch org and purch group
            RefBuyerPurchOrg::where('user_id',$input->user_id)->delete();
            RefBuyerPurchGroup::where('user_id',$input->user_id)->delete();
            $purchOrgs = [];
            foreach($input->purch_org_id as $orgId){
                $purchOrgs[] = [
                    'user_id'=>$input->user_id,
                    'purch_org_id'=>$orgId
                ];
            }
            if(count($purchOrgs)>0) RefBuyerPurchOrg::insert($purchOrgs);
            $purchGrps = [];
            foreach($input->purch_group_id as $grpId){
                $purchGrps[] = [
                    'user_id'=>$input->user_id,
                    'purch_group_id'=>$grpId
                ];
            }
            if(count($purchGrps)>0) RefBuyerPurchGroup::insert($purchGrps);
            DB::commit();
            $success = $buyer;
        } catch (Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());
        }
        return $success;
    }
    public function delete($buyerId){
        $buyer = RefBuyer::find($buyerId);
        $success = false;

        try {
            DB::beginTransaction();

            $buyer->delete();
            RefBuyerPurchOrg::where('user_id',$buyerId)->delete();
            RefBuyerPurchGroup::where('user_id',$buyerId)->delete();

            DB::commit();
            $success = $buyer;
        } catch (Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());
        }
        return $success;
    }
}
