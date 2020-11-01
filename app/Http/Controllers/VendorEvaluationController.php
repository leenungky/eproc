<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;
use App\Mail\TestMail;
use App\Repositories\VendorRepository;
use App\Repositories\BuyerRepository;
use App\RefListOption;
use App\RefProject;
use App\RefPurchaseOrg;
use App\User;
use App\Vendor;
use App\VendorProfile;
use App\VendorProfilePic;
use App\VendorEvaluationScore;
use App\VendorEvaluationScoreCategory;
use App\VendorEvaluationCriteria;
use App\VendorEvaluationCriteriaGroup;
use App\VendorEvaluationGeneral;
use App\VendorEvaluationAssignment;
use App\VendorEvaluationHistory;
use App\VendorEvaluationForm;
use App\VendorEvaluationFormDetail;
use App\VendorEvaluationWorkflow;
use App\PurchaseOrderHeader;
use App\PurchaseOrderItem;
use App\Traits\AccessLog;
use App\Jobs\ProcessEmail;

use View;
use DB;
use DataTables;
use Validator;

class VendorEvaluationController extends Controller
{
    use AccessLog;
    public $workflow = [];
    public $statuses = [];
    public $pages = [];
    public $vendorSelector = [];
    public $emailConfig = [];
    public $scoreAssignmentByCriteria = true;

    public function __construct(){
        $this->middleware('auth');
        $this->vendorRepo = new VendorRepository();
        $this->getVendor = $this->vendorRepo->getListVendorByType('candidate');
        $this->buyerRepo = new BuyerRepository();

        $config = config('eproc.vendor_evaluation');
        $this->workflow = $config['workflow'];
        $this->statuses = $config['statuses'];
        $this->pages = $config['pages'];
        $this->vendorSelector = $config['vendor_selector'];
        $this->emailConfig = $config['email_config'];
        $this->scoreAssignmentByCriteria = $config['score_assignment']=='criteria';
    }



    public function score(){
        $data = [
            'fields'=>['name','created_at'],
            'selectors'=>$this->vendorSelector,
            'isBuyerActive'=>$this->buyerRepo->userIsBuyer(auth()->user())
        ];
        return view('vendor.evaluation.list_score',$data);
    }
    public function score_data(){
        if (request()->ajax()) {
            $tmp = VendorEvaluationScoreCategory::all();
            $data = [];
            foreach($tmp as $item){
                $data[] = [
                    'id'=>$item->id,
                    'name'=>$item->name,
                    'categories_json'=>$item->categories_json,
                    'po_total'=>$item->po_total,
                    'po_count'=>$item->po_count,
                    'created_at'=>$item->created_at->toDateTimeString(),
                    'scores'=>$item->scores,
                ];
            }
            return DataTables::of($data)
            ->make(true);
        }
    }
    public function score_store(Request $request){
        if($request->ajax()){
            //validation
            try{
                DB::beginTransaction();
                $success = false;
                if(isset($request->id)){
                    //update
                    $score = VendorEvaluationScoreCategory::find($request->id);
                    $score->updated_by = auth()->user()->name;
                }else{
                    $score = new VendorEvaluationScoreCategory();
                    $score->created_by = auth()->user()->name;
                }
                $score->name = $request->name;
                $score->categories_json = $request->categories_json;
                $score->po_total = $request->po_total ?? 0;
                $score->po_count = $request->po_count ?? 0;
                $score->save();
                // $categories = [];
                VendorEvaluationScore::where('category_id',$score->id)->whereNotIn('name',$request->nm)->delete();
                foreach($request->nm as $key=>$val){
                    $category = [
                        'category_id'=>$score->id,
                        'name' => $request->nm[$key],
                        'lowest_score_operator' => $request->lso[$key],
                        'lowest_score' => $request->ls[$key],
                        'highest_score_operator' => $request->hso[$key],
                        'highest_score' => $request->hs[$key],
                    ];
                    VendorEvaluationScore::updateOrCreate(['category_id'=>$category['category_id'],'name'=>$category['name']],$category);
                }
                DB::commit();
                $success=true;
            }catch(Exception $e){
                DB::rollback();
            }

            return response()->json([
                'success'=>$success,
                'message' => 'Score: <strong>' . $score->name . '</strong>'.($success ? '':' NOT').' saved',
            ]);
        }
    }
    public function score_delete($id){
        $score = VendorEvaluationScoreCategory::findOrFail($id);
        //soft delete, so no need to delete VendorScore;
        $score->delete();
        return response()->json([
            'success'=>true,
            'message' => 'Score: <strong>' . $score->name . '</strong> deleted'
        ]);
    }



