<?php

namespace App\Repositories;

use App\Enums\TenderStatusEnum;
use App\Enums\TenderSubmissionEnum;
use App\Jobs\SendEmail;
use App\Mail\QueuingMail;
use App\Models\TenderReference;
use App\Models\TenderSignature;
use App\Models\TenderSignatureCommercial;
use App\Services\TenderMailService;
use App\TenderWorkflowHelper;
use Exception;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Auth;

class TenderSignatureRepository extends BaseRepository
{

    private $logName = 'TenderSignatureRepository';
    public $guarded = ['id','created_at','created_by','updated_at','updated_by','deleted_at','deleted_by'];
    private $_fields = [
        'tender_number','sign_by','position','type',
    ];

    public function fields()
    {
        return $this->_fields;
    }

    /**
     * find all data TenderParameter
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findAll()
    {
        try {
            $models = TenderSignature::all();
            return $models;
        } catch (Exception $e) {
            Log::error($this->logName . '::findAll error : ' . $e->getMessage());
            Log::error($e);
            throw $e;
        }
    }

    /**
     * find all buyer
     *
     * @param int $purchOrgId
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findBuyerOptions($purchOrgId, $purchGroupId = null)
    {
        try {
            $query = (new BuyerRepository)->getAllByPurchaseOrg($purchOrgId)
            ->select(
                    'ref_buyers.user_id',
                    'ref_buyers.buyer_name',
                    'user_extensions.position'
                )
                ->leftJoin('user_extensions','user_extensions.user_id','=','ref_buyers.user_id');
            if(!empty($purchGroupId)){
                $query = $query->whereRaw('ref_buyers.user_id IN select user_id from ref_buyer_purch_groups rbpg where purch_group_id=?', [$purchGroupId]);
            }
            return $query->get();
        } catch (Exception $e) {
            Log::error($this->logName . '::findBuyerOptions error : ' . $e->getMessage());
            Log::error($e);
            throw $e;
        }
    }

    /**
     * find data TenderParameter by id
     *
     * @param int $primaryKey
     *
     * @return \App\Models\TenderSignature $data
     */
    public function findById($primaryKey, $withThrow = true)
    {
        try {
            if ($withThrow) {
                return TenderSignature::findOrFail($primaryKey);
            } else {
                return TenderSignature::find($primaryKey);
            }
        } catch (Exception $e) {
            Log::error($this->logName . '::findById error : ' . $e->getMessage());
            Log::error($e);
            throw $e;
        }
    }

    /**
     * find data by tender number
     *
     * @param string $number
     * @param int $type
     * @param \App\Model\TenderSignature $user
     *
     * @return \Illuminate\Database\Eloquent\Collection $data
     */
    public function findByTenderNumber($number, $type = null)
    {
        try {
            $query = TenderSignature::select(
                    'tender_signatures.*',
                    DB::raw("ref_buyers.buyer_name as sign_by"),
                    'user_extensions.position'
                )
                ->join('ref_buyers','tender_signatures.sign_by_id','ref_buyers.user_id')
                ->leftJoin('user_extensions','user_extensions.user_id','=','ref_buyers.user_id')
                ->where('tender_number',$number);
            if(isset($type)){
                $query = $query->where('type',$type);
            }
            $query = $query->orderBy('order','asc');

            return $query->get();
        } catch (Exception $e) {
            Log::error($this->logName . '::findByTenderNumber error : ' . $e->getMessage());
            Log::error($e);
            throw $e;
        }
    }

    /**
     * find data approver
     *
     * @param string $number
     * @param \App\User $user
     *
     * @return \Illuminate\Database\Eloquent\Collection $data
     */
    public function findApprover($number, $user)
    {
        try {
            $query = TenderSignature::select(
                    'tender_signatures.*',
                    DB::raw("ref_buyers.buyer_name as sign_by"),
                    'user_extensions.position'
                )
                ->join('ref_buyers','tender_signatures.sign_by_id','ref_buyers.user_id')
                ->leftJoin('user_extensions','user_extensions.user_id','=','ref_buyers.user_id')
                ->where('tender_number',$number)
                ->where('type',2)
                ->where('sign_by_id',$user->id);
            $query = $query->orderBy('order','asc');

            return $query->get();
        } catch (Exception $e) {
            Log::error($this->logName . '::findApprover error : ' . $e->getMessage());
            Log::error($e);
            throw $e;
        }
    }

