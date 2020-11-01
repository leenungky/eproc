<?php

namespace App\Repositories;

use Exception;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

class TenderExcelRepository extends BaseRepository
{
    private $logName = 'TenderExcelRepository';
    private $templates;

    public function __construct(){
        $this->templates = Config('eproc.templates.excel');
    }

    public function processExcelTender($data){
        // dd($data);
        // dd($data['vendor_submissions']);
        // dd($data['summary']);
        $type = $data['type'];

        $file = resource_path().$this->templates[$type]['file'];
        $spreadsheet = IOFactory::load($file);

        $method = 'process_'.$type;
        if(method_exists($this, $method)){
            $spreadsheet = $this->$method($spreadsheet,$data);
        }

        $writer = IOFactory::createWriter($spreadsheet, "Xlsx");

        $tenderNumber = $data['tender']->tender_number;

        $this->stream($writer, $tenderNumber."_".strtoupper($type)."_Report_".date('dmY_His').".xlsx");
    }

    #region CBE
    private function getColPositionsCbe(){
        //this is template coordinate. if changed, then need to be changed also
        $colPositions = [
            'client' => [7,1],
            'project' => [7,3],
            'remarks' => [3,30],
            'approvals' => [
                'position' => [[1,41], [3,41], [4,41], [7,41], [10,41], [12,41], [15,41], [18,41], [20,41]],
                'name' => [[1,42], [3,42], [4,42], [7,42], [10,42], [12,42], [15,42], [18,42], [20,42]],
                'date' => [[1,43], [3,43], [4,43], [7,43], [10,43], [12,43], [15,43], [18,43], [20,43]],
            ],
            'item' => [
                //Data: [x,y,z] => 
                // x: Column Index (start form 1)
                // y: Row Index (start from 1)
                // z: Column Length
                'no' => [1,15,1], //A13
                'description' => [2,15,2], 
                'qty' => [4,15,1], 
                'unit' => [5,15,1],
            ],
            'vendor' => [
                //Data: [x,y,z] => 
                // x: Column Index (start form 1)
                // y: Row Index (start from 1)
                // z: Column Length
                'vendor_name' => [6,5,4],
                'quotation_no' => [6,6,4],
                'quotation_date' => [6,7,4],
                'quotation_validity' => [6,8,4], 
                'bid_currency' => [6,9,4], 
                'vendor_country' => [6,10,4], 
                'qty_negotiation' => [6,11,4], 

                'offer' => [6,15,2],
                'unit_price' => [8,15,1],
                'total_item_price' => [9,15,1],

                //Column Index Start for vendor line data
                'col_start' => 6,
                //Column length for vendor line data in array
                'cols' => [2,1,1],

                'footer_row_start' => 17,
                'total_quote_currency' => [8,17,1],
                'total_quote_price' => [9,17,1],
                'total_discount_currency' => [8,19,1],
                'total_discount_price' => [9,19,1],
                'final_currency' => [8,20,1],
                'final_price' => [9,20,1],

                'delivery_term' => [6,22,4],
                'delivery_time' => [6,23,4],
                'payment_term' => [6,24,4],
                'tax' => [6,25,4],
                'local_content' => [6,26,4],
                'technically' => [6,27,4],
            ],
            'line_merge' => [
                //Data: [x,z] =>
                // x: Column Index (start form 1)
                // z: Column Length
                [2,2],
            ],
            //x: col index, y: rowidx, z: collength, aa: rowlength
            'first_remark_pos' => [10,13,1,3],

            //last vendor row to duplicate in template file.
            'last_vendor_row' => 32,
            //last row in template file.
            'last_row' => 43,

            //number of already prepared data for vendor in template file.
            'default_vendor_count' => 1,
            'max_vendor_page_area' => 4,
            'sheet_print_area' => [1,1,22,43],
        ];
        return $colPositions;
    }
    private function process_cbe($spreadsheet, $data){
        $colPositions = $this->getColPositionsCbe();

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getDefaultRowDimension()->setRowHeight(-1);

        //put client name, project name and remarks//
        $tmp = $colPositions['client'];
        $cell = $sheet->getCellByColumnAndRow($tmp[0],$tmp[1]);
        $cell->setValue($data['tender']->client_name); 
        $tmp = $colPositions['project'];
        $cell = $sheet->getCellByColumnAndRow($tmp[0],$tmp[1]);
        $cell->setValue($data['tender']->project_name); 
        $tmp = $colPositions['remarks'];
        $cell = $sheet->getCellByColumnAndRow($tmp[0],$tmp[1]);
        $cell->setValue($data['tender']->remarks); 

        //put signatures//
        $approval = $colPositions['approvals'];
        foreach($data['signatures'] as $sign){
            //position
            $tmp = $approval['position'][$sign->order];
            $cell = $sheet->getCellByColumnAndRow($tmp[0],$tmp[1]);
            $cell->setValue(ucwords($sign->position)); 
            //name
            $tmp = $approval['name'][$sign->order];
            $cell = $sheet->getCellByColumnAndRow($tmp[0],$tmp[1]);
            $cell->setValue('Name: '.ucwords($sign->sign_by)); 
            //date
            $tmp = $approval['date'][$sign->order];
            $cell = $sheet->getCellByColumnAndRow($tmp[0],$tmp[1]);
            $date = Carbon::createFromFormat('d.m.Y H:i', $sign->updated_at);
            $cell->setValue('Date: '.$date->format('d.m.Y'));
        }

        //initialize positions
        $currentRow = $colPositions['item']['no'][1];
        $lastRow = $colPositions['last_row'];
        $currentNoValue = 1;
        $defaultVendorCount = $colPositions['default_vendor_count'];

        //prepare sheets based on vendor submission
        $vendorCount = count($data['vendor_submissions']);
        if($vendorCount > $defaultVendorCount){
            for($i=0;$i<$vendorCount-$defaultVendorCount;$i++){
                $this->addVendorCbe($sheet,$colPositions,$defaultVendorCount+$i);
            }
            $defaultVendorCount = $vendorCount;
        }

        //Buyer Items
        //no
        $no = $colPositions['item']['no'][0];
        $cell = $sheet->getCellByColumnAndRow($no,$currentRow);
        $cell->setValue($currentNoValue++); 
        //category description
        $desc = $colPositions['item']['description'][0];
        $cell = $sheet->getCellByColumnAndRow($desc,$currentRow);
        $cell->setValue('PR Items / Scope of Work'); 
        $cell->getStyle()->getFont()->setBold(true);
        $qty = $colPositions['item']['qty'][0];
        $unit = $colPositions['item']['unit'][0];

        //to make sure data showed is same in every item row by line_id
        $itemLineRows = [];
        $itemDeleted = [];

        foreach($data['pr_item_list'] as $item){
            $currentRow++;
            $this->addItemLine($sheet,$currentRow,$colPositions,$defaultVendorCount);
            $lastRow++;
            //no
            $cell = $sheet->getCellByColumnAndRow($no,$currentRow);
            $cell->setValue($item->number." - ".$item->line_number);
            //description
            $cell = $sheet->getCellByColumnAndRow($desc,$currentRow);
            $cell->setValue($item->description);
            //qty
            $cell = $sheet->getCellByColumnAndRow($qty,$currentRow);
            $cell->setValue($this->toQuantity($item->qty));
            //uom
            $cell = $sheet->getCellByColumnAndRow($unit,$currentRow);
            $cell->setValue($item->uom);
            $itemLineRows['line-'.$item->line_id] = $currentRow;
            if($item->deleteflg) $itemDeleted[] = $currentRow;
        }
        
        //foreach vendorsubmissions
        $subcnt = 0;
        $vendorname = $colPositions['vendor']['vendor_name'];
        $qno = $colPositions['vendor']['quotation_no'];
        $qdate = $colPositions['vendor']['quotation_date'];
        $qvalidity = $colPositions['vendor']['quotation_validity'];
        $currency = $colPositions['vendor']['bid_currency'];
        $country = $colPositions['vendor']['vendor_country'];
        $qtynegotiation = $colPositions['vendor']['qty_negotiation'];
        $offer = $colPositions['vendor']['offer'];
        $unitprice = $colPositions['vendor']['unit_price'];
        $totalitemprice = $colPositions['vendor']['total_item_price'];
        $quotcurrency = $colPositions['vendor']['total_quote_currency'];
        $quotprice = $colPositions['vendor']['total_quote_price'];
        $disccurrency = $colPositions['vendor']['total_discount_currency'];
        $discprice = $colPositions['vendor']['total_discount_price'];
        $finalcurrency = $colPositions['vendor']['final_currency'];
        $finalprice = $colPositions['vendor']['final_price'];
        $delivterm = $colPositions['vendor']['delivery_term'];
        $delivtime = $colPositions['vendor']['delivery_time'];
        $payterm = $colPositions['vendor']['payment_term'];
        $tax = $colPositions['vendor']['tax'];
        $tkdn = $colPositions['vendor']['local_content'];
        $technically = $colPositions['vendor']['technically'];

        foreach($data['vendor_submissions'] as $sub){
            $subcnt++;
            $currentBlock = ($subcnt-1)*$vendorname[2];
            foreach($data['summary']->data as $v){
                if($v->vendor_code == $sub['vendor']->vendor_code){
                    // $vendorCurrency = $sub['submission_header']['data']->currency_code;
                    $vendorCurrency = $v->currency_code_vendor;
                }
            }

            //vendor
            $cell = $sheet->getCellByColumnAndRow($vendorname[0]+$currentBlock,$vendorname[1]);
            // $cell->setValue($sub['vendor']->vendor_name.' ('.$sub['vendor']->vendor_code.')');
            $cell->setValue($sub['vendor']->vendor_name);
            //quotation_no
            $cell = $sheet->getCellByColumnAndRow($qno[0]+$currentBlock,$qno[1]);
            $cell->setValue($sub['submission_header']['data']->quotation_number ?? '');
            //quotation_date
            $cell = $sheet->getCellByColumnAndRow($qdate[0]+$currentBlock,$qdate[1]);
            $cell->setValue($sub['submission_header']['data']->quotation_date ?? '');
            // validity_quotation
            if(isset($sub['submission_header']['data']->quotation_date)){
                $date = Carbon::createFromFormat('d.m.Y H:i', $sub['submission_header']['data']->quotation_date);
                $date = $date->addDays($data['tender']->validity_quotation);
                $cell = $sheet->getCellByColumnAndRow($qvalidity[0]+$currentBlock,$qvalidity[1]);
                // $cell->setValue($data['tender']->validity_quotation);
                $cell->setValue($date->format('d.m.Y'));
            }
            // country
            $cell = $sheet->getCellByColumnAndRow($country[0]+$currentBlock,$country[1]);
            $cell->setValue($sub['vendor_detail']->country);
            // qtynegotiation ?
            // $cell = $sheet->getCellByColumnAndRow($qvalidity[0]+$currentBlock,$qvalidity[1]);
            // $cell->setValue($sub['submission_header']['data']->currency_code);
            
            //item data ?
            $currentRow = $colPositions['vendor']['offer'][1];
            $vendorTotal = 0;
            $totalItemAdditionalCost = 0;
            $vendorItemRows = 0;
            // foreach($sub['item']->data as $item){
            $vendorLineItem = [];
            foreach($sub['item'] as $item){
                if(!in_array($item->line_id, $vendorLineItem)){
                    $currentRow++;
                    $vendorItemRows++;
                    $vendorLineItem[]=$item->line_id;
                    $isShowLine = $data['type']=='cbe'; //show in report because cbe.
                }else{
                    $isShowLine = $data['type']=='nbe';
                }
                //offer
                $cell = $sheet->getCellByColumnAndRow($offer[0]+$currentBlock,$itemLineRows['line-'.$item->line_id]);
                $cell->setValue($item->description_vendor);
                //unit price
                $cell = $sheet->getCellByColumnAndRow($unitprice[0]+$currentBlock,$itemLineRows['line-'.$item->line_id]);
                $cell->setValue($item->uom);
                //total item price
                $cell = $sheet->getCellByColumnAndRow($totalitemprice[0]+$currentBlock,$itemLineRows['line-'.$item->line_id]);
                if($item->compliance=='no_quote'){
                    $cell->setValue(__('tender.process.compliance.'.$item->compliance));
                }else{
                    if($item->item_category==0){
                        $value = $item->subtotal_vendor+$item->additional_cost;
                        $cell->setValueExplicit($item->currency_code_vendor=='IDR' ? $this->toCurrencyIdr(round($value)) : $this->toCurrency($value), DataType::TYPE_STRING);
                        if($isShowLine) $vendorTotal+=$item->subtotal_vendor+$item->additional_cost;
                        if($isShowLine) $totalItemAdditionalCost+=$item->additional_cost;
                    }else{
                        $value = $item->total_overall_limit_vendor+$item->additional_cost;
                        $cell->setValueExplicit($item->currency_code_vendor=='IDR' ? $this->toCurrencyIdr(round($value)) : $this->toCurrency($value), DataType::TYPE_STRING);
                        if($isShowLine) $vendorTotal+=$item->total_overall_limit_vendor+$item->additional_cost;
                        if($isShowLine) $totalItemAdditionalCost+=$item->additional_cost;
                    }
                }
                $this->toText($sheet, $totalitemprice[0]+$currentBlock,$itemLineRows['line-'.$item->line_id]);
            }

            foreach($itemDeleted as $i){
                $cell = $sheet->getCellByColumnAndRow($totalitemprice[0]+$currentBlock,$i);
                $cell->setValue('DELETED');
                $cellStyle = $sheet->getStyleByColumnAndRow($totalitemprice[0]+$currentBlock,$i);
                $cellStyle->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }
            //TODO: additional cost + taxes?
            //
            
            $vendorItemRows = max($vendorItemRows,count($itemLineRows)); 
            // quote total 
            $cell = $sheet->getCellByColumnAndRow($quotprice[0]+$currentBlock,$quotprice[1]+$vendorItemRows);
            $cell->setValueExplicit($vendorCurrency=='IDR' ? $this->toCurrencyIdr(round($vendorTotal)) : $this->toCurrency($vendorTotal), DataType::TYPE_STRING);
            $this->toText($sheet, $quotprice[0]+$currentBlock,$quotprice[1]+$vendorItemRows);
            // discount total 
            $headerDiscount = 0;
            $finalTotal = 0;
            foreach($data['summary']->data as $v){
                if($v->vendor_code == $sub['vendor']->vendor_code){
                    // Log::info([$v->total_additional_cost, $totalItemAdditionalCost, $v->subtotal_vendor]);
                    $headerDiscount = $v->total_additional_cost - $totalItemAdditionalCost;
                    $finalTotal = $v->subtotal_vendor + $v->total_overall_limit_vendor + $v->total_additional_cost;
                    $vendorCurrency = $v->currency_code_vendor;
                }
            }
            $cell = $sheet->getCellByColumnAndRow($discprice[0]+$currentBlock,$discprice[1]+$vendorItemRows);
            if($data['tender']->conditional_type=='CT1'){
                $cell->setValueExplicit($headerDiscount == 0 ? '' : ($vendorCurrency=='IDR' ? $this->toCurrencyIdr(round($headerDiscount)) : $this->toCurrency($headerDiscount)), DataType::TYPE_STRING);
            }else{
                $cell->setValue('Item Level');
            }
            $this->toText($sheet, $discprice[0]+$currentBlock,$discprice[1]+$vendorItemRows);
            // final total 
            $cell = $sheet->getCellByColumnAndRow($finalprice[0]+$currentBlock,$finalprice[1]+$vendorItemRows);
            $cell->setValueExplicit($vendorCurrency=='IDR' ? $this->toCurrencyIdr(round($finalTotal)) : $this->toCurrency($finalTotal), DataType::TYPE_STRING);
            $this->toText($sheet, $finalprice[0]+$currentBlock,$finalprice[1]+$vendorItemRows);
            // delivery term 
            $cell = $sheet->getCellByColumnAndRow($delivterm[0]+$currentBlock,$delivterm[1]+$vendorItemRows);
            $cell->setValue($sub['submission_header']['data']->incoterm ?? '');
            // delivery time 
            $cell = $sheet->getCellByColumnAndRow($delivtime[0]+$currentBlock,$delivtime[1]+$vendorItemRows);
            $cell->setValue('');
            // payment term 
            $cell = $sheet->getCellByColumnAndRow($payterm[0]+$currentBlock,$payterm[1]+$vendorItemRows);
            $cell->setValue('');
            // tax 
            $cell = $sheet->getCellByColumnAndRow($tax[0]+$currentBlock,$tax[1]+$vendorItemRows);
            $cell->setValue('');
            // local_content 
            $cell = $sheet->getCellByColumnAndRow($tkdn[0]+$currentBlock,$tkdn[1]+$vendorItemRows);
            $cell->setValue(($sub['technical_header']['data']->tkdn_percentage ?? '0').'%');
            // technically 
            $cell = $sheet->getCellByColumnAndRow($technically[0]+$currentBlock,$technically[1]+$vendorItemRows);
            $cell->setValue('');

            // header currency
            $cell = $sheet->getCellByColumnAndRow($currency[0]+$currentBlock,$currency[1]);
            $cell->setValue($vendorCurrency);
            // quote currency 
            $cell = $sheet->getCellByColumnAndRow($quotcurrency[0]+$currentBlock,$quotcurrency[1]+$vendorItemRows);
            $cell->setValue($vendorCurrency);
            // discount currency 
            $cell = $sheet->getCellByColumnAndRow($disccurrency[0]+$currentBlock,$disccurrency[1]+$vendorItemRows);
            $cell->setValue($vendorCurrency);
            // final currency 
            $cell = $sheet->getCellByColumnAndRow($finalcurrency[0]+$currentBlock,$finalcurrency[1]+$vendorItemRows);
            $cell->setValue($vendorCurrency);


        }
        return $spreadsheet;
    }
    private function addVendorCbe($sheet,$colPositions,$vendorPos){
        $block = $colPositions['vendor']['vendor_name'];
        $colStart = $block[0];
        $rowStart = $block[1];
        $length = $block[2];
        $colEnd = $colStart + $length - 1;
        $rowEnd = $colPositions['last_vendor_row'];
        $newCol = $colStart + $length*$vendorPos;
        $newRow = $block[1];
        $remark = $colPositions['first_remark_pos'];

        $range = Coordinate::stringFromColumnIndex($colStart).$rowStart.':'.Coordinate::stringFromColumnIndex($colEnd).$rowEnd;
        $target = Coordinate::stringFromColumnIndex($newCol).$newRow;

        //insert column to persist print area if vendorcount > 4
        if($vendorPos >= $colPositions['max_vendor_page_area']){
            $newColString = Coordinate::stringFromColumnIndex($newCol);
            for($i=0;$i<$length;$i++){
                $sheet->insertNewColumnBefore($newColString);
            }
            $printArea = $colPositions['sheet_print_area'];
            $printRange = Coordinate::stringFromColumnIndex($printArea[0]).$printArea[1].':'.Coordinate::stringFromColumnIndex($printArea[2]+$length*($vendorPos-$colPositions['max_vendor_page_area']+1)).$printArea[3];
            $sheet->getPageSetup()->setPrintArea($printRange);
        }else{
            //copy remark to position
            $rangeRemark = Coordinate::stringFromColumnIndex($remark[0]+$length*($vendorPos-1)).$remark[1].':'.Coordinate::stringFromColumnIndex($remark[0]+$length*($vendorPos-1)).($remark[1]+$remark[3]);
            $targetRemark = Coordinate::stringFromColumnIndex($remark[0]+$length*$vendorPos).$remark[1];
            $this->copyRange($sheet, $rangeRemark, $targetRemark);
        }


        //copy vendor block
        $this->copyRange($sheet, $range, $target);
    }
    #endregion

