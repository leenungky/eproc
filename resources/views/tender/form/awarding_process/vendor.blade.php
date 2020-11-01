@section('contentbody')
<div class="tender-content">
    <ul class="nav nav-tabs" id="awarding-tab" role="tablist">
        <li class="nav-item" id="overview-li">
            <a class="nav-link active" id="overview-tab" data-toggle="tab" href="#overview-content" role="tab"
                aria-controls="overview" aria-selected="true">{{__('tender.process.tab_title_overview')}}</a>
        </li>
    </ul>
    <div class="tab-content" id="tab-overview">
        <div class="tab-pane fade active show" id="overview-content" role="tabpanel" aria-labelledby="overview-tab">
            @include('tender.form.awarding_process.vendor_tab_overview')
        </div>
    </div>
</div>
@endsection

@section('modules-scripts')
@parent
@include('tender.form.tender_process_admin')
<script type="text/javascript">
var awardingProcessType = 7;
var TabResult;
require(['datetimepicker',"bootstrap-fileinput-fas"], function(datetimepicker){
    Tabs = $('#awarding-tab li > a.nav-link');

    var _columnsResult = [
        @foreach ($tenderData[$type]['fields4'] as $field)
        {data: '{{$field}}', name: '{{$field}}', "width": {{$field == 'vendor_name' ? 0 : ($field == 'action_details_awarding' ? 40 : 110)}}},
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

    TabResult = new TabDocument({
        tabSelector : '#overview-content',
        stageType : awardingProcessType,
        columns: _columnsResult,
        actionView: "awarding_result",
        dtSelector : '#dt-vendor-awarding-result',
        dtDetailDocSelector : '#dt-commercial-document-result',
        dtDetailItemSelector : '#dt-commercial-items-result',
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
                let selector = TabResult.tabSelector + ' .card-tender-header';
                let _url = "{{ route('tender.dataItem', ['id' => $id, 'type' => $type]) }}?action_type=submission-header"
                $.ajax({
                    url : _url + '&vendor_id={{$vendor->id}}&stage_type='+TabResult.stageType+'&actionView=' + TabResult.actionView,
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
                    $(TabResult.tabSelector + ' #currency_code_header').val(data.currency_code);
                    $(TabResult.tabSelector + ' .quotation_number').text(data.quotation_number);
                    $(TabResult.tabSelector + ' .quotation_date').text(data.quotation_date);
                    $(TabResult.tabSelector + ' .quotation_note').text(data.quotation_note);
                    $(TabResult.tabSelector + ' .incoterm').text(data.incoterm);
                    $(TabResult.tabSelector + ' .incoterm_location').text(data.incoterm_location);
                    $(TabResult.tabSelector + ' .bid_bond_value').text(data.bid_bond_value);
                    $(TabResult.tabSelector + ' .bid_bond_end_date').text(data.bid_bond_end_date);

                    $(TabResult.tabSelector + ' .currency_code').val(data.currency_code);

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
                {
                    data: 'attachment', name: 'attachment',
                    render: function (data, type, row, meta) {
                        if(data){
                            let _tpl = '<a target="_blank" class="btn btn-link float-left" href="{{$storage}}/'+data+'">'+data.fileName()+'</a>';
                            return '<div>' + _tpl + '</div>';
                        }else{
                            return "";
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

    initLoad(); //init button actions

    if(typeof(ItemDetailPage) !== "undefined"){
        ItemDetailPage.init();
    }

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

    @if($tender->conditional_type == 'CT1')
        FormCostPage.init();
        $('#formAddcost_modal').on("shown.bs.modal", function () {
            try{
                FormCostPage.resetForm();
                FormCostPage.reloadTable(TabSelected.selectedRow.vendor_id, awardingProcessType, TabSelected.actionView);
            }catch{}
        });
    @endif

    $('#overview-tab').click();
    TabResult.init();
    $('.btn_scoring').hide();

    TabSelected = TabResult;
    $(TabResult.tabSelector + ' .btn_back_to').trigger('click');

    if(TabResult.table == null){
        TabResult.initTable();
    }else{
        TabResult.table.ajax.reload();
        TabResult.table.columns.adjust().draw();
    }
});
</script>
@endsection
