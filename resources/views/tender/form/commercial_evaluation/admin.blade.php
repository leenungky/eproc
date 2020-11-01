

@section('contentbody')
<div class="tab">
    <ul class="nav nav-tabs" id="commercial_evaluation-tab" role="tablist">
        <li class="nav-item" id="overview-li">
            <a class="nav-link" id="overview-tab" data-toggle="tab" href="#overview-content" role="tab"
                aria-controls="overview" aria-selected="true">{{__('tender.process.tab_title_overview')}}</a>
        </li>
        <li class="nav-item" id="commercial-li">
            <a class="nav-link @if(!$docTabEnable) disabled @endif" id="commercial-tab" data-toggle="tab" href="#commercial-content" role="tab"
                aria-controls="commercial" aria-selected="true">{{__('tender.process.tab_title_commercial')}}</a>
        </li>
        <li class="nav-item" id="evaluation-li">
            <a class="nav-link @if(!$evalTabEnable) disabled @endif" id="evaluation-tab" data-toggle="tab" href="#evaluation-content" role="tab"
                aria-controls="evaluation" aria-selected="true">{{__('tender.process.tab_title_evaluation')}}</a>
        </li>

        <li class="nav-item" id="approval-li">
            <a class="nav-link @if(!$evalTabEnable) disabled @endif" id="approval-tab" data-toggle="tab" href="#approval-content" role="tab"
                aria-controls="approval" aria-selected="true">{{__('tender.process.tab_title_approval')}}</a>
        </li>

    </ul>

    <div class="tab-content" id="tab-commercial_evaluation">
        <div class="tab-pane fade" id="overview-content" role="tabpanel" aria-labelledby="overview-tab">
            @include('tender.form.commercial_evaluation.admin_tab_overview')
        </div>
        <div class="tab-pane fade" id="commercial-content" role="tabpanel" aria-labelledby="commercial-tab">
            @include('tender.form.commercial_evaluation.admin_tab_commercial')
        </div>
        <div class="tab-pane fade" id="evaluation-content" role="tabpanel" aria-labelledby="evaluation-tab">
            @include('tender.form.commercial_evaluation.admin_tab_evaluation')
        </div>

        <div class="tab-pane fade" id="approval-content" role="tabpanel" aria-labelledby="approval-tab">
            @include('tender.form.commercial_evaluation.admin_tab_approval')
        </div>

    </div>
</div>
@endsection