    #region TBE
    private function process_tbe($spreadsheet, $data){
        //this is template coordinate. if changed, then need to be changed also
        $colPositions = [
            //RFQ position in col,row,collength
            'rfq' => [18,6,5],

            'item' => [
                //Data: [x,y,z] => 
                // x: Column Index (start form 1)
                // y: Row Index (start from 1)
                // z: Column Length
                'no' => [1,13,1], //A13
                'description' => [2,13,10], 
                'requirement' => [12,13,11], 
                'reference' => [23,13,5],
            ],
            'vendor' => [
                //Data: [x,y,z] => 
                // x: Column Index (start form 1)
                // y: Row Index (start from 1)
                // z: Column Length
                'block' => [28,8,15],
                'quotation_no' => [28,9,15],
                'quotation_date' => [28,10,15],
                'data' => [28,13,11], 
                'respond' => [39,13,4],

                //Column Index Start for vendor line data
                'col_start' => 28,
                //Column length for vendor line data in array
                'cols' => [11,4]
            ],
            //List of merged columns for each report line.
            //Line for vendor already inside vendor array.
            'line_merge' => [
                //Data: [x,z] =>
                // x: Column Index (start form 1)
                // z: Column Length
                [2,10],
                [12,11],
                [23,5],
            ],

            //last row in template file.
            'last_row' => 29,

            //number of already prepared data for vendor in template file.
            'default_vendor_count' => 1
        ];

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getDefaultRowDimension()->setRowHeight(-1);

        //header
        $cell = $sheet->getCellByColumnAndRow($colPositions['rfq'][0],$colPositions['rfq'][1])->setValue($data['tender']->tender_number);

        //initialize positions
        $currentRow = $colPositions['item']['no'][1];
        $lastRow = $colPositions['last_row'];
        $no = $colPositions['item']['no'][0];
        $currentNoValue = 1;
        $desc = $colPositions['item']['description'][0];
        $req = $colPositions['item']['requirement'][0];
        $ref = $colPositions['item']['reference'][0];
        $defaultVendorCount = $colPositions['default_vendor_count'];

        //prepare sheets based on vendor submission
        $vendorCount = count($data['vendor_submissions']);
        if($vendorCount > $defaultVendorCount){
            for($i=0;$i<$vendorCount-$defaultVendorCount;$i++){
                $this->addVendorTbe($sheet,$colPositions,$defaultVendorCount+$i);
            }
            $defaultVendorCount = $vendorCount;
        }

        //Buyer Items
        //no
        $cell = $sheet->getCellByColumnAndRow($no,$currentRow);
        $cell->setValue($currentNoValue++); 
        //category description
        $cell = $sheet->getCellByColumnAndRow($desc,$currentRow);
        $cell->setValue('PR Items / Scope of Work'); 
        $cell->getStyle()->getFont()->setBold(true);

        //to make sure data showed is same in every item row by line_id
        $itemLineRows = [];
        $itemDeleted = [];

        foreach($data['pr_item_list'] as $item){
            $currentRow++;
            $this->addItemLine($sheet,$currentRow,$colPositions,$defaultVendorCount);
            $lastRow++;
            //no
            $cell = $sheet->getCellByColumnAndRow($no,$currentRow);
            $cell->setValue($item->number." - ".$item->line_number);
            //description
            $cell = $sheet->getCellByColumnAndRow($desc,$currentRow);
            $cell->setValue($item->description);
            //qty && uom
            $cell = $sheet->getCellByColumnAndRow($req,$currentRow);
            $cell->setValue($this->toQuantity($item->qty)." ".$item->uom);
            $cellStyle = $sheet->getStyleByColumnAndRow($req,$currentRow);
            $cellStyle->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $itemLineRows['line-'.$item->line_id] = $currentRow;
            if($item->deleteflg) $itemDeleted[] = $currentRow;
        }

        $currentRow++;
        $this->addItemLine($sheet,$currentRow,$colPositions,$defaultVendorCount);
        $lastRow++;
        
        //foreach itemdetails
        $detailStart = $currentRow; //for vendor item detail submission start row
        $currentCategory = "";

        //to make sure data showed is same in every detail item row by line_id
        $itemDetailLineRows = [];
        // $itemDetailDataRows = [];

        // remove category status draft first.
        $dt = [];
        foreach($data['item_detail'] as $item){
            if($item->category_status!='draft') $dt[] = $item;
        }
        foreach($dt as $item){
                $currentRow++;
            $this->addItemLine($sheet,$currentRow,$colPositions,$defaultVendorCount);
            $lastRow++;

            if($currentCategory!=$item->category_name){

                $currentRow++;
                $this->addItemLine($sheet,$currentRow,$colPositions,$defaultVendorCount);
                $lastRow++;

                $currentCategory = $item->category_name;
                //no
                $cell = $sheet->getCellByColumnAndRow($no,$currentRow);
                $cell->setValue($currentNoValue++); 
                //categoryname
                $cell = $sheet->getCellByColumnAndRow($desc,$currentRow);
                $cell->setValue($currentCategory);

                if($item->template_id==1){
                    $currentRow++;
                    $this->addItemLine($sheet,$currentRow,$colPositions,$defaultVendorCount);
                    $lastRow++;
                }
            }
                
            if($item->template_id==1){
                //description
                $cell = $sheet->getCellByColumnAndRow($desc,$currentRow);
                $cell->setValue($item->description);
                //req
                $cell = $sheet->getCellByColumnAndRow($req,$currentRow);
                $cell->setValue($item->requirement);
                $cellStyle = $sheet->getStyleByColumnAndRow($req,$currentRow);
                $cellStyle->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                //ref
                $cell = $sheet->getCellByColumnAndRow($ref,$currentRow);
                $cell->setValue($item->reference);
            }else if($item->template_id==2){
                //description
                $cell = $sheet->getCellByColumnAndRow($req,$currentRow);
                // $cell->setValue($item->description);
                $cell->setValue($item->requirement);
            }
            $itemDetailLineRows['line-'.$item->line_id] = $currentRow;
            // $itemDetailDataRows[$currentRow] = [
            //     'description' => $item->description,
            //     'requirement' => $item->requirement,
            //     'reference' => $item->reference,
            //     'category_id' => $item->category_id
            // ];

        }

        $lastItemRow = $currentRow;

        //foreach vendorsubmissions
        $subcnt = 0;
        $block = $colPositions['vendor']['block'];
        $qno = $colPositions['vendor']['quotation_no'];
        $qdate = $colPositions['vendor']['quotation_date'];
        $qdata = $colPositions['vendor']['data'];
        $qrespond = $colPositions['vendor']['respond'];
        foreach($data['vendor_submissions'] as $sub){
            $subcnt++;
            $currentBlock = ($subcnt-1)*$block[2];
            //vendor
            $cell = $sheet->getCellByColumnAndRow($block[0]+$currentBlock,$block[1]);
            // $cell->setValue('Subcontractor/Bidder: '.$sub['vendor']->vendor_name.' ('.$sub['vendor']->vendor_code.')');
            $cell->setValue('Subcontractor/Bidder: '.$sub['vendor']->vendor_name);
            //quotation_no
            $cell = $sheet->getCellByColumnAndRow($qno[0]+$currentBlock,$qno[1]);
            $cell->setValue('Quotation No.: '.$sub['submission_header']['data']->quotation_number ?? '');
            //quotation_date
            $cell = $sheet->getCellByColumnAndRow($qdate[0]+$currentBlock,$qdate[1]);
            $cell->setValue('Quotation Date.: '.$sub['submission_header']['data']->quotation_date ?? '');

            //item data ?
            $currentRow = $colPositions['vendor']['data'][1];
            // foreach($sub['item']->data as $item){
            foreach($sub['item'] as $item){
                //data
                $currentRow++;
                $cell = $sheet->getCellByColumnAndRow($qdata[0]+$currentBlock,$itemLineRows['line-'.$item->line_id]);
                $cell->setValue($item->description_vendor);
                //qty && uom
                $cell = $sheet->getCellByColumnAndRow($qrespond[0]+$currentBlock,$itemLineRows['line-'.$item->line_id]);
                if($item->compliance=='no_quote'){
                    $cell->setValue(__('tender.process.compliance.'.$item->compliance));
                }else{
                    $cell->setValue($this->toQuantity($item->qty_vendor)." ".$item->uom);
                }
                $cellStyle = $sheet->getStyleByColumnAndRow($qrespond[0]+$currentBlock,$itemLineRows['line-'.$item->line_id]);
                $cellStyle->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }
            foreach($itemDeleted as $i){
                $cell = $sheet->getCellByColumnAndRow($qdata[0]+$currentBlock,$i);
                $cell->setValue('DELETED');
                $cell->getStyle()->getFont()->setBold(true);
                $cellStyle = $sheet->getStyleByColumnAndRow($qdata[0]+$currentBlock,$i);
                $cellStyle->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $cell = $sheet->getCellByColumnAndRow($qrespond[0]+$currentBlock,$i);
                $cell->setValue('DELETED');
                $cell->getStyle()->getFont()->setBold(true);
                $cellStyle = $sheet->getStyleByColumnAndRow($qrespond[0]+$currentBlock,$i);
                $cellStyle->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }

            //TODO: item detail
            // Log::info($itemDetailLineRows);
            foreach($sub['item_detail'] as $detail){
                foreach($itemDetailLineRows as $dt=>$row){
                    if($dt=='line-'.$detail->item_spec_id){
                        //data
                        $cell = $sheet->getCellByColumnAndRow($qdata[0]+$currentBlock,$row);
                        $cell->setValue($detail->data);
                        //respond
                        $cell = $sheet->getCellByColumnAndRow($qrespond[0]+$currentBlock,$row);
                        $cell->setValue($detail->respond);
                        break(1);
                    }
                }
                // foreach($itemDetailDataRows as $row=>$dt){
                //     if($dt['description']==$detail->description && 
                //         $dt['requirement']==$detail->requirement && 
                //         $dt['reference']==$detail->reference &&
                //         $dt['category_id']==$detail->category_id
                //     ){
                //         //data
                //         $cell = $sheet->getCellByColumnAndRow($qdata[0]+$currentBlock,$row);
                //         $cell->setValue($detail->data);
                //         //respond
                //         $cell = $sheet->getCellByColumnAndRow($qrespond[0]+$currentBlock,$row);
                //         $cell->setValue($detail->respond);
                //         break(1);
                //     }
                // }
            }
        }

        //setting autowidth
        for($i=$colPositions['item']['no'][1];$i<$lastItemRow;$i++){
            $spreadsheet->getActiveSheet()->getRowDimension($i)->setRowHeight(-1);
        }

        return $spreadsheet;
    }
    private function addVendorTbe($sheet,$colPositions,$vendorPos){
        $block = $colPositions['vendor']['block'];
        $colStart = $block[0];
        $rowStart = $block[1];
        $length = $block[2];
        $colEnd = $colStart + $length - 1;
        $rowEnd = $colPositions['last_row'];
        $newCol = $colStart + $length*$vendorPos;
        $newRow = $block[1];

        $range = Coordinate::stringFromColumnIndex($colStart).$rowStart.':'.Coordinate::stringFromColumnIndex($colEnd).$rowEnd;
        $target = Coordinate::stringFromColumnIndex($newCol).$newRow;

        $this->copyRange($sheet, $range, $target);
    }
    #endregion

