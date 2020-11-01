<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Buyer;
use App\RefPurchaseGroup;
use App\RefPurchaseOrg;
use App\User;
use App\Repositories\BuyerRepository;
use DB;
use Log;
use DataTables;

class BuyerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->buyer = new BuyerRepository();
    }

    function index(){
        return view('admin.buyer.list', [
            'fields' => explode(',','user_name,buyer_name,purch_org,purch_group,valid_from_date,valid_thru_date'),
            'purchGroups'=>RefPurchaseGroup::orderBy('description','asc')->get(),
            'purchOrgs'=>RefPurchaseOrg::orderBy('description','asc')->get(),
            'users'=>User::all(),
        ]);

    }
    public function store(Request $request){
        $this->validate($request, [
            'buyer_name' => 'required|string|max:50',
            'user_id' => 'required',
            'purch_org_id' => 'required|array|exists:ref_purchase_orgs,id',
            'purch_group_id' => 'required|array|exists:ref_purchase_groups,id',
            'valid_from_date' => 'required',
            'valid_thru_date' => 'required',
        ]);
        $buyer = $this->buyer->store($request);
        if($buyer!==false){
            return response()->json([
                'success'=>true, 
                'message' => 'Buyer: <strong>' . $request->buyer_name . '</strong> saved'
            ]);
        }else{
            return response()->json([
                'success'=>false, 
                'message' => 'Buyer: <strong>' . $request->buyer_name . '</strong> save FAILED'
            ]);
        }
    }
    public function store_old(Request $request){
        $this->validate($request, [
            'buyer_name' => 'required|string|max:50',
            'user_id' => 'required',
            'purch_org_id' => 'required',
            'purch_group_id' => 'required',
            'valid_from_date' => 'required',
            'valid_thru_date' => 'required',
        ]);
        if(isset($request->id)){
            //update
            $buyer = Buyer::find($request->id);
            $buyer->created_by = auth()->user()->name;
        }else{
            $buyer = new Buyer();
            $buyer->updated_by = auth()->user()->name;
        }

        $buyer->user_id = $request->user_id;
        $buyer->buyer_name = $request->buyer_name;
        $buyer->purch_org_id = $request->purch_org_id;
        $buyer->purch_group_id = $request->purch_group_id;
        $buyer->valid_from_date = $request->valid_from_date;
        $buyer->valid_thru_date = $request->valid_thru_date;
        $buyer->save();

        return response()->json([
            'success'=>true, 
            'message' => 'Buyer: <strong>' . $buyer->name . '</strong> saved'
        ]);
    }
    public function delete($id){
        $buyer = $this->buyer->delete($id);

        if($buyer!==false){
            return response()->json([
                'success'=>true, 
                'message' => 'Buyer: <strong>' . $buyer->name . '</strong> deleted'
            ]);
        }else{
            return response()->json([
                'success'=>false, 
                'message' => 'Delete buyer failed. Please Contact Administrator'
            ]);
        }
    }
    public function delete_old($id){
        $buyer = Buyer::find($id);
        $buyer->delete();

        return response()->json([
            'success'=>true, 
            'message' => 'Buyer: <strong>' . $buyer->name . '</strong> deleted'
        ]);
    }
    public function datatable_serverside(Request $request) {
        if (request()->ajax()) {
            $data = $this->buyer->getAll();
            return DataTables::of($data)
            ->addColumn('id', function($buyer){
                return 'id-'.$buyer->user_id;
            })
            ->make(true);
        }
    }
    public function datatable_serverside_old(Request $request) {
        if (request()->ajax()) {
            $data = Buyer::distinct('user_id')->get();
            return DataTables::of($data)
            ->make(true);
        }
    }
}
