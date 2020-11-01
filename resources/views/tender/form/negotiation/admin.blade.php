@section('contentbody')
<div class="tab" style="width:100%;">
    <ul class="nav nav-tabs" id="negotiation-tab" role="tablist">
        <li class="nav-item" id="overview-li">
            <a class="nav-link" id="overview-tab" data-toggle="tab" href="#overview-content" role="tab"
                aria-controls="overview" aria-selected="true">{{__('tender.process.tab_title_overview')}}</a>
        </li>
        <li class="nav-item" id="negotiation-li">
            <a class="nav-link @if(!$negotiationTabEnable) disabled @endif" id="negotiation-tab" data-toggle="tab"
                href="#negotiation-content" role="tab" aria-controls="negotiation"
                aria-selected="false">{{__('tender.process.tab_title_negotiation')}}</a>
        </li>
    </ul>

    <div class="tab-content" id="tab-negotiation">
        <div class="tab-pane fade" id="overview-content" role="tabpanel" aria-labelledby="overview-tab">
            @include('tender.form.negotiation.admin_tab_overview')
        </div>
        <div class="tab-pane fade" id="negotiation-content" role="tabpanel" aria-labelledby="negotiation-tab">
            @include('tender.form.negotiation.admin_tab_commercial')
        </div>
    </div>
</div>
@endsection

@section('modules-scripts')
@parent
@include('tender.form.tender_process_admin')
<script type="text/javascript">
    var NegotiationCommercialType = 6;
