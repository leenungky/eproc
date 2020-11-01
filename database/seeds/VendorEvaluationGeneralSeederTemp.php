<?php

use Illuminate\Database\Seeder;
use App\VendorEvaluationGeneral;
use App\User;
use App\Repositories\BuyerRepository;

class VendorEvaluationGeneralSeederTemp extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $gens = VendorEvaluationGeneral::all();
        $repo = new BuyerRepository();
        foreach($gens as $general){
            $u = User::where('name',$general->created_by)->first();
            $purchorgs = $repo->getUserPurchaseOrganization($u,false)->toArray();
            $general->purchase_org_id = implode(",",$purchorgs);
            $general->save();
        }
    }
}