    public function criteria_group(){
        $data = [
            'fields'=>['name','created_at'],
            'isBuyerActive'=>$this->buyerRepo->userIsBuyer(auth()->user())
        ];
        return view('vendor.evaluation.list_criteria_group',$data);
    }
    public function criteria_group_data(){
        if (request()->ajax()) {
            $data = $this->criteria_group_assignment_query()->get();
            return DataTables::of($data)
            ->make(true);
        }
    }
    public function criteria_group_store(Request $request){
        if($request->ajax()){
            //validation
            if(isset($request->id)){
                //update
                $cg = VendorEvaluationCriteriaGroup::find($request->id);
                $cg->updated_by = auth()->user()->name;
            }else{
                $cg = new VendorEvaluationCriteriaGroup();
                $cg->created_by = auth()->user()->name;
            }
            $cg->name = $request->name;
            $cg->save();

            return response()->json([
                'success'=>true,
                'message' => 'Group: <strong>' . $cg->name . '</strong> saved'
            ]);
        }
    }
    public function criteria_group_delete($id){
        $cg = VendorEvaluationCriteriaGroup::findOrFail($id);
        $criteria = VendorEvaluationCriteria::where('criteria_group_id',$id)->count();
        if($criteria>0){
            return response()->json([
                'success'=>false,
                'message' => 'Group: <strong>' . $cg->name . '</strong> is still in use'
            ]);
        }else{
            $cg->delete();
            return response()->json([
                'success'=>true,
                'message' => 'Group: <strong>' . $cg->name . '</strong> deleted'
            ]);
        }
    }
    public function criteria_group_assignment_query(){
        return VendorEvaluationCriteriaGroup::select(
            'vendor_evaluation_criteria_groups.*',
            DB::raw("sum(c.weighting) as total_weighting")
        )->leftJoin('vendor_evaluation_criterias as c', function($join){
            $join->on('c.criteria_group_id','=','vendor_evaluation_criteria_groups.id')
                ->whereNull('c.deleted_at');
        })
        ->groupBy('vendor_evaluation_criteria_groups.id');
    }
    public function criteria_group_json(){
        return response()->json([
            'success'=>true,
            'data'=>$this->criteria_group_assignment_query()->get(),
            'message' => 'done',
        ]);
    }