@section('modules-scripts')
@parent
@include('tender.form.tender_process_admin')
<script type="text/javascript">
require(["datatablesb4","dt.plugin.select",'datatables.fixed-column','datatables.rows-group'], function(datetimepicker){
    var CommercialType = 4;
    Tabs = $('#commercial_evaluation-tab li > a.nav-link');
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
    var TabCommercial = new TabDocument({
        tabSelector : '#commercial-content',
        stageType : CommercialType,
        dtSelector : '#dt-commercial-vendor',
        dtDetailDocSelector : '#dt-commercial-document',
        dtDetailItemSelector : '#dt-commercial-items',
        editable : {{$statusProcess == 'opened-4' ?  'true' : 'false'}},
        initVendorHeader : {
            data : null,
            loadData : function(data){
                let _CSELF = this;
                let selector = TabCommercial.tabSelector + ' .card-tender-header';
                let _url = "{{ route('tender.dataItem', ['id' => $id, 'type' => $type]) }}?action_type=submission-header"
                $.ajax({
                    url : _url + '&vendor_id='+data.vendor_id+'&stage_type='+TabCommercial.stageType,
                    type : 'GET',
                    contentType: "application/json; charset=utf-8",
                    dataType: "json",
                    beforeSend: function( xhr ) {
                        // Loading.Show(selector);
                    }
                }).done(function(response, textStatus, jqXhr) {
                    if(response.success){
                        _CSELF.data = response.data;
                        _CSELF.renderData(_CSELF.data);
                    }
                }).fail(defaultAjaxFail)
                .always(function(jqXHR, textStatus, errorThrown) {
                    // Loading.Hide(selector);
                });
            },
            renderData : function(data){
                if(data){
                    $(TabCommercial.tabSelector + ' #currency_code_header').val(data.currency_code);
                    $(TabCommercial.tabSelector + ' .quotation_number').text(data.quotation_number);
                    $(TabCommercial.tabSelector + ' .quotation_date').text(data.quotation_date);
                    $(TabCommercial.tabSelector + ' .quotation_note').text(data.quotation_note);
                    // $(TabCommercial.tabSelector + ' textarea[name="quotation_note"]').val(data.quotation_note);
                    $(TabCommercial.tabSelector + ' .incoterm').text(data.incoterm);
                    $(TabCommercial.tabSelector + ' .incoterm_location').text(data.incoterm_location);
                    $(TabCommercial.tabSelector + ' .bid_bond_value').text(data.bid_bond_value);
                    $(TabCommercial.tabSelector + ' .bid_bond_end_date').text(data.bid_bond_end_date);

                    $(TabCommercial.tabSelector + ' .currency_code').val(data.currency_code);

                    if(data.quotation_file && data.quotation_file!= '' && data.quotation_file!= 'undefined'){
                        $(TabCommercial.tabSelector + ' .quotation_file').show();
                        $(TabCommercial.tabSelector + ' .quotation_file').html('<i class="fa fa-paperclip"></i> '+data.quotation_file.fileName());
                        $(TabCommercial.tabSelector + ' .quotation_file').prop('href', '{{$storage}}/'+data.quotation_file);
                    }else{
                        $(TabCommercial.tabSelector + ' .quotation_file').hide();
                    }
                    if(data.bid_bond_file && data.bid_bond_file!= '' && data.bid_bond_file!= 'undefined'){
                        $(TabCommercial.tabSelector + ' .bid_bond_file').show();
                        $(TabCommercial.tabSelector + ' .bid_bond_file').html('<i class="fa fa-paperclip"></i> '+data.bid_bond_file.fileName());
                        $(TabCommercial.tabSelector + ' .bid_bond_file').prop('href', '{{$storage}}/'+data.bid_bond_file);
                    }else{
                        $(TabCommercial.tabSelector + ' .bid_bond_file').hide();
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
                    {data: 'description_vendor', name: 'description_vendor', "width": 300},
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
                        data: 'subtotal_vendor', name: 'subtotal_vendor', "width": 150,
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
                    {data: 'deleteflg', name: 'deleteflg', "width": 100, className: 'text-center',render: renderDeleteFlg, visible : false,},
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
            // console.log($(SELF.dtDetailItemSelector));
            SELF.tableDetailItem = $(SELF.dtDetailItemSelector).DataTable(options);
        },
    });

    initLoad();
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        if(e.target.id == 'overview-tab'){
            if(TabOverview.table == null){
                TabOverview.initTable();
            }
        }else
        if(e.target.id == 'commercial-tab'){
            TabSelected = TabCommercial;
            $(TabCommercial.tabSelector + ' .btn_back_to').trigger('click');
            if(TabCommercial.table == null){
                TabCommercial.initTable();
            }
        }else
        if(e.target.id == 'evaluation-tab'){
            if(TabEvaluation.table == null){
                TabEvaluation.initTable();
                TabEvaluation.initTableItem();
                TabEvaluation.initTableSum();
            }else{
                TabEvaluation.table.ajax.reload();
            }
        }
    });
    @if(in_array($statusProcess, ['registration-','registration-4']))
    $('button.btn_start_com').click(function(){
        $('#delete_modal .modal-title').text("{{__('tender.'.$type)}}");
        let _body = $('<div class="alert alert-warning" role="alert">{{__('tender.process.message_start_bidding')}}</div>')
        $('#delete_modal .modal-body').html(_body);
        $('#btn_delete_modal').click();
        $('#delete_modal #btn_confirm').off('click').on('click', function () {
            let data = {
                action_type : '{{\App\Enums\TenderSubmissionEnum::FLOW_STATUS[1]}}',
                stage_type : CommercialType,
            };
            submit(data, function(){
                $('#delete_modal .close').click();
                Loading.Show();
                location.href = "{{ route('tender.save', ['id'=>$id, 'type' => $type]) }}#commercial";
                location.reload();
            })
            return false;
        });
    });
    @endif
    @if($statusProcess == 'started-4')
    $('button.btn_open_com').click(function(){
        $('#delete_modal .modal-title').text("{{__('tender.'.$type)}}");
        let _body = $('<div class="alert alert-warning" role="alert">{{__('tender.process.message_start_bidding')}}</div>')
        $('#delete_modal .modal-body').html(_body);
        $('#btn_delete_modal').click();
        $('#delete_modal #btn_confirm').off('click').on('click', function () {
            @if($workflowValues[$actIndex] == \App\Enums\TenderSubmissionEnum::FLOW_STATUS[3])
            let data = {action_type : '{{\App\Enums\TenderSubmissionEnum::FLOW_STATUS[4]}}', stage_type : CommercialType};
            @else
            let data = {action_type : '{{\App\Enums\TenderSubmissionEnum::FLOW_STATUS[2]}}', stage_type : CommercialType};
            @endif
            submit(data, function(){
                $('#delete_modal .close').click();
                Loading.Show();
                location.href = "{{ route('tender.save', ['id'=>$id, 'type' => $type]) }}#commercial";
                location.reload();
            });
            return false;
        });
    });
    @endif
    initTab('commercial_evaluation-tab');
    TabCommercial.init();
});
</script>
@include('tender.form.commercial_approval_script')
@endsection

