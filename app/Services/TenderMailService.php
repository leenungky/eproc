<?php

namespace App\Services;

use App\Enums\TenderSubmissionEnum;
use App\Jobs\SendEmail;
use App\Mail\QueuingMail;
use App\Models\TenderAwardingAttachment;
use App\Models\TenderVendorAwarding;
use App\Repositories\TenderEvaluatorRepository;
use App\Repositories\TenderSignatureRepository;
use App\Repositories\TenderVendorRepository;
use Exception;
use Illuminate\Support\Facades\Log;

class TenderMailService
{
    private $logName = 'TenderMailService';

    public function __construct()
    {
    }

    public function sendEmailOnTenderAnnouncement($tender)
    {
        try {
            // $tenderVendor = (new TenderVendorRepository)->findByTenderNumber($tender->tender_number, 'draft');
            $tenderVendor = (new TenderVendorRepository)->findByTenderNumber($tender->tender_number, ['draft','invitation']);
            $teams = (new TenderEvaluatorRepository)->findByTenderNumber($tender->tender_number);
            $emailTeams = '';
            if ($teams->count() > 0) {
                $emailTeams = $teams->pluck('email')->toArray();
            }

            foreach ($tenderVendor as $vendor) {
                $mailTos = $vendor->pic_email;
                $paramsEmail = [
                    'mailtype' => 'tender_process.announcement',
                    'subject' => 'ANNOUNCED: Tender Announcement - ' . $tender->tender_number . ' ' . $tender->title,
                    'view_data' => [
                        'tender' => $tender,
                        'vendor' => $vendor,
                        'linkTender' => route('tender.show', ['id' => $tender->id, 'type' => 'process_registration']),
                    ],
                ];
                $details = [
                    'email' => $mailTos,
                    'mailable' => new QueuingMail((object) $paramsEmail),
                ];
                $details['cc'] = [];
                if (!empty($emailTeams)) {
                    $details['cc'] = array_merge($details['cc'], $emailTeams);
                }
                SendEmail::dispatch($details);
            }
        } catch (Exception $e) {
            Log::error($this->logName . '::sendEmailOnTenderAnnouncement error : ' . $e->getMessage());
            throw $e;
        }
    }
    public function sendEmailOnProposalChange($tender)
    {
        try {
            $tenderVendor = (new TenderVendorRepository)->findByTenderNumber($tender->tender_number, ['accepted', 'invitation']);
            $teams = (new TenderEvaluatorRepository)->findByTenderNumber($tender->tender_number);
            $buyer = (new TenderSignatureRepository)->findProposedBy($tender->tender_number);
            $emailTeams = '';
            if ($teams->count() > 0) {
                $emailTeams = $teams->pluck('email')->toArray();
            }

            foreach ($tenderVendor as $vendor) {
                $mailTos = $vendor->pic_email;
                $paramsEmail = [
                    'mailtype' => 'tender_process.proposal_change',
                    'subject' => 'UPDATED: Update Tender Data - ' . $tender->tender_number . ' ' . $tender->title,
                    'view_data' => [
                        'tender' => $tender,
                        'vendor' => $vendor,
                        'updatedBy' => $buyer, // $tender->userUpdatedBy(),
                        'linkTender' => route('tender.show', ['id' => $tender->id, 'type' => 'general_documents']),
                    ],
                ];
                $details = [
                    'email' => $mailTos,
                    'mailable' => new QueuingMail((object) $paramsEmail),
                ];
                $details['cc'] = [];
                if (!empty($emailTeams)) {
                    $details['cc'] = array_merge($details['cc'], $emailTeams);
                }
                SendEmail::dispatch($details);
            }
        } catch (Exception $e) {
            Log::error($this->logName . '::sendEmailOnProposalChange error : ' . $e->getMessage());
            throw $e;
        }
    }

    public function sendEmailOnTenderStarted($tender, $stageType, $params = null)
    {
        $stageName = '';
        if ($tender->submission_method == '2E') {
            $stageName = '2-Envelope';
        } else if ($tender->submission_method == '2') {
            $stageName = '2-Stage';
        }
        switch ($stageType) {
            case TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE['process_prequalification']:
                $this->sendEmailOnTenderPQStarted($tender, $stageType);
                break;
            case TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE['process_technical_evaluation']:
                if ($tender->submission_method == '1E') {
                    $this->sendEmailOnTender1EnvelopeStarted($tender, $stageType);
                } else {
                    $this->sendEmailOnTenderTechnicalStarted($tender, $stageType, $stageName);
                }
                break;
            case TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE['process_commercial_evaluation']:
                $this->sendEmailOnTenderCommercialStarted($tender, $stageType, $stageName);
                break;
        }
    }

