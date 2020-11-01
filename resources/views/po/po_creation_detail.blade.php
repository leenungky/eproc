@php
    $formName = 'frmgeneral';
    $formLayout = 'po.partials.profile';
    $document_type = isset($tenderData["models"]["po_header"]["document_type"]) ? strtoupper($tenderData["models"]["po_header"]["document_type"]) : "";
    $location_category = isset($tenderData["models"]["po_header"]["location_category"]) ? strtoupper($tenderData["models"]["po_header"]["location_category"]) : "";
    $str_header_text = "";
    foreach ($tenderData["models"]["po_header_text"] as $key => $value) {
        $str_header_text = $str_header_text ."\r\n". $value->TEXT_LINE;
    }
    $str_term_text = "";
    foreach ($tenderData["models"]["po_term_text"] as $key => $value) {
        $str_term_text = $str_term_text ."\r\n". $value->TEXT_LINE;
    }
    $isEditable = false;
    if (isset($tenderData["models"]["po_list"])){
        if ($tenderData["models"]["po_list"]->eproc_po_status=="draft"){
            $isEditable = true;
        }
    }

    $purchage_org_code = "";
    if (empty($tenderData["models"]["po_header"]->purchase_org_code)){
        $purchage_org_code = isset($tenderData['models']['purchaseOrg']->org_code) ? $tenderData['models']['purchaseOrg']->org_code : "";
    }else{
        $purchage_org_code = $tenderData["models"]["po_header"]->purchase_org_code;
    }
    $purchage_org_code = trim($purchage_org_code);

    $isVendor = Auth::user()->isVendor();
    $is_show = false;
@endphp

@extends('tender.show')

@section('contentheader')
<div class="card-header-left">
    <span class="heading-title">{{__('tender.'.$type)}}</span>
</div>
<div class="card-header-right" hiden>
    {{-- <span id="table_sum" class="badge badge-secondary" style="">Total HPS: xx.xxx.xxx</span> --}}
    <button class="btn btn-sm btn-primary btn-cancel" id="btnBack">Back</i></button>
    @if ($is_show)
        @if ($isEditable && !$isVendor)
                        &nbsp;&nbsp;
            <button id="btn_save_header" class="btn btn-sm btn_submit btn-primary">
                <i class="fa fa-save"></i>&nbsp;&nbsp;&nbsp;{{__('tender.save')}}</button>
                &nbsp;&nbsp;
                            {{-- <button id="btn-submit" class="btn btn-sm btn_submit btn-success" href="{{ route('tender.show', ['id' => $id, 'type' => $type, 'action' => 'detail-specification']) }}">
                <i class="fa fa-paper-plane"></i>&nbsp;&nbsp;&nbsp;{{__('tender.submit_sap')}} </button> --}}
        @endif
    @endif
</div>
@endsection

