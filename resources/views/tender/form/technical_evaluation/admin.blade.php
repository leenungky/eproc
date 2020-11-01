@section('contentbody')
<div class="tab" style="width:100%;">
    <ul class="nav nav-tabs" id="technical_evaluation-tab" role="tablist">
        <li class="nav-item" id="overview-li">
            <a class="nav-link" id="overview-tab" data-toggle="tab" href="#overview-content" role="tab"
                aria-controls="overview" aria-selected="true">{{__('tender.process.tab_title_overview')}}</a>
        </li>
        <li class="nav-item" id="technical-li">
            <a class="nav-link @if(!$docTabEnable) disabled @endif" id="technical-tab" data-toggle="tab" href="#technical-content" role="tab"
                aria-controls="technical" aria-selected="true">{{__('tender.process.tab_title_technical')}}</a>
        </li>
        <li class="nav-item" id="evaluation-li">
            <a class="nav-link @if(!$evalTabEnable) disabled @endif" id="evaluation-tab" data-toggle="tab" href="#evaluation-content" role="tab"
                aria-controls="evaluation" aria-selected="true">{{__('tender.process.tab_title_evaluation')}}</a>
        </li>
    </ul>

    <div class="tab-content" id="tab-technical_evaluation">
        <div class="tab-pane fade" id="overview-content" role="tabpanel" aria-labelledby="overview-tab">
            @include('tender.form.technical_evaluation.admin_tab_overview')
        </div>
        <div class="tab-pane fade" id="technical-content" role="tabpanel" aria-labelledby="technical-tab">
            @include('tender.form.technical_evaluation.admin_tab_technical')
        </div>
        <div class="tab-pane fade" id="evaluation-content" role="tabpanel" aria-labelledby="evaluation-tab">
            @include('tender.form.technical_evaluation.admin_tab_evaluation')
        </div>
    </div>
</div>
@endsection