    private function findAllApprover($number){
        try {
            $query = TenderSignature::select(
                    'tender_signatures.*',
                    DB::raw("ref_buyers.buyer_name as sign_by"),
                    'users.email',
                    'user_extensions.position'
                )
                ->join('ref_buyers','tender_signatures.sign_by_id','ref_buyers.user_id')
                ->join('users','tender_signatures.sign_by_id','users.id')
                ->leftJoin('user_extensions','user_extensions.user_id','=','ref_buyers.user_id')
                ->where('tender_number',$number)
                ->where('type', 2);
            $query = $query->orderBy('order','asc');

            return $query->get();
        } catch (Exception $e) {
            Log::error($this->logName . '::findApprover error : ' . $e->getMessage());
            throw $e;
        }
    }
    private function findApproverByOrder($number, $order)
    {
        try {
            $query = TenderSignature::select(
                    'tender_signatures.*',
                    DB::raw("ref_buyers.buyer_name as sign_by"),
                    'users.email',
                    'user_extensions.position'
                )
                ->join('ref_buyers','tender_signatures.sign_by_id','ref_buyers.user_id')
                ->join('users','tender_signatures.sign_by_id','users.id')
                ->leftJoin('user_extensions','user_extensions.user_id','=','ref_buyers.user_id')
                ->where('tender_number',$number)
                ->where('type', 2)
                ->where('order',$order);
            $query = $query->orderBy('order','asc');

            return $query->first();
        } catch (Exception $e) {
            Log::error($this->logName . '::findApprover error : ' . $e->getMessage());
            throw $e;
        }
    }

