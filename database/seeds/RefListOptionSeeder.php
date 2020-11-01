<?php

use Illuminate\Database\Seeder;

use App\RefListOption;
use Illuminate\Support\Facades\DB;

class RefListOptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        RefListOption::truncate();
        DB::statement('ALTER SEQUENCE ref_list_options_id_seq RESTART WITH 1');
        $data = [
            //submission method
            [
                'type'=>'submission_method_options',
                'key'=>'1E',
                'value'=>'single_envelope',
                'deleteflg' => false,
            ],[
                'type'=>'submission_method_options',
                'key'=>'2E',
                'value'=>'dual_envelope',
                'deleteflg' => false,
            ],[
                'type'=>'submission_method_options',
                'key'=>'2S',
                'value'=>'dual_stage',
                'deleteflg' => false,
            ],
            [
                'type'=>'submission_method_options',
                'key'=>'2SSPLIT',
                'value'=>'dual_envelope_split',
                'deleteflg' => true,
            ],
            //tender method
            [
                'type'=>'tender_method_options',
                'key'=>'SELECTION',
                'value'=>'direct_selection',
                'deleteflg' => false,
            ],[
                'type'=>'tender_method_options',
                'key'=>'APPOINTMENT',
                'value'=>'direct_appointment',
                'deleteflg' => false,
            ],[
                'type'=>'tender_method_options',
                'key'=>'LIMITED',
                'value'=>'limited_tender',
                'deleteflg' => false,
            ],[
                'type'=>'tender_method_options',
                'key'=>'COMPETITIVE',
                'value'=>'competitive_bidding',
                'deleteflg' => false,
            ],
            // evaluation_method_options
            [
                'type'=>'evaluation_method_options',
                'key'=>'ELIMINATION',
                'value'=>'elimination',
                'deleteflg' => true,
            ],[
                'type'=>'evaluation_method_options',
                'key'=>'SCORE',
                'value'=>'score',
                'deleteflg' => true,
            ],
            [
                'type'=>'evaluation_method_options',
                'key'=>'2S',
                'value'=>'dual_stage',
                'deleteflg' => false,
            ],
            //winning_method_options
            [
                'type'=>'winning_method_options',
                'key'=>'PACKAGE',
                'value'=>'package',
                'deleteflg' => false,
            ],[
                'type'=>'winning_method_options',
                'key'=>'ITEMIZE',
                'value'=>'itemize',
                'deleteflg' => false,
            ],
            //validity_quotation_options
            [
                'type'=>'validity_quotation_options',
                'key'=>'30',
                'value'=>'30',
                'deleteflg' => false,
            ],
            [
                'type'=>'validity_quotation_options',
                'key'=>'60',
                'value'=>'60',
                'deleteflg' => false,
            ],[
                'type'=>'validity_quotation_options',
                'key'=>'90',
                'value'=>'90',
                'deleteflg' => false,
            ],[
                'type'=>'validity_quotation_options',
                'key'=>'120',
                'value'=>'120',
                'deleteflg' => false,
            ],
            //tkdn_options
            [
                'type'=>'tkdn_options',
                'key'=>'MATERIAL',
                'value'=>'material',
                'deleteflg' => false,
            ],[
                'type'=>'tkdn_options',
                'key'=>'SERVICE',
                'value'=>'service',
                'deleteflg' => false,
            ],[
                'type'=>'tkdn_options',
                'key'=>'MATERIAL_SERVICE',
                'value'=>'material_service',
                'deleteflg' => false,
            ],
            //bid_visibility_options
            [
                'type'=>'bid_visibility_options',
                'key'=>'PUBLIC',
                'value'=>'public',
                'deleteflg' => false,
            ],[
                'type'=>'bid_visibility_options',
                'key'=>'PRIVATE',
                'value'=>'private',
                'deleteflg' => false,
            ],
            //incoterm_options
            // [
            //     'type'=>'incoterm_options',
            //     'key'=>'FRANCO',
            //     'value'=>'FRANCO',
            //     'deleteflg' => false,
            // ],[
            //     'type'=>'incoterm_options',
            //     'key'=>'LOCO',
            //     'value'=>'LOCO',
            //     'deleteflg' => false,
            // ],[
            //     'type'=>'incoterm_options',
            //     'key'=>'FOB',
            //     'value'=>'FOB',
            //     'deleteflg' => false,
            // ],[
            //     'type'=>'incoterm_options',
            //     'key'=>'C&F',
            //     'value'=>'C&F',
            //     'deleteflg' => false,
            // ],[
            //     'type'=>'incoterm_options',
            //     'key'=>'CIF',
            //     'value'=>'CIF',
            //     'deleteflg' => false,
            // ],
            //sanction types
            [
                'type'=>'sanction_types',
                'key'=>'RED',
                'value'=>'Blacklisted',
                'deleteflg' => false,
            ],[
                'type'=>'sanction_types',
                'key'=>'YELLOW',
                'value'=>'Warning',
                'deleteflg' => false,
            ],[
                'type'=>'sanction_types',
                'key'=>'GREEN',
                'value'=>'No Warning',
                'deleteflg' => false,
            ],
            //vendor_tax_codes
            [
                'type'=>'vendor_tax_codes',
                'key'=>'ID1',
                'value'=>'Tax Identification Number (NPWP)',
                'deleteflg' => false,
            ],[
                'type'=>'vendor_tax_codes',
                'key'=>'ID2',
                'value'=>'Non PKP',
                'deleteflg' => false,
            ],[
                'type'=>'vendor_tax_codes',
                'key'=>'ID3',
                'value'=>'SPPKP',
                'deleteflg' => false,
            ],[
                'type'=>'vendor_tax_codes',
                'key'=>'ID4',
                'value'=>'ID Card',
                'deleteflg' => false,
            ],[
                'type'=>'vendor_tax_codes',
                'key'=>'ID5',
                'value'=>'E-NOFA',
                'deleteflg' => false,
            ],[
                'type'=>'vendor_tax_codes',
                'key'=>'ID6',
                'value'=>'Surat Pembebasan Pajak',
                'deleteflg' => false,
            ],[
                'type'=>'vendor_tax_codes',
                'key'=>'ID7',
                'value'=>'Report SPT PPN',
                'deleteflg' => false,
            ],[
                'type'=>'vendor_tax_codes',
                'key'=>'ID8',
                'value'=>'SPT',
                'deleteflg' => false,
            ],[
                'type'=>'vendor_tax_codes',
                'key'=>'ZZ1',
                'value'=>'Tax Certificate of Residence',
                'deleteflg' => false,
            ],
            // conditionat type options
            [
                'type'               => 'conditional_type_option',
                'key'               => 'CT1',
                'value'              => 'l_header',
                'deleteflg' => false,
            ],
            [
                'type'               => 'conditional_type_option',
                'key'               => 'CT2',
                'value'              => 'l_item',
                'deleteflg' => false,
            ],
        ];


        $incoterms = explode(",","CFR,CIF,CIP,CPT,DAP,DAT,DDP,EXW,FAS,FCA,FH,FOB,FOT,UN");
        foreach($incoterms as $term){
            $data[] = [
                'type' => 'incoterm_options',
                'key' => $term,
                'value' => $term,
                'deleteflg' => false
            ];
        }
        $incoterm_old = explode(",","DAF,DDU,DEQ,DES");
        foreach($incoterm_old as $term){
            $data[] = [
                'type' => 'incoterm_options',
                'key' => $term,
                'value' => $term,
                'deleteflg' => true
            ];
        }

        RefListOption::insert($data);

        $data = [
            [
                'type'=>'item_specification_category',
                'key'=>'cat1',
                'value'=>'Equipment',
                'deleteflg' => false,
            ],[
                'type'=>'item_specification_category',
                'key'=>'cat2',
                'value'=>'Tools',
                'deleteflg' => false,
            ],[
                'type'=>'item_specification_category',
                'key'=>'cat3',
                'value'=>'Resources',
                'deleteflg' => false,
            ],
            [
                'type'=>'item_specification_category',
                'key'=>'cat4',
                'value'=>'Other',
                'deleteflg' => true,
            ],
            //tender method
            [
                'type'=>'item_specification_category',
                'key'=>'cat5',
                'value'=>'Comment',
                'deleteflg' => false,
            ]
        ];
        RefListOption::insert($data);

        $result = DB::select( DB::raw("select last_value from ref_list_options_id_seq;") );
        $seqId = $result[0]->last_value;
        DB::statement('ALTER SEQUENCE ref_list_options_id_seq RESTART WITH ' . ($seqId+1) .';');
    }
}
