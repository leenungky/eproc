<?php

namespace App\Enums;

class TenderSubmissionEnum
{
    const STAGE_TYPE = [
        1 => 'pre_qualification',
        2 => 'offer',
        3 => 'technical_offer',
        4 => 'commercial_offer',
        5 => 'negotiation_technical_offer',
        6 => 'negotiation_commercial_offer',
    ];

    const FLOW_STATUS = [
        1 => 'start',
        2 => 'open',
        3 => 'request_resubmission',
        4 => 'open_resubmission',
        5 => 'finish',
        6 => 'complete',
    ];

    const WORKFLOW_MAPPING_TYPE = [
        'process_prequalification' => 1,
        'process_tender_evaluation' => 2,
        'process_technical_evaluation' => 3,
        'process_commercial_evaluation' => 4,
        'negotiation_technical' => 5,
        'negotiation_commercial' => 6,
        'awarding_process' => 7,
    ];

    const STATUS_ITEM = [
        1 => 'draft',
        2 => 'submitted',
        3 => 'accepted',
        4 => 'rejected',
        5 => 'approved'
    ];
}