    public function sendEmailOnTenderResubmitted($tender, $stageType)
    {
        $stageName = '';
        if ($tender->submission_method == '2E') {
            $stageName = '2-Envelope';
        } else if ($tender->submission_method == '2') {
            $stageName = '2-Stage';
        }

        switch ($stageType) {
            case TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE['process_prequalification']:
                $this->sendEmailOnTenderPQResubmitted($tender, $stageType);
                break;
            case TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE['process_technical_evaluation']:
                if ($tender->submission_method == '1E') {
                    $this->sendEmailOnTender1EnvelopeResubmitted($tender, $stageType);
                } else {
                    $this->sendEmailOnTenderTechnicalResubmitted($tender, $stageType, $stageName);
                }
                break;
            case TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE['process_commercial_evaluation']:
                if ($tender->submission_method == '1E') {
                    $this->sendEmailOnTender1EnvelopeResubmitted($tender, $stageType);
                } else {
                    $this->sendEmailOnTenderCommercialResubmitted($tender, $stageType, $stageName);
                }
                break;
                // case TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE['negotiation_technical'] :
                //     $this->sendEmailOnTenderPQStarted($tender);
                // break;
                // case TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE['negotiation_commercial'] :
                //     $this->sendEmailOnTenderPQStarted($tender);
                // break;
        }
    }

    public function sendEmailOnTenderEvaluate($tender, $stageType)
    {
        $stageName = '';
        if ($tender->submission_method == '2E') {
            $stageName = '2-Envelope';
        } else if ($tender->submission_method == '2') {
            $stageName = '2-Stage';
        }
        switch ($stageType) {
            case TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE['process_prequalification']:
                $this->sendEmailOnTenderPQEvaluated($tender, $stageType);
                break;
            case TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE['process_tender_evaluation']:
                $this->sendEmailOnTender1EnvelopeEvaluated($tender, 4);
                break;
            case TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE['process_technical_evaluation']:
                if ($tender->submission_method == '1E') {
                    $this->sendEmailOnTender1EnvelopeEvaluated($tender, $stageType);
                } else {
                    $this->sendEmailOnTenderTechnicalEvaluated($tender, $stageType, $stageName);
                }
                break;
            case TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE['process_commercial_evaluation']:
                if ($tender->submission_method == '1E') {
                    $this->sendEmailOnTender1EnvelopeEvaluated($tender, $stageType);
                } else {
                    $this->sendEmailOnTenderCommercialEvaluated($tender, $stageType, $stageName);
                }
                break;
                // case TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE['negotiation_technical'] :
                //     $this->sendEmailOnTenderPQStarted($tender);
                // break;
                // case TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE['negotiation_commercial'] :
                //     $this->sendEmailOnTenderPQStarted($tender);
                // break;
        }
    }

    public function sendEmailOnTenderPQStarted($tender, $stageType)
    {
        try {
            $tenderVendor = (new TenderVendorRepository)->findForNotifyTenderStarted($tender, $stageType);
            $teams = (new TenderEvaluatorRepository)->findByTenderNumber($tender->tender_number);
            $buyer = (new TenderSignatureRepository)->findProposedBy($tender->tender_number);
            $emailTeams = '';
            if ($teams->count() > 0) {
                $emailTeams = $teams->pluck('email')->toArray();
            }

            foreach ($tenderVendor as $vendor) {
                $mailTos = $vendor->pic_email;
                $paramsEmail = [
                    'mailtype' => 'tender_process.tender-start',
                    'subject' => 'STARTED: Prequalification - ' . $tender->tender_number . ' ' . $tender->title,
                    'view_data' => [
                        'tenderStage' => 'Prequalification',
                        'tender' => $tender,
                        'vendor' => $vendor,
                        'buyer' => $buyer,
                        'linkTender' => route('tender.show', ['id' => $tender->id, 'type' => 'process_prequalification']),
                    ],
                ];
                $details = [
                    'email' => $mailTos,
                    'mailable' => new QueuingMail((object) $paramsEmail),
                ];
                $details['cc'] = [];
                if (!empty($emailTeams)) {
                    $details['cc'] = array_merge($details['cc'], $emailTeams);
                }
                SendEmail::dispatch($details);
            }
        } catch (Exception $e) {
            Log::error($this->logName . '::sendEmailOnTenderPQStarted error : ' . $e->getMessage());
            throw $e;
        }
    }
    public function sendEmailOnTenderPQResubmitted($tender, $stageType)
    {
        try {
            $tenderVendor = (new TenderVendorRepository)->findForNotifyTenderStarted($tender, $stageType);
            $teams = (new TenderEvaluatorRepository)->findByTenderNumber($tender->tender_number);
            $buyer = (new TenderSignatureRepository)->findProposedBy($tender->tender_number);
            $emailTeams = '';
            if ($teams->count() > 0) {
                $emailTeams = $teams->pluck('email')->toArray();
            }

            foreach ($tenderVendor as $vendor) {
                $mailTos = $vendor->pic_email;
                $paramsEmail = [
                    'mailtype' => 'tender_process.tender-resubmission',
                    'subject' => 'RESUBMISSION: Prequalification - ' . $tender->tender_number . ' ' . $tender->title,
                    'view_data' => [
                        'tenderStage' => 'Prequalification',
                        'tender' => $tender,
                        'vendor' => $vendor,
                        'buyer' => $buyer,
                        'linkTender' => route('tender.show', ['id' => $tender->id, 'type' => 'process_prequalification']),
                    ],
                ];
                $details = [
                    'email' => $mailTos,
                    'mailable' => new QueuingMail((object) $paramsEmail),
                ];
                $details['cc'] = [];
                if (!empty($emailTeams)) {
                    $details['cc'] = array_merge($details['cc'], $emailTeams);
                }
                SendEmail::dispatch($details);
            }
        } catch (Exception $e) {
            Log::error($this->logName . '::sendEmailOnTenderPQResubmitted error : ' . $e->getMessage());
            throw $e;
        }
    }
    public function sendEmailOnTenderPQEvaluated($tender, $stageType)
    {
        try {
            $tenderVendor = (new TenderVendorRepository)->findForNotifyTenderEvaluated($tender, $stageType);
            $teams = (new TenderEvaluatorRepository)->findByTenderNumber($tender->tender_number);
            $buyer = (new TenderSignatureRepository)->findProposedBy($tender->tender_number);
            $emailTeams = '';
            if ($teams->count() > 0) {
                $emailTeams = $teams->pluck('email')->toArray();
            }

            foreach ($tenderVendor as $vendor) {
                $mailTos = $vendor->pic_email;
                $paramsEmail = [
                    'mailtype' => 'tender_process.tender-evaluated',
                    'subject' => 'EVALUATED: Prequalification - ' . $tender->tender_number . ' ' . $tender->title,
                    'view_data' => [
                        'tenderStage' => 'Prequalification',
                        'tender' => $tender,
                        'vendor' => $vendor,
                        'buyer' => $buyer,
                        'linkTender' => route('tender.show', ['id' => $tender->id, 'type' => 'process_prequalification']),
                    ],
                ];
                $details = [
                    'email' => $mailTos,
                    'mailable' => new QueuingMail((object) $paramsEmail),
                ];
                $details['cc'] = [];
                if (!empty($emailTeams)) {
                    $details['cc'] = array_merge($details['cc'], $emailTeams);
                }
                SendEmail::dispatch($details);
            }
        } catch (Exception $e) {
            Log::error($this->logName . '::sendEmailOnTenderPQEvaluated error : ' . $e->getMessage());
            throw $e;
        }
    }

