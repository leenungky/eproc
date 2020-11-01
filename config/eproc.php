<?php
//this is default eproc config that will apply in all environments. if want to change, please change below.
//Find string "Change as you need here."

$defaults = [
    'max_file_upload_size' => env('MAX_FILE_UPLOAD', 5) * 1024, //5Mb
    'cachetime' => 300, //in seconds
    'company_code' => 1000,  //sap company code
    'default_currency' => 'IDR',
    'default_email' => 'lee.nungky@gmail.com',
    'default_onshore_email' => 'testabata@gmail.com',
    'default_offshore_email' => 'testabata@gmail.com',
    'default_legacy_password' => 'password[VENDOR_CODE]',
    'default_max_legacy_insert_per_transaction' => 10, //0: no maximum.
    'footer_text' => 'VOPROC - Copyright',
    'theme' => 'style2', //[style, style2]
    'mail_footnote' => '
        "This email is an automated notification, which is unable to receive replies.
        We\'re happy to help you with any questions or concerns you may have.
        Please contact us directly at +6221 352 2828 for [Purchog_Onshore]
        / +6221 2992 1828 for [Purchog_Offshore] or email us at eproc@timas.com"
    ',
    'templates' => [
        'excel' => [
            'cbe' => [
                'file' => '/templates/Commercial_Bid_Evaluation.xlsx',
            ],
            'tbe' => [
                'file' => '/templates/Technical_Bid_Evaluation.xlsx',
            ],
            'nbe' => [
                'file' => '/templates/Negotiation_Bid_Evaluation.xlsx',
            ],
        ]
    ],
    'sap' => [
        'default_variables' => [
            'PARTN_CAT' => '2',
            'PARTN_GRP' => [
                'local' => 'Z001',
                'foreign' => 'Z002'
            ]
        ],
        'functions' => [
            'pr_list' => [
                'wsdl' => env('SAP_URL', 'http://s4hdev-app1.timas.com:8030') . '/sap/bc/srt/wsdl/flv_10002A111AD1/bndg_url/sap/bc/srt/rfc/sap/zws_pr_list/310/zws_pr_list/zws_pr_list?sap-client=310',
                'proxy_function' => 'ZFM_PR_LIST', // 'ZfmPrList',
                'parameters' => [
                    'input' => [
                        'I_PR_LIST' => 'X',
                    ],
                    'output' => [
                        'TPrNo' => [
                            'item' => [
                                'Banfn' => ''
                            ]
                        ],
                        'TReturn' => [
                            'item' => [
                                'Txz01' => '',
                            ]
                        ],
                    ]
                ],
            ],
            'create_update_bp' => [
                'wsdl' => env('SAP_URL', 'http://s4hdev-app1.timas.com:8030') . '/sap/bc/srt/wsdl/flv_10002A111AD1/bndg_url/sap/bc/srt/rfc/sap/zws_bp_create_change/310/zws_bp_create_change/zws_bp_create_change?sap-client=310',
                'proxy_function' => 'ZFM_BP_CRCH',
                'parameters' => [
                    'input' => [
                        'I_DATA' => [
                            'PROC_TYPE' => '',
                            'PARTNER_NO' => '',
                            'VENDOR_NO' => '',
                            'PARTN_CAT' => '',
                            'PARTN_GRP' => '',
                            'SEARCHTERM1' => '',
                            'SEARCHTERM2' => '',
                            'NAME1' => '',
                            'NAME2' => '',
                            'NAME3' => '',
                            'NAME4' => '',
                            'EMAIL' => '',
                            'FAX' => '',
                            'POST_CODE1' => '',
                            'PO_BOX' => '',
                            'TEL_NUMBER' => '',
                            'STREET' => '',
                            'HOUSE_NO' => '',
                            'STR_SUPPL1' => '',
                            'STR_SUPPL2' => '',
                            'STR_SUPPL3' => '',
                            'LOCATION' => '',
                            'DISTRICT' => '',
                            'BUILDING' => '',
                            'FLOOR' => '',
                            'COUNTRY' => '',
                            'REGION' => '',
                            'CITY' => '',
                            'PARTNERLANGUAGE' => '',
                            'BUKRS' => '',
                            'AKONT' => '',
                            'FDGRV' => '',
                            'ZTERM' => '',
                            'REPRF' => '',
                            'ZWELS' => '',
                            'HBKID' => '',
                        ],
                        'T_BANK' => [
                            'item' => [       //bisa lebih dari satu items
                                'PARTNER_NO' => '',
                                'VENDOR_NO' => '',
                                'BKVID' => '',
                                'BANKS' => '',
                                'BANKL' => '',
                                'BANKN' => '',
                                'KOINH' => '',
                            ],
                        ],
                        'T_PURCHASING' => [
                            'item' => [       //bisa lebih dari satu items
                                'PARTNER_NO' => '',
                                'VENDOR_NO' => '',
                                'EKORG' => '',
                                'WAERS' => '',
                                'ZTERM' => '',
                                'VERKF' => '',
                                'TELF1' => '',
                                'EMAIL' => '',
                                'WEBRE' => '',
                                'LEBRE' => '',
                            ],
                        ],
                        'T_TAX' => [
                            'item' => [       //bisa lebih dari satu items
                                'PARTNER_NO' => '',
                                'VENDOR_NO' => '',
                                'TAX_TYPE' => '',
                                'TAX_NUMBER' => '',
                            ],
                        ],
                        'T_WITHT' => [
                            'item' => [       //bisa lebih dari satu items
                                'PARTNER_NO' => '',
                                'VENDOR_NO' => '',
                                'WT_WITHT' => '',
                                'WT_SUBJCT' => '',
                            ],
                        ],
                    ],
                    'output' => [
                        'O_DATA' => [
                            'PARTNER_NO' => ''
                        ],
                        'RETURN' => [
                            'item' => [
                                'TYPE' => '' //S: Succcess, E: Error
                            ],
                        ],
                    ],
                ],
            ],
            'bank_list' => [
                'wsdl' => env('SAP_URL', 'http://s4hdev-app1.timas.com:8030') . '/sap/bc/srt/wsdl/flv_10002A111AD1/bndg_url/sap/bc/srt/rfc/sap/zws_bp_get_bank_master/310/zws_bp_get_bank_master/zws_bp_get_bank_master?sap-client=310',
                'proxy_function' => 'ZFM_BP_GET_BANK_MASTER',
                'parameters' => [
                    'input' => [],
                    'output' => [
                        'T_BANK_MASTER' => [
                            'item' => [
                                'BANKS' => '',
                                'BANKL' => '',
                                'BANKA' => '',
                                'LOEVM' => '',
                            ]
                        ]
                    ],
                ],
            ],
            'bp_sanction' => [
                'wsdl' => env('SAP_URL', 'http://s4hdev-app1.timas.com:8030') . '/sap/bc/srt/wsdl/flv_10002A111AD1/bndg_url/sap/bc/srt/rfc/sap/zws_bp_sanction/310/zws_bp_sanction/zws_bp_sanction?sap-client=310',
                'proxy_function' => 'ZFM_BP_SANCTION',
                'parameters' => [
                    'input' => [
                        'T_DATA' => [
                            'item' => [
                                'PROC_TYPE' => '',        //5: unblock, 4: block
                                'PARTNER_NO' => '',
                                'VENDOR_NO' => '',
                                'EKORG' => '',
                            ],
                        ],
                    ],
                    'output' => [
                        'RETURN' => [
                            'item' => [
                                'PARTNER_NO' => '',
                            ]
                        ]
                    ],
                ],
            ],
            'project_list' => [
                'wsdl' => env('SAP_URL', 'http://s4hdev-app1.timas.com:8030') . '/sap/bc/srt/wsdl/flv_10002A111AD1/bndg_url/sap/bc/srt/rfc/sap/zws_bp_get_project/310/zws_bp_get_project/zws_bp_get_project?sap-client=310',
                'proxy_function' => 'ZFM_BP_GET_PROJECT',
                'parameters' => [
                    'input' => [],
                    'output' => [
                        'RETURN' => ['TYPE'],
                        'T_PROJECT' => [
                            'item' => [
                                'PROJECT_DEFINITION'
                            ]
                        ]
                    ]
                ]
            ],
            'legacy_list' => [
                'wsdl' => env('SAP_URL', 'http://s4hdev-app1.timas.com:8030') . '/sap/bc/srt/wsdl/flv_10002A111AD1/bndg_url/sap/bc/srt/rfc/sap/zws_bp_vendor_list_legacy/310/zws_bp_vendor_list_legacy/zws_bp_vendor_list_legacy?sap-client=310',
                'proxy_function' => 'ZFM_BP_VENDOR_LIST_LEGACY',
                'parameters' => [
                    'input' => [
                        'I_PURCHORG_CODE' => [
                            'EKORG_LOW' => '',
                            'EKORG_HIGH' => '',
                        ],
                        'I_VENDOR_NO' => [
                            'VENDOR_LOW' => '',
                            'VENDOR_HIGH' => '',
                        ]
                    ],
                    'output' => [
                        'I_DATA' => [
                            'item' => [
                                'PARTNER_NO' => ''
                            ]
                        ],
                        'RETURN' => [
                            'TYPE' => ''
                        ],
                        'T_BANK' => [
                            'item' => [
                                'PARTNER_NO' => ''
                            ]
                        ],
                        'T_PURCHASING' => [
                            'item' => [
                                'PARTNER_NO' => ''
                            ]
                        ],
                        'T_TAX' => [
                            'item' => [
                                'PARTNER_NO' => ''
                            ]
                        ],
                        'T_WITHT' => [
                            'item' => [
                                'PARTNER_NO' => ''
                            ]
                        ],
                    ],
                ],
            ],
            'create_po' => [
                'wsdl' => env('SAP_URL', 'http://s4hdev-app1.timas.com:8030') . '/sap/bc/srt/wsdl/flv_10002A111AD1/bndg_url/sap/bc/srt/rfc/sap/zws_po_create/310/zws_po_create/zws_po_create?sap-client=310',
                'proxy_function' => 'ZFM_PO_CREATE',
                'parameters' => [
                    'input' => [
                        'T_CONDHEAD' => [
                            'item' => [
                                'RUN_ID' => '',
                                'COND_COUNT' => '',
                                'COND_TYPE' => '',
                                'COND_VALUE' => '',
                                'CURRENCY' => '',
                            ],
                        ],
                        'T_CONDITEM' => [
                            'item' => [
                                'RUN_ID' => '',
                                'PO_ITEM' => '',
                                'COND_COUNT' => '',
                                'COND_TYPE' => '',
                                'COND_VALUE' => '',
                                'CURRENCY' => '',
                            ],
                        ],
                        'T_DETAIL' => [
                            'item' => [
                                'RUN_ID' => '',
                                'PO_ITEM' => '',
                                'EXT_LINE' => '',
                                'SERVICE' => '',
                                'QUANTITY' => '',
                                'GR_PRICE' => '',
                                'PRICE_UNIT' => '',
                                'BASE_UOM' => '',
                                'LIMIT' => '',
                                'GL_ACCOUNT' => '',
                                'COST_CODE' => '',
                                'USERF1_TEXT' => '',
                                'USERF2_TEXT' => '',
                            ],
                        ],
                        'T_HEADER' => [
                            'item' => [
                                'RUN_ID' => '',
                                'DOC_TYPE' => '',
                                'DOC_DATE' => '',
                                'PURCH_ORG' => '',
                                'PUR_GROUP' => '',
                                'VENDOR' => '',
                                'CURRENCY' => '',
                                'SALES_PERS' => '',
                                'TELEPHONE' => '',
                                'YOUR_REF' => '',
                                'OUR_REF' => '',
                                'PMNTTRMS' => '',
                                'INCOTERM1' => '',
                                'INCOTERMS2L' => '',
                                'DOWNPAY_TYPE' => '',
                                'DOWNPAY_PERCENT' => '',
                                'DOWNPAY_AMOUNT' => '',
                                'DOWNPAY_DUEDATE' => '',
                                'RETENTION_TYPE' => '',
                                'RETENTION_PERCENTAGE' => '',
                                'TRANS_VIA' => '',
                                'MODA_TRANS' => '',
                                'TKDN_OVERALL' => '',
                            ],
                        ],
                        'T_ITEM' => [
                            'item' => [
                                'RUN_ID' => '',
                                'PO_ITEM' => '',
                                'ITEM_CAT' => '',
                                'ACCTASSCAT' => '',
                                'MATERIAL' => '',
                                'SHORT_TEXT' => '',
                                'DELIVERY_DATE' => '',
                                'MATL_GROUP' => '',
                                'PLANT' => '',
                                'STGE_LOC' => '',
                                'QUANTITY' => '',
                                'NET_PRICE' => '',
                                'LIMIT_AMOUNT' => '',
                                'PRICE_UNIT' => '',
                                'PREQ_NAME' => '',
                                'TRACKINGNO' => '',
                                'TAX_CODE' => '',
                                'GR_BASEDIV' => '',
                                'GR_NON_VAL' => '',
                                'PO_UNIT' => '',
                                'GL_ACCOUNT' => '',
                                'COST_CODE' => '',
                                'PREQ_NO' => '',
                                'PREQ_ITEM' => '',
                            ],
                        ],
                        'T_TEXTHEAD' => [
                            'item' => [
                                'RUN_ID' => '',
                                'TEXT_ID' => '',
                                'TEXT_FORM' => '',
                                'TEXT_LINE' => '',
                            ],
                        ],
                        'T_TEXTITEM' => [
                            'item' => [
                                'RUN_ID' => '',
                                'PO_ITEM' => '',
                                'TEXT_ID' => '',
                                'TEXT_FORM' => '',
                                'TEXT_LINE' => '',
                            ],
                        ],
                    ],
                    'output' => [],
                ]
            ],
        ],
        'showed_fields' => [
            'prlist' => [
                'BANFN' =>    'number',
                'BNFPO' =>    'line_number',
                'MATNR' =>    'product_code',
                'MATKL' =>    'product_group_code',
                'TXZ01' =>    'description',
                'EKGRP' =>    'purch_group_code',
                'EKNAM' =>    'purch_group_name',
                'MENGE' =>    'qty',
                'MEINS' =>    'uom',
                'PREIS' =>    'est_unit_price',
                'PEINH' =>    'price_unit',
                'WAERS' =>    'currency_code',
                'PREIS2' =>    'subtotal',
                'LFDAT' =>    'expected_delivery_date',
                'LOEKZ' =>    'deleteflg',
                'KNTTP' =>    'account_assignment',
                'PSTYP' =>    'item_category',
                'SAKTO' =>    'gl_account',
                'COST_CODE' =>    'cost_code',
                'AFNAM' =>    'requisitioner',
                'ZRDESC' =>    'requisitioner_desc',
                'BEDNR' =>    'tracking_number',
                'BADAT' =>    'request_date',
                'ZZCERT' =>    'certification',
                'ZZSTAT' =>    'material_status',
                'WERKS' =>    'plant',
                'NAME1' =>    'plant_name',
                'LGORT' =>    'storage_loc',
                'LGOBE' =>    'storage_loc_name',
                'BSMNG' =>    'qty_ordered',
                'COST_DESC' =>    'cost_desc',
                'SUMLIMIT' =>    'overall_limit',
                'COMMITMENT' =>    'expected_limit',
            ],
            'prlist_services' => [
                'BANFN' =>    'number',
                'BNFPO' =>    'line_number',
                'EXTROW' => 'extrow',
                'SRVPOS' => 'srvpos',
                'KTEXT1' => 'ktext1',
                'MENGE' => 'qty',
                'MEINS' => 'uom',
                'WAERS' => 'currency_code',
                'BRTWR' => 'est_unit_price',
                'NETWR' => 'netwr',
                'COST_CODE' => 'cost_code',
                'COST_DESC' => 'cost_desc',
            ],
            'prlist_item_text' => [
                'PREQ_NO' =>    'number',
                'PREQ_ITEM' =>    'line_number',
                'TEXT_ID' => 'text_id',
                'TEXT_ID_DESC' => 'text_id_desc',
                'TEXT_FORM' => 'text_form',
                'TEXT_LINE' => 'text_line',
            ],
            'bank_list' => [
                'BANKS' => 'country_code',
                'BANKL' => 'bank_key',
                'BANKA' => 'description',
                'LOEVM' => 'deleteflg',
            ],
            'bp_sanction' => [
                'PARTNER_NO' => 'bp_code',
                'VENDOR_NO' => 'sap_code',
                'TYPE' => 'type',
                'ID' => 'id',         //ok or ??
                'NUMBER' => 'number',       //3 digits?
                'MESSAGE' => 'message',
                'LOG_NO' => 'log_no',
                'LOG_MSG_NO' => 'log_message',
                'MESSAGE_V1' => 'message_1',
                'MESSAGE_V2' => 'message_2',
                'MESSAGE_V3' => 'message_3',
                'MESSAGE_V4' => 'message_4',
                'PARAMETER' => 'parameter',
                'ROW' => 'row',
                'FIELD' => 'field',
                'SYSTEM' => 'system',
            ],
            'create_update_bp' => [],
            'project_list' => [
                'PROJECT_DEFINITION' => 'code',
                'PROJECT_DESCRIPTION' => 'name',
                'COMPANY_CODE' => 'company_code',
                'PLANT' => 'plant',
                'PURCHASING_ORG' => 'purchasing_org',
                'START_DATE' => 'start_date',
                'FINISH_DATE' => 'finish_date',
                'SYSTEM_STATUS' => 'system_status',
                'USER_STATUS' => 'user_status'
            ],
        ],
    ],

    'tender_status_options' => [
        'DRAFT' => 'draft',
        'ANNOUNCEMENT' => 'announcement',
        'ACTIVE' => 'active',
        'CANCELLED' => 'cancelled',
        'DISCARDED' => 'discarded',
        'COMPLETED' => 'completed',
    ],

    'tender_number' => [
        'prepend' => 'RFQ-',
        'pad' => 7,
    ],

    'po_number' => [
        'prepend' => 'PO-',
        'pad' => 7,
    ],

    'avl_number' => [
        'format' => "[NUMBER]/[MM]/[YYYY]",
        'pad' => 5,
        'max' => 99999,
    ],

    'vendor_management' => [
        'tin_foreign' => 'TIMAS Tax Number',
        'company_description' => 'PT Timas Suplindo',
    ],

    'upload_file_size' => 10240, // 10MB

    'vendor_evaluation' => [
        'score_assignment' => 'criteria', //criteria or evaluation
        'workflow' => [
            'concept' => ['Admin Vendor', 'manual'],
            'submission' => ['Admin Vendor', 'manual'],
            'pending_approval' => ['Procurement Manager', 'auto'],
            'approval' => ['Procurement Manager', 'manual']
        ],
        'statuses' => [
            'CONCEPT' => 'CONCEPT',
            'SUBMISSION' => 'SUBMISSION',
            'REVISE' => 'REVISE',
            'APPROVED' => 'APPROVED'
        ],
        'pages' => [
            'general' => 'general',
            'assignment' => 'assignment',
            'form' => 'form',
        ],
        'vendor_selector' => [
            'YEARLY',
            'PROJECT'
        ],
        'email_config' => [
            'SUBMISSION' => [
                'mailtype' => 'evaluation_submit',
                'to' => ['Procurement Manager'],
                'cc' => ['Buyer'],
                'subject' => 'SUBMITTED: Vendor Evaluation [NAME]',
            ],
            'REVISE' => [
                'mailtype' => 'evaluation_revise',
                'to' => ['Buyer'],
                'cc' => ['Procurement Manager'],
                'subject' => 'REVISED: Vendor Evaluation [NAME]',
            ],
            'APPROVED' => [
                'mailtype' => 'evaluation_approve',
                'to' => ['Procurement Manager'],
                'cc' => ['Buyer'],
                'subject' => 'EVALUATED: Vendor Evaluation [NAME]',
            ],
        ],
    ],

    'vendor_sanction' => [
        'sanction_statuses' => [
            'SUBMITTED' => 'SUBMITTED',
            'APPROVED' => 'APPROVED',
            'REVISE' => 'REVISE'
        ],
        'workflow' => [
            'submission' => ['Admin Vendor', 'auto'],
            'pending_approval' => ['Procurement Manager', 'auto'],
            'approval' => ['Procurement Manager', 'manual']
        ],
        'email_config' => [
            'SUBMISSION' => [
                'mailtype' => 'sanction_approval',
                'to' => ['Procurement Manager'],
                'cc' => ['Admin Vendor'],
                'subject' => 'FOR APPROVAL: Vendor Sanction [NAME]'
            ],
            'REVISE' => [
                'mailtype' => 'sanction_revise',
                'to' => ['Admin Vendor'],
                'cc' => ['Procurement Manager'],
                'subject' => 'REVISED: Vendor Sanction [NAME]'
            ],
            'APPROVAL' => [
                'mailtype' => [
                    'RED' => 'sanction_red',
                    'GREEN' => 'sanction_green',
                    'YELLOW' => 'sanction_yellow',
                    'UNBLACKLIST' => 'sanction_unblacklist',
                ],
                'subject' => [
                    'RED' => 'BLACKLISTED: Vendor Sanction [NAME]',
                    'GREEN' => 'NO SANCTION: Vendor Sanction [NAME]',
                    'YELLOW' => 'WARNING: Vendor Sanction [NAME]',
                    'UNBLACKLIST' => 'UNBLACKLIST: Vendor Sanction [NAME]',
                ],
                'to' => 'vendor',
                'cc' => ['Procurement Manager', 'Admin Vendor', 'Buyer'],
            ],
        ],
    ],
    'document_type' => [
        ["ZPAC", "PO_ASSET_COR"], ["ZPAP", "PO_ASSET_PROJECT"], ["ZPOL", "PO_PRO_MAT"], ["ZPOM", "PO_COR_MAT"],
        ["ZSOC", "SO_PRO_CPA"], ["ZSOE", "SO_PRO_SER"], ["ZSOG", "SO_PRO_MSA"], ["ZSOC", "SO_COR_SER"]
    ],
    'location_category' => ["Head Office", "Representative Office", "Branch Office"],
];

//Change as you need here.
if (env('APP_ENV') == 'local') {
    $defaults['theme']="style2";
    $defaults['default_email'] = 'lee.nungky@gmail.com';
    $defaults['default_onshore_email'] = 'testabata@gmail.com';
    $defaults['default_offshore_email'] = 'testabata@gmail.com';
    $defaults['default_legacy_password'] = 'password[VENDOR_CODE]';
} else {
    $defaults['theme']="style";
    $defaults['default_email'] = 'lee.nungky@gmail.com';
    $defaults['default_onshore_email'] = 'testabata@gmail.com';
    $defaults['default_offshore_email'] = 'testabata@gmail.com';
    $defaults['default_legacy_password'] = 'password[VENDOR_CODE]';
}

return $defaults;