    #region NBE
    private function process_nbe($spreadsheet, $data){
        return $this->process_cbe($spreadsheet, $data);
    }
    #endregion

    
    private function addItemLine($sheet,$row,$colPositions,$defaultVendorCount){
        $sheet->insertNewRowBefore($row);
        foreach($colPositions['line_merge'] as $cols){
            $sheet->mergeCellsByColumnAndRow($cols[0],$row,$cols[0]+$cols[1]-1,$row);
        }

        $colStart = $colPositions['vendor']['col_start'];
        for($i=0;$i<$defaultVendorCount;$i++){
            foreach($colPositions['vendor']['cols'] as $col){
                $sheet->mergeCellsByColumnAndRow($colStart,$row,$colStart+$col-1,$row);
                $colStart+=$col;
            }
        }
    }
    private function save($writer, $filename){
        $writer->save($filename);
    }

    private function stream($writer, $filename){
        // header('Content-Type: application/vnd.ms-excel');
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'.$filename.'"');
        $writer->save("php://output");
    }

    private function copyRange($sheet, $srcRange, $dstCell) {
        // Validate source range. Examples: A2:A3, A2:AB2, A27:B100
        if( !preg_match('/^([A-Z]+)(\d+):([A-Z]+)(\d+)$/', $srcRange, $srcRangeMatch) ) {
            // Wrong source range
            return;
        }
        // Validate destination cell. Examples: A2, AB3, A27
        if( !preg_match('/^([A-Z]+)(\d+)$/', $dstCell, $destCellMatch) ) {
            // Wrong destination cell
            return;
        }
    
        $srcColumnStart = $srcRangeMatch[1];
        $srcRowStart = $srcRangeMatch[2];
        $srcColumnEnd = $srcRangeMatch[3];
        $srcRowEnd = $srcRangeMatch[4];
    
        $destColumnStart = $destCellMatch[1];
        $destRowStart = $destCellMatch[2];
    
        // For looping purposes we need to convert the indexes instead
        // Note: We need to subtract 1 since column are 0-based and not 1-based like this method acts.
    
        $srcColumnStart = Coordinate::columnIndexFromString($srcColumnStart);// - 1;
        $srcColumnEnd = Coordinate::columnIndexFromString($srcColumnEnd);// - 1;
        $destColumnStart = Coordinate::columnIndexFromString($destColumnStart);// - 1;
        // Log::info([
        //     '$srcColumnStart'=>$srcColumnStart,
        //     '$srcColumnEnd' => $srcColumnEnd,
        //     '$destColumnStart' => $destColumnStart
        // ]);
    
        $rowCount = 0;
        for ($row = $srcRowStart; $row <= $srcRowEnd; $row++) {
            $colCount = 0;
            for ($col = $srcColumnStart; $col <= $srcColumnEnd; $col++) {
                $cell = $sheet->getCellByColumnAndRow($col, $row);
                $style = $sheet->getStyleByColumnAndRow($col, $row);
                $dstCell = Coordinate::stringFromColumnIndex($destColumnStart + $colCount) . (string)($destRowStart + $rowCount);
                $sheet->setCellValue($dstCell, $cell->getValue());
                $sheet->duplicateStyle($style, $dstCell);
    
                // Set width of column, but only once per row
                if ($rowCount === 0) {
                    $w = $sheet->getColumnDimensionByColumn($col)->getWidth();
                    $sheet->getColumnDimensionByColumn ($destColumnStart + $colCount)->setAutoSize(false);
                    $sheet->getColumnDimensionByColumn ($destColumnStart + $colCount)->setWidth($w);
                }
    
                $colCount++;
            }
    
            $h = $sheet->getRowDimension($row)->getRowHeight();
            $sheet->getRowDimension($destRowStart + $rowCount)->setRowHeight($h);
    
            $rowCount++;
        }
        foreach ($sheet->getMergeCells() as $mergeCell) {
            $mc = explode(":", $mergeCell);
            $mergeColSrcStart = Coordinate::columnIndexFromString(preg_replace("/[0-9]*/", "", $mc[0]));// - 1;
            $mergeColSrcEnd = Coordinate::columnIndexFromString(preg_replace("/[0-9]*/", "", $mc[1]));// - 1;
            $mergeRowSrcStart = ((int)preg_replace("/[A-Z]*/", "", $mc[0]));
            $mergeRowSrcEnd = ((int)preg_replace("/[A-Z]*/", "", $mc[1]));
    
            $relativeColStart = $mergeColSrcStart - $srcColumnStart;
            $relativeColEnd = $mergeColSrcEnd - $srcColumnStart;
            $relativeRowStart = $mergeRowSrcStart - $srcRowStart;
            $relativeRowEnd = $mergeRowSrcEnd - $srcRowStart;
    
            if (0 <= $mergeRowSrcStart && $mergeRowSrcStart >= $srcRowStart && $mergeRowSrcEnd <= $srcRowEnd) {
                if (0 <= $mergeColSrcStart && $mergeColSrcStart >= $srcColumnStart && $mergeColSrcEnd <= $srcColumnEnd) {
                    $targetColStart = Coordinate::stringFromColumnIndex($destColumnStart + $relativeColStart);
                    $targetColEnd = Coordinate::stringFromColumnIndex($destColumnStart + $relativeColEnd);
                    $targetRowStart = $destRowStart + $relativeRowStart;
                    $targetRowEnd = $destRowStart + $relativeRowEnd;
        
                    $merge = (string)$targetColStart . (string)($targetRowStart) . ":" . (string)$targetColEnd . (string)($targetRowEnd);
                    //Merge target cells
                    $sheet->mergeCells($merge);
                }
            }
        }
    }

