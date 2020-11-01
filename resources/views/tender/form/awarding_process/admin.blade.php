@section('contentbody')
<style>
    .frmTenderHeader, .div-date {
        height: 220px;
    }
</style>
@if($tender->bid_bond == 1)
    <style>
        .picker5 .bootstrap-datetimepicker-widget{
            top: -120px !important;
        }

        .picker6 .bootstrap-datetimepicker-widget{
            top: -155px !important;
            z-index: 9999;
        }
    </style>
@else
    <style>
        .picker5 .bootstrap-datetimepicker-widget{
            top: -35px !important;
        }
        .picker6 .bootstrap-datetimepicker-widget{
            top: -85px !important;
            z-index: 9999;
        }
    </style>
@endif
<div class="tab" style="width:100%;">
    <ul class="nav nav-tabs" id="awarding-process-tab" role="tablist">
        <li class="nav-item" id="awarding-li">
            <a class="nav-link" id="awarding-tab" data-toggle="tab" href="#awarding-content" role="tab"
                aria-controls="awarding" aria-selected="true">{{__('tender.process.tab_title_awarding')}}</a>
        </li>
        <li class="nav-item" id="result-li">
            <a class="nav-link" id="result-tab" data-toggle="tab" href="#result-content" role="tab"
                aria-controls="result" aria-selected="false">{{__('tender.process.tab_title_awarding_result')}}</a>
        </li>
    </ul>

    <div class="tab-content" id="tab-awarding-process">
        <div class="tab-pane fade" id="awarding-content" role="tabpanel" aria-labelledby="awarding-tab">
            @include('tender.form.awarding_process.admin_tab_awarding')
        </div>
        <div class="tab-pane fade" id="result-content" role="tabpanel" aria-labelledby="result-tab">
            @include('tender.form.awarding_process.admin_tab_result')
        </div>
    </div>
</div>
@endsection
@section('styles')
<style>
    .dt-center {
        text-align: center;
    }

    table.dataTable th.select-checkbox {
        position: relative
    }

    table.dataTable th.select-checkbox:before,
    table.dataTable th.select-checkbox:after {
        display: block;
        position: absolute;
        top: 1.2em;
        left: 50%;
        width: 12px;
        height: 12px;
        box-sizing: border-box,
    }

    table.dataTable th.select-checkbox:before {
        content: ' ';
        margin-top: -6px;
        margin-left: -6px;
        border: 1px solid black;
        border-radius: 3px;
        background-color: #ffffff;
    }

    table.dataTable tr.selected th.select-checkbox:after {
        content: '\2714';
        margin-top: -11px;
        margin-left: -4px;
        text-align: center;
        text-shadow: 1px 1px #B0BED9, -1px -1px #B0BED9, 1px -1px #B0BED9, -1px 1px #B0BED9
    }

    table.dataTable tr th.select-checkbox.selected::after {
        content: "✔";
        margin-top: -11px;
        margin-left: -4px;
        text-align: center;
        text-shadow: rgb(176, 190, 217) 1px 1px, rgb(176, 190, 217) -1px -1px, rgb(176, 190, 217) 1px -1px, rgb(176, 190, 217) -1px 1px;
    }
</style>
@endsection
@section('modules-scripts')
@parent
@include('tender.form.tender_process_admin')
<script type="text/javascript">
    var awardingProcessType = 7;
    var TabAwarding;
    var TabResult;
    var canSubmit = false;
    var enableDoc = false;
    var currentDocumentDate, currentDeliveryDate;
    let maxUploadSize = parseInt(`{{ config('eproc.upload_file_size') }}`);
    var fileinputOptions = {'theme': 'fas', 'showUpload':false, 'showPreview':false,'previewFileType':'any', initialPreview : [],initialPreviewConfig: [], maxFileSize : maxUploadSize};

    @if ($canSubmit)
        enableDoc= true;
        canSubmit = true;
    @endif