var TabNegotiationOverview;
var TabNego;
require(["datatablesb4","dt.plugin.select",'datatables.fixed-column','datatables.rows-group','datetimepicker'], function(datetimepicker){
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

    var _columnsOverview = [
                @foreach ($tenderData[$type]['fields'] as $field)
                {data: '{{$field}}', name: '{{$field}}', "width": {{$field == 'vendor_name' ? 0 : ($field == 'action_negotiation_status' ? 120 : 110)}}},
                @endforeach
            ];

    TabNegotiationOverview = new TabDocument({
        tabSelector : '#overview-content',
        stageType : NegotiationCommercialType,
        actionView: "overview",
        columns: _columnsOverview,
        dtSelector : '#dt-vendor-negotiation',
        dtDetailDocSelector : '#dt-commercial-document',
        dtDetailItemSelector : '#dt-commercial-items',
        editable : {{$statusProcess == 'opened-4' ?  'true' : 'false'}},
        rowCallback: function(row, data){
            if(parseFloat(data.score_tc) > 0){
                $('td:eq(5)', row).html(parseFloat(data.score_tc).formatMoney(2, ".", ","));
            }else{
                $('td:eq(5)', row).html("");
            }

            if(parseFloat(data.score_com) > 0){
                $('td:eq(6)', row).html(parseFloat(data.score_com).formatMoney(2, ".", ","));
            }else{
                $('td:eq(6)', row).html("");
            }
        },
        initVendorHeader : {
            data : null,
            loadData : function(data){
                let _CSELF = this;
                let selector = TabNegotiationOverview.tabSelector + ' .card-tender-header';
                let _url = "{{ route('tender.dataItem', ['id' => $id, 'type' => $type]) }}?action_type=submission-header"
                $.ajax({
                    url : _url + '&vendor_id='+data.vendor_id+'&stage_type='+TabNegotiationOverview.stageType,
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
                if(data){
                    $(TabNegotiationOverview.tabSelector + ' #currency_code_header').val(data.currency_code);
                    $(TabNegotiationOverview.tabSelector + ' .quotation_number').text(data.quotation_number);
                    $(TabNegotiationOverview.tabSelector + ' .quotation_date').text(data.quotation_date);
                    $(TabNegotiationOverview.tabSelector + ' .quotation_note').text(data.quotation_note);
                    // $(TabNegotiationOverview.tabSelector + ' textarea[name="quotation_note"]').val(data.quotation_note);
                    $(TabNegotiationOverview.tabSelector + ' .incoterm').text(data.incoterm);
                    $(TabNegotiationOverview.tabSelector + ' .incoterm_location').text(data.incoterm_location);
                    $(TabNegotiationOverview.tabSelector + ' .bid_bond_value').text(data.bid_bond_value);
                    $(TabNegotiationOverview.tabSelector + ' .bid_bond_end_date').text(data.bid_bond_end_date);

                    $(TabNegotiationOverview.tabSelector + ' .currency_code').val(data.currency_code);

                    if(data.quotation_file && data.quotation_file!= '' && data.quotation_file!= 'undefined'){
                        $(TabNegotiationOverview.tabSelector + ' .quotation_file').show();
                        $(TabNegotiationOverview.tabSelector + ' .quotation_file').html('<i class="fa fa-paperclip"></i> '+data.quotation_file.fileName());
                        $(TabNegotiationOverview.tabSelector + ' .quotation_file').prop('href', '{{$storage}}/'+data.quotation_file);
                    }else{
                        $(TabNegotiationOverview.tabSelector + ' .quotation_file').hide();
                    }
                    if(data.bid_bond_file && data.bid_bond_file!= '' && data.bid_bond_file!= 'undefined'){
                        $(TabNegotiationOverview.tabSelector + ' .bid_bond_file').show();
                        $(TabNegotiationOverview.tabSelector + ' .bid_bond_file').html('<i class="fa fa-paperclip"></i> '+data.bid_bond_file.fileName());
                        $(TabNegotiationOverview.tabSelector + ' .bid_bond_file').prop('href', '{{$storage}}/'+data.bid_bond_file);
                    }else{
                        $(TabNegotiationOverview.tabSelector + ' .bid_bond_file').hide();
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
                ajax : _url + '&action_type=submission-items&vendor_id='+data.vendor_id,
                // fixedColumns: true,
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
                    },
                    // {data: 'deleteflg', name: 'deleteflg', "width": 100, className: 'text-center',render: renderDeleteFlg}
                ],
            };
            options.createdRow = function(row,data,index){
                if(data.deleteflg == 'x' || data.deleteflg == 'X'){
                    $(row).addClass("bg-warning");
                }
            }
            if(SELF.tableDetailItem != null){
                SELF.tableDetailItem.destroy();
            }
            SELF.tableDetailItem = $(SELF.dtDetailItemSelector).DataTable(options);
        },
    });

    TabNego = {
        initTableSum : function(){
            let SELF = this;
            let dtOptions = getDTOptions();
            let _url = "{{ route('tender.dataItem', ['id' => $id, 'type' => $type]) }}";
            let options = {
                deferRender: dtOptions.deferRender,
                rowId: dtOptions.rowId,
                lengthChange: false,
                searching: false,
                paging: false,
                language: dtOptions.language,
                scrollY: "300px",
                scrollX: true,
                scrollCollapse: true,
                ajax : _url + '?action_type=summary-items',
                rowsGroup: [
                    'vendor_code:name',
                    'vendor_name:name',
                ],
                columns: [
                    {data: 'vendor_code', name: 'vendor_code', "width": 80},
                    {data: 'vendor_name', name: 'vendor_name', "width": 250},
                    {data: 'item_version_comm', name: 'item_version_comm', "width": 50},
                    {
                        data: 'total_additional_cost', name: 'total_additional_cost', "width": 100,
                        render: function (data, type, row, meta) {
                            let total = data || 0;
                            return formatCurrency(total, row.currency_code_vendor);
                        }
                    },
                    {
                        data: 'subtotal_vendor', name: 'subtotal_vendor', "width": 100,
                        render: function (data, type, row, meta) {
                            let total = !isNaN(data) ? parseFloat(data) : 0;

                            return formatCurrency(total, row.currency_code_vendor);
                        }
                    },
                    {
                        data: 'total_overall_limit_vendor', name: 'total_overall_limit_vendor', "width": 100,
                        render: function (data, type, row, meta) {
                            let total = !isNaN(data) ? parseFloat(data) : 0;

                            return formatCurrency(total, row.currency_code_vendor);
                        }
                    },
                    {data: 'currency_code_vendor', name: 'currency_code_vendor',"width": 75},
                ],
            };

            if(SELF.tableSummary != null){
                SELF.tableSummary.destroy();
            }
            SELF.tableSummary = $('#dt-negotiation-summary').DataTable(options);
        }
    }

    var TabNegotiationCommercial = new TabDocument({
        tabSelector : '#negotiation-content',
        stageType : NegotiationCommercialType,
        actionView: "negotiation",
        dtSelector : '#dt-negotiation-vendor',
        dtDetailDocSelector : '#dt-negotiation-document',
        dtDetailItemSelector : '#dt-negotiation-items',
        editable : {{$statusProcess == 'opened-4' ?  'true' : 'false'}},
        rowCallback: function(row, data){
            if(parseFloat(data.score_tc) > 0){
                $('td:eq(2)', row).html(parseFloat(data.score_tc).formatMoney(2, ".", ","));
            }else{
                $('td:eq(2)', row).html("");
            }

            if(parseFloat(data.score_com) > 0){
                $('td:eq(3)', row).html(parseFloat(data.score_com).formatMoney(2, ".", ","));
            }else{
                $('td:eq(3)', row).html("");
            }
        },
        initVendorItem : function(SELF){
            let dtOptions = getDTOptions();
            let _url = "{{ route('tender.dataItem', ['id' => $id, 'type' => $type]) }}";
            let options = {
                deferRender: dtOptions.deferRender,
                rowId: dtOptions.rowId,
                lengthChange: false,
                searching: false,
                paging: false,
                language: dtOptions.language,
                ajax : _url + '?action_type=comparison-items',
                scrollY:        "300px",
                scrollX:        true,
                scrollCollapse: true,
                fixedColumns:   {
                    leftColumns: 2,
                },
                orderFixed: [0, 'asc'],
                rowsGroup: [
                    'description:name',
                    'number:name',
                    'line_number:name',
                    'product_code:name',
                    'product_group_code:name',
                    'vendor_name:name',
                ],
                columns: [
                    {
                        data: 'id', name: 'id',"width": 15,"className": 'text-center',
                        render: function (data, type, row, meta) {
                            return parseInt(meta.row) + parseInt(meta.settings._iDisplayStart) + 1;
                        }
                    },
                    {data: 'description', name: 'description', "width": 150},
                    {data: 'number', name: 'number',"width": 40},
                    {data: 'line_number', name: 'line_number', "width": 40},
                    {data: 'product_code', name: 'product_code', "width": 40},
                    {data: 'product_group_code', name: 'product_group_code', "width": 40},
                    {data: 'vendor_name', name: 'vendor_name', "width": 100},
                    {
                        data: 'item_version_comm', name: 'item_version_comm', "width": 50,
                        render : function ( data, type, row, dt ) {
                            return '<a href="" class="open-detail" >'+data+'</a>';
                        }
                    },
                    { data: 'description_vendor', name: 'description_vendor', "width": 150 },
                    {
                        data: 'qty_vendor', name: 'qty_vendor', "width": 30,
                        render: function (data, type, row, meta) {
                            return formatQty(data);
                        }
                    },
                    {
                        data: 'price_unit_vendor', name: 'price_unit_vendor', "width": 30,
                        render: function (data, type, row, meta) {
                            let total = data || 0;
                            return formatDecimal(total);
                        }
                    },
                    {
                        data: 'est_unit_price_vendor', name: 'est_unit_price_vendor', "width": 100,
                        render: function (data, type, row, meta) {
                            return formatCurrency(data, row.currency_code_vendor);
                        }
                    },
                    {
                        data: 'overall_limit_vendor', name: 'overall_limit_vendor', "width": 100,
                        render: function (data, type, row, meta) {
                            return formatCurrency(data, row.currency_code_vendor);
                        }
                    },
                    {
                        data: 'additional_cost', name: 'additional_cost', "width": 100,
                        render: function (data, type, row, meta) {
                            return formatCurrency(data, row.currency_code_vendor);
                        }
                    },
                    {
                        data: 'subtotal_vendor', name: 'subtotal_vendor', "width": 100,
                        render: function (data, type, row, meta) {
                            let total = 0
                            if(row.compliance == 'no_quote'){
                                return "{{ __('tender.process.compliance.no_quote')}}";
                            }
                            if(parseInt(row.item_category) == 0){
                                total = parseFloat(row.subtotal_vendor) + parseFloat(row.additional_cost);
                            }
                            return formatCurrency(total, row.currency_code_vendor);
                        }
                    },
                    {
                        data: 'total_overall_limit_vendor', name: 'total_overall_limit_vendor', "width": 100,
                        render: function (data, type, row, meta) {
                            let total = 0;
                            if(row.compliance == 'no_quote'){
                                return "{{ __('tender.process.compliance.no_quote')}}";
                            }
                            if(parseInt(row.item_category) != 0){
                                total = parseFloat(row.total_overall_limit_vendor) + parseFloat(row.additional_cost);
                            }
                            return formatCurrency(total, row.currency_code_vendor);
                        }
                    }
                ],
            };
            if(SELF.tableDetailItem != null){
                SELF.tableDetailItem.destroy();
            }
            SELF.tableDetailItem = $('#dt-negotiation-items').DataTable(options);
        },
        initActionItem : function(SELF){
            $(SELF.dtDetailItemSelector + ' tbody').on('click','.open-detail', function(e){
                e.preventDefault();

                SELF.tableItemSelectedRow = SELF.tableDetailItem.row($(this).parents('tr'));
                SELF.openItemDetailRow(SELF.tableItemSelectedRow.data());
            });
        }
    });

    initLoad(); //init button actions

    if(typeof(ItemDetailPage) !== "undefined"){
        ItemDetailPage.init();
    }

    $('#formItemDetail_modal').on("shown.bs.modal", function () {
        var vendor_id, submission_method = 6;
        if(TabSelected.selectedRow){
            vendor_id = TabSelected.selectedRow.vendor_id;
            submission_method = TabSelected.selectedRow.submission_method;
        }else{
            vendor_id = TabSelected.tableItemSelectedRow.data().vendor_id;
            submission_method = TabSelected.tableItemSelectedRow.data().submission_method;
        }

        try{
            ItemDetailPage.resetForm();
            ItemDetailPage.reloadTable(vendor_id, submission_method);
            ItemDetailPage.ForceCloseModal = false;
        }catch(e){
            console.error(e);
        }
    });
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        if(e.target.id == 'overview-tab'){
            TabSelected = TabNegotiationOverview;
            $(TabNegotiationOverview.tabSelector + ' .btn_back_to').trigger('click');
            if(TabNegotiationOverview.table == null){
                TabNegotiationOverview.initTable();
            }else{
                TabNegotiationOverview.table.ajax.reload();
                TabNegotiationOverview.table.columns.adjust().draw();
            }
        }else
        if(e.target.id == 'negotiation-tab'){
            TabSelected = TabNegotiationCommercial;
            $(TabNegotiationCommercial.tabSelector + ' .btn_back_to').trigger('click');
            if(TabNegotiationCommercial.table == null){
                TabNegotiationCommercial.initTable();
                TabNegotiationCommercial.initVendorItem(TabSelected);
                TabNegotiationCommercial.initActionItem(TabSelected);

                TabNego.initTableSum();

            }else{
                TabNegotiationCommercial.table.ajax.reload();
                TabNegotiationCommercial.table.columns.adjust().draw();
                TabNegotiationCommercial.tableDetailItem.ajax.reload();
                TabNegotiationCommercial.tableDetailItem.columns.adjust().draw();
            }
        }
    });

    $(".btn_negotiation_bottom").click(function(e){
        var actionType = $(this).attr("data-action-type");
        var modalTitle = $(this).attr("data-modal-title");
        var alertMsg = $(this).attr("data-alert-message");
        var rowData = TabNegotiationOverview.table.rows().data()[0];

        $('#delete_modal .modal-title').text(modalTitle);
        let _body = $('<div class="alert alert-warning" role="alert">' + alertMsg + '</div>')
        $('#delete_modal .modal-body').html(_body);
        $('#btn_delete_modal').click();
        $('#delete_modal #btn_confirm').off('click').on('click', function () {
            let data = {
                action_type : actionType,
                stage_type : NegotiationCommercialType,
                tender_number : rowData.tender_number
            };
            submit(data, function(){
                $('#delete_modal .close').click();
                window.location.reload();
            });
            return false;
        });
    });

    @if($tender->conditional_type == 'CT1')
        FormCostPage.init();
        $('#formAddcost_modal').on("shown.bs.modal", function () {
            try{
                FormCostPage.resetForm();
                FormCostPage.reloadTable(TabSelected.selectedRow.vendor_id);
            }catch{}
        });
    @endif

    $('#overview-tab').click();
    TabNegotiationOverview.init();
    TabNegotiationCommercial.init();
    $('.btn_scoring').hide();
});