    public function criteria(){
        $data = [
            'fields'=>
                $this->scoreAssignmentByCriteria
                ? ['name','criteria_group_name','description','weighting','minimum_score','maximum_score','created_at']
                : ['name','criteria_group_name','description','created_at'],
            'criteriaGroups'=>$this->criteria_group_assignment_query()->get(),
            'isBuyerActive'=>$this->buyerRepo->userIsBuyer(auth()->user()),
            'scoreAssignment'=>$this->scoreAssignmentByCriteria
        ];
        return view('vendor.evaluation.list_criteria',$data);
    }
    public function criteria_data(){
        if (request()->ajax()) {
            $data = VendorEvaluationCriteria::orderBy('criteria_group_id','asc')->orderBy('id','asc')->get();
            return DataTables::of($data)
            ->make(true);
        }
    }
    public function criteria_store(Request $request){
        if($request->ajax()){
            //validation
            $rules = [
                'name' => 'required',
                'description' => 'required',
                'criteria_group_id' => 'required|numeric'
            ];
            if($this->scoreAssignmentByCriteria){
                $rules = array_merge($rules, [
                    'weighting' => 'required|numeric',
                    'minimum_score' => 'required|numeric',
                    'maximum_score' => 'required|numeric'
                ]);
            }
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json([
                    'status' => 200,
                    'success' => false,
                    'message' => $validator->errors()->first(),
                ], 200);
                exit();
            }

            if(isset($request->id)){
                //update
                $criteria = VendorEvaluationCriteria::find($request->id);
                $criteria->updated_by = auth()->user()->name;
                $isCreate = false;
            }else{
                $criteria = new VendorEvaluationCriteria();
                $criteria->created_by = auth()->user()->name;
                $isCreate = true;
            }
            $criteria->name = $request->name;
            $criteria->description = $request->description;
            $criteria->criteria_group_id = $request->criteria_group_id;

            if($this->scoreAssignmentByCriteria){
                $criteria->weighting = $request->weighting ?? 0;
                $criteria->minimum_score = $request->minimum_score ?? 0;
                $criteria->maximum_score = $request->maximum_score ?? 100;
                $continue = $this->criteria_can_save_score($criteria, $isCreate);
                $message = $continue ? '' : "Can't save criteria. Total weighting over 100";
            }else{
                $continue = true;
                $message = '';
            }

            if($continue){
                $criteria->save();

                return response()->json([
                    'success'=>true,
                    'message' => 'Criteria: <strong>' . $criteria->name . '</strong> saved'
                ]);
            }else{
                return response()->json([
                    'success'=>false,
                    'message' => $message
                ]);
            }
        }
    }
    public function criteria_delete($id){
        $criteria = VendorEvaluationCriteria::findOrFail($id);
        $criteria->delete();
        return response()->json([
            'success'=>true,
            'message' => 'Criteria: <strong>' . $criteria->name . '</strong> deleted'
        ]);
    }
    public function criteria_can_save_score($data,$isCreate=true){
        $query = $this->criteria_group_assignment_query()
            ->where('criteria_group_id',$data->criteria_group_id);
        if(!$isCreate){
            $query->where('c.id','<>',$data->id);
        }
        $output = $query->first();
        if(is_null($output)){
            return $data->weighting<=100;
        }else{
            return $output->total_weighting + $data->weighting <= 100;
        }
    }



    public function evaluation(){
        $data = [
            'fields'=>['name','description','category_name','status','start_date','end_date'],
            'scoreCategories'=>VendorEvaluationScoreCategory::pluck('name','id'),
            'categories'=>VendorEvaluationScoreCategory::select('id','name','categories_json')->get(),
            'criteriaGroups'=>VendorEvaluationCriteriaGroup::select('id','name')->get(),
            'projects'=>RefProject::orderBy('code')->get(),
            'isBuyerActive'=>$this->buyerRepo->userIsBuyer(auth()->user()),
            'scoreAssignment'=>$this->scoreAssignmentByCriteria
        ];
        return view('vendor.evaluation.list_evaluation',$data);
    }
    public function evaluation_data(){
        if (request()->ajax()) {
            $data = VendorEvaluationGeneral::all();
            return DataTables::of($data)
            ->make(true);
        }
    }
    public function evaluation_store_finish(Request $request){
        return $this->evaluation_store($request,true);
    }
    public function evaluation_store(Request $request, $finish=false){
        if($request->ajax()){
            //validation
            $success = false;
            $message = "";
            $userid = auth()->user()->userid;
            try{
                DB::beginTransaction();
                if(isset($request->id)){
                    //update
                    $general = VendorEvaluationGeneral::find($request->id);
                    $update = true;
                    $oldCriteriaGroup = $general->criteria_group_id;
                    $general->updated_by = auth()->user()->userid;
                }else{
                    $general = new VendorEvaluationGeneral();
                    $update = false;
                    $oldCriteriaGroup = null;
                    $general->created_by = auth()->user()->userid;
                }

                $general->created_by = auth()->user()->name;
                $general->name = $request->name;
                $general->description = $request->description;
                $general->project_code = $request->project_code ?? '';
                $general->start_date = $request->start_date;
                $general->year = $request->year;
                $general->end_date = $request->end_date;
                $general->category_id = $request->category_id;
                $general->criteria_group_id = $request->criteria_group_id;
                $general->status = $this->statuses['CONCEPT'];
                $general->is_finished = $finish ? 1 : 0;

                if(is_null($general->purchase_org_id)){
                    $orgs = $this->buyerRepo->getUserPurchaseOrganization(auth()->user())->toArray();
                    Log::debug("user purch org:".json_encode($orgs));
                    if(count($orgs)>0){
                        $general->purchase_org_id = implode(",",$orgs);
                    }
                }
                $general->save();

                if(!is_null($general->criteria_group_id)){
                    //Reset Assignment Form if new or update new criteria group
                    if(!$update || ($update && $oldCriteriaGroup != $general->criteria_group_id)){
                        $this->resetAssignmentForm($general->id, $general->criteria_group_id);
                    }
                }

                //Reset Evaluation Form
                VendorEvaluationFormDetail::where('vendor_evaluation_id',$general->id)->delete();
                VendorEvaluationForm::where('vendor_evaluation_id',$general->id)->delete();

                //add to history //20200723 only insert history for main process
                $history['vendor_evaluation_id'] = $general->id;
                $history['username'] = auth()->user()->name;
                $history['pic'] = $request->pic;
                $history['activity_date'] = now();
                if(isset($request->id)){
                    //update
                    $history['activity'] = 'general-finished';
                    $history['comments'] = "finish";
                }else{
                    $history['activity'] = 'general-created';
                    $history['comments'] = "create";
                    $this->finishActivity($history,'manual');
                }

                $success = true;
                $message = 'Evaluation Project: <strong>' . $general->name . '</strong> saved';
                DB::commit();
            }catch(Exception $e){
                $message = 'Evaluation Project: <strong>' . $general->name . '</strong> not saved';
                DB::rollback();
            }

            return response()->json([
                'success' => $success,
                'message' => $message,
                'data' => ['id'=>$general->id],
            ]);
        }
    }
    private function resetAssignmentForm($generalId, $criteriaGroupId){
        VendorEvaluationAssignment::where('vendor_evaluation_id',$generalId)->delete();
        $criterias = VendorEvaluationCriteria::where('criteria_group_id',$criteriaGroupId)->get();
        $rows = [];
        $sequence = 0;
        foreach($criterias as $criteria){
            $sequence++;
            $rows[] = [
                'vendor_evaluation_id' => $generalId,
                'criteria_id' => $criteria->id,
                'weighting' => $criteria->weighting,
                'minimum_score' => $criteria->minimum_score,
                'maximum_score' => $criteria->maximum_score,
                'sequence' => $sequence,
            ];
        }
        VendorEvaluationAssignment::insert($rows);
    }
    public function evaluation_comment_history(){
        $query = VendorEvaluationWorkflow::select(
                        'vendor_evaluation_workflows.*',
                        DB::raw('COALESCE(users.userid, \'system\') as userid'),
                        'users.name'
                    )
                    ->leftJoin('users', function($join){
                        $join->on('users.userid', '=', 'vendor_evaluation_workflows.created_by');
                    });
        return $query;
    }
    public function evaluation_detail($id,$type='general'){
        $pages = $this->pages;
        foreach($pages as $page=>$value){
            if($page=='form'){
                $dt = VendorEvaluationAssignment::select(
                    DB::raw("sum(weighting) as total_weight")
                )
                ->where('vendor_evaluation_id',$id)
                ->first();
                if($dt->total_weight == 100) $availables[$page] = $value;
            }else{
                $availables[$page] = $value;
            }
        }
        $general = VendorEvaluationGeneral::find($id);
        $creatorPurchOrgs = is_null($general->purchase_org_id) ? [] : explode(",", $general->purchase_org_id);
        $isBuyerActive = $this->buyerRepo->userIsBuyer(auth()->user());

        $samePurchOrg = false;
        $userPurchOrgs = $this->buyerRepo->getUserPurchaseOrganization(auth()->user())->toArray();
        foreach($userPurchOrgs as $purchOrg){
            if(in_array($purchOrg, $creatorPurchOrgs)) $samePurchOrg = true;
        }

        $data = [
            'general'=>$general,
            'id'=>$id,
            'type'=>$type,
            'pages'=>$pages,
            'availablePages'=>$availables,
            'samePurchOrg'=>$samePurchOrg,
            'isBuyerActive'=>$isBuyerActive,
            'scoreAssignment'=>$this->scoreAssignmentByCriteria,
            'criteriaGroups'=>VendorEvaluationCriteriaGroup::select('id','name')->get(),
        ];

        //checking. if type is form and assignment is empaty, then redirect to assignment instead of form//
        if($type=='form' && VendorEvaluationAssignment::where('vendor_evaluation_id',$id)->count()==0){
            return redirect()->route('vendor.evaluation.evaluation_detail', ['id' => $id]);
        }
        if($type=='general'){
            $data['scoreCategories'] = VendorEvaluationScoreCategory::pluck('name','id');
            $data['categories'] = VendorEvaluationScoreCategory::select('id','name','categories_json')->get();
            $data['projects'] = RefProject::orderBy('code')->get();
            $data['histories'] = VendorEvaluationHistory::select('*')->where('vendor_evaluation_id',$id)->orderBy('activity_date','desc')->get();
            $data['commentHistories'] = $this->evaluation_comment_history()
                ->where('vendor_evaluation_id',$id)
                ->orderBy('id','desc')->get();
        }
        if($type=='assignment'){
            $data['fields'] = ['criteria_name','weighting','minimum_score','maximum_score','sequence'];
            if(!is_null($general->criteria_group_id)){
                $data['criterias'] = VendorEvaluationCriteria::where('criteria_group_id',$general->criteria_group_id)->pluck('name','id');
            }else{
                $data['criterias'] = VendorEvaluationCriteria::pluck('name','id');
            }
        }
        if($type=='form'){
            //add vendor only if forms is empty//
            if(VendorEvaluationForm::where('vendor_evaluation_id',$id)->count()==0){

                $purch_orgs = $this->buyerRepo->getUserPurchaseOrganization(auth()->user());
                $query = PurchaseOrderHeader::select(
                    'purchase_order_headers.vendor_id',
                    DB::raw('sum(total) as po_sum'),
                    DB::raw('count(number) as po_cnt')
                )
                ->join('vendors', function($join) use ($purch_orgs){
                    $join->on('vendors.id','=','purchase_order_headers.vendor_id')
                        ->where('vendors.registration_status','vendor')
                        ->whereIn('vendors.purchase_org_id',$purch_orgs);
                });
                // ->join('vendor_profiles', function($join){
                //     $join->on('vendors.id','=','vendor_profiles.vendor_id')
                //         ->where('vendor_profiles.company_warning','<>','RED');
                // });
                if($general->category_type=='YEARLY'){
                    $query->whereBetween('date',[$general->start_date,$general->end_date])
                    ->groupBy('purchase_order_headers.vendor_id')
                    ->havingRaw('sum(purchase_order_headers.total) >= ? or count(purchase_order_headers.number) >= ?',[$general->po_total,$general->po_count]);
                }else if($general->category_type=='PROJECT'){
                    $query->whereIn('number',function($query) use ($general){
                        $query->select('number')
                              ->from('purchase_order_items')
                              ->where('project_code',$general->project_code);
                    })
                    ->whereBetween('date',[$general->start_date,$general->end_date])
                    ->groupBy('purchase_order_headers.vendor_id');
                }else{
                    $query->groupBy('purchase_order_headers.vendor_id');
                }

                //run query to get vendor data and put into vendorevaluationform
                $vendors = $query->get();

                $evalForms = [];
                foreach($vendors as $vendor){
                    $evalForms[] = [
                        'vendor_evaluation_id'=>$id,
                        'vendor_id'=>$vendor->vendor_id,
                        'total_po_document'=>$vendor->po_cnt,
                        'total_po_value'=>$vendor->po_sum,
                        'total_score'=>0,
                        'evaluated_by'=>auth()->user()->name,
                    ];
                }

                VendorEvaluationForm::insert($evalForms);
            }
            $data['fields'] = ['vendor_code','company_name','total_score','score_categories_name','city','province','country','purchasing_organization',
                                'sanction_type','company_status','year','project_code','start_date','end_date','evaluated_by'];
            $data['fieldSizes'] = [50,300,50,50,150,150,150,130,
                                150,50,40,150,70,70,100];

            $data['sanctionTypes']=RefListOption::where('type','sanction_types')->where('deleteflg',false)->pluck('value','key');
            $data['assignments']=VendorEvaluationAssignment::where('vendor_evaluation_id',$id)->get();
        }
        return view('vendor.evaluation.detail_'.$type,$data);
    }
    public function evaluation_detail_data(Request $request,$id,$type){
        if (request()->ajax()) {
            if($type=='assignment'){
                $data = VendorEvaluationAssignment::where('vendor_evaluation_id',$id)->orderBy('sequence')->get();
                return DataTables::of($data)
                ->make(true);
            }else if($type=='form'){
                $closure = Vendor::getCurrent();
                $data = $closure->select(
                    DB::raw('ef.id as id'),
                    DB::raw('eg.id as evaluation_id'),
                    DB::raw('vendors.id as vendor_id'),
                    'vendors.vendor_code',
                    'g.company_name',
                    DB::raw('city_description as city'),
                    DB::raw('region_description as province'),
                    DB::raw('country_description as country'),
                    DB::raw('po.description as purchasing_organization'),
                    // DB::raw('coalesce(s.sanction_type, \'GREEN\') as sanction_type'),
                    // DB::raw('case sanction_type when \'RED\' then 0 else 1 end as company_status'),
                    DB::raw('coalesce(p.company_warning, \'GREEN\') as sanction_type'),
                    DB::raw('case p.company_warning when \'RED\' then 0 else 1 end as company_status'),
                    'eg.year',
                    'eg.project_code',
                    'ef.total_po_document',
                    'ef.total_po_value',
                    'eg.start_date',
                    'eg.end_date',
                    'ef.total_score',
                    'ef.evaluated_by',
                    'eg.status',
                    DB::raw('es.name as score_categories_name')
                )
                ->join('vendor_evaluation_forms as ef', function($join){
                    $join->on('ef.vendor_id','=','vendors.id');
                })
                ->join('vendor_evaluation_generals as eg', function($join){
                    $join->on('ef.vendor_evaluation_id','=','eg.id');
                })
                // ->leftJoin('vendor_sanctions as s', function($join){
                //     $join->on('s.vendor_profile_id','=','p.id')
                //          ->where('s.status','=','APPROVED')
                //          ->whereNotNull('s.deleted_at');
                // })
                ->join('ref_purchase_orgs as po', function($join){
                    $join->on('vendors.purchase_org_id','=','po.id');
                })
                ->join('vendor_evaluation_score_categories as esc', function($join){
                    $join->on('esc.id','=','eg.category_id');
                })
                ->leftJoin('vendor_evaluation_scores as es', function($join){
                    $join->on('esc.id','=','es.category_id')
                    ->whereRaw("CASE WHEN es.lowest_score_operator = '>=' THEN es.lowest_score <= ef.total_score ELSE es.lowest_score < ef.total_score END")
                    ->whereRaw("CASE WHEN es.highest_score_operator = '<=' THEN es.highest_score >= ef.total_score ELSE es.highest_score > ef.total_score END");
                })
                ->leftJoin('ref_countries as countries', function($join){
                    $join->on('countries.country_code','=','g.country');
                })
                ->leftJoin('ref_provinces as prov', function($join){
                    $join->on('prov.country_code','=','g.country')
                    ->where('prov.region_code','=','g.province');
                })
                ->leftJoin('ref_cities as cities', function($join){
                    $join->on('cities.city_code','=','g.city');
                })
                ->where('eg.id',$id);
                // dd($data->toSql());exit;
                return DataTables::of($data)
                ->make(true);
            }else{
                $data = new Collection();
                return DataTables::of($data)
                ->make(true);
            }
        }
    }
    public function evaluation_detail_store_finish(Request $request,$id,$type){
        $message = "";
        $success = false;
        if (request()->ajax()) {
            try{
                DB::beginTransaction();
                if($type=='assignment'){
                    VendorEvaluationAssignment::where('vendor_evaluation_id',$id)->update(['is_finished'=>1]);
                }
                $success=true;
                DB::commit();
            }catch(Exception $e){
                DB::rollback();
            }
            return response()->json([
                'success'=>$success,
                'message' => $message,
                'data'=>['id'=>$id]
            ]);
        }
    }
    public function evaluation_detail_store(Request $request,$id,$type){
        $message = "";
        $success = false;
        if (request()->ajax()) {
            try{
                DB::beginTransaction();
                if($type=='form'){
                    $assignments = VendorEvaluationAssignment::where('vendor_evaluation_id',$request->evaluation_id)->get();
                    $inputs = [];
                    $scores = $request->score;
                    $cumulative = 0;
                    foreach($request->criteria as $key=>$criteria){
                        $inputs[] = [
                            'vendor_evaluation_id'=>$request->evaluation_id,
                            'criteria_id'=>$criteria,
                            'vendor_id'=>$request->vendor_id,
                            'score'=>intval($scores[$key]),
                        ];
                        foreach($assignments as $assignment){
                            if($assignment->criteria_id==$criteria){
                                $cumulative += intval($scores[$key])*intval($assignment->weighting);
                            }
                        }
                    }
                    $cumulative = $cumulative/100.0;

                    //delete before insert;
                    VendorEvaluationFormDetail::where('vendor_evaluation_id',$request->evaluation_id)->where('vendor_id',$request->vendor_id)->delete();
                    VendorEvaluationFormDetail::insert($inputs);

                    //put cumulative value to vendor data;
                    $evaluation = VendorEvaluationForm::where('vendor_evaluation_id',$request->evaluation_id)
                                        ->where('vendor_id',$request->vendor_id)->first();
                    $evaluation->total_score = $cumulative;
                    $evaluation->save();

                    $data = Vendor::find($request->vendor_id);
                    //set history data;
                    $history['activity'] = 'vendor-evaluation';
                    $history['comments'] = "update-".$data->vendor_code;
                    $message = ucwords($type).': <strong>Score</strong> saved';
                }else{
                    if($type=='assignment'){
                        if(isset($request->id)){
                            //update
                            $data = VendorEvaluationAssignment::find($request->id);
                            $history['activity'] = 'assignment-update-line';
                            $history['comments'] = "update";
                        }else{
                            //insert
                            $data = VendorEvaluationAssignment::where('vendor_evaluation_id',$id)
                                        ->where('criteria_id',$request->criteria_id)
                                        ->first();
                            if(is_null($data)){
                                $data = new VendorEvaluationAssignment();
                                $tmp = VendorEvaluationAssignment::select(DB::raw('coalesce(max(sequence),0)+1 as next_sequence'))
                                        ->where('vendor_evaluation_id',$id)
                                        ->first();
                                $data['sequence'] = $tmp->next_sequence;
                                $history['activity'] = 'assignment-create-line';
                                $history['comments'] = "create";
                            }else{
                                //update. can't have duplicate evaluation_id and criteria_id
                                $history['activity'] = 'assignment-update-line';
                                $history['comments'] = "update";
                            }
                        }
                    }
                    $data['vendor_evaluation_id'] = $id;
                    foreach($request->input() as $field=>$value){
                        if(!in_array($field,['id','_token'])){
                            $data->$field = $value;
                        }
                    }
                    $data->save();
                    //clear score data if change assignment data
                    VendorEvaluationFormDetail::where('vendor_evaluation_id',$id)->delete();
                    VendorEvaluationForm::where('vendor_evaluation_id',$id)->update(['total_score'=>0]);
                    $message = ucwords($type).': <strong>' . $data->name . '</strong> saved';
                }

                $history['vendor_evaluation_id'] = $id;
                $history['username'] = auth()->user()->name;
                $history['activity_date'] = now();

                //2020-07-23 no need to save other than main process
                // $this->finishActivity($history,'manual');

                $success=true;
                DB::commit();
            }catch(Exception $e){
                DB::rollback();
            }
            return response()->json([
                'success'=>$success,
                'message' => $message,
                'data'=>['id'=>$data->id]
            ]);
        }
    }
    public function evaluation_detail_delete(Request $request,$id,$type,$subid){
        $message = "";
        if (request()->ajax()) {
            if($type=='assignment'){
                $data = VendorEvaluationAssignment::find($subid);
                $data->delete();
                VendorEvaluationAssignment::where('sequence','>',$data->sequence)
                    ->where('vendor_evaluation_id',$id)
                    ->update(['sequence'=>DB::raw('sequence-1')]);
                $message = 'Assignment: <strong>' . $data->name . '</strong> deleted';
            }
            if($type=='form'){
                $data = VendorEvaluationForm::find($subid);
                $data->delete();
                VendorEvaluationFormDetail::where('vendor_evaluation_id',$data->vendor_evaluation_id)->delete();
                $message = 'Vendor: <strong>' . $data->vendor_id . '</strong> deleted';

            }
            return response()->json([
                'success'=>true,
                'message' => $message
            ]);
        }
    }
    public function evaluation_detail_up(Request $request,$id,$type,$subid){
        $message = "";
        if (request()->ajax()) {
            try{
                DB::beginTransaction();
                if($type=='assignment'){
                    $data1 = VendorEvaluationAssignment::find($subid);
                    $tmp = $data1->sequence*1;
                    $data1->sequence = $tmp-1;
                    $data1->save();
                    $data2 = VendorEvaluationAssignment::where('vendor_evaluation_id',$id)->where('sequence',$tmp-1)->where('id','<>',$subid)->first();
                    $data2->sequence = $tmp;
                    $data2->save();
                }
                $success=true;
                DB::commit();
            }catch(Exception $e){
                DB::rollback();
            }
            return response()->json([
                'success'=>$success,
                'message' => "Data".($success?'':' not')." swapped.",
            ]);
        }
    }
    public function evaluation_detail_down(Request $request,$id,$type,$subid){
        $message = "";
        $success = false;
        if (request()->ajax()) {
            try{
                DB::beginTransaction();
                if($type=='assignment'){
                    $data1 = VendorEvaluationAssignment::find($subid);
                    $tmp = $data1->sequence*1;
                    $data1->sequence = $tmp+1;
                    $data1->save();
                    $data2 = VendorEvaluationAssignment::where('vendor_evaluation_id',$id)->where('sequence',$tmp+1)->where('id','<>',$subid)->first();
                    $data2->sequence = $tmp;
                    $data2->save();
                }
                $success=true;
                DB::commit();
            }catch(Exception $e){
                DB::rollback();
            }
            return response()->json([
                'success'=>$success,
                'message' => "Data".($success?'':' not')." swapped.",
            ]);
        }
    }
    public function evaluation_detail_form_data(Request $request,$id,$subid){
        //subid is vendor_id
        if (request()->ajax()) {
            $data = VendorEvaluationFormDetail::where('vendor_evaluation_id',$id)
                ->where('vendor_id',$subid)->get();
            return response()->json([
                'success'=>true,
                'data' => $data,
            ]);
        }
    }
    public function evaluation_detail_submit(Request $request, $id){
        if (request()->ajax()) {
            try{
                DB::beginTransaction();
                $data = VendorEvaluationGeneral::find($id);
                $data->status=$this->statuses["SUBMISSION"];
                $rev = $data->revision + 1;
                $data->revision=$rev;
                $data->save();

                $resubmission = VendorEvaluationWorkflow::where('vendor_evaluation_id', $id)->count() > 0;

                // Submission and Approval generate
                $userid = auth()->user()->userid;
                $workflow = config('workflow.evaluation-submission.tasks');
                $i = 0;
                foreach($workflow as $task){
                    $vendorEvaluationWorkflow = new VendorEvaluationWorkflow([
                        'vendor_id' => null,
                        'vendor_evaluation_id' => $id,
                        'activity' => ($i==0 && $resubmission) ? 'Resubmission' : $task['activity'],
                        'remarks' => ($i==0 && $resubmission) ? 'Resubmission' : $task['remarks'],
                        'started_at' => $task['started_at']=='now' ? now() : null,
                        'finished_at' => $task['finished_at']=='now' ? now() : null,
                        'created_by' => $i==0 ? $userid : 'system'
                    ]);
                    $vendorEvaluationWorkflow->save();
                    $i++;
                }

                $this->sendEmail($data,'SUBMISSION');

                $history['activity'] = 'submission';
                $history['comments'] = "submission {$data->name} rev.{$rev}";
                $history['vendor_evaluation_id'] = $id;
                $history['username'] = auth()->user()->name;
                $history['activity_date'] = now();
                $this->finishActivity($history,'manual','concept');
                DB::commit();
                return response()->json([
                    'success'=>true,
                    'message'=>$data->name." submitted",
                    'data' => ['id'=>$data->id,'name'=>$data->name],
                ]);
            }catch(Exception $e){
                DB::rollback();
                Log::error($e->getMessage());
                return response()->json([
                    'success'=>false,
                    'message'=>"Error: ".$e->getMessage(),
                ]);
            }
        }
    }
    public function evaluation_detail_approval(Request $request, $id){
        $this->middleware('auth');
        if($request->ajax()){
            //validation

            $success = false;
            try{
                DB::beginTransaction();
                $data = VendorEvaluationGeneral::find($request->id);
                $data->updated_by = auth()->user()->name;
                $flow = VendorEvaluationWorkflow::where('vendor_evaluation_id', $request->id)
                ->whereNull('finished_at')
                ->orderBy('id','desc')
                ->first();
                if($flow){
                    $flow->update([
                        'finished_at' => now(),
                        'created_by' => auth()->user()->userid,
                        'remarks' => $request->comment
                    ]);
                }
                if($request->approved=='true'){
                    $data->status = $this->statuses['APPROVED'];
                    $data->save();
                    $this->sendEmail($data,'APPROVED');
                }else{
                    $data->status = $this->statuses['REVISE'];
                    $data->deleted_at = now();
                    $data->save();
                    $this->sendEmail($data,'REVISE');
                }


                //TODO: create history//
                $lastHistory = VendorEvaluationHistory::where('vendor_evaluation_id',$request->id)
                    ->orderBy('id','desc')->first();
                $history['activity'] = 'approval';
                $history['comments'] = $request->comment;
                $history['vendor_evaluation_id'] = $id;
                $history['username'] = auth()->user()->name;
                $history['status'] = $data->status;
                $this->finishActivity($history,'manual',$lastHistory->status);

                $message = 'Data saved'.
                DB::commit();
                $success=true;
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
            $data['status'] = $data['activity'] == 'approval' ? $data['status'] : $activity['key'];
            $data['role'] = $activity['val'][0];
            $data['activity_date'] = date('Y-m-d H:i:s');
            VendorEvaluationHistory::insert($data);
            $this->finishActivity($data,'auto',$activity['key']);
        }else if($type=='manual'){
            //only run if activity manual, and finish activity called manually.
            $data['status'] = $data['activity'] == 'approval' ? $data['status'] : $activity['key'];
            $data['role'] = $activity['val'][0];
            $data['activity_date'] = date('Y-m-d H:i:s');
            VendorEvaluationHistory::insert($data);
            $this->finishActivity($data,'auto',$activity['key']);
        }
    }

    public function sendEmail($evaluation,$emailType){
        $vendors = DB::table('vendor_evaluation_forms as f')
                    ->select(
                        'vp.company_name',
                        'vp.company_type',
                        DB::raw('rpo.org_code as purchase_organization_code'),
                        DB::raw('rpo.description as purchase_organization'),
                        'pic.full_name',
                        'pic.email'
                    )
                    ->join('vendors as v', 'v.id', '=', 'f.vendor_id')
                    ->join('vendor_profiles as vp', 'v.id', '=', 'vp.vendor_id')
                    ->join('ref_purchase_orgs as rpo', 'rpo.id', '=', 'v.purchase_org_id')
                    ->join('vendor_profile_pics as pic', function ($join) {
                        $join->on('pic.vendor_profile_id', '=', 'vp.id')
                             ->where('pic.primary_data',true)
                             ->where('pic.is_current_data',true)
                             ->whereNull('pic.deleted_at');
                    })
                    ->where('f.vendor_evaluation_id',$evaluation->id)
                    ->get();
        $pics = [];
        foreach($vendors as $vendor){
            $pics[$vendor->full_name] = $vendor->email;
        }

        $to = $this->emailConfig[$emailType]['to'];
        $cc = $this->emailConfig[$emailType]['cc'];

        $recipients = $to!='vendor' ? User::role($to)->join("user_extensions","user_extensions.user_id","=","users.id")->where("user_extensions.status","=",1)->whereNotNull("users.email")->pluck('email', 'name')->toArray() : $pics;
        $ccs = $cc!='vendor' ? User::role($cc)->join("user_extensions","user_extensions.user_id","=","users.id")->where("user_extensions.status","=",1)->whereNotNull("users.email")->pluck('email')->toArray() : $pics;

        Log::debug('====== Vendor Evaluation ID ['.$evaluation->id.'] send email: '.$emailType);
        Log::debug('Role To: '.json_encode($to));
        Log::debug('Role Cc: '.json_encode($cc));
        Log::debug('Email To: '.json_encode($recipients));
        Log::debug('Email Cc: '.json_encode($ccs));
        foreach($vendors as $vendor){
            $subject = str_replace('[NAME]',$vendor->company_name,$this->emailConfig[$emailType]['subject']);
            // foreach ($recipients as $name=>$email) {
                $arrdata = [];
                $arrdata['mailtype'] = $this->emailConfig[$emailType]['mailtype'];
                $arrdata['vendor_name'] = $vendor->company_name;
                $arrdata['vendor_type'] = $vendor->company_type;
                // $arrdata['recipient_name'] = $name;
                $arrdata['purchasing_organization_code'] = $vendor->purchase_organization_code;
                $arrdata['purchasing_organization'] = $vendor->purchase_organization;
                $arrdata['subject'] = $subject;
                $arrdata = (object) $arrdata;
                if($this->vendorRepo->isValidEmail($recipients) && $this->vendorRepo->isValidEmail($ccs)){
                    ProcessEmail::dispatch($recipients, $ccs, $arrdata);
                    // Mail::to($recipients)->cc($ccs)->send(new TestMail($arrdata));//check again//
                }else{
                    $this->log("===========email failed==============. email :".json_encode($recipients).", cc: ".json_encode($ccs).", obj: ".json_encode($arrdata));
                }
            // }
        }
    }
}