require(["datatablesb4","bootstrap-fileinput-fas","dt.plugin.select",'datatables.fixed-column','datatables.rows-group','datetimepicker'], function(datetimepicker){
    Tabs = $('#tender_evaluation-tab li > a.nav-link');

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
            format : uiDateFormat,
        });

        $('#datetimepicker6').datetimepicker({
            format : uiDateFormat,
        });

        $("#datetimepicker5").off("hide.datetimepicker").on("hide.datetimepicker", function (e) {
            var dataSelected = TabResult.initVendorHeader.data;
            var val = $("#datetimepicker5").val();
            val = moment(val, uiDateFormat).format(dbDateFormat);
            if(val !== currentDocumentDate){
                var _url = '{{url("")}}/po/' + dataSelected.tender_number + '/' + dataSelected.vendor_code + '/document_date?document_date=' + val;
                $.post(_url, function(data){
                    if (data.success){
                        currentDocumentDate = val;
                        showAlert("Data saved.", "success", 3000);
                    }else{
                        showAlert("Data not saved.", "danger", 3000);
                    }
                });
            }
        });

        $("#datetimepicker6").off("hide.datetimepicker").on("hide.datetimepicker", function (e) {
            var dataSelected = TabResult.initVendorHeader.data;
            var val = $("#datetimepicker6").val();
            val = moment(val, uiDateFormat).format(dbDateFormat);

            if(val !== currentDeliveryDate){
                var _url = '{{url("")}}/po/' + dataSelected.tender_number + '/' + dataSelected.vendor_code + '/delivery_date?delivery_date=' + val;
                $.post(_url, function(data){
                    if (data.success){
                        currentDeliveryDate = val;
                        showAlert("Data saved.", "success", 3000);
                    }else{
                        showAlert("Data not saved.", "danger", 3000);
                    }
                });
            }
        });

        $("#document_type").change(function(){
            var dataSelected = TabResult.initVendorHeader.data;
            var val = $(this).val();
            var _url = '{{url("")}}/po/' + dataSelected.tender_number + '/' + dataSelected.vendor_code + '/document_type?document_type=' + val;
            $.post(_url, function(data){
                if (data.success){
                    showAlert("Data saved.", "success", 3000);
                }else{
                    showAlert("Data not saved.", "danger", 3000);
                }
            });
        });

        $("#tkdn_percentage").change(function(){
            var dataSelected = TabResult.initVendorHeader.data;
            let data = {
                action_type : 'save-tdkn-percentage',
                stage_type : awardingProcessType,
                vendor_id : dataSelected.vendor_id,
                vendor_code : dataSelected.vendor_code,
                tkdn_percentage: getAutonumricValue($(this))
            };

            submit(data, function(){
                // window.location.reload();
            });
        });

        $("#tkdn_file").change(function(){
            let file = $(this).parents('div.input-group').find('input[type="file"]')[0].files[0];
            var dataSelected = TabResult.initVendorHeader.data;
            if(file){
                let formData = new FormData();
                formData.append('action_type', 'upload-tkdn-file');
                formData.append('stage_type', awardingProcessType);
                formData.append('vendor_id', dataSelected.vendor_id);
                formData.append('vendor_code', dataSelected.vendor_code);

                formData.append('attachment', file, file.name);

                submitUpload(formData, function(response){
                    if(response.success && response.data){
                        initInputFileTKDN(response.data.tkdn_file);
                    }
                }, TabResult.tabSelector);
            }
        });

        $('#btn-file-edit').on('click', function(e){
            e.preventDefault();
            $("#container-file-upload").find('input[type=file]').trigger('click');
        });

        $(".attachment").fileinput(fileinputOptions);
    });

    var _columnsAwarding = [
                @foreach ($tenderData[$type]['fields'] as $field)
                {data: '{{$field}}', name: '{{$field}}', "width": {{$field == 'vendor_name' ? 0 : ($field == 'action_details_awarding' ? 20 : 110)}}},
                @endforeach
            ];
    var _columnsResult = [
                @foreach ($tenderData[$type]['fields2'] as $field)
                {data: '{{$field}}', name: '{{$field}}', "width": {{$field == 'vendor_name' ? 0 : ($field == 'action_details_awarding' ? 50 : 80)}}},
                @endforeach
            ];

    customInitVendorDocument = function(tabSelected, data){
        let SELF = tabSelected;
        let dtOptions = getDTOptions();
        let _url = "{{ route('tender.dataItem', ['id' => $id, 'type' => $type]) }}?action_type=submission-detail-admin&stage_type="+SELF.stageType;
        let options = {
            deferRender: dtOptions.deferRender,
            rowId: dtOptions.rowId,
            lengthChange: false,
            searching: false,
            "paging":   false,
            "ordering": false,
            "info":     false,
            language: dtOptions.language,
            ajax : _url + '&vendor_id='+data.vendor_id,
            columns: [
                {
                    data: 'id', name: 'id',"width": 50,"className": 'text-center',
                    render: function (data, type, row, meta) {
                        return parseInt(meta.row) + parseInt(meta.settings._iDisplayStart) + 1;
                    }
                },
                {data: 'description', name: 'description',"width": 300},
                {data: 'is_required_text', name: 'is_required_text', "width": 100},
                {
                    data: 'attachment', name: 'attachment',
                    render: function (data, type, row, meta) {
                        if(data && data!=''){
                            let _tpl = '<a target="_blank" class="btn btn-link float-left" href="{{$storage}}/'+data+'">'+data.fileName()+'</a>';
                            return '<div>' + _tpl + '</div>';
                        }
                        return '';
                    }
                }
            ],
        };

        if(SELF.tableDetailDoc != null){
            SELF.tableDetailDoc.destroy();
        }

        SELF.tableDetailDoc = $(SELF.dtDetailDocSelector).DataTable(options);
    }

    TabAwarding = new TabDocument({
        tabSelector : '#awarding-content',
        stageType : awardingProcessType,
        columns: _columnsAwarding,
        actionView: "awarding",
        dtSelector : '#dt-vendor-awarding',
        dtDetailDocSelector : '#dt-commercial-document',
        dtDetailItemSelector : '#dt-commercial-items',
        editable : {{$statusProcess == 'opened-4' ?  'true' : 'false'}},
        currentDataHeader: null,
        processing: false,
        rowCallback: function(row, data){
            if(parseFloat(data.score_tc) > 0){
                $('td:eq(4)', row).html(parseFloat(data.score_tc).formatMoney(2, ".", ","));
            }else{
                $('td:eq(4)', row).html("");
            }

            if(parseFloat(data.score_com) > 0){
                $('td:eq(5)', row).html(parseFloat(data.score_com).formatMoney(2, ".", ","));
            }else{
                $('td:eq(5)', row).html("");
            }
        },
        initVendorHeader : {
            data : null,
            data_document_type : null,
            loadData : function(data){
                let _CSELF = this;
                let selector = TabAwarding.tabSelector + ' .card-tender-header';
                let _url = "{{ route('tender.dataItem', ['id' => $id, 'type' => $type]) }}?action_type=submission-header"
                $.ajax({
                    url : _url + '&vendor_id='+data.vendor_id+'&stage_type='+TabAwarding.stageType+'&actionView=' + TabAwarding.actionView,
                    type : 'GET',
                    contentType: "application/json; charset=utf-8",
                    dataType: "json",
                    beforeSend: function( xhr ) {
                        Loading.Show(selector);
                    }
                }).done(function(response, textStatus, jqXhr) {
                    if(response.success){
                        _CSELF.data = response.data;
                        _CSELF.renderData(_CSELF.data);
                    }
                }).fail(defaultAjaxFail)
                .always(function(jqXHR, textStatus, errorThrown) {
                    Loading.Hide(selector);
                });


            },
            renderData : function(data){
                TabAwarding.currentDataHeader = data;
                if(data){
                    $(TabAwarding.tabSelector + ' #currency_code_header').val(data.currency_code);
                    $(TabAwarding.tabSelector + ' input[name="vendor_id"]').val(data.vendor_id);
                    $(TabAwarding.tabSelector + ' input[name="vendor_code"]').val(data.vendor_code);
                    $(TabAwarding.tabSelector + ' .quotation_number').text(data.quotation_number);
                    $(TabAwarding.tabSelector + ' .quotation_date').text(data.quotation_date);
                    $(TabAwarding.tabSelector + ' .quotation_note').text(data.quotation_note);
                    $(TabAwarding.tabSelector + ' .incoterm').text(data.incoterm);
                    $(TabAwarding.tabSelector + ' .incoterm_location').text(data.incoterm_location);
                    $(TabAwarding.tabSelector + ' .bid_bond_value').text(data.bid_bond_value);
                    $(TabAwarding.tabSelector + ' .bid_bond_end_date').text(data.bid_bond_end_date);

                    $(TabAwarding.tabSelector + ' .currency_code').val(data.currency_code);

                    if(data.quotation_file && data.quotation_file!= '' && data.quotation_file!= 'undefined'){
                        $(TabAwarding.tabSelector + ' .quotation_file').show();
                        $(TabAwarding.tabSelector + ' .quotation_file').html('<i class="fa fa-paperclip"></i> '+data.quotation_file.fileName());
                        $(TabAwarding.tabSelector + ' .quotation_file').prop('href', '{{$storage}}/'+data.quotation_file);
                    }else{
                        $(TabAwarding.tabSelector + ' .quotation_file').hide();
                    }
                    if(data.bid_bond_file && data.bid_bond_file!= '' && data.bid_bond_file!= 'undefined'){
                        $(TabAwarding.tabSelector + ' .bid_bond_file').show();
                        $(TabAwarding.tabSelector + ' .bid_bond_file').html('<i class="fa fa-paperclip"></i> '+data.bid_bond_file.fileName());
                        $(TabAwarding.tabSelector + ' .bid_bond_file').prop('href', '{{$storage}}/'+data.bid_bond_file);
                    }else{
                        $(TabAwarding.tabSelector + ' .bid_bond_file').hide();
                    }
                }
            }
        },
        initVendorItem : function(SELF, data){
            let dtOptions = getDTOptions();
            let _url = "{{ route('tender.dataItem', ['id' => $id, 'type' => $type]) }}?stage_type="+SELF.stageType;
            let options = {
                deferRender: dtOptions.deferRender,
                rowId: dtOptions.rowId,
                lengthChange: false,
                searching: false,
                paging: false,
                language: dtOptions.language,
                select: {style: "multi", info: false},
                ajax : _url + '&action_type=submission-items&vendor_id='+data.vendor_id+'&actionView=' + TabAwarding.actionView,
                initComplete: function(settings, json){
                    $("th.select-checkbox").removeClass("sorting_asc");

                    // @if(isset($isSubmit) && $isSubmit)
                    //     TabAwarding.tableDetailItem.on('user-select', function (e, dt, type, cell, originalEvent) {
                    //         e.preventDefault();
                    //     });
                    // @else
                    //     TabAwarding.tableDetailItem.on('user-select', function (e, dt, type, cell, originalEvent) {
                    //         var data = dt.data().toArray()[cell.index().row];
                    //         if(data.disabled){
                    //             e.preventDefault();
                    //         }
                    //     });
                    //     initSelectBoxAll();
                    // @endif

                    $(".btn-save-items-awarding").prop("disabled", false);
                    if((TabAwarding.selectedRow.po_number || "") !== "" || !canSubmit){
                        TabAwarding.tableDetailItem.off("user-select").on('user-select', function (e, dt, type, cell, originalEvent) {
                            e.preventDefault();
                        });
                        $(".btn-save-items-awarding").prop("disabled", true);
                    }else{
                        TabAwarding.tableDetailItem.off("user-select").on('user-select', function (e, dt, type, cell, originalEvent) {
                            var data = dt.data().toArray()[cell.index().row];
                            if(data.disabled || data.compliance === "no_quote"){
                                e.preventDefault();
                            }
                        });

                        initSelectBoxAll();
                    }
                },
                rowCallback: function(row, data, dataIndex){
                    if(data.selected){
                        TabAwarding.tableDetailItem.row(':eq('+dataIndex+')', { page: 'current' }).select();
                    }
                    if(data.disabled || data.compliance === "no_quote"){
                        $('td:eq(0)', row).removeClass("select-checkbox");
                        $(row).css("background-color", "#c0c0c0a3");
                    }
                    if(data.deleteflg === "x"){
                        $('td:eq(0)', row).removeClass("select-checkbox");
                        $(row).addClass("bg-warning");
                    }
                },
                columns: [
                    {
                        "data": null,
                        render: function (data, type, row, meta) {
                            return '';
                        },
                        orderable: false,
                        className: 'select-checkbox text-center',
                    },
                    {
                        data: 'id', name: 'id',"width": 50,"className": 'text-center',
                        render: function (data, type, row, meta) {
                            return parseInt(meta.row) + parseInt(meta.settings._iDisplayStart) + 1;
                        }
                    },
                    {data: 'number', name: 'number',"width": 50},
                    {
                        data: 'line_number', name: 'line_number', "width": 100, className : 'td-value',
                        render : function ( data, type, row, dt ) {
                            return '<a href="" class="open-detail" >'+data+'</a>';
                        },
                    },
                    {data: 'description_vendor', name: 'description_vendor', "width": 200},
                    {
                        data: 'qty_vendor', name: 'qty_vendor', "width": 100,
                        render: function (data, type, row, meta) {
                            return formatQty(data);
                        }
                    },
                    {data: 'uom', name: 'uom', "width": 100},
                    {
                        data: 'est_unit_price_vendor', name: 'est_unit_price_vendor', "width": 100,
                        render: function (data, type, row, meta) {
                            let total = data || 0;
                            return formatCurrency(total, row.currency_code_vendor);
                        }
                    },
                    {
                        data: 'overall_limit_vendor', name: 'overall_limit_vendor', "width": 100,
                        render: function (data, type, row, meta) {
                            let total = data || 0;
                            return formatCurrency(total, row.currency_code_vendor);
                        }
                    },
                    {
                        data: 'price_unit_vendor', name: 'price_unit_vendor', "width": 100,
                        render: function (data, type, row, meta) {
                            let total = data || 0;
                            return formatDecimal(total);
                        }
                    },
                    {
                        data: 'subtotal_vendor', name: 'subtotal_vendor', "width": 100,
                        render: function (data, type, row, meta) {
                            let total = data || 0;
                            return formatCurrency(total, row.currency_code_vendor);
                        }
                    },
                    {data: 'currency_code_vendor', name: 'currency_code_vendor', "width": 100},
                    {
                        data: 'compliance',name: 'compliance',"width": 150,
                        render : function(data, type, row){
                            return row.compliance_text;
                        }
                    },
                    // {data: 'deleteflg', name: 'deleteflg', "width": 100, className: 'text-center',render: renderDeleteFlg}
                ]
            };

            if(SELF.tableDetailItem != null){
                SELF.tableDetailItem.destroy();
            }
            SELF.tableDetailItem = $(SELF.dtDetailItemSelector).DataTable(options);
            TabAwarding.tableDetailItem = SELF.tableDetailItem;
        },
        initVendorDocument: function(data){
            customInitVendorDocument(TabAwarding, data);
        }
    });

    TabResult = new TabDocument({
        tabSelector : '#result-content',
        stageType : awardingProcessType,
        columns: _columnsResult,
        autoWidth: false,
        po_number: null,
        actionView: "awarding_result",
        dtSelector : '#dt-vendor-awarding-result',
        dtDetailDocSelector : '#dt-commercial-document-result',
        dtDetailItemSelector : '#dt-commercial-items-result',
        editable : {{$statusProcess == 'opened-4' ?  'true' : 'false'}},
        rowCallback: function(row, data){
            if(parseFloat(data.score_tc) > 0){
                $('td:eq(6)', row).html(parseFloat(data.score_tc).formatMoney(2, ".", ","));
            }else{
                $('td:eq(6)', row).html("");
            }

            if(parseFloat(data.score_com) > 0){
                $('td:eq(7)', row).html(parseFloat(data.score_com).formatMoney(2, ".", ","));
            }else{
                $('td:eq(7)', row).html("");
            }
        },
        initVendorHeader : {
            data : null,
            loadData : function(data){
                let _CSELF = this;
                TabResult.po_number = data.po_number;

                $("#document_type").prop("disabled", false);
                $("#datetimepicker5").prop("disabled", false);
                $("#datetimepicker6").prop("disabled", false);
                $("#tkdn_percentage").prop("disabled", false);
                $("#btn-file-edit").show();

                if((data.po_number || "") !== "" || !canSubmit){
                    $("#document_type").prop("disabled", true);
                    $("#datetimepicker5").prop("disabled", true);
                    $("#datetimepicker6").prop("disabled", true);
                    $("#tkdn_percentage").prop("disabled", true);
                    $("#btn-file-edit").hide();
                }
                let selector = TabResult.tabSelector + ' .card-tender-header';
                let _url = "{{ route('tender.dataItem', ['id' => $id, 'type' => $type]) }}?action_type=submission-header"
                $.ajax({
                    url : _url + '&vendor_id='+data.vendor_id+'&stage_type='+TabResult.stageType+'&actionView=' + TabResult.actionView,
                    type : 'GET',
                    contentType: "application/json; charset=utf-8",
                    dataType: "json",
                    beforeSend: function( xhr ) {
                        Loading.Show(selector);
                    }
                }).done(function(response, textStatus, jqXhr) {
                    if(response.success){
                        _CSELF.data = response.data;
                        _CSELF.renderData(_CSELF.data);
                    }
                }).fail(defaultAjaxFail)
                .always(function(jqXHR, textStatus, errorThrown) {
                    // Loading.Hide(selector);
                    _CSELF.renderDocumentType();
                });
            },
            renderDocumentType: function(){
                let _CSELF = this;
                let selector = TabResult.tabSelector + ' .card-tender-header';
                try{
                    var _docurl = "{{url('/')}}/po/" + _CSELF.data.tender_number + "/" + _CSELF.data.vendor_code + "/ItemList?data_type=document_type";
                    $.ajax({
                        url : _docurl,
                        type : 'GET',
                        contentType: "application/json; charset=utf-8",
                        dataType: "json",
                        beforeSend: function( xhr ) {
                            Loading.Show(selector);
                        }
                    }).done(function(response, textStatus, jqXhr) {
                        _CSELF.data_document_type = response;
                        var data = _CSELF.data_document_type;

                        if(data.document_date){
                            currentDocumentDate = moment(data.document_date).format(uiDateFormat);
                            $("#datetimepicker5").val(currentDocumentDate);
                        }
                        else
                            $("#datetimepicker5").val(null);

                        if(data.delivery_date){
                            currentDeliveryDate = moment(data.delivery_date).format(uiDateFormat);
                            $("#datetimepicker6").val(currentDeliveryDate);
                        }
                        else
                            $("#datetimepicker6").val(null);

                        $("#document_type").val(data.document_type);
                        initInputPercentage();
                        Loading.Hide(selector);
                    }).fail(defaultAjaxFail)
                    .always(function(jqXHR, textStatus, errorThrown) {
                        Loading.Hide(selector);
                    });
                }catch(ex){
                    Loading.Hide(selector);
                    console.log(ex);
                }
            },
            renderData : function(data){
                $(TabResult.tabSelector + ' #tkdn_percentage').val('');
                if(data){
                    $(TabResult.tabSelector + ' #currency_code_header').val(data.currency_code);
                    $(TabResult.tabSelector + ' .quotation_number').text(data.quotation_number);
                    $(TabResult.tabSelector + ' .quotation_date').text(data.quotation_date);
                    $(TabResult.tabSelector + ' .quotation_note').text(data.quotation_note);
                    $(TabResult.tabSelector + ' .incoterm').text(data.incoterm);
                    $(TabResult.tabSelector + ' .incoterm_location').text(data.incoterm_location);
                    $(TabResult.tabSelector + ' .bid_bond_value').text(data.bid_bond_value);
                    $(TabResult.tabSelector + ' .bid_bond_end_date').text(data.bid_bond_end_date);

                    $(TabResult.tabSelector + ' .currency_code').val(data.currency_code);
                    if(data.tkdn_percentage){
                        $(TabResult.tabSelector + ' #tkdn_percentage').val(formatPercentage(data.tkdn_percentage));
                    }else{
                        $(TabResult.tabSelector + ' #tkdn_percentage').val(null);
                    }
                    initInputFileTKDN(data.tkdn_file);

                    if(data.quotation_file && data.quotation_file!= '' && data.quotation_file!= 'undefined'){
                        $(TabResult.tabSelector + ' .quotation_file').show();
                        $(TabResult.tabSelector + ' .quotation_file').html('<i class="fa fa-paperclip"></i> '+data.quotation_file.fileName());
                        $(TabResult.tabSelector + ' .quotation_file').prop('href', '{{$storage}}/'+data.quotation_file);
                    }else{
                        $(TabResult.tabSelector + ' .quotation_file').hide();
                    }
                    if(data.bid_bond_file && data.bid_bond_file!= '' && data.bid_bond_file!= 'undefined'){
                        $(TabResult.tabSelector + ' .bid_bond_file').show();
                        $(TabResult.tabSelector + ' .bid_bond_file').html('<i class="fa fa-paperclip"></i> '+data.bid_bond_file.fileName());
                        $(TabResult.tabSelector + ' .bid_bond_file').prop('href', '{{$storage}}/'+data.bid_bond_file);
                    }else{
                        $(TabResult.tabSelector + ' .bid_bond_file').hide();
                    }
                }
            }
        },
        initVendorItem : function(SELF, data){
            let dtOptions = getDTOptions();
            let _url = "{{ route('tender.dataItem', ['id' => $id, 'type' => $type]) }}?stage_type="+SELF.stageType;
            let options = {
                deferRender: dtOptions.deferRender,
                rowId: dtOptions.rowId,
                lengthChange: false,
                searching: false,
                paging: false,
                language: dtOptions.language,
                ajax : _url + '&action_type=submission-items&vendor_id='+data.vendor_id+'&actionView=' + TabResult.actionView,
                initComplete: function(){
                    var api = this.api();
                    api.on('select', function (e, dt, type, indexes) {
                        var a = indexes;
                    });
                    api.on('deselect', function (e, dt, type, indexes) {

                    });
                },
                columns: [
                    {
                        data: 'id', name: 'id',"width": 50,"className": 'text-center',
                        render: function (data, type, row, meta) {
                            return parseInt(meta.row) + parseInt(meta.settings._iDisplayStart) + 1;
                        }
                    },
                    {data: 'number', name: 'number',"width": 50},
                    {
                        data: 'line_number', name: 'line_number', "width": 100, className : 'td-value',
                        render : function ( data, type, row, dt ) {
                            return '<a href="" class="open-detail" >'+data+'</a>';
                        },
                    },
                    {data: 'description_vendor', name: 'description_vendor', "width": 200},
                    {
                        data: 'qty_vendor', name: 'qty_vendor', "width": 100,
                        render: function (data, type, row, meta) {
                            return formatQty(data);
                        }
                    },
                    {data: 'uom', name: 'uom', "width": 100},
                    {
                        data: 'est_unit_price_vendor', name: 'est_unit_price_vendor', "width": 100,
                        render: function (data, type, row, meta) {
                            let total = data || 0;
                            return formatCurrency(total, row.currency_code_vendor);
                        }
                    },
                    {
                        data: 'overall_limit_vendor', name: 'overall_limit_vendor', "width": 100,
                        render: function (data, type, row, meta) {
                            let total = data || 0;
                            return formatCurrency(total, row.currency_code_vendor);
                        }
                    },
                    {
                        data: 'price_unit_vendor', name: 'price_unit_vendor', "width": 100,
                        render: function (data, type, row, meta) {
                            let total = data || 0;
                            return formatDecimal(total);
                        }
                    },
                    {
                        data: 'subtotal_vendor', name: 'subtotal_vendor', "width": 100,
                        render: function (data, type, row, meta) {
                            let total = data || 0;
                            return formatCurrency(total, row.currency_code_vendor);
                        }
                    },
                    {data: 'currency_code_vendor', name: 'currency_code_vendor', "width": 100},
                    {
                        data: 'compliance',name: 'compliance',"width": 150,
                        render : function(data, type, row){
                            return row.compliance_text;
                        }
                    }
                ],
            };

            if(SELF.tableDetailItem != null){
                SELF.tableDetailItem.destroy();
            }
            SELF.tableDetailItem = $(SELF.dtDetailItemSelector).DataTable(options);
        },
        initVendorDocument: function(data){
            customInitVendorDocument(TabResult, data);
            TabResult.initVendorAttachment(data);
            TabResult.initActionAttachment();
        }
    });
    TabResult.tableAttachment = null;
    TabResult.dtAttachmentSelector = "#dt-awarding-attachment";

    TabResult.initVendorAttachment = function(data){
        let SELF = TabResult;
        let dtOptions = getDTOptions();
        let _url = "{{ route('tender.dataItem', ['id' => $id, 'type' => $type]) }}?stage_type="+awardingProcessType;
        let options = {
            deferRender: dtOptions.deferRender,
            rowId: dtOptions.rowId,
            lengthChange: false,
            searching: false,
            paging: false,
            info: false,
            language: dtOptions.language,
            ajax : _url + '&action_type=awarding-attacment&vendor_id='+data.vendor_id,
            fixedColumns: true,
            columns: [
                {
                    data: 'id', name: 'id',"width": 50,"className": 'text-center',
                    render: function (data, type, row, meta) {
                        return parseInt(meta.row) + parseInt(meta.settings._iDisplayStart) + 1;
                    }
                },
                {data: 'description', name: 'description',"width": 300},
                {data: 'is_required_text', name: 'is_required_text', "width": 100},
                {
                    data: 'attachment', name: 'attachment',
                    render: function (data, type, row, meta) {
                        if(data && data!= ''){
                            let _tpl = '<a target="_blank" class="btn btn-link float-left" href="{{$storage}}/'+data+'">'+data.fileName()+'</a>';
                            if(enableDoc)
                                _tpl += '<a href="" class="btn btn-link float-right delete-document"><i class="fa fa-trash"></i></a>';
                            return '<div>' + _tpl + '</div>';
                        }else{
                            let _tpl = '';
                            if(enableDoc){
                                _tpl = '<div class="input-group input-group-sm">' +
                                    '<div class="custom-file">' +
                                        '<input type="file" id="attachment-'+row.id +'" class="attachment" name="result_attachment" ' +
                                            'class="custom-file-input custom-file-input-sm" data-id="'+row.id +'">' +
                                        '<label id="attachment-'+row.id +'-label" class="custom-file-label" ' +
                                            'for="attachment-'+row.id +'"></label>' +
                                    '</div>' +
                                    '<div class="input-group-prepend"><button class="btn btn-sm btn-success upload" data-id="'+row.id +'">Upload</button></div>'+
                                '</div>';
                            }
                            return _tpl;
                        }
                    }
                },
            ],
        };

        //## Initilalize Datatables
        if(SELF.tableAttachment != null){
            SELF.tableAttachment.destroy();
        }
        SELF.tableAttachment = $(SELF.dtAttachmentSelector).DataTable(options);
    };

    TabResult.initActionAttachment = function(){
        let SELF = TabResult;

        $(SELF.dtAttachmentSelector + ' tbody').on('change','.attachment', function(e){
            let id = $(this).data('id');
            let files = $(this).prop('files');
            if(files && files.length > 0){
                $('#attachment-'+id+'-label').text(files[0].name);
            }
        });
        $(SELF.dtAttachmentSelector + ' tbody').on('click','.upload', function(e){
            let dtrow = SELF.tableAttachment.row($(this).parents('tr')).data();
            let file = $(this).parents('div.input-group').find('input[type="file"]')[0].files[0];
            if(file){
                let formData = new FormData();
                formData.append('action_type', 'upload-awarding-attachment');
                formData.append('stage_type', SELF.stageType);
                formData.append('vendor_id', dtrow.vendor_id);
                formData.append('vendor_code', dtrow.vendor_code);

                if(dtrow.id != null && dtrow.id != ''){
                    formData.append('id', dtrow.id);
                }
                formData.append('line_id', dtrow.line_id);
                if(dtrow.order != null && dtrow.order != ''){
                    formData.append('order', dtrow.order);
                }
                formData.append('attachment', file, file.name);

                submitUpload(formData, function(){
                    SELF.tableAttachment.ajax.reload(function(json){

                    });
                }, SELF.tableAttachment);
            }else{
                showAlert("Document is required.", "warning", 3000);
            }
        });
        $(SELF.dtAttachmentSelector + ' tbody').on('click','.delete-document', function(e){
            e.preventDefault();
            let dtrow = SELF.tableAttachment.row($(this).parents('tr')).data();
            let selectedRow = SELF.tableAttachment.row($(this).parents('tr'));
            dtrow.attachment = '';
            submit({
                action_type : 'delete-awarding-attachment',
                id : dtrow.id,
                vendor_id : dtrow.ivendor_id,
                vendor_code : dtrow.vendor_code,
                'stage_type' : SELF.stageType},
            function(response){
                dtrow.id = response.data.id;
                selectedRow.data( dtrow ).draw();

            }, SELF.dtAttachmentSelector);
        });
    };

    initLoad(); //init button actions

    if(typeof(ItemDetailPage) !== "undefined"){
        ItemDetailPage.init();
    }

    $(".btn-save-items-awarding").click(function(){
        var dataItemsSelected = TabAwarding.tableDetailItem.rows({ selected: true }).data().toArray();

        let data = {
            'action_type' : 'save-tender-items',
            'stage_type' : awardingProcessType,
            'vendor_id' : $(TabAwarding.tabSelector + ' input[name="vendor_id"]').val(),
            'vendor_code' : $(TabAwarding.tabSelector + ' input[name="vendor_code"]').val(),
            'items' : dataItemsSelected
        };

        submit(data,function(){
            // TabAwarding.tableDetailItem.ajax.reload();
            window.location.reload();
            // $('.card-tender-item .btn-save-items').prop('disabled', true);
            // SELF.setupButton(SELF, enableDoc);
        });
    });

    $('#formItemDetail_modal').on("shown.bs.modal", function () {
        var vendor_id;
        if(TabSelected.selectedRow){
            vendor_id = TabSelected.selectedRow.vendor_id;
        }else{
            vendor_id = TabSelected.tableItemSelectedRow.data().vendor_id;
        }

        try{
            ItemDetailPage.resetForm();
            ItemDetailPage.reloadTable(vendor_id, awardingProcessType, TabSelected.actionView);
            ItemDetailPage.ForceCloseModal = false;
        }catch(e){
            console.error(e);
        }
    });

    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        if(e.target.id == 'awarding-tab'){
            TabSelected = TabAwarding;
            $(TabAwarding.tabSelector + ' .btn_back_to').trigger('click');
            if(TabAwarding.table == null){
                TabAwarding.initTable();
            }else{
                TabAwarding.table.ajax.reload();
                TabAwarding.table.columns.adjust().draw();
            }
        }else
        if(e.target.id == 'result-tab'){
            TabSelected = TabResult;
            $(TabResult.tabSelector + ' .btn_back_to').trigger('click');
            if(TabResult.table == null){
                TabResult.initTable();
            }else{
                TabResult.table.ajax.reload();
                TabResult.table.columns.adjust().draw();
            }
        }
    });

    $(".btn_awarding_bottom").click(function(e){
        var actionType = $(this).attr("data-action-type");
        var modalTitle = $(this).attr("data-modal-title");
        var alertMsg = $(this).attr("data-alert-message");
        var list = TabAwarding.table.rows().data().toArray()
        var rowData = list[0];

        var items = list.filter(f => !f.HasAwarding);

        $('#delete_modal .modal-title').text(modalTitle);
        let _body = $('<div class="alert alert-warning" role="alert">' + alertMsg + '</div>')
        $('#delete_modal .modal-body').html(_body);
        $('#btn_delete_modal').click();
        $('#delete_modal #btn_confirm').off('click').on('click', function () {
            let data = {
                action_type : actionType,
                stage_type : awardingProcessType,
                tender_number : rowData.tender_number,
                items: items
            };
            $('#delete_modal .close').click();
            submit(data, function(response){
                if(response.sap_result && response.sap_result.details){
                    let msg = "";
                    $.each(response.sap_result.details, function(i, detail){
                        if(!detail.status){
                            msg += `<p style='margin-bottom: 0px;color:red;'>${detail.vendor_number} SAP PO Creation failed.</p>`;
                        }else{
                            msg += `<p style='margin-bottom: 0px;'><strong>${detail.vendor_number} SAP PO Creation success.</strong></p>`;
                        }

                        console.log(detail.messages);
                    });
                    if(msg !== ""){
                        showAlert(msg, "info", 10000);
                        setTimeout(function(){
                            window.location.reload();
                        }, 3000);
                    }else{
                        window.location.reload();
                    }
                }else{
                    window.location.reload();
                }
            });
            return false;
        });
    });

    @if($tender->conditional_type == 'CT1')
        FormCostPage.init();
        $('#formAddcost_modal').on("shown.bs.modal", function () {
            try{
                FormCostPage.resetForm();
                FormCostPage.reloadTable(TabSelected.selectedRow.vendor_id, awardingProcessType, TabSelected.actionView);
            }catch{}
        });
    @endif

    TabAwarding.init();
    TabResult.init();
    $('.btn_scoring').hide();
    $('#awarding-tab').click();
});

