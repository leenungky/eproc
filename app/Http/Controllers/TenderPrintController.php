<?php

namespace App\Http\Controllers;

use App\Enums\TenderSubmissionEnum;
use App\Repositories\TenderBidDocRequirementRepository;
use App\Repositories\TenderEvaluatorRepository;
use App\Repositories\TenderItemsRepository;
use App\Repositories\TenderProcessRepository;
use App\Repositories\TenderProcessNegotiationRepository;
use App\Repositories\TenderRepository;
use App\Repositories\TenderScheduleRepository;
use App\Repositories\TenderSignatureRepository;
use App\Repositories\TenderExcelRepository;
use Barryvdh\DomPDF\PDF;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class TenderPrintController extends Controller
{
    protected $pdf;

    public function __construct(TenderRepository $repo)
    {
        $this->middleware('auth', ['only' => ['print']]);
        $this->repo = $repo;
    }

    public function printWorkPlane($id, PDF $pdf)
    {
        // abort_if(Gate::denies('tender_index'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $this->pdf = $pdf;
        $tender = $this->repo->findTenderParameterById($id, true, true);
        return $this->_printWorkPlane($tender, $pdf);
    }

    public function print($id, $type = 'parameters', $print='', PDF $pdf)
    {
        abort_if(Gate::denies('tender_' . $type . '_read'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $this->pdf = $pdf;
        $tender = $this->repo->findTenderParameterById($id, true, true);
        try {
            $method = 'printTender' . str_replace('_', '', ucwords($type, '_'));
            if(method_exists($this, $method)){
                return $this->$method($tender, $type, $print);
            }else{
                return $this->showDefault($tender, $type, $print);
            }
        } catch (Exception $e) {
            Log::error($e);
            throw $e;
        }
    }

    private function defaultViewData($tender, $type = 'parameters')
    {
        return [
            'id'                => $tender->id,
            'type'              => $type,
            'tender'            => $tender,
        ];
    }

    private function showDefault($tender, $type, $print)
    {
        $viewData = array_merge($this->defaultViewData($tender, $type), [
            'proposedVendors' => (new TenderProcessRepository)->findVendorSubmission($tender->tender_number, 1),
            'signatures' => (new TenderSignatureRepository())->findByTenderNumber($tender->tender_number, 2),
        ]);

        $pdf = $this->pdf->loadView('tender.print.' . $type, $viewData);
        // return $pdf->download('pre_qualification.pdf');
        return $pdf->stream('pre_qualification.pdf');
        // return View('tender.print.' . $type, $viewData);
    }

    private function printTenderProcessPrequalification($tender, $type, $print)
    {
        // $finishedBy = (new TenderEvaluatorRepository())->findAll($tender->tender_number)
        //     // ->join('tender_references', function ($join) {
        //     //     $join->on('tender_references.tender_number', '=', 'tender_evaluators.tender_number');
        //     //     $join->on('tender_references.ref_type', '=', TenderSubmissionEnum::FLOW_STATUS[6]);
        //     // })
        //     ->where('submission_method', TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE['process_prequalification'])
        //     ->where('tep.buyer_type_name','=>', 'prequalification_finish,single_envelope_finish')
        //     ->get();
        // $proposedBy = (new TenderSignatureRepository())->findByTenderNumber($tender->tender_number, 1);
        $viewData = array_merge($this->defaultViewData($tender, $type), [
            'proposedVendors' => (new TenderProcessRepository)->findVendorSubmission($tender->tender_number, 1),
            'signatures' => (new TenderSignatureRepository())->findByTenderNumber($tender->tender_number),
            // 'proposedBy' => $proposedBy[0] ?? null,
        ]);

        $pdf = $this->pdf->loadView('tender.print.' . $type, $viewData);
        return $pdf->stream('pre_qualification.pdf');
    }

    private function printTenderSchedules($tender, $type, $print)
    {
        return $this->_printWorkPlane($tender, $this->pdf);
    }

    private function _printWorkPlane($tender, $pdf)
    {
        $viewData = [
            'id' => $tender->id,
            'tender' => $tender,
            // 'proposedVendors' => (new TenderProcessRepository)->findVendorSubmission($tender->tender_number, 1),
            'proposedVendors' => (new TenderRepository)->findItem($tender, 'proposed_vendors')->getData(),
            'prItemList' => (new TenderItemsRepository)->findByTenderNumber($tender->tender_number, []),
            'schedules' => (new TenderScheduleRepository)->findByTenderNumber($tender->tender_number),
            'signatures' => (new TenderSignatureRepository())->findByTenderNumber($tender->tender_number, 2),
            'bidDocument' => (new TenderBidDocRequirementRepository())->findByTenderNumber($tender->tender_number),
            'title' => 'Workplane'
        ];
        // dd($viewData['bidDocument']->toArray());
        // dd($viewData['proposedVendors']);

        $pdf = $this->pdf->loadView('tender.print.workplane', $viewData)
            ->setPaper('a4', 'landscape');
        return $pdf->stream('workplane.pdf');
        // return View('tender.print.workplane', $viewData);
    }

    private function printTenderProcessTenderEvaluation($tender, $type, $print){
        //tbe or cbe or nbe
        return $this->printTenderExcelReport($tender, $type, $print);
    }

    private function printTenderProcessTechnicalEvaluation($tender, $type, $print){
        //tbe or cbe or nbe
        return $this->printTenderExcelReport($tender, $type, 'tbe');
    }
    private function printTenderProcessCommercialEvaluation($tender, $type, $print){
        //tbe or cbe or nbe
        return $this->printTenderExcelReport($tender, $type, 'cbe');
    }
    private function printTenderNegotiation($tender, $type, $print){
        //tbe or cbe or nbe
        return $this->printTenderExcelReport($tender, $type, 'nbe');
    }

    private function printTenderExcelReport($tender, $type, $reportType){
        // $stageType = $reportType == 'tbe' ? 3 : 4;
        switch($reportType){
            case 'tbe': $stageType = 3; break;
            case 'cbe': $stageType = 4; break;
            case 'nbe': $stageType = 6; break;
        }

        $tpRepo = new TenderProcessRepository();
        //find vendor submissions
        $proposedVendors = $tpRepo->findVendorSubmission($tender->tender_number, $stageType);
        $vendorSubmissions = [];
        foreach($proposedVendors as $vendor){
            if(in_array($stageType, [3,4])){
                $vendorData = [
                    'vendor' => $vendor,
                    'submission_header' => $tpRepo->findItem($tender, $stageType, ['action_type'=>'submission-header','vendor_id'=>$vendor->vendor_id]),
                    'submission_detail' => $tpRepo->findItem($tender, $stageType, ['action_type'=>'submission-detail-admin','vendor_id'=>$vendor->vendor_id])->getData(),
                    'item' => $tpRepo->findItem($tender, $stageType, ['action_type'=>'comparison-items-report','vendor_id'=>$vendor->vendor_id,'data_type'=>0])
                ];
                $summary = $tpRepo->findItem($tender, $stageType, ['action_type'=>'summary-items'])->getData();
            }
            if($stageType==3){
                $vendorData['item_detail'] = (new \App\Repositories\TenderItemSpecificationRepository)->findVendorItemDetailByVendor($tender->tender_number, $vendor->vendor_id, $stageType)->get();
            }
            if($stageType==4){
                $vendorData['technical_header'] = $tpRepo->findItem($tender, 3, ['action_type'=>'submission-header','vendor_id'=>$vendor->vendor_id]);
                $vendorData['vendor_detail'] = (new \App\Repositories\VendorRepository)->getVendorById($vendor->vendor_id);
            }
            if($stageType==6){
                $neRepo = new TenderProcessNegotiationRepository();
                $vendorData = [
                    'vendor' => $vendor,
                    'submission_header' => $neRepo->findItem($tender, $stageType, ['action_type'=>'submission-header','vendor_id'=>$vendor->vendor_id]),
                    'submission_detail' => $neRepo->findItem($tender, $stageType, ['action_type'=>'submission-detail-admin','vendor_id'=>$vendor->vendor_id])->getData(),
                    'item' => $neRepo->findItem($tender, $stageType, ['action_type'=>'comparison-items-report','vendor_id'=>$vendor->vendor_id,'data_type'=>0])
                ];
                $vendorData['technical_header'] = $neRepo->findItem($tender, 3, ['action_type'=>'submission-header','vendor_id'=>$vendor->vendor_id]);
                $vendorData['vendor_detail'] = (new \App\Repositories\VendorRepository)->getVendorById($vendor->vendor_id);
                $summary = $neRepo->findItem($tender, $stageType, ['action_type'=>'summary-items'])->getData();
            }
            $vendorSubmissions[] = $vendorData;
        }

        $viewData = [
            'id' => $tender->id,
            'tender' => $tender,
            'type' => $reportType, 
            'pr_item_list' => (new TenderItemsRepository)->findByTenderNumber($tender->tender_number, [])->sortBy('id'),
            'vendor_submissions' => $vendorSubmissions,
            // 'comparison' => $tpRepo->findItem($tender, $stageType, ['action_type'=>'comparison-items'])->getData(),
            'summary' => $summary ?? null,
            'item_detail' => DB::table('tender_item_detail as id')
                                ->select(
                                    'id.*',
                                    'idc.key',
                                    'idc.category_name',
                                    'idc.order',
                                    'idc.template_id',
                                    'idc.public_status as category_status',
                                    'idc.line_id as category_line_id'
                                )
                                ->leftJoin('tender_item_detail_category as idc', function($join){
                                    $join->on('id.category_id','idc.line_id');
                                })
                                ->where('id.tender_number',$tender->tender_number)
                                ->where('id.public_status','announced')
                                ->whereNull('id.deleted_at')
                                ->orderBy('idc.order')
                                ->orderBy('id.id')
                                ->get(),

        ];
        if($stageType==4){
            // $viewData['comparison'] = $tpRepo->findItem($tender, $stageType, ['action_type'=>'comparison-items','vendor_id'=>$vendor->vendor_id])->getData();
            $viewData['summary'] = $tpRepo->findItem($tender, $stageType, ['action_type'=>'summary-items'])->getData();
        }
        if($stageType==4 || $stageType==6){
            $viewData['signatures'] = (new \App\Repositories\TenderSignatureRepository)->findCommercialSignaturesByTenderNumber($tender->tender_number);
        }

        return (new TenderExcelRepository)->processExcelTender($viewData);
    }
}