    private function toText($sheet, $col, $row){
        $sheet->getStyleByColumnAndRow($col,$row)->getNumberFormat()->applyFromArray([
            'formatCode' => NumberFormat::FORMAT_TEXT,
        ]);
    }
    private function toCurrency($value){
        return number_format($value,2,',','.');
    }
    private function toCurrencyIdr($value){
        return number_format($value,0,',','.');
    }
    private function toQuantity($value){
        return number_format($value,3,',','');
    }

    //example
    public function testExcel1(){
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Hello World !');
        
        //using xlsx
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        return $writer;
    }

    public function testExcel2(){
        $spreadsheet = new Spreadsheet();
        //set default font
        $spreadsheet->getDefaultStyle()
            ->getFont()
            ->setName('Arial')
            ->setSize(10);
            
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1',"Participants");
        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1')->getFont()->setSize(20);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getRowDimension(1)->setRowHeight(24);
        $sheet->getCellByColumnAndRow(5,5)->setValue("Test posisi 5,5");
        $sheet->mergeCellsByColumnAndRow(5,5,7,5);

        $tableHead = [
            'font' => [
                'color' => ['rgb'=>'FFFFFF'],
                'bold' => true,
                'size' => 11,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb'=>'538ED5'],
            ]
        ];

        $sheet->getStyle('E5')->applyFromArray($tableHead);

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, "Xlsx");
        return $writer;
    }

}