@section('contentbody')
<div class="has-footer">
    @if (session('status'))
    <div class="alert alert-success" role="alert">
        {{ session('status') }}
    </div>
    @endif
    <div class="">
        <div class="tab">
            <ul class="nav nav-tabs" id="technical_evaluation-tab" role="tablist">
            <li class="nav-item" id="general-li">
                    <a class="nav-link active" id="general-tab" data-toggle="tab" href="#general-content" role="tab" aria-controls="overview" aria-selected="true">General</a>
                </li>
                <li class="nav-item" id="general-li">
                    <a class="nav-link" id="vendor-tab" data-toggle="tab" href="#vendor-content" role="tab" aria-controls="vendor" aria-selected="true">Vendor</a>
                </li>
                <li class="nav-item" id="general-li">
                    <a class="nav-link" id="condition-tab" data-toggle="tab" href="#condition-content" role="tab" aria-controls="condition" aria-selected="true">Condition</a>
                </li>
                <li class="nav-item" id="orgdata-li">
                    <a class="nav-link " id="orgdata-tab" data-toggle="tab" href="#orgdata-content" role="tab" aria-controls="org-data" aria-selected="true">Organization Data</a>
                </li>
                <li class="nav-item" id="text-li">
                    <a class="nav-link " id="text-tab" data-toggle="tab" href="#text-content" role="tab" aria-controls="text" aria-selected="true">Text</a>
                </li>
            </ul>
            <form id="frm-po-header">
                <div class="tab-content" id="tab-technical_evaluation">
                    <div class="tab-pane fade active show" id="general-content" role="tabpanel" aria-labelledby="general-tab">
                        <br/>
                        <div class="row">
                            <div class="col-sm-6">
                                <table class="table table-borderless">
                                    <tbody>
                                        <tr>
                                            <th class="th-label" width="50%">E-Proc PO Number</th>
                                            <td class="td-value"><input type="text" class="form-control form-control-sm" value="{{isset($tenderData['models']['po_list']->eproc_po_number) ? $tenderData['models']['po_list']->eproc_po_number : ''}}" disabled></td>
                                        </tr>
                                        <tr>
                                            <th class="th-label" width="50%">SAP PO Number</th>
                                            <td class="td-value"><input type="text" class="form-control form-control-sm" value="{{isset($tenderData['models']['po_list']->sap_po_number) ? $tenderData['models']['po_list']->sap_po_number : ''}}" disabled></td>
                                        </tr>
                                        <tr>
                                            <th class="th-label">PO Document Type</th>
                                            <td class="td-value">
                                                <select name="document_type"  class="form-control form-control-sm sel_document" data-id="{{$vendor->vendor_profiles_id}}" disabled>
                                                    <option value=""> -- Select -- </option>
                                                    @foreach (config('eproc.document_type') as $key=>$value)
                                                        @php
                                                            $selected = ($value[0]==$document_type) ? "selected" : "";
                                                        @endphp
                                                        <option value="{{$value[0]}}" {{$selected}}>{{$value[1]}}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="th-label">Document Date</th>
                                            <td class="td-value">
                                                <div class="col-sm-12" style="padding: 0px;">
                                                    <input type="text" name="document_date" class="form-control form-control-sm datetimepicker-input" id="datetimepicker5" data-toggle="datetimepicker" data-target="#datetimepicker5"  disabled/>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-sm-6">
                                <table class="table table-borderless">
                                    <tbody>
                                        <tr>
                                            <th class="th-label">Incoterm</th>
                                            <td class="td-value">
                                                    <select id="incoterms" name="incoterms"  class="custom-select custom-select-sm"  disabled>
                                                        @php $incoterm_value = isset($tenderData['models']['header_commercial']->incoterm) ? $tenderData['models']['header_commercial']->incoterm : '' @endphp
                                                        @foreach ($incotermOptions as $key=>$value)
                                                            <option value="{{$key}}" @if($key==$incoterm_value) selected @endif>{{__($value)}} </option>
                                                        @endforeach
                                                    </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="th-label">Incoterm Location</th>
                                            <td class="td-value"><input name="incoterms_location" type="text" class="form-control form-control-sm" value="{{isset($tenderData['models']['header_commercial']->incoterm_location) ? $tenderData['models']['header_commercial']->incoterm_location : ''}}"  disabled></td>
                                        </tr>
                                        @if ($tender->tkdn_option==1)
                                        <tr>
                                            <th class="th-label">TKDN Overall Percentage</th>
                                            <td class="td-value"><input name="tkd_percentage" type="text" class="form-control form-control-sm" value="{{isset($tenderData['models']['header_technical']->tkdn_percentage) ? $tenderData['models']['header_technical']->tkdn_percentage : ''}}"  disabled></td>
                                        </tr>
                                        @endif

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="vendor-content" role="tabpanel" aria-labelledby="vendor-tab">
                        <br/>
                        <div class="row">
                            <div class="col-sm-8">
                                <table class="table table-borderless" width="100%">
                                    <tbody>
                                        <tr>
                                            <th class="th-label" width="20%">E-Proc Vendor Number</th>
                                            <td class="td-value" width="40%"><input type="text" class="form-control form-control-sm" value="{{$tenderData['models']['vendor']->vendor_code}}" disabled></td>
                                            <td width="10%"></td>
                                        </tr>
                                        <tr>
                                            <th class="th-label">E-Proc Vendor Name</th>
                                            <td class="td-value"><input type="text" class="form-control form-control-sm" value="{{$tenderData['models']['vendor']->vendor_name}}" disabled></td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <th class="th-label">Location Category*</th>
                                            <td class="td-value">
                                                <div class="input-group">
                                                    <select id="po-location-category" name="location_category"  class="form-control form-control-sm sel_compay" data-id="{{$vendor->vendor_profiles_id}}"  disabled>
                                                        <option value=""> -- Select -- </option>
                                                            @php
                                                            foreach(config('eproc.location_category') as $value){
                                                                $selected = (strtoupper(trim($location_category))==strtoupper(trim($value))) ? "selected" : "";
                                                                echo '<option value="'.$value.'" '.$selected.'>'.$value.'</option>';
                                                            }
                                                            @endphp
                                                    </select>
                                                &nbsp;
                                                @if (!empty($tenderData['models']['po_header']->vendor_profile_id))
                                                    &nbsp;
                                                    <select name="vendor_profile_id"  class="form-control form-control-sm"  data-id="{{$vendor->vendor_profiles_id}}" disabled>
                                                        <option value=""> -- Select -- </option>
                                                        @foreach($tenderData['models']['vendor_profiles'] as $key=>$value)
                                                            @php
                                                                $address = $value->address_1.", ".$value->address_2;
                                                                $selectedprofile = $tenderData['models']['po_header']->vendor_profile_id == $value->vendor_profile_id ? "selected" : "";
                                                            @endphp
                                                            <option value="{{$value->vendor_profile_id}}" {{$selectedprofile}}>{{$address}}</option>
                                                        @endforeach
                                                    </select>
                                                @endif
                                            </div>
                                            </td>
                                            <td><a href="#" title="expand address" class="btn-expand"><i class="fa fa-expand" aria-hidden="true"></i>&nbsp;</a></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="condition-content" role="tabpanel" aria-labelledby="condition-tab">
                        <br/>
                        <div class="row">
                            <div class="col-sm-6">
                                <table class="table table-borderless">
                                    <tbody>
                                        <tr>
                                            <th class="th-label">Header Level</th>
                                            <td class="td-value">
                                                     {{--@if($tender->conditional_type == 'CT1') --}}
                                                <div id="btn_additional_cost" class="btn btn-sm btn-outline-success ml-2" data-toggle="modal"
                                                data-target="#formAddcost_modal" data-backdrop="static" data-keyboard="false">
                                                <i class="fas fa-plus mr-2" aria-hidden="true"></i>{{__('tender.item_cost.title')}}
                                            </div>
                                        {{-- @endif --}}
                                                    </td>
                                        </tr>
                                        <tr>
                                            <th class="th-label">Net Value</th>
                                            <td class="td-value"><input type="text" name="net-value" class="form-control form-control-sm" value="0" disabled></td>
                                        </tr>
                                        <tr>
                                            <th class="th-label">Additional cost</th>
                                            <td class="td-value"><input name="total-header" type="text" class="form-control form-control-sm" value="0" disabled></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="orgdata-content" role="tabpanel" aria-labelledby="orgdata-tab">
                        <br/>
                        <div class="row">
                            <div class="col-sm-6">
                                <table class="table table-borderless">
                                    <tbody>
                                        <tr>
                                            <th class="th-label">Purchasing Organization</th>
                                            <td class="td-value"><input type="text" class="form-control form-control-sm"  value="{{$tenderData['models']['purchaseGroup']->group_code}} - {{$tenderData['models']['purchaseGroup']->description}}" disabled></td>
                                        </tr>
                                        <tr>
                                            <th class="th-label">Purchasing Group</th>
                                            <td class="td-value">
                                                <select name="purchase_org_code"  class="form-control form-control-sm"  disabled>
                                                    <option value=""> -- Select -- </option>

                                                    @foreach ($purchase_org_all as $key=>$value)
                                                        @php $selected = ($purchage_org_code == $value->org_code) ? "selected" : ""; @endphp
                                                        <option value="{{$value->org_code}}" {{$selected}}>{{$value->org_code}} - {{$value->description}} - {{$purchage_org_code}}</option>
                                                    @endforeach

                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="th-label">Company Code</th>
                                            <td class="td-value">
                                                @php
                                                    $company_code = $tenderData['models']['company_code'];
                                                    $companyCode = (isset($company_code)) ? $company_code->company_code." - ".$company_code->description : "";
                                                @endphp
                                                <input type="text" class="form-control form-control-sm" value="{{$companyCode}}"  disabled>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="text-content" role="tabpanel" aria-labelledby="text-tab">
                        <br/>
                        <div class="row">
                            <div class="col-md-12 col-sm-12">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <label class="panel-title"><strong>Header Text</strong></label>
                                    </div>
                                    <div class="panel-body">
                                        <div class="" style="padding: 0">
                                            <div class="form-group row mb-2">
                                                <div class="col-12">
                                                <textarea name="header_text" class="form-control form-control-sm" data-val="s" rows="5" disabled>{{$str_header_text}}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                            </div>
                            <div class="col-md-12 col-sm-12">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <label class="panel-title"><strong>Terms of Payment</strong></label>
                                    </div>
                                    <div class="panel-body">
                                        <div class="" style="padding: 0">
                                            <div class="form-group row mb-2">
                                                <div class="col-12">
                                                <textarea name="term_of_payment" class="form-control form-control-sm" data-val="s" rows="5"  disabled>{{$str_term_text}}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

        </div>
        <div class="tab">
            <ul class="nav nav-tabs" id="technical_evaluation-tab" role="tablist">
                <li class="nav-item" id="overview-li">
                    <a class="nav-link active" id="overview-tab" data-toggle="tab" href="#overview-content" role="tab" aria-controls="overview" aria-selected="true">PO Item</a>
                </li>
                <!-- <li class="nav-item" id="technical-li">
                    <a class="nav-link " id="technical-tab" data-toggle="tab" href="#technical-content" role="tab" aria-controls="technical" aria-selected="true">Technical</a>
                </li>
                <li class="nav-item" id="evaluation-li">
                    <a class="nav-link " id="evaluation-tab" data-toggle="tab" href="#evaluation-content" role="tab" aria-controls="evaluation" aria-selected="true">Evaluation</a>
                </li> -->
            </ul>
            <div class="tab-content" id="tab-technical_evaluation">
                <div class="tab-pane fade active show" id="overview-content" role="tabpanel" aria-labelledby="overview-tab">
                    <div class="tab-body">
                    <div class="row">
                            <div class="col-sm-12" hiden="">
                                <div class="form-inline" style="float:right">

                                    <span style="width:20px;"></span>
                                    <select name="currency" class="custom-select custom-select-sm" disabled>
                                        @foreach ($tenderData['models']['currency'] as $field)
                                        @php
                                            $selectedcurr = $tenderData["models"]["header_commercial"]->currency_code == $field->currency ? "selected" : "";
                                        @endphp
                                            <option value="{{$field->currency}}" {{$selectedcurr}}>{{$field->currency}} - {{$field->description}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <table id="datatable_serverside" class="table table-sm table-bordered table-striped table-vcenter table-wrap">
                                    <thead>
                                        <tr>
                                                <th>Nomor PR</th>
                                            @foreach ($tenderData['tender_'.$type]['fields'] as $field)
                                                <th>{{__('tender.po.'.$field)}}</th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                </div>
            </div>
        </div>
    </div>
</div>

<div class="app-footer">
    <div class="app-footer__inner">
        <div class="app-footer-left">
            <div id="page_numbers" style="display:inherit"></div>
        </div>

    </div>
</div>
@endsection

@section('modals')
    <?php
    $modal1 = [
        'title' => __("homepage.create_new_entry"),
        'contents' => '',
        'form_layout' => $formLayout,
        'form_name' => $formName,
    ];
    $modal2 = [
        'title'=> '',
        'contents'=>'',
        'form_layout'=>'po.partials.form_item_detail',
        'form_name'=>'formItemDetail',
        'modal_class' => 'modal-xl'
    ];
    $modal3 = [
        'title'=> __('tender.item_cost.title'),
        'contents'=>'',
        'form_layout'=>'po.partials.form_item_add_cost',
        'form_name'=>'formAddcost',
    ];
    ?>
    @include('layouts.modal_common',$modal1)
    @include('layouts.modal_common',$modal2)
    @include('layouts.modal_common',$modal3)
    @include('layouts.modal_delete')
@endsection

@section('footer')
@endsection

@section('modules-scripts')
@include('po.partials.profile_script')
<script>
var postalCodes = {!!json_encode($postalCodes)!!};
</script>
@include('layouts.datatableoption')
<script type="text/javascript">
var table;
var frmId = '#formAddcost';
var fields = {!! json_encode($tenderData['tender_'.$type]['fields_item']) !!};
//var fields = ["number","line_number","product_code","product_group_code","description","purch_group_code","purch_group_name","qty","uom","est_unit_price","price_unit","currency_code","subtotal","expected_delivery_date","deleteflg","account_assignment","item_category","gl_account","cost_code","requisitioner","requisitioner_desc","tracking_number","request_date","certification","material_status","plant","plant_name","storage_loc","storage_loc_name","qty_ordered","cost_desc","overall_limit","expected_limit"];
var _baseurl = "{{ route('po.show_detail', ['id' => $tender->tender_number, 'vcode'=> $vendor->vendor_code ,'type' => 'ItemList']) }}";
//var _baseurl = "{{ route('tender.dataItem', ['id' => $id, 'type' => 'items']) }}";
var m_item_id = 0;
var m_item_category = 0;

require(["datatablesb4","dt.plugin.select",'datetimepicker'], function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(document).ajaxStart(function() {
        Loading.Show();
    });
    $(document).ajaxComplete(function() {
        Loading.Hide();
    });

    $(function () {
        $('#datetimepicker5').datetimepicker({

           format : uiDateFormat
        });
        @if(isset($tenderData["models"]["po_header"]["document_date"]))
            var val = "{{$tenderData["models"]["po_header"]["document_date"]}}";
            var val = moment(val).format(uiDateFormat);
            $('#datetimepicker5').val(val);
        @endif

    });

    var DTTable = function(elmId){
        let SELF = this;
        this.table = null;
        this.init = function(callback, data){
            let dtOptions = getDTOptions();
            let options = {
                deferRender: dtOptions.deferRender,
                rowId: dtOptions.rowId,
                lengthChange: false,
                searching: false,
                processing: true,
                language: dtOptions.language,
                ajax : {
                    url : "{{ route('po.show_detail', ['id' => $tender->tender_number, 'vcode'=> $vendor->vendor_code ,'type' => 'ItemList']) }}?eproc_po_number={{$eproc_po_number}}",
                    // complete : function(){},
                },
                columns: [
                    {
                        data: 'id', name: 'id',"width": 50,"className": 'text-center',
                        'visible' :  false,

                    },
                    @foreach ($tenderData['tender_'.$type]['fields'] as $field)
                        @if ($field=="po_item"){
                                data: '{{$field}}', name: '{{$field}}',
                                "render": function ( data, type, row ) {
                                    return '<a href="javascript:void(0)" val-cat="' + row.item_category + '" val-id="' + row.item_id  + '" class="open-detail" >' + data + '</a>';
                                },
                            },
                        @elseif ($field=="delivery_date"){
                            data: '{{$field}}', name: '{{$field}}',
                            "render": function ( data, type, row ) {
                                return '<div class="col-sm-12" style="padding: 0px;"><input id="valid_date-' + row.id  + '" val-id="' + row.item_id  + '" class="form-control form-control-sm datetimepicker-input" value="' + data + '"  data-toggle="datetimepicker" readonly data-target="#valid_date-' + row.id  + '"></div>';
                            },
                        },
                        @elseif (in_array($field,["total","est_unit_price","overall_limit"])){
                            data: '{{$field}}', name: '{{$field}}',class: 'text-right',
                            "render": function ( data, type, row ) {
                                return '<span class="total">' + formatAmmount(data, getCurrencyCode()) + '</span>';
                            },
                        },
                        @else
                            {data: '{{$field}}', name: '{{$field}}'},
                        @endif
                    @endforeach
                ],
                initComplete: function(settings, json) {
                    console.log(json.data + "==========json init complete");
                    var sub_total = 0;
                    $.each(json.data, function(k, v) {
                        $('#valid_date-' + v.id).datetimepicker({
                            format : uiDateFormat
                        });
                        var val = moment(v.tgl_pekerjaan_serah_terima).format(uiDateFormat);
                        console.log(val + "====");
                        $('#valid_date-'+ v.id).val(val);
                        sub_total = sub_total + v.total;
                    });
                    $("input[name='net-value']").val(formatAmmount(sub_total, getCurrencyCode()));
                    var url_total = "{{route('po.show_detail', ['id' => $tender->tender_number, 'vcode'=> $vendor->vendor_code, 'type' => 'total'])}}?total=" + sub_total;
                    url_total = url_total + "&eproc_po_number={{$eproc_po_number}}"
                    $.get(url_total, function(data){
                        $("input[name='total-header']").val(formatAmmount(data.data, getCurrencyCode()));
                    });

                }
            };
            SELF.table = $('#' + elmId).DataTable(options);
            let tabId = elmId;
            if(typeof callback == 'function'){
                callback(elmId);
            }
            return SELF.table;
        },
        this.sum= function(col, callback){
            if(typeof callback == 'function'){
                $('#' + elmId).on( 'draw.dt', function () {
                    callback(SELF.table.column(col).data().sum());
                });
            }else{
                return SELF.table.column(col).data().sum();
            }
        }
    };
    var DTTableItem = function(elmId){
        let SELF = this;
        this.IsChanged = false;
        this.OriginalData = [];
        this.elmId = elmId;
        this.table = null;
        this.options = {};
        this.init = function(callbcak){
            SELF.IsChanged =false;
            let dtOptions = getDTOptions();
            let options = Object.assign({
                deferRender: dtOptions.deferRender,
                rowId: dtOptions.rowId,
                lengthChange: false,
                searching: false,
                language: dtOptions.language,
                autoWidth: false,
            }, SELF.options);
            //## Initilalize Datatables
            SELF.table = $('#' + elmId).DataTable(options);
            let tabId = elmId;
            if(typeof callback == 'function'){
                callback(elmId);
            }
            return SELF.table;
        },
        this.reload = function(_url){
            SELF.IsChanged =false;
            $.ajax({
                url : _url,
                contentType: "application/json; charset=utf-8",
                dataType: "json",
                beforeSend: function( xhr ) {
                    Loading.Show('#' + elmId + ' tbody');
                }
            }).done(function(response, textStatus, jqXhr) {
                SELF.OriginalData = response.data;
                SELF.table.rows.add( response.data ).draw();
            }).fail(defaultAjaxFail)
            .always(function(jqXHR, textStatus, errorThrown) {
                Loading.Hide('#' + elmId + ' tbody');
            });
        };
        this.isDataChanged = function(){
            return SELF.IsChanged;
        }
    };

    var ItemsPage = {
        table : null,
        selectedRow : null,
        init : function(){
            var SELF = this;
            SELF.table = new DTTable('datatable_serverside')
                .init(function(elmId){
                    $('#page_numbers').ready(function () {
                        $('#' + elmId +'_paginate').appendTo($('#page_numbers'));
                        $('#' + elmId +'_info').css("padding", ".375rem .75rem").appendTo($('#page_numbers'));
                    });
                });
            $('#btn_next_flow').click(function(){
                onClickNext();
            });

            // action column
            $('#datatable_serverside tbody').on('click','.open-detail', function(e){
                m_item_id = $(this).attr("val-id");
                m_item_category = $(this).attr("val-cat");
                e.preventDefault();

                var _url = "{{ route('po.show_detail', ['id' => $tender->tender_number, 'vcode'=> $vendor->vendor_code ,'type' => 'ItemList']) }}?data_type=null";
                _url = _url + "&item_id=" + m_item_id;

                $.get(_url, function(data ) {
                    let dtrow = data.data[0];
                    console.log(data);
                    // let dtrow = SELF.table.row($(this).parents('tr')).data();
                    SELF.selectedRow = dtrow;
                    SELF.openDetailRow(dtrow);
                });

            });
            $('#datatable_serverside tbody').on('click','.deleteRow', function(e){
                e.preventDefault();
                let dtrow = SELF.table.row($(this).parents('tr')).data();
                SELF.deleteRow(dtrow);
            });
        },
        openDetailRow : function(dtrow){
            console.log(dtrow);
            console.log(dtrow.number);
            $('#formItemDetail_modal .modal-title').html('PR ' + dtrow.number + ' / ' + dtrow.line_number );
            $('#formItemDetail_modal .title-left').html('PR ' + dtrow.number);
            $('#formItemDetail_modal .title-right').html(dtrow.line_number);
            $('#pr-item input[name="id"]').val(dtrow.id)
            var arrEdit = ["qty", "overall_limit","est_unit_price","description","expected_delivery_date"];
            for(let ix in fields){
                //$('#formItemDetail_modal #pr-item #' + fields[ix]).html("aa");
                @if($editable)
                    var isItemCat = true;//dtrow.item_category == 0;
                    if(arrEdit.includes(fields[ix]) && (isItemCat)){
                        // $('#formItemDetail_modal #pr-item input[name="'+fields[ix]+'"]').val(dtrow[fields[ix]]);
                        if (fields[ix]=="expected_delivery_date"){
                            $('#formItemDetail_modal #pr-item #' + fields[ix])
                                .html('<div class="col-sm-12" style="padding: 0px;"><input id="date1" name="'+fields[ix]+'" class="form-control form-control-sm datetimepicker-input" data-toggle="datetimepicker" data-target="#date1" autocomplete="off" data-value="'+moment(dtrow[fields[ix]], uiDateFormat).format(uiDateFormat)+'" /></div>');
                        }else{
                            $('#formItemDetail_modal #pr-item #' + fields[ix])
                                .html('<input name="'+fields[ix]+'" class="form-control form-control-sm" value="'+dtrow[fields[ix]]+'" />');
                        }
                    }else{
                        $('#formItemDetail_modal #pr-item #' + fields[ix]).html(dtrow[fields[ix]]);
                    }
                    @else
                        $('#formItemDetail_modal #pr-item #' + fields[ix]).html(dtrow[fields[ix]]);
                    @endif

            }
            $('#formItemDetail_modal').modal();
        },
        deleteRow : function(dtrow){
            let rowinfo = dtrow['number'] + ' / '+dtrow['line_number'];
            $('#delete_modal .modal-title').text("Delete "+rowinfo);
            $('#delete_modal .modal-body').text("Are you sure to delete "+rowinfo+"?");
            $('#btn_delete_modal').click();
            $('#delete_modal #btn_confirm').off('click').on('click', function () {
                $.ajax({
                    type: 'DELETE',
                    url: "{{ route('tender.show',['id'=>$id,'type'=>$type]) }}/"+dtrow['id'],
                    cache : false,
                    beforeSend: function( xhr ) {
                        Loading.Show();
                    }
                }).done(function(response) {
                    if(response.success){
                        $('#delete_modal .close').click();
                        showAlert(rowinfo+" deleted", "success", 3000);
                        SELF.table.ajax.reload();
                        resetForm();
                    }else{
                        showAlert(rowinfo+" not deleted", "danger", 3000);
                    }
                }).always(function(jqXHR, textStatus, errorThrown) {
                    Loading.Hide();
                });
                return false;
            });
        },
        reloadTable : function(){
            console.log("reloadtable==============================1");
            this.table.ajax.reload(function() {
                var sub_total = 0;
                $('#datatable_serverside tbody tr').each(function() {
                    var inputEl = $(this).find("td .col-sm-12 input");
                    var val =moment($(inputEl).val()).format(uiDateFormat);
                    var id = $(this).attr("id");
                    console.log(id);
                    var total = $(this).find(".total").text();
                    console.log("total: "+ total);
                    sub_total = sub_total + parseInt(total.replace(/\./g,''));
                    console.log("sub total: "+ total);
                    $('#valid_date-' + id).datetimepicker({
                        format : uiDateFormat
                    });
                    $(inputEl).val(val);
                });
                $("input[name='net-value']").val(formatAmmount(sub_total, getCurrencyCode()));
                    $.get("{{route('po.show_detail', ['id' => $tender->tender_number, 'vcode'=> $vendor->vendor_code, 'type' => 'total'])}}?eproc_po_number={{$eproc_po_number}}&total=" + sub_total, function(data){
                    $("input[name='total-header']").val(formatAmmount(data.data, getCurrencyCode()));
                });
            });
        },
    }

    var FormCostPage = {
        table : null,
        selectedRow : null,
        initTable : function(callback){
            var SELF = this;
            var elmId = 'dt-add-cost';
            let dtOptions = getDTOptions();
            let options = {
                deferRender: dtOptions.deferRender,
                rowId: dtOptions.rowId,
                lengthChange: false,
                searching: false,
                language: dtOptions.language,
                autoWidth: false,
                columns: [
                    {
                        data: 'id', name: 'id',"width": 50,"className": 'text-center',
                        'visible' : @if($editable) true @else false @endif,
                        @if($editable)
                            "render": function ( data, type, row ) {
                                var str_return = '';
                                @if ($is_show)
                                    str_return = str_return +
                                    '<a href="" class="col-action col-edit editRow mr-2" ><i class="fa fa-edit"></i></a>'+
                                    '<a href="" class="col-action col-delete deleteRow"><i class="fa fa-trash"></i></a>';
                                @endif
                                return str_return;
                            },
                        @endif
                    },
                    {data: 'calculation_pos', name: 'calculation_pos',"visible": false},
                    {data: 'conditional_code', name: 'conditional_code',"visible": false},
                    {data: 'conditional_type', name: 'conditional_type',"visible": false},
                    {data: 'conditional_name', name: 'conditional_name'},
                    {
                        data: 'percentage', name: 'percentage',"width": 250,
                        render: function (data, type, row, meta) {
                            if(parseFloat(data || 0) > 0)
                                return formatPercentage(data);
                            else
                                return "";
                        },
                    },
                    {
                        data: 'value', name: 'value',"width": 250,
                        render: function (data, type, row, meta) {
                            if(parseFloat(data || 0) > 0)
                                return formatAmmount(data, getCurrencyCode());
                            else
                                return "";
                        },
                    },
                ],
                "order": [[ 1, "asc" ]],
            };
            //## Initilalize Datatables
            SELF.table = $('#' + elmId).DataTable(options);
            let tabId = elmId;
            if(typeof callback == 'function'){
                callback(elmId);
            }
            return SELF.table;
        },
        init : function(){
            var SELF = this;
            SELF.initTable(function(elmId){
                $('#vpage_numbers').ready(function () {
                    $('#' + elmId +'_paginate').appendTo($('#vpage_numbers'));
                    $('#' + elmId +'_info').css("padding", ".375rem .75rem").appendTo($('#vpage_numbers'));
                });
            });

            // action form
            $(frmId+'_modal').on("shown.bs.modal", function () {
                try{
                    SELF.resetForm();
                    SELF.reloadTable();
                    initInputQty();
                    initInputDecimal(getCurrencyCode());
                    @if (!$is_show && false)
                        $("#formItemDetail-save").hide();
                    @endif
                    //initInputPercentage();
                }catch{}
            });
            $("#btn_additional_cost").click(function(){
                isEdit = false;
                SELF.resetForm();
                SELF.table.rows().remove().draw();
            });
            $(frmId+'-save').click(function(){
                if(SELF.validateSubmit()){
                    SELF.submit(function(){
                        $(frmId+'_modal .close').click();
                        ItemsPage.reloadTable();
                        SELF.resetForm();
                        $(frmId+'_fieldset').attr("disabled",false);
                    });
                }
            });

            @if(!$editable)
            $(frmId+'-save').hide();
            @endif

            // action column
            $('#dt-add-cost tbody').on('click','.editRow', function(e){
                e.preventDefault();
                let dtrow = SELF.table.row($(this).parents('tr')).data();
                SELF.selectedRow = SELF.table.row($(this).parents('tr'));
                $('#dt-add-cost tbody .deleteRow').show();
                $(this).parents('tr').find('.deleteRow').hide();
                SELF.editRow(dtrow);
            });
            $('#dt-add-cost tbody').on('click','.deleteRow', function(e){
                e.preventDefault();
                let dtrow = SELF.table.row($(this).parents('tr'));
                SELF.deleteRow(dtrow);
            });

            // action form cost
            $(frmId+'_modal').on('change','select[name="conditional_code"]', function(e){
                let selected = $(this).find(":selected");
                let conditional_name = selected.text();
                let clculation_type = selected.data('calculation-type');
                let clculation_post = selected.data('calculation-pos');
                $(frmId+'_modal input[name="conditional_name"]').val(conditional_name);
                $(frmId+'_modal input[name="calculation_pos"]').val(clculation_post);
                if(clculation_type == 1){
                    $(frmId+'_modal div.g-percentage').show();
                    $(frmId+'_modal div.g-value').hide();
                }else{
                    $(frmId+'_modal div.g-percentage').hide();
                    $(frmId+'_modal div.g-value').show();
                }
            })
            $(frmId+'_modal button.btn-add').click(function(e){
                e.preventDefault();
                if(SELF.validateRow()){
                    SELF.saveRow( $(frmId+'_modal input[name="id"]').val());
                    SELF.resetForm();
                }
            });
            $(frmId+'_modal button.btn-cancel').click(function(e){
                e.preventDefault();
                SELF.resetForm();
                $('#dt-add-cost tbody .deleteRow').show();
            });
        },
        reloadTable : function(){
            let SELF = this;
            var elmId = 'dt-add-cost';
            let _url = "{{ route('po.show_detail', ['id' => $tender->tender_number, 'vcode'=> $vendor->vendor_code ,'type' => 'ItemList']) }}";
            let _type = "CT1&eproc_po_number={{$eproc_po_number}}";
            $.ajax({
                url : _url + '?data_type=4&cost_type=' + _type,
                contentType: "application/json; charset=utf-8",
                dataType: "json",
                beforeSend: function( xhr ) {
                    Loading.Show('#' + elmId + ' tbody');
                }
            }).done(function(response, textStatus, jqXhr) {
                SELF.table.rows.add( response.data ).draw();
            }).fail(defaultAjaxFail)
            .always(function(jqXHR, textStatus, errorThrown) {
                Loading.Hide('#' + elmId + ' tbody');
            });
        },
        resetForm : function(){
            $(frmId+'_modal input[name="id"]').val('');
            $(frmId+'')[0].reset();
            this.selectedRow = null;
            $(frmId+'_modal div.g-percentage').hide();
            $(frmId+'_modal div.g-value').hide();
            $(frmId+'_modal button.btn-add').html("{{__('common.add')}}");
        },
        saveRow : function(id){
            var SELF = this;
            let isEdit = true;
            if(!id || id==''){
                isEdit = false;
                id = (new Date()).getTime();
            }else{
                id = parseInt(id);
            }

            let selected = $(frmId+'_modal select[name="conditional_code"]').find(":selected");
            let clculation_type = selected.data('calculation-type');
            let data = {
                id : id,
                conditional_name : $(frmId+'_modal input[name="conditional_name"]').val() || '',
                calculation_pos : $(frmId+'_modal input[name="calculation_pos"]').val() || '',
                conditional_code : $(frmId+'_modal select[name="conditional_code"]').val() || '',
                conditional_type : $(frmId+'_modal input[name="conditional_type"]').val() || '',
                percentage : $(frmId+'_modal input[name="percentage"]').val() || '',
                // value : $(frmId+'_modal input[name="value"]').val() || '',
                //percentage : getAutonumricValue($(frmId+'_modal input[name="percentage"]')) || '',
                value : getAutonumricValue($(frmId+'_modal input[name="value"]')) || '',

            };
            if(clculation_type == 1){
                data.value = null;
            }else{
                data.percentage = null;
            }

            if(isEdit == true){
                SELF.selectedRow.data( data ).draw();
            }else{
                SELF.table.row.add(data).draw();
            }
            $('#dt-add-cost tbody .deleteRow').show();
        },
        validateRow : function(){
            var SELF = this;
            let valid = true;
            let conditionalCode = $(frmId+'_modal select[name="conditional_code"]').val();
            if(!conditionalCode || conditionalCode == ''){
                valid = false;
                showAlert("Name is required", "warning");
            }
            let selected = $(frmId+'_modal select[name="conditional_code"]').find(":selected");
            let clculation_type = selected.data('calculation-type');

            let percentage = $(frmId+'_modal input[name="percentage"]').val();
            if(clculation_type == 1 && percentage > 100){
                valid = false;
                showAlert("Max percentage is 100", "warning");
            }
            if(clculation_type == 1 && percentage < 0){
                valid = false;
                showAlert("Min percentage is 0", "warning");
            }

            let count = this.table.rows().count();
            let _data = this.table.rows().data();
            let oldConditionalCode = SELF.selectedRow ? SELF.selectedRow.data().conditional_code : ''; // SELF.selectedRow.conditional_code;

            if(count > 0 && (conditionalCode != '' && conditionalCode!=oldConditionalCode)){
                for(let ix=0;ix<count;ix++){
                    if(conditionalCode == _data[ix].conditional_code){
                        showAlert("Duplicate " + _data[ix].conditional_name, "warning");
                        return false;
                    }
                }
            }

            return valid;
        },
        editRow : function(dtrow){
            var SELF = this;
            $(frmId+'_modal input[name="id"]').val(dtrow.id);
            $(frmId+'_modal input[name="conditional_name"]').val(dtrow.conditional_name);
            $(frmId+'_modal input[name="calculation_pos"]').val(dtrow.calculation_pos);
            $(frmId+'_modal select[name="conditional_code"]').val(dtrow.conditional_code);
            $(frmId+'_modal select[name="conditional_code"]').trigger('change');
            $(frmId+'_modal input[name="percentage"]').val(dtrow.percentage);
            // $(frmId+'_modal input[name="value"]').val(dtrow.value);
            if(getCurrencyCode() == "IDR"){
                $(frmId+'_modal input[name="value"]').val(parseFloat(dtrow.value).toFixed(0));
            }else{
                $(frmId+'_modal input[name="value"]').val(parseFloat(dtrow.value).toFixed(2));
            }
            $(frmId+'_modal input[name="calculation_pos"]').val(dtrow.calculation_pos);
            $(frmId+'_modal button.btn-add').html("{{__('common.update')}}");
        },
        deleteRow : function(dtrow){
            dtrow.remove().draw();
            this.selectedRow = null;
        },
        validateSubmit : function(){
            let valid = true;
            // if(this.table.rows().count() <= 0){
            //     valid = false;
            //     showAlert("Please input one or more data", "warning");
            // }
            return valid;
        },
        submit : function(callback)
        {
            let SELF = this;
            //SUBMIT
            let dataTable = SELF.table.rows().data();
            let additonalCost = [];
            for(let ix=0;ix<SELF.table.rows().count();ix++){
                additonalCost[ix] = dataTable[ix];
                delete additonalCost[ix]['id'];
            }

            let params = {
                item : null,
                cost : additonalCost,
                cost_type : $("#formAddcost input[name='conditional_type']").val(),
            };
            // console.log(params);
            // return;
            $.ajax({
                url : "{{ route('po.save', ['id'=>$id, 'vcode'=> $vendor->vendor_code, 'type'=>'items']) }}?cost_type=CT1&eproc_po_number={{$eproc_po_number}}",
                type : 'POST',
                data : JSON.stringify(params),
                contentType: "application/json; charset=utf-8",
                dataType: "json",
                beforeSend: function( xhr ) {
                    $(frmId+'_fieldset').attr("disabled",true);
                    Loading.Show();
                }
            }).done(function(response, textStatus, jqXhr) {
                if(response.success){
                    if(typeof callback == 'function'){
                        callback();
                    }
                    showAlert("Data saved.", "success", 3000);
                }else{
                    showAlert("Data not saved.", "danger", 3000);
                }
            }).fail(defaultAjaxFail)
            .always(function(jqXHR, textStatus, errorThrown) {
                $(frmId+'_fieldset').attr("disabled",false);
                Loading.Hide();
            });
        },
    }

    var ItemDetailPage = {
        ServiceTable : null,
        TaxTable : null,
        CostTable : null,
        FormTaxCode : {
            selectedRow : null,
            resetForm : function(){
                this.selectedRow = null;
                $('#tax-item input[name="id"]').val('');
                $('#tax-item select[name="tax_code"]').val('');
                $('#tax-item button.btn-add').html("{{__('common.add')}}");
            },
            editRow : function(dtrow){
                $('#tax-item input[name="id"]').val(dtrow.id);
                $('#tax-item select[name="tax_code"]').val(dtrow.tax_code);
                $('#tax-item button.btn-add').html("{{__('common.update')}}");
            },
            deleteRow : function(selectedRow){
                selectedRow.remove().draw();
                this.selectedRow = null;
            },
            validateRow : function(){
                let SELF = this;
                let valid = true;
                let taxCode = $('#tax-item select[name="tax_code"]').val();
                if(!taxCode || taxCode == ''){
                    valid = false;
                    showAlert("Tax Code is required", "warning");
                }

                let count = ItemDetailPage.TaxTable.table.rows().count();
                let _data = ItemDetailPage.TaxTable.table.rows().data();
                let oldTaxCode = SELF.selectedRow ? SELF.selectedRow.data().tax_code : '';
                if(count > 0 && (taxCode != '' && oldTaxCode != taxCode)){
                    for(let ix=0;ix<count;ix++){
                        if(taxCode == _data[ix].tax_code){
                            showAlert("Duplicate " + _data[ix].description, "warning");
                            return false;
                        }
                    }
                }
                return valid;
            },
            saveRow : function(id){
                let isEdit = true;
                if(!id || id==''){
                    isEdit = false;
                    id = (new Date()).getTime();
                }else{
                    id = parseInt(id);
                }
                let data = {
                    id : id,
                    tax_code : $('#tax-item select[name="tax_code"]').val() || '',
                    description : $('#tax-item select[name="tax_code"]').find(':selected').text() || '',
                };
                if(isEdit == true){
                    this.selectedRow.data( data ).draw();
                }else{
                    ItemDetailPage.TaxTable.table.row.add(data).draw();
                }
                ItemDetailPage.TaxTable.IsChanged = true;
                $('#dt-tax-item tbody .deleteRow').show();
            },
            init : function(){
                let SELF = this;
                // action column item - tax code
                $('#dt-tax-item tbody').on('click','.editRow', function(e){
                    e.preventDefault();
                    let dtrow = ItemDetailPage.TaxTable.table.row($(this).parents('tr')).data();
                    SELF.selectedRow = ItemDetailPage.TaxTable.table.row($(this).parents('tr'));
                    $('#dt-tax-item tbody .deleteRow').show();
                    $(this).parents('tr').find('.deleteRow').hide();
                    SELF.editRow(dtrow);
                });
                $('#dt-tax-item tbody').on('click','.deleteRow', function(e){
                    e.preventDefault();
                    SELF.selectedRow = ItemDetailPage.TaxTable.table.row($(this).parents('tr'));
                    SELF.deleteRow(SELF.selectedRow);
                });

                // action form item - tax code
                $('#tax-item button.btn-add').click(function(e){
                    e.preventDefault();
                    if(SELF.validateRow()){
                        SELF.saveRow( $('#tax-item input[name="id"]').val());
                        SELF.resetForm();
                    }
                });
                $('#tax-item button.btn-cancel').click(function(e){
                    e.preventDefault();
                    SELF.resetForm();
                    $('#dt-tax-item tbody .deleteRow').show();
                });
            },
        },
        FormCost : {
            selectedRow : null,
            resetForm : function(){
                this.selectedRow = null;
                $('#cost-item input[name="id"]').val('');
                $('#cost-item select[name="conditional_code"]').val('');
                $('#cost-item select[name="conditional_code"]').trigger('change');
                $('#cost-item input[name="conditional_name"]').val('');
                $('#cost-item input[name="calculation_pos"]').val('');
                $('#cost-item input[name="percentage"]').val('');
                $('#cost-item input[name="value"]').val('');

                $('#cost-item div.g-percentage').hide();
                $('#cost-item div.g-value').hide();
                $('#cost-item button.btn-add').html("{{__('common.add')}}");
            },
            editRow : function(dtrow){
                $('#cost-item input[name="id"]').val(dtrow.id);
                $('#cost-item select[name="conditional_code"]').val(dtrow.conditional_code);
                $('#cost-item select[name="conditional_code"]').trigger('change');
                $('#cost-item input[name="conditional_name"]').val(dtrow.conditional_name);
                $('#cost-item input[name="calculation_pos"]').val(dtrow.calculation_pos);
                $('#cost-item input[name="percentage"]').val(dtrow.percentage);
                // $('#cost-item input[name="value"]').val(dtrow.value);

                if(getCurrencyCode() == "IDR"){
                    $('#cost-item input[name="value"]').val(parseFloat(dtrow.value).toFixed(0));
                }else{
                    $('#cost-item input[name="value"]').val(parseFloat(dtrow.value).toFixed(2));
                }

                $('#cost-item button.btn-add').html("{{__('common.update')}}");
            },
            deleteRow : function(selectedRow){
                selectedRow.remove().draw();
                this.selectedRow = null;
            },
            validateRow : function(editMode){
                var SELF = this;
                let valid = true;
                let conditionalCode = $('#cost-item select[name="conditional_code"]').val();
                if(!conditionalCode || conditionalCode == ''){
                    valid = false;
                    showAlert("Name is required", "warning");
                }

                let selected = $('#cost-item select[name="conditional_code"]').find(":selected");
                let clculation_type = selected.data('calculation-type');
                let percentage = $('#cost-item input[name="percentage"]').val();
                if(clculation_type == 1 && percentage > 100){
                    valid = false;
                    showAlert("Max percentage is 100", "warning");
                }
                if(clculation_type == 1 && percentage < 0){
                    valid = false;
                    showAlert("Min percentage is 0", "warning");
                }

                let count = ItemDetailPage.CostTable.table.rows().count();
                let _data = ItemDetailPage.CostTable.table.rows().data();

                let oldConditionalCode = SELF.selectedRow ? SELF.selectedRow.data().conditional_code : ''; // SELF.selectedRow.conditional_code;
                if(count > 0 && (conditionalCode != '' && conditionalCode!=oldConditionalCode)){
                    for(let ix=0;ix<count;ix++){
                        if(conditionalCode == _data[ix].conditional_code){
                            showAlert("Duplicate " + _data[ix].conditional_name, "warning");
                            return false;
                        }
                    }
                }
                return valid;
            },
            saveRow : function(id){
                let isEdit = true;
                if(!id || id==''){
                    isEdit = false;
                    id = (new Date()).getTime();
                }else{
                    id = parseInt(id);
                }

                let selected = $('#cost-item select[name="conditional_code"]').find(":selected");
                let clculation_type = selected.data('calculation-type');
                let data = {
                    id : id,
                    conditional_name : $('#cost-item input[name="conditional_name"]').val() || '',
                    calculation_pos : $('#cost-item input[name="calculation_pos"]').val() || '',
                    conditional_code : $('#cost-item select[name="conditional_code"]').val() || '',
                    conditional_type : $('#cost-item input[name="conditional_type"]').val() || '',
                    // percentage : $('#cost-item input[name="percentage"]').val() || '',
                    // value : $('#cost-item input[name="value"]').val() || '',
                    percentage : getAutonumricValue($('#cost-item input[name="percentage"]')) || '',
                    value : getAutonumricValue($('#cost-item input[name="value"]')) || '',
                };
                if(clculation_type == 1){
                    data.value = null;
                }else{
                    data.percentage = null;
                }
                console.log(data);
                if(isEdit == true){
                    this.selectedRow.data( data ).draw();
                }else{
                    ItemDetailPage.CostTable.table.row.add(data).draw();
                }
                ItemDetailPage.CostTable.IsChanged = true;
                $('#dt-cost-item tbody .deleteRow').show();
            },
            init : function(){
                var SELF = this;
                // action column item - tax code
                $('#dt-cost-item tbody').on('click','.editRow', function(e){
                    e.preventDefault();
                    let dtrow = ItemDetailPage.CostTable.table.row($(this).parents('tr')).data();
                    SELF.selectedRow = ItemDetailPage.CostTable.table.row($(this).parents('tr'));
                    console.log(SELF.selectedRow);
                    $('#dt-cost-item tbody .deleteRow').show();
                    $(this).parents('tr').find('.deleteRow').hide();
                    SELF.editRow(dtrow);
                });
                $('#dt-cost-item tbody').on('click','.deleteRow', function(e){
                    e.preventDefault();
                    SELF.selectedRow = ItemDetailPage.CostTable.table.row($(this).parents('tr'));
                    SELF.deleteRow(SELF.selectedRow);
                });

                // action form item - tax code
                $('#cost-item select[name="conditional_code"]').on('change', function(e){
                    let selected = $(this).find(":selected");
                    let conditional_name = selected.text();
                    let clculation_type = selected.data('calculation-type');
                    let clculation_post = selected.data('calculation-pos');
                    $('#cost-item input[name="conditional_name"]').val(conditional_name);
                    $('#cost-item input[name="calculation_pos"]').val(clculation_post);
                    if(clculation_type == 1){
                        $('#cost-item div.g-percentage').show();
                        $('#cost-item div.g-value').hide();
                    }else{
                        $('#cost-item div.g-percentage').hide();
                        $('#cost-item div.g-value').show();
                    }
                })
                $('#cost-item button.btn-add').click(function(e){
                    e.preventDefault();
                    if(SELF.validateRow()){
                        SELF.saveRow( $('#cost-item input[name="id"]').val());
                        SELF.resetForm();
                    }
                });
                $('#cost-item button.btn-cancel').click(function(e){
                    e.preventDefault();
                    SELF.resetForm();
                    $('#dt-cost-item tbody .deleteRow').show();
                });
            },
        },
        init : function(){
            var SELF = this;
            $('#formItemDetail_modal').on("shown.bs.modal", function () {
                try{
                    SELF.resetForm();
                    SELF.reloadTable();
                    SELF.ForceCloseModal = false;
                    SELF.initModalShow();
                    $("#formItemDetail-save").hide();
                }catch(e){
                    console.error(e);
                }
            });

            // table item tax
            SELF.TaxTable = new DTTableItem('dt-tax-item');
            SELF.TaxTable.options = {
                columns: [
                    {
                        data: 'id', name: 'id',"width": 50,"className": 'text-center',
                        'visible' : @if($editable) true @else false @endif,
                        @if($editable)
                            "render": function ( data, type, row ) {
                                var str_return = '';
                                @if ($is_show)
                                    str_return = str_return +
                                    '<a href="" class="col-action col-edit editRow mr-2" ><i class="fa fa-edit"></i></a>'+
                                    '<a href="" class="col-action col-delete deleteRow"><i class="fa fa-trash"></i></a>';
                                @endif
                                return str_return;
                            },
                        @endif
                    },
                    {data: 'tax_code', name: 'tax_code',"visible": true},
                    {data: 'description', name: 'description',"visible": true},
                ],
                "order": [[ 1, "asc" ]],
            };
            SELF.TaxTable.init(function(elmId){});
            // table item addional cost
            SELF.CostTable = new DTTableItem('dt-cost-item');
            SELF.CostTable.options = {
                columns: [
                    {
                        data: 'id', name: 'id',"width": 50,"className": 'text-center',
                        'visible' : @if($editable) true @else false @endif,
                        @if($editable)
                            "render": function ( data, type, row ) {
                                var str_return = '';
                                @if ($is_show)
                                    str_return = str_return +
                                    '<a href="" class="col-action col-edit editRow mr-2" ><i class="fa fa-edit"></i></a>'+
                                    '<a href="" class="col-action col-delete deleteRow"><i class="fa fa-trash"></i></a>';
                                @endif
                                return str_return;
                            },
                        @endif
                    },
                    {data: 'calculation_pos', name: 'calculation_pos',"visible": false},
                    {data: 'conditional_code', name: 'conditional_code',"visible": false},
                    {data: 'conditional_type', name: 'conditional_type',"visible": false},
                    {data: 'conditional_name', name: 'conditional_name'},
                    {data: 'percentage', name: 'percentage',"width": 250},
                    {
                        data: 'value', name: 'value',"width": 250,
                        render: function (data, type, row, meta) {
                            data = parseFloat(data) || 0;


                            if(row.calculation_type == 1){
                                data = data.toFixed(2);
                            }else{
                                if(getCurrencyCode() == "IDR"){
                                    data = data.toFixed(0);
                                }else{
                                    data = data.toFixed(2);
                                }
                            }
                            return data > 0 ? data : '';
                        },
                    },
                ],
                "order": [[ 1, "asc" ]],
            };
            SELF.CostTable.init(function(elmId){});
            // table item services
            SELF.ServiceTable = new DTTableItem('dt-service-item');
            SELF.ServiceTable.options = {
                columns: [
                    {data: 'EXTROW', name: 'EXTROW',"visible": true},
                    {data: 'KTEXT1', name: 'KTEXT1',"visible": true},
                    {data: 'MENGE', name: 'MENGE',"visible": true},
                    {data: 'MEINS', name: 'MEINS'},
                    {data: 'BRTWR', name: 'BRTWR',"visible": true},
                    {data: 'WAERS', name: 'WAERS',"width": 250},
                    {data: 'COST_CODE', name: 'COST_CODE',"visible": true},
                    {data: 'COST_DESC', name: 'COST_DESC',"visible": true},
                ],
                "order": [[ 3, "asc" ]],
            };
            SELF.ServiceTable.init(function(elmId){});

            @if(!$editable)
            $('#formItemDetail-save').hide();
            @else
            $('#formItemDetail-save').show();
            $('#formItemDetail-save').click(function(e){
                e.preventDefault();
                if(SELF.validateSubmit()){
                    console.log("===2");
                    SELF.submit(function(){
                        console.log("===1");
                        $('#formItemDetail_modal .close').click();
                        ItemsPage.reloadTable();
                        SELF.resetForm();
                    });
                }
                console.log("===3");
            });

            // $('textarea[name="item_text"]').keyup(function() {
            $('textarea[name="item_text"]').on('input keydown keyup focus',function() {
                let lines = inputItemTextLength($(this).val(), 132);
                $(this).val(lines.join(''));
            });
            @endif

            SELF.FormTaxCode.init();
            SELF.FormCost.init();
        },
        initModalShow : function(){
            let SELF = this;
            // $('#pr-item input[name="qty"]').keyup(function(event){
            //     // if(isNumberKey(event.keyCode)){
            //     //     SELF.onChangeSelectedQty(ItemsPage.selectedRow);
            //     // }
            //     // return false;
            //     if(!isNumberKey(event.keyCode)) event.preventDefault();
            // });
            $('#pr-item input[name="qty"]').change(function(e){
                SELF.onChangeSelectedQty(ItemsPage.selectedRow);
            });
            initInputQty();
            initInputDecimal(getCurrencyCode());
            //initInputPercentage();
            $('#date1').datetimepicker({
                format: uiDateFormat,
            });
            var val = moment(ItemsPage.selectedRow.expected_delivery_date).format(uiDateFormat);
            $("#date1").val(val);
        },
        resetForm : function(){
            this.FormTaxCode.resetForm();
            this.FormCost.resetForm();
        },
        reloadTable : function(){
            this.reloadItemText();
            if(ItemsPage.selectedRow && m_item_category == 0){
                $('#service-item').hide();
            }else{
                $('#service-item').show();
                if(this.ServiceTable){
                    this.ServiceTable.table.clear().draw();
                    let _Url = _baseurl + "?data_type=1&number="+ItemsPage.selectedRow.number+"&line_number="+ItemsPage.selectedRow.line_number + "&eproc_po_number={{$eproc_po_number}}";
                    this.ServiceTable.reload(_Url);
                }
            }
            if(this.TaxTable){
                this.TaxTable.table.clear().draw();
                let _taxUrl = _baseurl + "?data_type=3&pr_id="+m_item_id + "&eproc_po_number={{$eproc_po_number}}";
                this.TaxTable.reload(_taxUrl);
            }
            if(this.CostTable){
                this.CostTable.table.clear().draw();
                let _costUrl = _baseurl + "?data_type=4&pr_id="+m_item_id+"&cost_type="+$('#cost-item input[name="conditional_type"]').val() + "&eproc_po_number={{$eproc_po_number}}";
                this.CostTable.reload(_costUrl);
            }
        },
        reloadItemText : function(){
            // let _url = _baseurl + "?data_type=2&number="+ItemsPage.selectedRow.number+"&line_number="+ItemsPage.selectedRow.line_number;
            let _url = _baseurl + "?data_type=2&item_id="+m_item_id+ "&eproc_po_number={{$eproc_po_number}}";
            $.ajax({
                url : _url,
                contentType: "application/json; charset=utf-8",
                dataType: "json",
                beforeSend: function( xhr ) {
                    Loading.Show('#text-item textarea[name="item_text"]');
                }
            }).done(function(response, textStatus, jqXhr) {
                let itemText = '';
                var newline = String.fromCharCode(13, 10);
                if(response.data && response.data.length > 0){
                    for(let ix in response.data){
                        itemText += response.data[ix].TEXT_LINE + response.data[ix].TEXT_FORM.replace('*',newline);
                    }
                }
                $('#text-item textarea[name="item_text"]').val(itemText);
                $('#text-item textarea[name="item_text"]').attr('data-val', itemText);
            }).fail(defaultAjaxFail)
            .always(function(jqXHR, textStatus, errorThrown) {
                Loading.Hide('#text-item textarea[name="item_text"]');
            });
        },
        onChangeSelectedQty : function(dtrow){
            let qty = $('#pr-item input[name="qty"]').val();
            let maxQty = Number(dtrow.qty) + Number(dtrow.qty_available);
            if(Number(qty) > maxQty){
                showAlert("Max QTY is "+maxQty, "warning", 3000);
                $('#pr-item input[name="qty"]').val(dtrow.qty);
                return false;
            }
            if(Number(qty) <= 0){
                showAlert("Min QTY is 1", "warning", 3000);
                $('#pr-item input[name="qty"]').val(dtrow.qty);
                return false;
            }
            return true;
        },
        validateSubmit : function(){
            let SELF = this;
            let valid = true;
            let qty = $('#pr-item input[name="qty"]').val();
            let itemText = $('textarea[name="item_text"]').val();
            let itemTextOld = $('textarea[name="item_text"]').data('val');
            if(!SELF.TaxTable.isDataChanged()
                && !SELF.CostTable.isDataChanged()
                && ItemsPage.selectedRow.qty == qty
                && itemTextOld == itemText){
                    valid = false;
                showAlert("There are no data changes", "warning");
            }
            if( SELF.onChangeSelectedQty(ItemsPage.selectedRow) == false){
                valid = false;
            }
            return valid;
        },
        submit : function(callback)
        {
            let SELF = this;
            //SUBMIT
            let taxCodes = [];
            let countTax = SELF.TaxTable.table.rows().count();
            if(countTax > 0){
                let TaxTable = SELF.TaxTable.table.rows().data();
                for(let ix=0;ix<countTax;ix++){
                    taxCodes[ix] = TaxTable[ix];
                    delete taxCodes[ix]['id'];
                }
            }

            let additonalCost = [];
            let countCost = SELF.CostTable.table.rows().count();
            if(countCost > 0){
                let CostTable = SELF.CostTable.table.rows().data();
                for(let ix=0;ix<countCost;ix++){
                    additonalCost[ix] = CostTable[ix];
                    delete additonalCost[ix]['id'];
                }
            }
            var val = $('#pr-item input[name="expected_delivery_date"]').val();
            let params = {
                item : {
                    id : $('#pr-item input[name="id"]').val(),
                    qty : $('#pr-item input[name="qty"]').val(),
                    description : $('#pr-item input[name="description"]').val(),
                    est_unit_price : $('#pr-item input[name="est_unit_price"]').val(),
                    overall_limit : $('#pr-item input[name="overall_limit"]').val(),
                    expected_delivery_date: moment(val, uiDateFormat).format(dbDateFormat),
                },
                cost : additonalCost,
                tax : taxCodes,
                item_text : $('textarea[name="item_text"]').val(),
                cost_type: $('#cost-item input[name="conditional_type"]').val(),
            };
            // console.log(params);
            // return;
            $.ajax({
                url : "{{ route('po.save', ['id'=>$id, 'vcode'=> $vendor->vendor_code,'type'=>'items']) }}?eproc_po_number={{$eproc_po_number}}",
                type : 'POST',
                data : JSON.stringify(params),
                contentType: "application/json; charset=utf-8",
                dataType: "json",
                beforeSend: function( xhr ) {
                    Loading.Show();
                }
            }).done(function(response, textStatus, jqXhr) {
                if(response.success){
                    ItemDetailPage.TaxTable.IsChanged = false;
                    ItemDetailPage.CostTable.IsChanged = false;
                    if(typeof callback == 'function'){
                        callback();
                    }
                    showAlert("Data saved.", "success", 3000);
                }else{
                    showAlert("Data not saved.", "danger", 3000);
                }
            }).fail(defaultAjaxFail)
            .always(function(jqXHR, textStatus, errorThrown) {
                Loading.Hide();
            });
        },
    }

    $(document).ready(function(){
        ItemsPage.init();
        FormCostPage.init();
        ItemDetailPage.init();
        @if (!$isEditable)
            $("#frmgeneral-save").remove();
            $("#formItemDetail-save").remove();
        @endif
        @if (!$is_show)
            $(".btn-success").remove();
            $("#formAddcost-save").remove();
        @endif
        $(".sel_compay").change(function(){
            $("select[name='vendor_profile_id']").hide();
            $(".btn-expand").hide();
            var loc_category = $(this).val();
            console.log($(this).val());
            if (loc_category !=""){
                $("select[name='vendor_profile_id']").val("");
                $.get("{{route('po.find-data','address')}}?id={{$id}}&tender_number={{$tender->tender_number}}&vprof_id={{$vendor->vendor_profiles_id}}&cat=" + $(this).val(),function(data){
                    if (data.total > 0){
                        $("select[name='vendor_profile_id']").html("<option value=\"\"> -- Select -- </option>");
                        if (loc_category == "Head Office"){
                            $("select[name='vendor_profile_id']").html("");
                        }
                        $.each(data.data, function(i, item){
                            $("select[name='vendor_profile_id']").append(new Option(item.address_1 +", " + item.address_2, item.id));
                        })
                        $("select[name='vendor_profile_id']").show();
                    }
                    $("select[name='vendor_profile_id']").change(function(){
                        $(this).attr("data-id", $(this).val());
                    })
                    $(".btn-expand").show();
                    $('.btn-expand').unbind('click').bind('click', function (e) {
                        var el = $("select[name='vendor_profile_id']");
                        // if ($(el).val()==""){
                        //     showAlert("can't be blank.", "danger", 3000);
                        //     return false;
                        // }
                        editCurrentData(el);
                    });

                });
            }
        })
        $(".btn-expand").click(function(e){
            var el = $("select[name='vendor_profile_id']");
            editCurrentData(el);
        });
        $("#btn_save_header").click(function(e){
            e.preventDefault();
            $('#delete_modal .modal-title').text("Save ");
            $('#delete_modal .modal-body').text("Are you sure to save");
            $("#btn_confirm").text("OK");
            $('#btn_delete_modal').click();
            $('#delete_modal #btn_confirm').off('click').on('click', function () {
                $("#delete_modal").modal("toggle");
                var valStart = $("input[name='document_date']").val();
                var val = moment(valStart, uiDateFormat).format(dbDateFormat);
                $("input[name='document_date']").val(val);
                var dt = $("#frm-po-header").serialize();
                $("input[name='document_date']").val(valStart);
                var detail = [];
                $('#datatable_serverside tbody tr').each(function() {
                    var inputEl = $(this).find("td .col-sm-12 input");
                    var val =moment($(inputEl).val(), uiDateFormat).format(dbDateFormat);
                    var id = $(this).attr("id");
                    var item = {id: id, val: val};
                    detail.push(item);
                });
                var params ={item: dt, detail: detail};
                console.log(params);
                $.post("{{ route('po.save', ['id'=>$id, 'vcode'=> $vendor->vendor_code, 'type'=>'po_header']) }}",params, function( data ) {
                    if (data.success){
                        showAlert("Data saved.", "success", 3000);
                        location.href = _baseurl;
                        location.reload(true);
                    }else{
                        showAlert("Data not saved.", "danger", 3000);
                    }

                });
            });
        })

        $("#btn-submit").click(function(e){
            $('#delete_modal .modal-title').text("Submit to SAP");
            $('#delete_modal .modal-body').text("Are you sure to submit");
            $("#btn_confirm").text("OK");
            $('#btn_delete_modal').click();
            $('#delete_modal #btn_confirm').off('click').on('click', function () {
                $.ajax({
                    url : "{{ route('po.save', ['id'=>$id, 'vcode'=> $vendor->vendor_code, 'type'=>'submit_sap']) }}",
                    type : 'POST',
                    contentType: "application/json; charset=utf-8",
                    dataType: "json",
                    beforeSend: function( xhr ) {
                        $("#delete_modal").modal("toggle");
                        Loading.Show();
                    }
                }).done(function(response, textStatus, jqXhr) {
                    Loading.Hide();
                    location.href = _baseurl;
                    location.reload(true);
                    console.log("done");
                }).fail(defaultAjaxFail)
                .always(function(jqXHR, textStatus, errorThrown) {
                    $(frmId+'_fieldset').attr("disabled",false);
                    Loading.Hide();
                });
            });
        });

        // $(".btn-cancel").click(function(e){
        $("#btnBack").click(function(e){
            e.preventDefault();
            // $('#delete_modal .modal-title').text("Cancel ");
            // $('#delete_modal .modal-body').text("Are you sure to cancel");
            // $("#btn_confirm").text("OK");
            // $('#btn_delete_modal').click();
            // $('#delete_modal #btn_confirm').off('click').on('click', function () {
                window.location.href='/po/{{$tenderData["tender_id"]}}/po_creation';
            //});
        });

        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            console.log(e.target.id);
            if(e.target.id == 'text-tab'){
                $('textarea[name="header_text"]').on('input keydown keyup focus',function() {
                    let lines = inputItemTextLength($(this).val(), 132);
                    $(this).val(lines.join(''));
                });
                $('textarea[name="term_of_payment"]').on('input keydown keyup focus',function() {
                    let lines = inputItemTextLength($(this).val(), 132);
                    $(this).val(lines.join(''));
                });
            }
        });

    }); //document.ready
});

</script>
@endsection