    public function sendEmailOnTender1EnvelopeStarted($tender, $stageType)
    {
        try {
            $tenderVendor = (new TenderVendorRepository)->findForNotifyTenderStarted($tender, $stageType);
            $teams = (new TenderEvaluatorRepository)->findByTenderNumber($tender->tender_number);
            $buyer = (new TenderSignatureRepository)->findProposedBy($tender->tender_number);
            $emailTeams = '';
            if ($teams->count() > 0) {
                $emailTeams = $teams->pluck('email')->toArray();
            }

            foreach ($tenderVendor as $vendor) {
                $mailTos = $vendor->pic_email;
                $paramsEmail = [
                    'mailtype' => 'tender_process.tender-start',
                    'subject' => 'STARTED: 1-Envelope - ' . $tender->tender_number . ' ' . $tender->title,
                    'view_data' => [
                        'tenderStage' => '1-Envelope',
                        'tender' => $tender,
                        'vendor' => $vendor,
                        'buyer' => $buyer,
                        'linkTender' => route('tender.show', ['id' => $tender->id, 'type' => 'process_tender_evaluation']),
                    ],
                ];
                $details = [
                    'email' => $mailTos,
                    'mailable' => new QueuingMail((object) $paramsEmail),
                ];
                $details['cc'] = [];
                if (!empty($emailTeams)) {
                    $details['cc'] = array_merge($details['cc'], $emailTeams);
                }
                SendEmail::dispatch($details);
            }
        } catch (Exception $e) {
            Log::error($this->logName . '::sendEmailOnTender1EnvelopeStarted error : ' . $e->getMessage());
            throw $e;
        }
    }
    public function sendEmailOnTender1EnvelopeResubmitted($tender, $stageType)
    {
        try {
            $tenderVendor = (new TenderVendorRepository)->findForNotifyTenderStarted($tender, $stageType);
            $teams = (new TenderEvaluatorRepository)->findByTenderNumber($tender->tender_number);
            $buyer = (new TenderSignatureRepository)->findProposedBy($tender->tender_number);
            $emailTeams = '';
            if ($teams->count() > 0) {
                $emailTeams = $teams->pluck('email')->toArray();
            }

            foreach ($tenderVendor as $vendor) {
                $mailTos = $vendor->pic_email;
                $paramsEmail = [
                    'mailtype' => 'tender_process.tender-resubmission',
                    'subject' => 'RESUBMISSION: 1-Envelope - ' . $tender->tender_number . ' ' . $tender->title,
                    'view_data' => [
                        'tenderStage' => '1-Envelope',
                        'tender' => $tender,
                        'vendor' => $vendor,
                        'buyer' => $buyer,
                        'linkTender' => route('tender.show', ['id' => $tender->id, 'type' => 'process_tender_evaluation']),
                    ],
                ];
                $details = [
                    'email' => $mailTos,
                    'mailable' => new QueuingMail((object) $paramsEmail),
                ];
                $details['cc'] = [];
                if (!empty($emailTeams)) {
                    $details['cc'] = array_merge($details['cc'], $emailTeams);
                }
                SendEmail::dispatch($details);
            }
        } catch (Exception $e) {
            Log::error($this->logName . '::sendEmailOnTender1EnvelopeResubmitted error : ' . $e->getMessage());
            throw $e;
        }
    }
    public function sendEmailOnTender1EnvelopeEvaluated($tender, $stageType)
    {
        try {
            $tenderVendor = (new TenderVendorRepository)->findForNotifyTenderEvaluated($tender, $stageType);
            $teams = (new TenderEvaluatorRepository)->findByTenderNumber($tender->tender_number);
            $buyer = (new TenderSignatureRepository)->findProposedBy($tender->tender_number);
            $emailTeams = '';
            if ($teams->count() > 0) {
                $emailTeams = $teams->pluck('email')->toArray();
            }
            foreach ($tenderVendor as $vendor) {
                $mailTos = $vendor->pic_email;
                $paramsEmail = [
                    'mailtype' => 'tender_process.tender-evaluated',
                    'subject' => 'EVALUATED: 1-Envelope - ' . $tender->tender_number . ' ' . $tender->title,
                    'view_data' => [
                        'tenderStage' => '1-Envelope',
                        'tender' => $tender,
                        'vendor' => $vendor,
                        'buyer' => $buyer,
                        'linkTender' => route('tender.show', ['id' => $tender->id, 'type' => 'process_tender_evaluation']),
                    ],
                ];
                $details = [
                    'email' => $mailTos,
                    'mailable' => new QueuingMail((object) $paramsEmail),
                ];
                $details['cc'] = [];
                if (!empty($emailTeams)) {
                    $details['cc'] = array_merge($details['cc'], $emailTeams);
                }
                SendEmail::dispatch($details);
            }
        } catch (Exception $e) {
            Log::error($this->logName . '::sendEmailOnTender1EnvelopeEvaluated error : ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * @param \App\TenderParameter $tender
     * @param string $stageName, 2-Envelope | 2-Stage
     *
     * @return void
     */
    public function sendEmailOnTenderTechnicalStarted($tender, $stageType, $stageName)
    {
        try {
            $tenderVendor = (new TenderVendorRepository)->findForNotifyTenderStarted($tender, $stageType);
            $teams = (new TenderEvaluatorRepository)->findByTenderNumber($tender->tender_number);
            $buyer = (new TenderSignatureRepository)->findProposedBy($tender->tender_number);
            $emailTeams = '';
            if ($teams->count() > 0) {
                $emailTeams = $teams->pluck('email')->toArray();
            }

            foreach ($tenderVendor as $vendor) {
                $mailTos = $vendor->pic_email;
                $paramsEmail = [
                    'mailtype' => 'tender_process.tender-start',
                    'subject' => 'STARTED: ' . $stageName . ' - Technical ' . $tender->tender_number . ' ' . $tender->title,
                    'view_data' => [
                        'tenderStage' => $stageName . ' - Technical ',
                        'tender' => $tender,
                        'vendor' => $vendor,
                        'buyer' => $buyer,
                        'linkTender' => route('tender.show', ['id' => $tender->id, 'type' => 'process_technical_evaluation']),
                    ],
                ];
                $details = [
                    'email' => $mailTos,
                    'mailable' => new QueuingMail((object) $paramsEmail),
                ];
                $details['cc'] = [];
                if (!empty($emailTeams)) {
                    $details['cc'] = array_merge($details['cc'], $emailTeams);
                }
                SendEmail::dispatch($details);
            }
        } catch (Exception $e) {
            Log::error($this->logName . '::sendEmailOnTenderTechnicalStarted error : ' . $e->getMessage());
            throw $e;
        }
    }
    public function sendEmailOnTenderTechnicalResubmitted($tender, $stageType, $stageName)
    {
        try {
            $tenderVendor = (new TenderVendorRepository)->findForNotifyTenderStarted($tender, $stageType);
            $teams = (new TenderEvaluatorRepository)->findByTenderNumber($tender->tender_number);
            $buyer = (new TenderSignatureRepository)->findProposedBy($tender->tender_number);
            $emailTeams = '';
            if ($teams->count() > 0) {
                $emailTeams = $teams->pluck('email')->toArray();
            }

            foreach ($tenderVendor as $vendor) {
                $mailTos = $vendor->pic_email;
                $paramsEmail = [
                    'mailtype' => 'tender_process.tender-resubmission',
                    'subject' => 'RESUBMISSION: ' . $stageName . ' - Technical ' . $tender->tender_number . ' ' . $tender->title,
                    'view_data' => [
                        'tenderStage' => $stageName . ' - Technical ',
                        'tender' => $tender,
                        'vendor' => $vendor,
                        'buyer' => $buyer,
                        'linkTender' => route('tender.show', ['id' => $tender->id, 'type' => 'process_technical_evaluation']),
                    ],
                ];
                $details = [
                    'email' => $mailTos,
                    'mailable' => new QueuingMail((object) $paramsEmail),
                ];
                $details['cc'] = [];
                if (!empty($emailTeams)) {
                    $details['cc'] = array_merge($details['cc'], $emailTeams);
                }
                SendEmail::dispatch($details);
            }
        } catch (Exception $e) {
            Log::error($this->logName . '::sendEmailOnTenderTechnicalResubmitted error : ' . $e->getMessage());
            throw $e;
        }
    }
    public function sendEmailOnTenderTechnicalEvaluated($tender, $stageType, $stageName)
    {
        try {
            $tenderVendor = (new TenderVendorRepository)->findForNotifyTenderEvaluated($tender, $stageType);
            $teams = (new TenderEvaluatorRepository)->findByTenderNumber($tender->tender_number);
            $buyer = (new TenderSignatureRepository)->findProposedBy($tender->tender_number);
            $emailTeams = '';
            if ($teams->count() > 0) {
                $emailTeams = $teams->pluck('email')->toArray();
            }

            foreach ($tenderVendor as $vendor) {
                $mailTos = $vendor->pic_email;
                $paramsEmail = [
                    'mailtype' => 'tender_process.tender-evaluated',
                    'subject' => 'EVALUATED: ' . $stageName . ' - Technical ' . $tender->tender_number . ' ' . $tender->title,
                    'view_data' => [
                        'tenderStage' => $stageName . ' - Technical ',
                        'tender' => $tender,
                        'vendor' => $vendor,
                        'buyer' => $buyer,
                        'linkTender' => route('tender.show', ['id' => $tender->id, 'type' => 'process_technical_evaluation']),
                    ],
                ];
                $details = [
                    'email' => $mailTos,
                    'mailable' => new QueuingMail((object) $paramsEmail),
                ];
                $details['cc'] = [];
                if (!empty($emailTeams)) {
                    $details['cc'] = array_merge($details['cc'], $emailTeams);
                }
                SendEmail::dispatch($details);
            }
        } catch (Exception $e) {
            Log::error($this->logName . '::sendEmailOnTenderTechnicalEvaluated error : ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * @param \App\TenderParameter $tender
     * @param string $stageName, 2-Envelope | 2-Stage
     *
     * @return void
     */
    public function sendEmailOnTenderCommercialStarted($tender, $stageType, $stageName)
    {
        try {
            $tenderVendor = (new TenderVendorRepository)->findForNotifyTenderStarted($tender, $stageType);
            $teams = (new TenderEvaluatorRepository)->findByTenderNumber($tender->tender_number);
            $buyer = (new TenderSignatureRepository)->findProposedBy($tender->tender_number);
            $emailTeams = '';
            if ($teams->count() > 0) {
                $emailTeams = $teams->pluck('email')->toArray();
            }
            foreach ($tenderVendor as $vendor) {
                $mailTos = $vendor->pic_email;
                $paramsEmail = [
                    'mailtype' => 'tender_process.tender-start',
                    'subject' => 'STARTED: ' . $stageName . ' - Commercial ' . $tender->tender_number . ' ' . $tender->title,
                    'view_data' => [
                        'tenderStage' => $stageName . ' - Commercial',
                        'tender' => $tender,
                        'vendor' => $vendor,
                        'buyer' => $buyer,
                        'linkTender' => route('tender.show', ['id' => $tender->id, 'type' => 'process_commercial_evaluation']),
                    ],
                ];
                $details = [
                    'email' => $mailTos,
                    'mailable' => new QueuingMail((object) $paramsEmail),
                ];
                $details['cc'] = [];
                if (!empty($emailTeams)) {
                    $details['cc'] = array_merge($details['cc'], $emailTeams);
                }
                SendEmail::dispatch($details);
            }
        } catch (Exception $e) {
            Log::error($this->logName . '::sendEmailOnTenderCommercialStarted error : ' . $e->getMessage());
            throw $e;
        }
    }
    public function sendEmailOnTenderCommercialResubmitted($tender, $stageType, $stageName)
    {
        try {
            $tenderVendor = (new TenderVendorRepository)->findForNotifyTenderStarted($tender, $stageType);
            $teams = (new TenderEvaluatorRepository)->findByTenderNumber($tender->tender_number);
            $buyer = (new TenderSignatureRepository)->findProposedBy($tender->tender_number);
            $emailTeams = '';
            if ($teams->count() > 0) {
                $emailTeams = $teams->pluck('email')->toArray();
            }

            foreach ($tenderVendor as $vendor) {
                $mailTos = $vendor->pic_email;
                $paramsEmail = [
                    'mailtype' => 'tender_process.tender-resubmission',
                    'subject' => 'RESUBMISSION: ' . $stageName . ' - Commercial ' . $tender->tender_number . ' ' . $tender->title,
                    'view_data' => [
                        'tenderStage' => $stageName . ' - Commercial',
                        'tender' => $tender,
                        'vendor' => $vendor,
                        'buyer' => $buyer,
                        'linkTender' => route('tender.show', ['id' => $tender->id, 'type' => 'process_commercial_evaluation']),
                    ],
                ];
                $details = [
                    'email' => $mailTos,
                    'mailable' => new QueuingMail((object) $paramsEmail),
                ];
                $details['cc'] = [];
                if (!empty($emailTeams)) {
                    $details['cc'] = array_merge($details['cc'], $emailTeams);
                }
                SendEmail::dispatch($details);
            }
        } catch (Exception $e) {
            Log::error($this->logName . '::sendEmailOnTenderCommercialResubmitted error : ' . $e->getMessage());
            throw $e;
        }
    }
    public function sendEmailOnTenderCommercialEvaluated($tender, $stageType, $stageName)
    {
        try {
            $tenderVendor = (new TenderVendorRepository)->findForNotifyTenderEvaluated($tender, $stageType);
            $teams = (new TenderEvaluatorRepository)->findByTenderNumber($tender->tender_number);
            $buyer = (new TenderSignatureRepository)->findProposedBy($tender->tender_number);
            $emailTeams = '';
            if ($teams->count() > 0) {
                $emailTeams = $teams->pluck('email')->toArray();
            }

            foreach ($tenderVendor as $vendor) {
                $mailTos = $vendor->pic_email;
                $paramsEmail = [
                    'mailtype' => 'tender_process.tender-evaluated',
                    'subject' => 'EVALUATED: ' . $stageName . ' - Commercial ' . $tender->tender_number . ' ' . $tender->title,
                    'view_data' => [
                        'tenderStage' => $stageName . ' - Commercial',
                        'tender' => $tender,
                        'vendor' => $vendor,
                        'buyer' => $buyer,
                        'linkTender' => route('tender.show', ['id' => $tender->id, 'type' => 'process_commercial_evaluation']),
                    ],
                ];
                $details = [
                    'email' => $mailTos,
                    'mailable' => new QueuingMail((object) $paramsEmail),
                ];
                $details['cc'] = [];
                if (!empty($emailTeams)) {
                    $details['cc'] = array_merge($details['cc'], $emailTeams);
                }
                SendEmail::dispatch($details);
            }
        } catch (Exception $e) {
            Log::error($this->logName . '::sendEmailOnTenderCommercialEvaluated error : ' . $e->getMessage());
            throw $e;
        }
    }


    public function sendEmailOnTenderNegotiationStarted($tender, $stageType, $params)
    {
        try {
            $tenderVendor = (new TenderVendorRepository)->findForNotifyTenderStarted($tender, $stageType, $params['vendor_id']);
            $teams = (new TenderEvaluatorRepository)->findByTenderNumber($tender->tender_number);
            $buyer = (new TenderSignatureRepository)->findProposedBy($tender->tender_number);
            $emailTeams = '';
            if ($teams->count() > 0) {
                $emailTeams = $teams->pluck('email')->toArray();
            }

            foreach ($tenderVendor as $vendor) {
                $mailTos = $vendor->pic_email;
                $paramsEmail = [
                    'mailtype' => 'tender_process.tender-start',
                    'subject' => 'REQUESTED: Negotiation - ' . $tender->tender_number . ' ' . $tender->title,
                    'view_data' => [
                        'tenderStage' => 'Negotiation',
                        'tender' => $tender,
                        'vendor' => $vendor,
                        'buyer' => $buyer,
                        'linkTender' => route('tender.show', ['id' => $tender->id, 'type' => 'negotiation']),
                    ],
                ];
                $details = [
                    'email' => $mailTos,
                    'mailable' => new QueuingMail((object) $paramsEmail),
                ];
                $details['cc'] = [];
                if (!empty($emailTeams)) {
                    $details['cc'] = array_merge($details['cc'], $emailTeams);
                }
                SendEmail::dispatch($details);
            }
        } catch (Exception $e) {
            Log::error($this->logName . '::sendEmailOnTenderNegotiationStarted error : ' . $e->getMessage());
            throw $e;
        }
    }
    public function sendEmailOnTenderNegotiationResubmitted($tender, $stageType)
    {
        try {
            $tenderVendor = (new TenderVendorRepository)->findForNotifyTenderStarted($tender, $stageType);
            $teams = (new TenderEvaluatorRepository)->findByTenderNumber($tender->tender_number);
            $buyer = (new TenderSignatureRepository)->findProposedBy($tender->tender_number);
            $emailTeams = '';
            if ($teams->count() > 0) {
                $emailTeams = $teams->pluck('email')->toArray();
            }

            foreach ($tenderVendor as $vendor) {
                $mailTos = $vendor->pic_email;
                $paramsEmail = [
                    'mailtype' => 'tender_process.tender-resubmission',
                    'subject' => 'RESUBMISSION: Negotiation - ' . $tender->tender_number . ' ' . $tender->title,
                    'view_data' => [
                        'tenderStage' => 'Negotiation',
                        'tender' => $tender,
                        'vendor' => $vendor,
                        'buyer' => $buyer,
                        'linkTender' => route('tender.show', ['id' => $tender->id, 'type' => 'negotiation']),
                    ],
                ];
                $details = [
                    'email' => $mailTos,
                    'mailable' => new QueuingMail((object) $paramsEmail),
                ];
                $details['cc'] = [];
                if (!empty($emailTeams)) {
                    $details['cc'] = array_merge($details['cc'], $emailTeams);
                }
                SendEmail::dispatch($details);
            }
        } catch (Exception $e) {
            Log::error($this->logName . '::sendEmailOnTenderNegotiationResubmitted error : ' . $e->getMessage());
            throw $e;
        }
    }
    public function sendEmailOnTenderNegotiationEvaluated($tender, $stageType)
    {
        try {
            $tenderVendor = (new TenderVendorRepository)->findForNotifyTenderEvaluated($tender, $stageType);
            $teams = (new TenderEvaluatorRepository)->findByTenderNumber($tender->tender_number);
            $buyer = (new TenderSignatureRepository)->findProposedBy($tender->tender_number);
            $emailTeams = '';
            if ($teams->count() > 0) {
                $emailTeams = $teams->pluck('email')->toArray();
            }

            foreach ($tenderVendor as $vendor) {
                $mailTos = $vendor->pic_email;
                $paramsEmail = [
                    'mailtype' => 'tender_process.tender-evaluated',
                    'subject' => 'EVALUATED: Negotiation - ' . $tender->tender_number . ' ' . $tender->title,
                    'view_data' => [
                        'tenderStage' => 'Negotiation',
                        'tender' => $tender,
                        'vendor' => $vendor,
                        'buyer' => $buyer,
                        'linkTender' => route('tender.show', ['id' => $tender->id, 'type' => 'negotiation']),
                    ],
                ];
                $details = [
                    'email' => $mailTos,
                    'mailable' => new QueuingMail((object) $paramsEmail),
                ];
                $details['cc'] = [];
                if (!empty($emailTeams)) {
                    $details['cc'] = array_merge($details['cc'], $emailTeams);
                }
                SendEmail::dispatch($details);
            }
        } catch (Exception $e) {
            Log::error($this->logName . '::sendEmailOnTenderNegotiationEvaluated error : ' . $e->getMessage());
            throw $e;
        }
    }

    public function sendEmailOnTenderAwardingSubmitted($tender, $stageType, $type)
    {
        try {
            $tenderVendor = (new TenderVendorRepository)->findForNotifyTenderAwarded($tender);
            $teams = (new TenderEvaluatorRepository)->findByTenderNumber($tender->tender_number);
            $buyer = (new TenderSignatureRepository)->findProposedBy($tender->tender_number);
            $emailTeams = '';
            if ($teams->count() > 0) {
                $emailTeams = $teams->pluck('email')->toArray();
            }

            // $pathFile = public_path('storage/tender/' . $tender->tender_number . '/' . 'process_tender_evaluation');

            foreach ($tenderVendor as $vendor) {
                $dataAttc = TenderAwardingAttachment::where([
                    ["tender_number", "=", $tender->tender_number],
                    ["vendor_id", "=", $vendor->vendor_id],
                ])->first();

                $attachment = null;
                if ($dataAttc && isset($dataAttc->attachment) && !empty($dataAttc->attachment)) {
                    $pathFile = public_path('storage') . '/'. 'tender/' . $tender->tender_number . '/' . $type . '' . $dataAttc->attachment;

                    $attachment = [
                        'file' => $pathFile, //. '/' . $vendor->vendor_code . '/attachemnt/5f8a6a6e03524/download.jpg', // public_path('pdf/sample.pdf'),
                        'detail' => [
                            'as' => 'PO Document.jpg',
                            // 'mime' => 'application/pdf',
                        ]
                    ];
                }

                $mailTos = $vendor->pic_email;
                $paramsEmail = [
                    'mailtype' => 'tender_awarding.submit',
                    'subject' => 'EVALUATED: Awarding - ' . $tender->tender_number . ' ' . $tender->title,
                    'view_data' => [
                        'tenderStage' => 'Awarding',
                        'tender' => $tender,
                        'vendor' => $vendor,
                        'buyer' => $buyer,
                        'linkTender' => route('tender.show', ['id' => $tender->id, 'type' => 'awarding_process']),
                    ],
                    'attachment' => $attachment
                ];
                $details = [
                    'email' => $mailTos,
                    'mailable' => new QueuingMail((object) $paramsEmail),
                ];
                $details['cc'] = [];
                if (!empty($emailTeams)) {
                    $details['cc'] = array_merge($details['cc'], $emailTeams);
                }
                SendEmail::dispatch($details);
            }
        } catch (Exception $e) {
            Log::error($this->logName . '::sendEmailOnTenderAwardingSubmitted error : ' . $e->getMessage());
            throw $e;
        }
    }
    public function sendEmailOnTenderAwardingResubmitted($tender, $stageType)
    {
        try {
            $tenderVendor = (new TenderVendorRepository)->findForNotifyTenderAwarded($tender);
            // dd($tenderVendor->count());
            $teams = (new TenderEvaluatorRepository)->findByTenderNumber($tender->tender_number);
            $buyer = (new TenderSignatureRepository)->findProposedBy($tender->tender_number);
            $emailTeams = '';
            if ($teams->count() > 0) {
                $emailTeams = $teams->pluck('email')->toArray();
            }

            foreach ($tenderVendor as $vendor) {
                $mailTos = $vendor->pic_email;
                $paramsEmail = [
                    'mailtype' => 'tender_awarding.resubmit',
                    'subject' => 'UPDATED: Awarding - ' . $tender->tender_number . ' ' . $tender->title,
                    'view_data' => [
                        'tenderStage' => 'Awarding',
                        'tender' => $tender,
                        'vendor' => $vendor,
                        'buyer' => $buyer,
                        'linkTender' => route('tender.show', ['id' => $tender->id, 'type' => 'awarding_process']),
                    ],
                ];
                $details = [
                    'email' => $mailTos,
                    'mailable' => new QueuingMail((object) $paramsEmail),
                ];
                $details['cc'] = [];
                if (!empty($emailTeams)) {
                    $details['cc'] = array_merge($details['cc'], $emailTeams);
                }
                SendEmail::dispatch($details);
            }
        } catch (Exception $e) {
            Log::error($this->logName . '::sendEmailOnTenderAwardingResubmitted error : ' . $e->getMessage());
            throw $e;
        }
    }


    public function findStageType($tender){
        switch($tender->submission_method){
            case '1E':
                $stageName = '1-Envelope';
                $typeName = 'process_tender_evaluation';
                break;
            case '2E':
                $stageName = '2-Envelope Commercial';
                $typeName = 'process_commercial_evaluation';
                break;
            case '2S':
                $stageName = '2-Stage Commercial';
                $typeName = 'process_commercial_evaluation';
                break;
        }
        return ['stageName'=>$stageName, 'typeName'=>$typeName];
    }
    public function sendEmailOnCommercialApprovalSubmit($tender, $approver){
        try {
            $teams = (new TenderEvaluatorRepository())->findByTenderNumber($tender->tender_number);
            $emailTeams = '';
            if($teams->count() > 0){
                $emailTeams = $teams->pluck('email')->toArray(); // implode(',', $teams->pluck('email')->toArray());
            }
            $mailTos = $approver->email;
            $stageType = $this->findStageType($tender);
            $paramsEmail = [
                'mailtype' => 'tender_commercial_approval.proposal_submission',
                'subject' => 'FOR APPROVAL: '.$stageType['stageName'].' - '.$tender->tender_number.' '.$tender->title,
                'view_data' => [
                    'tender' => $tender,
                    'approver' => $approver,
                    'submission_method_name' => $stageType['stageName'],
                    'linkTender' => route('tender.show', ['id' => $tender->id, 'type' => $stageType['typeName'].'?tab=approval']),
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
            Log::error($this->logName . '::sendEmailOnCommercialApprovalSubmit error : ' . $e->getMessage());
            throw $e;
        }
    }
    public function sendEmailOnCommercialApprovalReject($tender, $order, $proposer, $approver, $approvers){
        try {
            $teams = (new TenderEvaluatorRepository())->findByTenderNumber($tender->tender_number);
            $emailTeams = '';
            if($teams->count() > 0){
                $emailTeams = $teams->pluck('email')->toArray(); // implode(',', $teams->pluck('email')->toArray());
            }

            $mailTos = $proposer->email;
            $stageType = $this->findStageType($tender);
            $paramsEmail = [
                'mailtype' => 'tender_commercial_approval.proposal_rejected',
                'subject' => 'REVISED: '.$stageType['stageName'].' - '.$tender->tender_number.' '.$tender->title,
                'view_data' => [
                    'tender' => $tender,
                    'proposer' => $proposer,
                    'approver' => $approver,
                    'submission_method_name' => $stageType['stageName'],
                    'linkTender' => route('tender.show', ['id' => $tender->id, 'type' => $stageType['typeName'].'?tab=approval']),
                ],
            ];
            $details = [
                'email' => $mailTos,
                'mailable' => new QueuingMail((object) $paramsEmail),
            ];

            $details['cc'] = [$approver->email];

            //cc previous approvers
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
            Log::error($this->logName . '::sendEmailOnCommercialApprovalReject error : ' . $e->getMessage());
            throw $e;
        }
    }
    public function sendEmailOnCommercialApprovalApproved($tender, $order, $approver, $nextApprover){
        try {
            $teams = (new TenderEvaluatorRepository())->findByTenderNumber($tender->tender_number);
            $emailTeams = '';
            if($teams->count() > 0){
                $emailTeams = $teams->pluck('email')->toArray();
            }

            $mailTos = $nextApprover->email;
            $stageType = $this->findStageType($tender);
            $paramsEmail = [
                'mailtype' => 'tender_commercial_approval.proposal_approved',
                'subject' => 'FOR APPROVAL: '.$stageType['stageName'].' - '.$tender->tender_number.' '.$tender->title,
                'view_data' => [
                    'tender' => $tender,
                    'approver' => $approver,
                    'nextApprover' => $nextApprover,
                    'submission_method_name' => $stageType['stageName'],
                    'linkTender' => route('tender.show', ['id' => $tender->id, 'type' => $stageType['typeName'].'?tab=approval']),
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
    public function sendEmailOnCommercialApprovalFullyApproved($tender, $proposer, $approvers){
        try {
            $teams = (new TenderEvaluatorRepository())->findByTenderNumber($tender->tender_number);
            $emailTeams = '';
            if($teams->count() > 0){
                $emailTeams = $teams->pluck('email')->toArray();
            }

            $mailTos = $proposer->email;
            $stageType = $this->findStageType($tender);
            $paramsEmail = [
                'mailtype' => 'tender_commercial_approval.proposal_fully_approved',
                'subject' => 'APPROVED: '.$stageType['stageName'].' - '.$tender->tender_number.' '.$tender->title,
                'view_data' => [
                    'tender' => $tender,
                    'proposer' => $proposer,
                    'submission_method_name' => $stageType['stageName'],
                    'linkTender' => route('tender.show', ['id' => $tender->id, 'type' => $stageType['typeName'].'?tab=approval']),
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
            Log::error($this->logName . '::sendEmailOnCommercialApprovalFullyApproved error : ' . $e->getMessage());
            throw $e;
        }
    }
}