@section('modules-scripts')
@parent
@include('tender.form.tender_process_admin')
<script type="text/javascript">
require(["datatablesb4","dt.plugin.select",'datatables.fixed-column','datatables.rows-group'], function(datetimepicker){
    var TechnicalType = 3;
    Tabs = $('#technical_evaluation-tab li > a.nav-link');
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
    var TabTechnical = new TabDocument({
        tabSelector : '#technical-content',
        stageType : TechnicalType,
        dtSelector : '#dt-technical-vendor',
        dtDetailDocSelector : '#dt-technical-document',
        dtDetailItemSelector : '#dt-technical-items',
        editable : {{$statusProcess == 'opened-3' ? 'true' : 'false'}},
        initVendorHeader : {
            data : null,
            loadData : function(data){
                let _CSELF = this;
                let selector = TabTechnical.tabSelector + ' .card-tender-header';
                let _url = "{{ route('tender.dataItem', ['id' => $id, 'type' => $type]) }}?action_type=submission-header"
                $.ajax({
                    url : _url + '&vendor_id='+data.vendor_id+'&stage_type='+TabTechnical.stageType,
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
                    $(TabTechnical.tabSelector + ' .quotation_number').text(data.quotation_number);
                    $(TabTechnical.tabSelector + ' .quotation_date').text(data.quotation_date);
                    $(TabTechnical.tabSelector + ' .quotation_note').text(data.quotation_note);
                    $(TabTechnical.tabSelector + ' .tkdn_percentage').text(formatPercentage(data.tkdn_percentage));
                    if(data.quotation_file && data.quotation_file!= '' && data.quotation_file!= 'undefined'){
                        $(TabTechnical.tabSelector + ' .quotation_file').show();
                        $(TabTechnical.tabSelector + ' .quotation_file').html('<i class="fa fa-paperclip"></i> '+data.quotation_file.fileName());
                        $(TabTechnical.tabSelector + ' .quotation_file').prop('href', '{{$storage}}/'+data.quotation_file);
                    }else{
                        $(TabTechnical.tabSelector + ' .quotation_file').hide();
                    }
                    if(data.tkdn_file && data.tkdn_file!= '' && data.tkdn_file!= 'undefined'){
                        $(TabTechnical.tabSelector + ' .tkdn_file').show();
                        $(TabTechnical.tabSelector + ' .tkdn_file').html('<i class="fa fa-paperclip"></i> '+data.tkdn_file.fileName());
                        $(TabTechnical.tabSelector + ' .tkdn_file').prop('href', '{{$storage}}/'+data.tkdn_file);
                    }else{
                        $(TabTechnical.tabSelector + ' .tkdn_file').hide();
                    }
                    if(data.proposed_item_file && data.proposed_item_file!= '' && data.proposed_item_file!= 'undefined'){
                        $(TabTechnical.tabSelector + ' .proposed_item_file').show();
                        $(TabTechnical.tabSelector + ' .proposed_item_file').html('<i class="fa fa-paperclip"></i> '+data.proposed_item_file.fileName());
                        $(TabTechnical.tabSelector + ' .proposed_item_file').prop('href', '{{$storage}}/'+data.proposed_item_file);
                    }else{
                        $(TabTechnical.tabSelector + ' .proposed_item_file').hide();
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
                processing: true,
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
                    {data: 'number', name: 'number',"width": 100},
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
        if(e.target.id == 'technical-tab'){
            TabSelected = TabTechnical;
            $(TabTechnical.tabSelector + ' .btn_back_to').trigger('click');
            if(TabTechnical.table == null){
                TabTechnical.initTable();
            }else{
                TabTechnical.table.ajax.reload();
                TabTechnical.table.columns.adjust().draw();
            }
        }
        else
        if(e.target.id == 'commercial-tab'){
            TabSelected = TabCommercial;
            if(TabCommercial.table == null){
                TabCommercial.initTable();
            }
            $(TabCommercial.tabSelector + ' .btn_back_to').trigger('click');
        }else
        if(e.target.id == 'evaluation-tab'){
            if(TabEvaluation.table == null){
                TabEvaluation.initTable();
                TabEvaluation.initTableItem(true);
                TabEvaluation.initTableSum(true);
            }else{
                TabEvaluation.table.ajax.reload();
            }
        }
    });
    @if($statusProcess == 'registration-')
    $('button.btn_start_tc').click(function(){
        $('#delete_modal .modal-title').text("{{__('tender.'.$type)}}");
        let _body = $('<div class="alert alert-warning" role="alert">{{__('tender.process.message_start_bidding')}}</div>')
        $('#delete_modal .modal-body').html(_body);
        $('#btn_delete_modal').click();
        $('#delete_modal #btn_confirm').off('click').on('click', function () {
            let data = {
                action_type : '{{\App\Enums\TenderSubmissionEnum::FLOW_STATUS[1]}}',
                stage_type : TechnicalType,
            };
            submit(data, function(){
                $('#delete_modal .close').click();
                Loading.Show();
                location.href = "{{ route('tender.save', ['id'=>$id, 'type' => $type]) }}#technical";
                location.reload();
            })
            return false;
        });
    });
    @endif
    @if($statusProcess == 'started-3')
    $('button.btn_open_tc').click(function(){
        $('#delete_modal .modal-title').text("{{__('tender.'.$type)}}");
        let _body = $('<div class="alert alert-warning" role="alert">{{__('tender.process.message_start_bidding')}}</div>')
        $('#delete_modal .modal-body').html(_body);
        $('#btn_delete_modal').click();
        $('#delete_modal #btn_confirm').off('click').on('click', function () {
            @if($workflowValues[$actIndex] == \App\Enums\TenderSubmissionEnum::FLOW_STATUS[3])
            let data = {action_type : '{{\App\Enums\TenderSubmissionEnum::FLOW_STATUS[4]}}', stage_type : TechnicalType};
            @else
            let data = {action_type : '{{\App\Enums\TenderSubmissionEnum::FLOW_STATUS[2]}}', stage_type : TechnicalType};
            @endif
            submit(data, function(){
                $('#delete_modal .close').click();
                Loading.Show();
                location.href = "{{ route('tender.save', ['id'=>$id, 'type' => $type]) }}#technical";
                location.reload();
            });
            return false;
        });
    });
    @endif
    initTab('technical_evaluation-tab');
    TabTechnical.init();
});
</script>
@endsection

