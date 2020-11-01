<?php
//access: config('workflow.')
return [
    'tender' => [

        //definition for guarded table fields.
        'tables' => [],
        //definition for pages and table needed to show and store data.
        'pages' => [
            'parameters'         => ['tender_parameters'],
            'internal_documents' => ['tender_internal_documents'],
            'general_documents'  => ['tender_general_documents'],
            'aanwijzings'        => ['tender_aanwijzings'],
            'items'              => ['tender_items'],
            'proposed_vendors'   => ['tender_vendors'],
            'weightings'         => ['tender_weightings'],
            'evaluators'         => ['tender_evaluators'],
            'bidding_document_requirements' => ['tender_bidding_document_requirements'],
            'schedules' => ['tender_schedule'],
            'procurement_approval' => ['tender_procurement_approval'],
            'process_registration' => ['tender_process_registration'],
            'process_prequalification' => ['tender_process_prequalification'],
            'process_tender_evaluation' => ['process_tender_evaluation'],

            // 'process_bid_opening' => ['tender_process_bid_opening'],
            // 'process_bid_evaluation' => ['tender_process_bid_evaluation'],

            'process_technical_evaluation' => ['process_technical_evaluation'],
            'process_commercial_evaluation' => ['process_commercial_evaluation'],

            'auction' => ['tender_auction'],
            'negotiation' => ['tender_negotiation'],
            'awarding_process' => ['tender_awarding_process'],
            // 'awarding_approval' => ['tender_awarding_approval'],
            // 'create_po' => ['tender_create_po'],
            'po_creation' => ['tender_create_po'],
        ],

        //definition for available pages in each step (wk).
        //still need to check parameter if the page really is available
        'pages_available' => [
            'wk0'   => ['parameters',],
            'wk1'   => [
                'internal_documents', 'general_documents', 'aanwijzings',
                'items', 'proposed_vendors',
                'weightings', 'evaluators', 'bidding_document_requirements', 'schedules'
            ],
            'wk2'   => ['procurement_approval'],
            // 'wk3'   => ['process_registration','process_prequalification','process_bid_opening','process_bid_evaluation'],
            'wk3'   => ['process_registration','process_prequalification','process_tender_evaluation','process_technical_evaluation','process_commercial_evaluation'],
            'wk4'   => ['auction'],
            'wk5'   => ['negotiation'],
            'wk6'   => ['awarding_process'],
            // 'wk7'   => ['awarding_approval'],
            // 'wk8'   => ['create_po'],
            'wk8'   => ['po_creation'],
        ],

        //definition for the order of the workflow
        'order' => [
            'tender_requirements'           => ['wk0', 'wk1'],
            'procurement_approval'          => ['wk2'],
            'tender_process'          => ['wk3'],
            'auction'                       => ['wk4'],
            'negotiation'                   => ['wk5'],
            'awarding_process'              => ['wk6'],
            // 'awarding_approval'             => ['wk7'],
            // 'create_po'                     => ['wk8'],
            'po_creation'                     => ['wk8'],
        ],

        'status' => ['draft','announcement','active','cancelled','discarded','completed'],
        'page_status' => [
            'parameters'         => 0,
            'internal_documents' => 0,
            'general_documents'  => 0,
            'aanwijzings'        => 0,
            'items'              => 0,
            'proposed_vendors'   => 0,
            'weightings'         => 0,
            'evaluators'         => 0,
            'bidding_document_requirements' => 0,
            'schedules' => 0,
            'procurement_approval' => 0,
            'process_registration' => 2,
            'process_prequalification' => 2,
            'process_tender_evaluation' => 2,
            'process_technical_evaluation' => 2,
            'process_commercial_evaluation' => 2,
            'auction' => 2,
            'negotiation' => 2,
            'awarding_process' => 2,
            // 'awarding_approval' => 2,
            // 'create_po' => 2 //was 5. when is the tender complete?
            'po_creation' => 2 //was 5. when is the tender complete?
        ],


    ], //END TENDER WORKFLOW CONFIGURATION

    'applicant-submission' => [
        'tasks' => [
            0 => [
                'activity' => 'Form Submission',
                'started_at' => 'now',
                'finished_at' => 'now',
                'remarks' => 'Form Submission',
                'permission' => '',
            ],
            1 => [
                'activity' => 'Workflow Started',
                'started_at' => 'now',
                'finished_at' => 'now',
                'remarks' => 'Workflow Started',
                'permission' => '',
            ],
            2 => [
                'activity' => 'Approval By Admin',
                'started_at' => 'now',
                'finished_at' => 'null',
                'remarks' => '',
                'permission' => 'submit_applicant_approval',
            ],
        ]
    ],

    'vendor-submission' => [
        'tasks' => [
            0 => [
                'activity' => 'Approval By Admin',
                'started_at' => 'now',
                'finished_at' => 'null',
                'remarks' => '',
                'permission' => 'update_vendor_admin_approval',
                'approver' => 'Admin Vendor',
            ],
            1 => [
                'activity' => 'Approval By QMR',
                'started_at' => 'null',
                'finished_at' => 'null',
                'remarks' => '',
                'permission' => 'update_vendor_qmr_approval',
                'approver' => 'QMR',
            ],
        ]
    ],

    'update-vendor-submission' => [
        'tasks' => [
            0 => [
                'activity' => 'Approval By Admin',
                'started_at' => 'now',
                'finished_at' => 'null',
                'remarks' => '',
                'permission' => 'update_vendor_admin_approval',
                'approver' => 'Admin Vendor',
            ],
            1 => [
                'activity' => 'Approval By QMR',
                'started_at' => 'null',
                'finished_at' => 'null',
                'remarks' => '',
                'permission' => 'update_vendor_qmr_approval',
                'approver' => 'QMR',
            ],
        ]
    ],

    'sanction-submission' => [
        'tasks' => [
            0 => [
                'activity' => 'Initial Submission',
                'started_at' => 'now',
                'finished_at' => 'now',
                'remarks' => 'Submission',
                'permission' => 'vendor_sanction_modify',
                'approver' => 'Admin Vendor',
            ],
            1 => [
                'activity' => 'Approval By Procurement Manager',
                'started_at' => 'now',
                'finished_at' => 'null',
                'remarks' => '',
                'permission' => 'vendor_sanction_approval',
                'approver' => 'Procurement Manager',
            ],
        ]
    ],

    'evaluation-submission' => [
        'tasks' => [
            0 => [
                'activity' => 'Initial Submission',
                'started_at' => 'now',
                'finished_at' => 'now',
                'remarks' => 'Submission',
                'permission' => 'vendor_evaluation_modify',
                'approver' => 'Admin Vendor',
            ],
            1 => [
                'activity' => 'Approval By Procurement Manager',
                'started_at' => 'now',
                'finished_at' => 'null',
                'remarks' => '',
                'permission' => 'vendor_evaluation_approval',
                'approver' => 'Procurement Manager',
            ],
        ]
    ]

];
