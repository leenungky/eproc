<?php
//access: config('tables.')
// for field maximum length checking
return [
    'page_contents' => [
        'language' => 64,
        'title' => 255,
    ],
    'ref_buyers' => [
        'buyer_name' => 255,
    ],
    'ref_material_groups' => [
        'description' => 255,
    ],
    'users' => [
        'name' => 255,
        'userid' => 255,
        'email' => 255,
        'password' => 255,
    ],
    'user_extensions' => [
        'position' => 255,
    ],
    'vendors' => [
        'vendor_name' => 255,
        'president_director' => 200,
        'address_1' => 255,
        'address_2' => 255,
        'address_3' => 255,
        'address_4' => 255,
        'address_5' => 255,
        'house_number' => 20,
        'postal_code' => 20,
        'phone_number' => 26,
        'phone_number_ext' => 5,
        'fax_number' => 26,
        'fax_number_ext' => 5,
        'company_email' => 255,
        'company_site' => 255,
        'pic_full_name' => 200,
        'pic_mobile_number' => 32,
        'pic_email' => 255,
        'tender_ref_number' => 255,
        'idcard_number' => 100,
        'tin_number' => 100,
        'pkp_number' => 100,
        'non_pkp_number' => 100,
        'tax_number' => 100,
        'street' => 255,
        'building_name' => 255,
        'kavling_floor_number' => 255,
        'village' => 255,
        'rt' => 3,
        'rw' => 3,
        'identity_number' => 100,
        'tender_ref_number' => 255,
        'tax_identification_number' => 100,
    ],
    'vendor_evaluation_criterias' => [
        'name' => 64,
        'description' => 255,
    ],
    'vendor_evaluation_criteria_groups' => [
        'name' => 64,
    ],
    'vendor_evaluation_generals' => [
        'name' => 64,
        'description' => 255,
    ],
    'vendor_evaluation_scores' => [
        'name' => 32,
    ],
    'vendor_evaluation_score_categories' => [
        'name' => 32,
    ],
    'vendor_profile_bank_accounts' => [
        'account_number' => 255,
        'account_holder_name' => 255,
        'bank_address' => 255,
    ],
    'vendor_profile_bodbocs' => [
        'full_name' => 255,
        'position' => 255,
        'email' => 255,
        'phone_number' => 255,
    ],
    'vendor_profile_business_permits' => [
        'business_permit_number' => 255,
        'issued_by' => 255,
    ],
    'vendor_profile_certifications' => [
        'description' => 255,
    ],
    'vendor_profile_competencies' => [
        'detail_competency' => 255,
    ],
    'vendor_profile_deeds' => [
        'deed_number' => 255,
        'notary_name' => 255,
        'sk_menkumham_number' => 255,
    ],
    'vendor_profile_experience' => [
        'project_name' => 255,
        'project_location' => 255,
        'contract_owner' => 255,
        'address' => 255,
        'postal_code' => 255,
        'contact_person' => 255,
        'phone_number' => 255,
        'contract_number' => 255,
        'contract_value' => 255,
        'bast_wan_number' => 255,
    ],
    'vendor_profile_experts' => [
        'full_name' => 255,
        'education' => 255,
        'university' => 255,
        'experts_university' => 255,
        'major' => 255,
        'ktp_number' => 255,
        'address' => 720,
        'job_experience' => 720,
        'certification_number' => 255,
    ],
    'vendor_profile_financials' => [
        'public_accountant_full_name' => 255,

    ],
    'vendor_profile_generals' => [
        'company_name' => 255,
        'postal_code' => 20,
        'address_1' => 255,
        'address_2' => 255,
        'address_3' => 255,
        'phone_number' => 26,
        'phone_number_ext' => 5,
        'fax_number' => 26,
        'fax_number_ext' => 5,
        'website' => 255,
        'company_email' => 255,
        'street' => 255,
        'building_name' => 255,
        'kavling_floor_number' => 255,
        'village' => 255,
        'rt' => 3,
        'rw' => 3,
        'house_number' => 20,
        'postal_code' => 20
    ],
    'vendor_profile_pics' => [
        'username' => 255,
        'full_name' => 255,
        'email' => 255,
        'phone' => 255,
    ],
    'vendor_profile_shareholders' => [
        'full_name' => 255,
        'email' => 255,
        'ktp_number' => 255,
    ],
    'vendor_profile_taxes' => [
        'tax_document_number' => 255,
    ],
    //equipment
    'vendor_profile_tools' => [ 
        'measurement' => 255,
        'brand' => 255,
        'condition' => 255,
        'location' => 255,
        'ownership' => 255,
    ],
    'vendor_sanctions' => [
        'letter_number' => 32,
        'description' => 255,

    ],
    'vendor_evaluation_histories' => [
        'comments' => 255,
    ],
    'vendor_evaluation_workflows' => [
        'remarks' => 255,
    ],
    'vendor_history_statuses' => [
        'remarks' => 255,
    ],
    'vendor_sanction_histories' => [
        'comments' => 255,
    ],
    'vendor_sanction_workflows' => [
        'remarks' => 255,
    ],
    'vendor_workflows' => [
        'remarks' => 255,
    ],
];