function startNegotiation(el){
    var rowData = TabNegotiationOverview.table.row( $(el).parents('tr') ).data();
    $('#delete_modal .modal-title').text("{{__('tender.process.btn_request_negotiation')}}");
    let _body = $('<div class="alert alert-warning" role="alert">{{__('tender.process.message_request_negotiation')}} '+rowData.vendor_name+'?</div>')
    $('#delete_modal .modal-body').html(_body);
    $('#btn_delete_modal').click();
    $('#delete_modal #btn_confirm').off('click').on('click', function () {
        let data = {
            action_type : '{{\App\Enums\TenderSubmissionEnum::FLOW_STATUS[1]}}',
            stage_type : NegotiationCommercialType,
            vendor_id : rowData.vendor_id
        };
        submit(data, function(){
            $('#delete_modal .close').click();
            TabNegotiationOverview.table.ajax.reload();
        });
        return false;
    });
}

function openNegotiation(el){
    var rowData = TabNegotiationOverview.table.row( $(el).parents('tr') ).data();
    $('#delete_modal .modal-title').text("{{__('tender.process.btn_open_negotiation')}}");
    let _body = $('<div class="alert alert-warning" role="alert">{{__('tender.process.message_open_negotiation')}} '+rowData.vendor_name+'?</div>')
    $('#delete_modal .modal-body').html(_body);
    $('#btn_delete_modal').click();
    $('#delete_modal #btn_confirm').off('click').on('click', function () {
        let data = {
            action_type : '{{\App\Enums\TenderSubmissionEnum::FLOW_STATUS[2]}}',
            stage_type : NegotiationCommercialType,
            tender_number : rowData.tender_number,
            vendor_id : rowData.vendor_id
        };
        submit(data, function(){
            $('#delete_modal .close').click();
            Loading.Show();
            window.location.reload();
        });
        return false;
    });
}

function reOpenNegotiation(el){
    var rowData = TabNegotiationOverview.table.row( $(el).parents('tr') ).data();
    $('#delete_modal .modal-title').text("{{__('tender.process.btn_open_negotiation')}}");
    let _body = $('<div class="alert alert-warning" role="alert">{{__('tender.process.message_open_negotiation')}} '+rowData.vendor_name+'?</div>')
    $('#delete_modal .modal-body').html(_body);
    $('#btn_delete_modal').click();
    $('#delete_modal #btn_confirm').off('click').on('click', function () {
        let data = {
            action_type : '{{\App\Enums\TenderSubmissionEnum::FLOW_STATUS[4]}}',
            stage_type : NegotiationCommercialType,
            tender_number : rowData.tender_number,
            vendor_id : rowData.vendor_id
        };
        submit(data, function(){
            $('#delete_modal .close').click();
            Loading.Show();
            window.location.reload();
        });
        return false;
    });
}
</script>
@endsection
