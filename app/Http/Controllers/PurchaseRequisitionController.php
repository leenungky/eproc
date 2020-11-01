<?php

namespace App\Http\Controllers;

use App\Jobs\SyncSapPRList;
use App\RefPurchaseGroup;
use App\RefPurchaseOrg;
use App\RefListOption;
use App\Repositories\PurchaseRequisitionRepository;
use Illuminate\Http\Request;
use DataTables;
use Exception;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Response;

class PurchaseRequisitionController extends Controller
{
    private $repo;
    public function __construct(PurchaseRequisitionRepository $repo)
    {
        $this->middleware('auth');
        $this->repo = $repo;
    }

    public function index()
    {
        abort_if(Gate::denies('tender_pr_selection'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $fields = config('eproc.sap.showed_fields.prlist');
        return view('purchase_requisition.list', [
            'fields' => $fields,
            // 'data' => json_encode($data),
            'purchGroups' => RefPurchaseGroup::all(),
            'purchOrgs' => RefPurchaseOrg::all(),
            'tenderMethod' => RefListOption::where('type', 'tender_method_options')->where('deleteflg', false)->pluck('value', 'key'),
            'submissionMethod' => RefListOption::where('type', 'submission_method_options')->where('deleteflg', false)->pluck('value', 'key'),
            'evaluationMethod' => RefListOption::where('type', 'evaluation_method_options')->where('deleteflg', false)->pluck('value', 'key'),
        ]);
    }

    private function _deleteSAPData($id)
    {
        //do something.
    }

    public function delete($id)
    {
        //send deletion info to sap
        $this->_deleteSAPData($id);

        //then sync sap data.
        //$this->_syncSAPData();

        //then delete in table//
        // $pr = PurchaseRequisition::find($id);
        // $pr->updated_by = Auth::user()->name;
        // $pr->save();
        // $pr->delete();

        return "{status:200,message:'success'}";
    }

    public function datatable_serverside(Request $request)
    {
        if (request()->ajax()) {
            $data = $this->repo->findAll();
            return DataTables::of($data)
                // ->addColumn('DT_RowId', function($row){
                //     return 'id-' . $row->id;
                // })
                ->make(true);
        }
    }

    public function syncSapData(Request $request)
    {
        if (request()->ajax()) {
            try {
                $data = SyncSapPRList::dispatchNow();
                return response()->json([
                    'status' => 200,
                    'success' => true,
                    'message' => 'sync data success. ' . count($data) . ' data synchronized successfully',
                ]);
            } catch (Exception $e) {
                return response()->json([
                    'status' => 200,
                    'success' => false,
                    'message' => 'sync data failed',
                ]);
            }
        }
    }
}