    public function findProposedBy($number)
    {
        try {
            $query = TenderSignature::select(
                    'tender_signatures.*',
                    DB::raw("ref_buyers.buyer_name as sign_by"),
                    'users.email',
                    'user_extensions.position'
                )
                ->join('ref_buyers','tender_signatures.sign_by_id','ref_buyers.user_id')
                ->join('users','tender_signatures.sign_by_id','users.id')
                ->leftJoin('user_extensions','user_extensions.user_id','=','ref_buyers.user_id')
                ->where('tender_number',$number)
                ->where('type', 1);
            $query = $query->orderBy('order','asc');

            return $query->first();
        } catch (Exception $e) {
            Log::error($this->logName . '::findProposedBy error : ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * save bulk record
     *
     * @param array $paramAll
     * @param string $tenderNumber
     *
     * @return \App\Models\TenderSignature updated data
     *
     * @throws \Exception
     */
    public function saveBulk($paramAll, $tenderNumber)
    {
        try {
            DB::beginTransaction();
            foreach($paramAll as $params){
                $model = new TenderSignature();
                if(isset($params['id'])){
                    $model = TenderSignature::find($params['id']);
                }
                $params['tender_number'] = $tenderNumber;
                $model->fill($params);
                $model->save();
            }
            DB::commit();
            $modelAll = TenderSignature::where('tender_number', $tenderNumber)->get();
            return $modelAll;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($this->logName . '::save error : ' . $e->getMessage());
            Log::error($e);
            throw $e;
        }
    }

    /**
     * save record
     *
     * @param \App\TenderParameter $tender
     * @param array $params
     * @param string $page
     *
     * @return \App\Models\TenderSignature updated data
     *
     * @throws \Exception
     */
    public function save($params, $tender = null, $page = null)
    {
        try {
            DB::beginTransaction();
            $model = new TenderSignature();
            if(isset($params['id'])){
                $model = TenderSignature::find($params['id']);
            }
            $model->fill($params);
            $model->save();

            if($tender != null && $page != null){
                // update workflow
                $result = $this->onApproval($model, $tender, $page);
                DB::commit();
                return [
                    'data' => $model,
                    'next' => $result
                        ? route('tender.show', ['id' => $tender->id, 'type' => $result['page']])
                        : null,
                ];
            }
            DB::commit();
            return $model;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($this->logName . '::save error : ' . $e->getMessage());
            Log::error($e);
            throw $e;
        }
    }

    /**
     * save record
     *
     * @param array $params
     *
     * @return \App\Models\TenderSchedule updated data
     *
     * @throws \Exception
     */
    public function resetStatus($tenderNumber)
    {
        try {
            $model = TenderSignature::where('tender_number',$tenderNumber)
                ->where('type', 2)
                ->update(['status' => 'draft']);
            return $model;
        } catch (Exception $e) {
            Log::error($this->logName . '::resetStatus error : ' . $e->getMessage());
            Log::error($e);
            throw $e;
        }
    }

    /**
     * update tender on approval action
     *
     * @param \App\Models\TenderSignature $approval
     * @param \App\TenderParameter $tender
     * @param string $page
     *
     * @return \App\TenderParameter updated data
     *
     */
    public function onApproval($approval, $tender, $page)
    {
        $result = null;
        if($approval->status == 'rejected'){
            $result = (new TenderWorkflowHelper())->doneApprovalProposal($approval->status, $tender, $page);
            $this->sendEmailOnProposalReject($tender, $approval->order);
        }else if($approval->status == 'approved'){
            $lastApprover = TenderSignature::where('tender_number',$tender->tender_number)
                ->where('type', 2)
                ->orderBy('order','desc')
                ->first();
            if($approval->order == $lastApprover->order){
                $result = (new TenderWorkflowHelper())->doneApprovalProposal($approval->status, $tender, $page);
                $this->sendEmailOnProposalFullyApproved($tender);
                // (new TenderProcessRepository)->sendEmailOnTenderAnnouncement($tender);
                (new TenderMailService)->sendEmailOnTenderAnnouncement($tender);
            }else{
                $this->sendEmailOnProposalApproved($tender, $approval->order);
            }
        }
        return $result;
    }


    /**
     * delete record
     *
     * @param int $primaryKey
     *
     * @return bool
     *
     * @throws \Exception
     */
    public function delete($primaryKey)
    {
        try {
            DB::beginTransaction();
            $model = TenderSignature::findOrFail($primaryKey);
            $result = $model->delete();
            DB::commit();
            return $result;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($this->logName . '::delete error : ' . $e->getMessage());
            Log::error($e);
            throw $e;
        }
    }

    // TODO : move to class TenderMailService
    public function sendEmailOnProposalSubmit($tender)
    {
        try {
            $approver = $this->findApproverByOrder($tender->tender_number, 1);
            $teams = (new TenderEvaluatorRepository())->findByTenderNumber($tender->tender_number);
            $emailTeams = '';
            if($teams->count() > 0){
                $emailTeams = $teams->pluck('email')->toArray(); // implode(',', $teams->pluck('email')->toArray());
            }

            $mailTos = $approver->email;
            $paramsEmail = [
                'mailtype' => 'tender_procurement_approval.proposal_submission',
                'subject' => 'FOR APPROVAL: Tender Proposal - '.$tender->tender_number.' '.$tender->title,
                'view_data' => [
                    'tender' => $tender,
                    'approver' => $approver,
                    'linkTender' => route('tender.show', ['id' => $tender->id, 'type' => 'procurement_approval']),
                ],
            ];
            $details = [
                'email' => $mailTos,
                'mailable' => new QueuingMail((object) $paramsEmail),
            ];
            if(!empty($emailTeams)){
                $details['cc'] = $emailTeams;
            }
            SendEmail::dispatch($details);
        } catch (Exception $e) {
            Log::error($this->logName . '::sendEmailOnProposalSubmit error : ' . $e->getMessage());
            throw $e;
        }
    }
    // TODO : move to class TenderMailService
    public function sendEmailOnProposalReject($tender, $order)
    {
        try {
            $proposer = $this->findProposedBy($tender->tender_number);
            $approver = $this->findApproverByOrder($tender->tender_number, $order);
            $teams = (new TenderEvaluatorRepository())->findByTenderNumber($tender->tender_number);
            $emailTeams = '';
            if($teams->count() > 0){
                $emailTeams = $teams->pluck('email')->toArray(); // implode(',', $teams->pluck('email')->toArray());
            }

            $mailTos = $proposer->email;
            $paramsEmail = [
                'mailtype' => 'tender_procurement_approval.proposal_rejected',
                'subject' => 'REVISED: Tender Proposal - '.$tender->tender_number.' '.$tender->title,
                'view_data' => [
                    'tender' => $tender,
                    'proposer' => $proposer,
                    'approver' => $approver,
                    'linkTender' => route('tender.show', ['id' => $tender->id, 'type' => 'procurement_approval']),
                ],
            ];
            $details = [
                'email' => $mailTos,
                'mailable' => new QueuingMail((object) $paramsEmail),
            ];

            $details['cc'] = [$approver->email];

            //cc previous approvers
            $approvers = $this->findAllApprover($tender->tender_number);
            if(!is_null($approvers)){
                foreach($approvers as $appr){
                    if($appr->sign_by_id == $approver->sign_by_id) break;
                    else $details['cc'][] = [$appr->email];
                }
            }

            if(!empty($emailTeams)){
                $details['cc'] = array_merge($details['cc'], $emailTeams);
            }
            SendEmail::dispatch($details);
        } catch (Exception $e) {
            Log::error($this->logName . '::sendEmailOnProposalReject error : ' . $e->getMessage());
            throw $e;
        }
    }
    // TODO : move to class TenderMailService
    public function sendEmailOnProposalApproved($tender, $order)
    {
        try {
            $approver = $this->findApproverByOrder($tender->tender_number, $order);
            $nextApprover = $this->findApproverByOrder($tender->tender_number, ($order+1));
            $teams = (new TenderEvaluatorRepository())->findByTenderNumber($tender->tender_number);
            $emailTeams = '';
            if($teams->count() > 0){
                $emailTeams = $teams->pluck('email')->toArray();
            }

            $mailTos = $nextApprover->email;
            $paramsEmail = [
                'mailtype' => 'tender_procurement_approval.proposal_approved',
                'subject' => 'FOR APPROVAL: Tender Proposal - '.$tender->tender_number.' '.$tender->title,
                'view_data' => [
                    'tender' => $tender,
                    'approver' => $approver,
                    'nextApprover' => $nextApprover,
                    'linkTender' => route('tender.show', ['id' => $tender->id, 'type' => 'procurement_approval']),
                ],
            ];
            $details = [
                'email' => $mailTos,
                'mailable' => new QueuingMail((object) $paramsEmail),
            ];

            $details['cc'] = [$approver->email];
            if(!empty($emailTeams)){
                $details['cc'] = array_merge($details['cc'], $emailTeams);
            }
            SendEmail::dispatch($details);
        } catch (Exception $e) {
            Log::error($this->logName . '::sendEmailOnProposalApproved error : ' . $e->getMessage());
            throw $e;
        }
    }
    // TODO : move to class TenderMailService
    public function sendEmailOnProposalFullyApproved($tender)
    {
        try {
            $proposer = $this->findProposedBy($tender->tender_number);
            $approvers = $this->findAllApprover($tender->tender_number);

            $teams = (new TenderEvaluatorRepository())->findByTenderNumber($tender->tender_number);
            $emailTeams = '';
            if($teams->count() > 0){
                $emailTeams = $teams->pluck('email')->toArray();
            }

            $mailTos = $proposer->email;
            $paramsEmail = [
                'mailtype' => 'tender_procurement_approval.proposal_fully_approved',
                'subject' => 'APPROVED: Tender Proposal - '.$tender->tender_number.' '.$tender->title,
                'view_data' => [
                    'tender' => $tender,
                    'proposer' => $proposer,
                    'linkTender' => route('tender.show', ['id' => $tender->id, 'type' => 'procurement_approval']),
                ],
            ];
            $details = [
                'email' => $mailTos,
                'mailable' => new QueuingMail((object) $paramsEmail),
            ];

            foreach($approvers as $appr){
                $details['cc'][] = $appr->email;
            }
            // $details['cc'] = [$approver1->email, $approver2->email];
            if(!empty($emailTeams)){
                $details['cc'] = array_merge($details['cc'], $emailTeams);
            }
            SendEmail::dispatch($details);
        } catch (Exception $e) {
            Log::error($this->logName . '::sendEmailOnProposalFullyApproved error : ' . $e->getMessage());
            throw $e;
        }
    }

    #region Comercial Signature and Approval
    public function findCommercialBuyerOptions($purchOrgId, $purchGroupId = null){
        return $this->findBuyerOptions($purchOrgId, $purchGroupId);
    }
    public function findCommercialSignaturesByTenderNumber($number, $type = null)
    {
        try {
            $query = TenderSignatureCommercial::select(
                    'tender_signature_commercials.*',
                    DB::raw("ref_buyers.buyer_name as sign_by"),
                    'user_extensions.position'
                )
                ->join('ref_buyers','tender_signature_commercials.sign_by_id','ref_buyers.user_id')
                ->leftJoin('user_extensions','user_extensions.user_id','=','ref_buyers.user_id')
                ->whereNull('tender_signature_commercials.deleted_at')
                ->where('tender_number',$number);
            if(isset($type)){
                $query = $query->where('type',$type);
            }
            $query = $query->orderBy('order','asc');

            return $query->get();
        } catch (Exception $e) {
            Log::error($this->logName . '::findByTenderNumber error : ' . $e->getMessage());
            throw $e;
        }
    }
    public function saveCommercialSignature($tender, $paramAll, $type){
        try {
            DB::beginTransaction();
            switch($paramAll['subaction']){
                case 'save':
                    $tender->commercial_approval_type = $paramAll['commercial_approval_type'];
                    $tender->commercial_approval_status = TenderSubmissionEnum::STATUS_ITEM[1];
                    $tender->save();

                    TenderSignatureCommercial::where('tender_number',$tender->tender_number)->delete();

                    foreach($paramAll as $params){
                        if(is_array($params)){
                            $model = new TenderSignatureCommercial();
                            $params['tender_number'] = $tender->tender_number;
                            $model->fill($params);
                            $model->save();
                        }
                    }
                    $returnData = [
                        'signatures' => $this->findCommercialSignaturesByTenderNumber($tender->tender_number),
                        'tender' => $tender
                    ];
                break;

                case 'submit':
                    TenderSignatureCommercial::where('tender_number',$tender->tender_number)->onlyTrashed()->forceDelete();
                    $tender->commercial_approval_status = \App\Enums\TenderSubmissionEnum::STATUS_ITEM[2];
                    $tender->save();

                    DB::statement("
                        insert into tender_references (tender_number, ref_type, ref_value, submission_method, created_by, created_at)
                        values ('".$tender->tender_number."','".$paramAll['subaction']."','".$paramAll['action_type']."','".TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE[$type]."','".Auth::user()->userid."',now())
                    ");

                    //send email to first approver
                    $approver = $this->findCommercialByOrder($tender->tender_number, 1);
                    (new TenderMailService)->sendEmailOnCommercialApprovalSubmit($tender, $approver);
                    $returnData = [
                        'signatures' => $this->findCommercialSignaturesByTenderNumber($tender->tender_number),
                        'tender' => $tender
                    ];
                break;

                //approval
                case TenderSubmissionEnum::STATUS_ITEM[4]:
                case TenderSubmissionEnum::STATUS_ITEM[5]:
                    $returnData = $this->saveCommercialApproval($tender, $paramAll);
                    DB::statement("
                        insert into tender_references (tender_number, ref_type, ref_value, submission_method, created_by, created_at)
                        values ('".$tender->tender_number."','".$paramAll['subaction']."','".$paramAll['action_type']."','".TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE[$type]."','".Auth::user()->userid."',now())
                    ");
                break;
            }
            DB::commit();
            return [
                'data' => $returnData,
                'next' => null,
                'isCompleteApproval' => $returnData['isCompleteApproval'] ?? false
            ];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($this->logName . '::delete error : ' . $e->getMessage());
            throw $e;
        }
    }
    public function saveCommercialApproval($tender, $params){
        try {
            DB::beginTransaction();
            $model = new TenderSignatureCommercial();
            if(isset($params['id'])){
                $model = TenderSignatureCommercial::find($params['id']);
            }
            $model->fill($params);
            $model->save();

            if($params['status']==TenderSubmissionEnum::STATUS_ITEM[4]){
                //rejected
                $tender->commercial_approval_status=$params['status'];
                $tender->save();
                $proposer = $this->findCommercialProposedBy($tender->tender_number);
                $approver = $this->findCommercialByOrder($tender->tender_number, $model->order);
                $allApprover = $this->findCommercialAllApprover($tender->tender_number);
                (new TenderMailService)->sendEmailOnCommercialApprovalReject($tender, $model->order, $proposer, $approver, $allApprover);
            }else{
                $lastApprover = TenderSignatureCommercial::where('tender_number',$tender->tender_number)
                    ->where('type', 2)
                    ->orderBy('order','desc')
                    ->first();
                if($model->order == $lastApprover->order){
                    $proposer = $this->findCommercialProposedBy($tender->tender_number);
                    $allApprover = $this->findCommercialAllApprover($tender->tender_number);
                    (new TenderMailService)->sendEmailOnCommercialApprovalFullyApproved($tender, $proposer, $allApprover);
                }else{
                    $approver = $this->findCommercialByOrder($tender->tender_number, $model->order);
                    $nextApprover = $this->findCommercialByOrder($tender->tender_number, ($model->order+1));
                    (new TenderMailService)->sendEmailOnCommercialApprovalApproved($tender, $model->order, $approver, $nextApprover);
                }
            }
    
            $approval = [
            'signatures' => $this->findCommercialSignaturesByTenderNumber($tender->tender_number),
            'tender' => $tender
            ];
            $completeApproval = true;
            $cnt = 0;
            foreach($approval['signatures'] as $sign){
                if($sign->type==2){
                    $completeApproval = $completeApproval && $sign->status==TenderSubmissionEnum::STATUS_ITEM[5];
                    $cnt++;
                }
            }
            $approval['isCompleteApproval'] = ($cnt>0) ? $completeApproval : false;
            
            DB::commit();
            return $approval;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($this->logName . '::save error : ' . $e->getMessage());
            Log::error($e);
            throw $e;
        }
    }
    private function findCommercialApproverQuery($number){
        $query = TenderSignatureCommercial::select(
            'tender_signature_commercials.*',
            DB::raw("ref_buyers.buyer_name as sign_by"),
            'users.email',
            'user_extensions.position'
        )
        ->join('ref_buyers','tender_signature_commercials.sign_by_id','ref_buyers.user_id')
        ->join('users','tender_signature_commercials.sign_by_id','users.id')
        ->leftJoin('user_extensions','user_extensions.user_id','=','ref_buyers.user_id')
        ->where('tender_number',$number);
        return $query;
    }
    public function findCommercialApprover($number, $user){
        try {
            $query = $this->findCommercialApproverQuery($number)
                ->where('type',2)
                ->where('sign_by_id',$user->id)
                ->orderBy('order','asc');

            return $query->get();
        } catch (Exception $e) {
            Log::error($this->logName . '::findCommercialApprover error : ' . $e->getMessage());
            throw $e;
        }
    }
    public function findCommercialByOrder($number, $order){
        try {
            $query = $this->findCommercialApproverQuery($number)
                ->where('type',2)
                ->where('order',$order)
                ->orderBy('order','asc');
            return $query->first();
        } catch (Exception $e) {
            Log::error($this->logName . '::findCommercialByOrder error : ' . $e->getMessage());
            // Log::error($e);
            throw $e;
        }
    }
    public function findCommercialProposedBy($number){
        try {
            $query = $this->findCommercialApproverQuery($number)
                ->where('type',1)
                ->orderBy('order','asc');

            return $query->first();
        } catch (Exception $e) {
            Log::error($this->logName . '::findCommercialApprover error : ' . $e->getMessage());
            throw $e;
        }
    }
    public function findCommercialAllApprover($number){
        try {
            $query = $this->findCommercialApproverQuery($number)
                ->where('type',2)
                ->orderBy('order','asc');

            return $query->get();
        } catch (Exception $e) {
            Log::error($this->logName . '::findCommercialAllApprover error : ' . $e->getMessage());
            throw $e;
        }
    }
    public function findCommercialByTenderNumber($number, $type = null){
        try {
            $query = $this->findCommercialApproverQuery($number);
            if(isset($type)){
                $query = $query->where('type',$type);
            }
            $query = $query->orderBy('order','asc');

            return $query->get();
        } catch (Exception $e) {
            Log::error($this->logName . '::findCommercialByTenderNumber error : ' . $e->getMessage());
            // Log::error($e);
            throw $e;
        }
    }
    #endregion
}