function setAwarding(el){
    var rowData = TabAwarding.table.row( $(el).parents('tr') ).data();
    var awarding_status = $(el).val();

    let data = {
        action_type : 'set-awarding',
        stage_type : awardingProcessType,
        vendor_id : rowData.vendor_id,
        vendor_code : rowData.vendor_code,
        awarding_status: awarding_status
    };

    submit(data, function(){
        TabAwarding.table.ajax.reload();
        canSubmit = true;
        $("#btn_submit_awarding").prop("disabled", false);
        window.location.reload();
    });
}

function setAwardingOld(el){
    var rowData = TabAwarding.table.row( $(el).parents('tr') ).data();
    var awarding_status = $(el).attr("data-awarding-status");

    var msg = String.Format("{{__('tender.process.message_set_awarding')}}", awarding_status.toUpperCase(), rowData.vendor_name);

    $('#delete_modal .modal-title').text("{{__('tender.process.title_set_awarding')}}",);
    let _body = $('<div class="alert alert-warning" role="alert">'+msg+'</div>');

    $('#delete_modal .modal-body').html(_body);
    $('#btn_delete_modal').click();
    $('#delete_modal #btn_confirm').off('click').on('click', function () {
        let data = {
            action_type : 'set-awarding',
            stage_type : awardingProcessType,
            vendor_id : rowData.vendor_id,
            vendor_code : rowData.vendor_code,
            awarding_status: awarding_status
        };
        submit(data, function(){
            $('#delete_modal .close').click();
            TabAwarding.table.ajax.reload();
        });
        return false;
    });
}

function initSelectBoxAll(){
    if(TabAwarding.tableDetailItem){
        TabAwarding.tableDetailItem.off("click", "th.select-checkbox").on("click", "th.select-checkbox", function() {
        var rowData = TabAwarding.tableDetailItem.rows().data().toArray();

        if ($(this).hasClass("selected")) {
            TabAwarding.tableDetailItem.rows().deselect();
            $(this).removeClass("selected");
        } else {
            $.each(rowData, function(i, data) {
                if(!data.disabled){
                    TabAwarding.tableDetailItem.rows(i).select();
                }
            });
            $(this).addClass("selected");
        }
    });
    }
}

function submitUpload(frmData, callback, selector){
        let _url = "{{ route('tender.save', ['id'=>$id, 'type'=>$type]) }}";
        $.ajax({
            url : _url,
            type : 'POST',
            data : frmData,
            cache : false,
            enctype: 'multipart/form-data',
            processData: false,
            contentType: false,
            beforeSend: function( xhr ) {
                Loading.Show(selector);
            }
        }).done(function(response, textStatus, jqXhr) {
            if(response.success){
                if(typeof callback == 'function') callback(response);
                showAlert("Document saved.", "success", 3000);
            }else{
                showAlert("Document not saved.", "danger", 3000);
            }
        }).fail(defaultAjaxFail)
        .always(function(jqXHR, textStatus, errorThrown) {
            Loading.Hide(selector);
        });
}

function initInputFileTKDN(tkdn_file){
    if(tkdn_file){
        var fileUrl = '{{$storage}}/' + tkdn_file;
        var fileName = tkdn_file.fileName();
        $("#container-file-upload").hide();
        $("#file-view").show();
        $("#file-info").attr("href", fileUrl);
        $("#file-info").html(fileName);
    }else{
        if((TabResult.po_number || "") !== "" || !canSubmit){
            $("#container-file-upload").hide();
        }else{
            $("#container-file-upload").show();
        }
        $("#file-view").hide();
        $("#file-info").attr("href", "");
        $("#file-info").html("");
    }
}
</script>
@endsection
