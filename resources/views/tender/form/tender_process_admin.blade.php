<script type="text/javascript">
    var Tabs = null;
    var TabSelected = null;
    var URLDatatable = "{{ route('tender.dataItem', ['id' => $id, 'type' => $type]) }}";
    var PRListField = {!! json_encode($tenderData[$type]['prlist']) !!};

    var TabOverview = {
        table : null,
        initTable : function(){
            let SELF = this;
            let dtOptions = getDTOptions();
            let options = {
                deferRender: dtOptions.deferRender,
                rowId: dtOptions.rowId,
                lengthChange: false,
                searching: false,
                processing: true,
                language: dtOptions.language,
                ajax : "{{ route('tender.dataItem', ['id' => $id, 'type' => $type]) }}?tab=overview",
                columns: [
                    {data: 'vendor_code', name: 'vendor_code', "width": 75},
                    {data: 'vendor_name', name: 'vendor_name'},
                    {data: 'status', name: 'status', "width": 150},
                    {data: 'submission_date', name: 'submission_date', "width": 150},
                ],
                columnDefs:[
                    {
                        "render": function ( data, type, row, dt ) {
                            var column = dt.settings.aoColumns[dt.col].data;
                            let tmp = data;
                            switch(column){
                                case 'status':
                                    return renderStatus(data, type, row, dt)
                                default:
                                    return data;
                            }
                        },
                        "targets": "_all"
                    }
                ],
            };
            //## Initilalize Datatables
            SELF.table = $('#dt-vendor-submission').DataTable(options);

            $("#overview-content .page_numbers").ready(function () {
                $("#dt-vendor-submission_paginate").appendTo($("#overview-content .page_numbers"));
                $("#dt-vendor-submission_info").css("padding", ".375rem .75rem").appendTo($("#overview-content .page_numbers"));
            });
        },
    };
    var TabDocument = function(options){
        this.tabSelector = options.tabSelector;
        this.stageType = options.stageType;
        this.columns = options.columns;
        this.actionView= options.actionView;
        this._initVendorDocument = options.initVendorDocument;
        this.rowCallback = options.rowCallback;
        this.dtSelector = options.dtSelector;
        this.vendorSelected = "{{$vendorSelected ?? ''}}"; // 1322; // options.dtSelector;
        this.autoWidth = options.autoWidth;
        this.table = null;
        this.selectedRow= null;
        this.editable = options.editable;
        this.processing = typeof(options.processing) == 'undefined' ? true : options.processing;

        this.initTable = function(){
            let SELF = this;
            let dtOptions = getDTOptions();
            dtOptions.autoWidth = true;
            if(typeof SELF.autoWidth !== "undefined"){
                dtOptions.autoWidth = SELF.autoWidth;
            }

            var _url = "{{ route('tender.dataItem', ['id' => $id, 'type' => $type])}}?stage_type="+SELF.stageType;

            if(SELF.actionView){
                _url = _url + '&actionView=' + SELF.actionView;
            }

            var _columns = [
                    @foreach ($tenderData[$type]['fields2'] as $field)
                    {data: '{{$field}}', name: '{{$field}}', "width": {{$field == 'vendor_name' ? 200 : 100}}},
                    @endforeach
                ];

            if(SELF.columns){
                _columns = SELF.columns;
            }

            let options = {
                deferRender: dtOptions.deferRender,
                rowId: dtOptions.rowId,
                autoWidth: dtOptions.autoWidth,
                lengthChange: false,
                searching: false,
                processing: SELF.processing,
                language: dtOptions.language,
                ajax : _url,
                // fixedColumns: true,
                columns: _columns,
                rowCallback: function(row, data){
                    if(typeof(SELF.rowCallback) === "function"){
                        SELF.rowCallback(row, data);
                    }
                },
                initComplete: function(settings, json){
                    if(typeof(SELF.initComplete) === "function"){
                        SELF.initComplete(settings, json);
                    }
                },
                columnDefs:[
                    {
                        "render": function ( data, type, row, dt ) {
                            var column = dt.settings.aoColumns[dt.col].data;
                            let tmp = data;
                            switch(column){
                                case 'status':
                                    return renderStatus(data, type, row, dt)
                                case 'awarding_status':
                                    return renderAwardingStatus(data, type, row, dt)
                                case 'submission_date':
                                    let _tpl = '<a class="btn btn-link float-left openDetail" href="#" data-vendor-id="'+row.vendor_id+'">'+data+'</a>';
                                    return _tpl;
                                case 'action_details_awarding':
                                    dt.settings.aoColumns[dt.col].sClass = "dt-center";
                                    var _statusWin = "{{\App\Models\TenderVendorAwarding::STATUS[1]}}";
                                    if(data && data == _statusWin){
                                        // return '<a class="btn btn-link float-left openDetail" href="#" data-vendor-id="'+row.vendor_id+'"><i class="fa fa-eye"></i></a>';
                                        return '<a class="btn btn-link float-left openDetail" href="#" data-vendor-id="'+row.vendor_id+'">Detail</a>';
                                    }else{
                                        // return '<span><i class="fa fa-eye"></i></span>';
                                        return '<span>Detail</span>';
                                    }
                                case 'action_negotiation_status':
                                            if(data == null){
                                                return `<button id="request_negotiation" @if(isset($canStart) && $canStart) onClick="startNegotiation(this)" @else disabled @endif class="btn btn-primary btn-sm">{{__('tender.process.btn_request_negotiation')}}</button>`;
                                            }else if(data=='start' || data=='submitted'){
                                                return `<button id="open_negotiation" @if(isset($canOpen) && $canOpen) onClick="openNegotiation(this)" @else disabled @endif class="btn btn-success btn-sm">{{__('tender.process.btn_open_negotiation')}}</button>`;
                                            }else if(data=='request_resubmission' || data=='resubmitted'){
                                                return `<button id="open_negotiation" @if(isset($canOpen) && $canOpen) onClick="reOpenNegotiation(this)" @else disabled @endif class="btn btn-success btn-sm">{{__('tender.process.btn_reopen_negotiation')}}</button>`;
                                            }else if(data=='open'){
                                                return `<p class="text-warning" style="margin-bottom: 0;">{{__('tender.process.btn_negotiation_opened')}}</p>`;
                                            }else if(data=='open_resubmission'){
                                                return `<p class="text-warning" style="margin-bottom: 0;">{{__('tender.process.btn_negotiation_reopened')}}</p>`;
                                            }else if(data=='finish' || data=='complete'){
                                                return `<p class="text-success" style="margin-bottom: 0;">{{__('tender.process.btn_negotiation_finished')}}</p>`;
                                            }else{
                                                return "";
                                            }
                                    break;
                                case 'action_awarding_status':
                                        dt.settings.aoColumns[dt.col].sClass = "dt-center";

                                        @if((isset($canWin) && $canWin))
                                            let _btn = data;
                                            if(type === "display"){
                                                let statusWin = "{{\App\Models\TenderVendorAwarding::STATUS[1]}}";
                                                let statusLose = "{{\App\Models\TenderVendorAwarding::STATUS[2]}}";
                                                // if(data == null){
                                                //     var _btn = `<button data-awarding-status="`+statusLose+`" onClick="setAwarding(this)" class="btn btn-danger btn-sm">{{__('tender.process_status_awarding.lose')}}</button>&nbsp;`;
                                                //     _btn = _btn +`<button data-awarding-status="`+statusWin+`" onClick="setAwarding(this)" class="btn btn-primary btn-sm">{{__('tender.process_status_awarding.winner')}}</button>`;

                                                //     return _btn;
                                                // }else if(data=='winner'){
                                                //     return `<p class="text-success" style="margin-bottom: 0;">{{__('tender.process.lbl_status_win')}}</p>`;
                                                // }else if(data=='lose'){
                                                //     return `<p class="text-warning" style="margin-bottom: 0;">{{__('tender.process.lbl_status_lose')}}</p>`;
                                                // }else{
                                                //     return "";
                                                // }

                                                let disabled = "";
                                                if((row.po_number || "") !== "" || ["submitted","resubmitted"].includes(row.status)){
                                                    disabled = "disabled";
                                                }
                                                let classLose = 'active';
                                                let classWin = 'active';
                                                if(data){
                                                    if(data==statusWin){
                                                        classLose = disabled;
                                                    }else{
                                                        classWin = disabled;
                                                    }
                                                }else{
                                                    classLose = '';
                                                    classWin = '';
                                                }
                                                // @if(isset($isSubmit) && $isSubmit)
                                                //     disabled = "disabled";
                                                // @endif

                                                _btn = '<div class="btn-group btn-group-toggle" data-toggle="buttons">' +
                                                        '<label class="btn btn-outline-danger '+classLose+'">' +
                                                            '<input type="radio" '+disabled+' name="rd_awarding" onClick="setAwarding(this)" value="'+statusLose+'" autocomplete="off"> {{__('tender.process_status_awarding.lose')}}' +
                                                        '</label>' +
                                                        '<label class="btn btn-outline-success '+classWin+'">' +
                                                            '<input type="radio" '+disabled+' name="rd_awarding" onClick="setAwarding(this)" value="'+statusWin+'" autocomplete="off"> {{__('tender.process_status_awarding.winner')}}' +
                                                        '</label>' +
                                                    '</div>';
                                            }
                                            return _btn;
                                        @else
                                            return "";
                                        @endif
                                    break;
                                case 'po_number':
                                    let dataTpl = (!data || data == 'null') ? '' : data;
                                    return '<a class="text-popup wd-200 text-dark" href="" data-content="'+row.sap_message+'">'+dataTpl+'</a>';
                                default:
                                    return data;
                            }
                        },
                        "targets": "_all"
                    },
                    { width: 2000, "targets": ['submission_date'] }
                ],
            };
            //## Initilalize Datatables
            SELF.table = $(SELF.dtSelector).DataTable(options);

            $(SELF.tabSelector + ' tbody').on('click','.text-popup', function(e){
                e.preventDefault();
                $('.alert .close').click();
                let message = $(this).data('content');
                if(message){
                    message = message.replace(/\n/g, "<br />");
                }
                showAlert(message, "warning", -1);
            });
            $(SELF.tabSelector + " .page_numbers").ready(function () {
                $(SELF.dtSelector + "_paginate").appendTo($(SELF.tabSelector +" .page_numbers"));
                $(SELF.dtSelector + "_info").css("padding", ".375rem .75rem").appendTo($(SELF.tabSelector + " .page_numbers"));
            });
        };

        this.dtDetailDocSelector = options.dtDetailDocSelector;
        this.tableDetailDoc = null;
        this.initVendorDocument = function(data){
            if(typeof this._initVendorDocument === "function"){
                this._initVendorDocument(data);
                return;
            }
            let SELF = this;
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
                    },
                    {
                        data: 'status', name: 'status',className: 'text-center',
                        render: function (data, type, row, meta) {
                            if(row.attachment && row.attachment!=''){
                                if(SELF.editable){
                                    let statusAccepted = "{{\App\Models\TenderVendorSubmissionDetail::STATUS[3]}}";
                                    let statusRejected = "{{\App\Models\TenderVendorSubmissionDetail::STATUS[4]}}";
                                    let _tpl = '<div class="btn-group btn-group-toggle" data-toggle="buttons">' +
                                        '<label class="btn btn-outline-danger '+(data == statusRejected ? 'active' : '')+'">' +
                                            '<input type="radio" name="evaluate" value="'+statusRejected+'" autocomplete="off"> {{__('common.rejected')}}' +
                                        '</label>' +
                                        '<label class="btn btn-outline-success '+(data == statusAccepted ? 'active' : '')+'">' +
                                            '<input type="radio" name="evaluate" value="'+statusAccepted+'" autocomplete="off"> {{__('common.accepted')}}' +
                                        '</label>' +
                                    '</div>';
                                    return _tpl;
                                }else{
                                    return renderStatus(data, type, row, meta);
                                }
                            }
                            return '';
                        }
                    },
                ],
            };
            if(SELF.tableDetailDoc != null){
                SELF.tableDetailDoc.destroy();
            }
            SELF.tableDetailDoc = $(SELF.dtDetailDocSelector).DataTable(options);
        }

        this.dtDetailItemSelector = options.dtDetailItemSelector;
        this.tableDetailItem = null;
        this.tableItemSelectedRow = null;
        this.initVendorItem = function(data){
            options.initVendorItem(this, data);
        };
        this.initVendorHeader = options.initVendorHeader;

        this.init = function(){
            let SELF = this;

            // action tableItem
            SELF.initActionItem();

            $(SELF.dtSelector + ' tbody').on('click','.openDetail', function(e){
                e.preventDefault();
                let dtrow = SELF.table.row($(this).parents('tr')).data();
                SELF.selectedRow = dtrow;
                $(SELF.tabSelector + ' .card-submission').hide();
                $(SELF.tabSelector + ' .app-footer-left.page-number').hide();
                $(SELF.tabSelector + ' .card-submission-detail').show();
                $(SELF.tabSelector + ' .button-detail').show();
                $(SELF.tabSelector + ' .button-header').hide();

                SELF.initVendorDocument(dtrow);
                $(SELF.tabSelector + ' .vendor-title').text(dtrow.vendor_name);

                SELF.initVendorItem(dtrow);
                SELF.initVendorHeader.loadData(dtrow);

                TenderComments.selector = SELF.tabSelector + ' .btn_comment';
                TenderComments.loadData(SELF.selectedRow.vendor_code, SELF.stageType, true);
                $('#btn_item_detail').attr('href', "{{ route('tender.show', ['id' => $id, 'type' => $type, 'action' => 'detail-specification']) }}/"+dtrow.vendor_id);
            });
            $(SELF.tabSelector + ' .btn_back_to').on('click',function(e){
                e.preventDefault();
                $(SELF.tabSelector + ' .card-submission').show();
                $(SELF.tabSelector + ' .app-footer-left.page-number').show();
                $(SELF.tabSelector + ' .card-submission-detail').hide();
                $(SELF.tabSelector + ' .button-detail').hide();
                $(SELF.tabSelector + ' .button-header').show();
                SELF.selectedRow = null;
            });
            $(SELF.tabSelector + ' .btn_log').on('click',function(e){
                e.preventDefault();
                $('#popup-history').modal('show');
            });
            $(SELF.tabSelector + ' .btn_comment').on('click',function(e){
                e.preventDefault();
                $('#popup-comments textarea[name="comments"]').val('');
                $('#popup-comments').modal('show');
                $('#popup-comments .message-list').animate({ scrollTop: 10000 }, 500);
            });
            $(SELF.tabSelector + ' .btn_scoring').on('click',function(e){
                e.preventDefault();
                $('#popup-scoring').modal('show');
            });
            @if($statusProcess == 'opened-3' || $statusProcess == 'opened-4')
            $(SELF.tabSelector + ' .dt-bid-doc-requirement tbody').on('click','input[name="evaluate"]', function(e){
                e.preventDefault();
                let dtrow = SELF.tableDetailDoc.row($(this).parents('tr')).data();
                let data = {
                    'action_type' : 'evaluate-submission-detail',
                    stage_type : TabSelected.stageType,
                    'id' : dtrow.id,
                    'status': $(this).val(),
                };
                submit(data, function(){}, $(e.target).parents('td'));
            });
            $(SELF.tabSelector + ' .btn_request_resubmission').on('click',function(e){
                e.preventDefault();
                $('#delete_modal .modal-title').text("{{__('tender.'.$type)}}");
                let _body = $('<div class="alert alert-warning" role="alert">{{__('tender.process.message_start_bidding')}}</div>')
                $('#delete_modal .modal-body').html(_body);
                $('#btn_delete_modal').click();
                $('#delete_modal #btn_confirm').off('click').on('click', function () {
                    let data = {action_type : '{{\App\Enums\TenderSubmissionEnum::FLOW_STATUS[3]}}', stage_type : TabSelected.stageType};
                    submit(data, function(){
                        $('#delete_modal .close').click();
                        Loading.Show();
                        location.href = "{{ route('tender.save', ['id'=>$id, 'type' => $type]) }}" + SELF.tabSelector.replace('-content', '');
                        location.reload();
                    });
                    return false;
                });
            });
            $(SELF.tabSelector + ' .btn_finish_tab').on('click',function(e){
                e.preventDefault();
                $('#delete_modal .modal-title').text("{{__('tender.'.$type)}}");
                let _body = $('<div class="alert alert-warning" role="alert">{{__('tender.process.message_start_bidding')}}</div>')
                $('#delete_modal .modal-body').html(_body);
                $('#btn_delete_modal').click();
                $('#delete_modal #btn_confirm').off('click').on('click', function () {
                    let data = {action_type : '{{\App\Enums\TenderSubmissionEnum::FLOW_STATUS[5]}}', stage_type : TabSelected.stageType};
                    submit(data, function(){
                        $('#delete_modal .close').click();
                        Loading.Show();
                        location.href = "{{ route('tender.save', ['id'=>$id, 'type' => $type]) }}" + SELF.tabSelector.replace('-content', '');
                        location.reload();
                    });
                    return false;
                });
            });
            @endif
        };
        this.initActionItem = function(enableDoc){
            let SELF = this;
            if(typeof(options.initActionItem) === "function"){
                options.initActionItem(SELF);
            }else{
                $(SELF.dtDetailItemSelector + ' tbody').on('click','.open-detail', function(e){
                    e.preventDefault();
                    SELF.tableItemSelectedRow = SELF.tableDetailItem.row($(this).parents('tr'));
                    SELF.openItemDetailRow(SELF.tableItemSelectedRow.data());
                });
            }
        };
        this.openItemDetailRow = function(dtrow){
            $('#formItemDetail_modal .modal-title').html('PR ' + dtrow.number + ' / ' + dtrow.line_number );
            $('#formItemDetail_modal .title-left').html('PR ' + dtrow.number);
            $('#formItemDetail_modal .title-right').html(dtrow.line_number);
            $('#pr-item input[name="id"]').val(dtrow.id);

            if(typeof options.openItemDetailRow == 'function'){
                options.openItemDetailRow(this, PRListField, dtrow);
            }else{
                for(let ix in PRListField){
                    switch(PRListField[ix]){
                        case 'qty':
                            $('#formItemDetail_modal #pr-item #' + PRListField[ix]).html(dtrow.qty_vendor);
                            break;
                        case 'description':
                            $('#formItemDetail_modal #pr-item #' + PRListField[ix]).html(dtrow.description_vendor);
                            break;
                        case 'price_unit':
                            $('#formItemDetail_modal #pr-item #' + PRListField[ix]).html(dtrow.price_unit_vendor);
                            break;
                        case 'est_unit_price':
                            $('#formItemDetail_modal #pr-item #' + PRListField[ix]).html(dtrow.est_unit_price_vendor);
                            break;
                        case 'overall_limit':
                            $('#formItemDetail_modal #pr-item #' + PRListField[ix]).html(dtrow.overall_limit_vendor);
                            break;
                        case 'currency_code':
                            $('#formItemDetail_modal #pr-item #' + PRListField[ix]).html(dtrow['currency_code_vendor']);
                            break;
                        default:
                            $('#formItemDetail_modal #pr-item #' + PRListField[ix]).html(dtrow[PRListField[ix]]);
                            break;
                    }
                }
                $('#formItemDetail_modal #pr-item select[name="compliance"]').val(dtrow.compliance);
                $('#formItemDetail_modal textarea[name="item_text"]').prop('disabled', true);
                $('#formItemDetail_modal #tax-item .form-area').prop('hidden', true);
                $('#formItemDetail_modal #cost-item .form-area').prop('hidden', true);
            }
            $('#formItemDetail_modal').modal();
        };
        this.initComplete = function(settings, json){
            let SELF = this;
            let detail = $( SELF.dtSelector + ' tbody .openDetail');
            // console.log(SELF.vendorSelected);
            if(SELF.vendorSelected != null && SELF.vendorSelected != ''){
                for(let ix in detail){
                    if($(detail[ix]).data('vendorId') == SELF.vendorSelected){
                        $(detail[ix]).trigger( "click" );
                        break;
                    }
                }
            }
            if(SELF.tabSelector=='#awarding-content'){
                SELF.table.ajax.reload();
            }
        }
    };
    var TabEvaluation = {
        table : null,
        tableItem : null,
        tableSummary : null,
        initTable : function(){
            let SELF = this;
            let dtOptions = getDTOptions();
            let _url = "{{ route('tender.dataItem', ['id' => $id, 'type' => $type])}}";

            let options = {
                deferRender: dtOptions.deferRender,
                rowId: dtOptions.rowId,
                lengthChange: false,
                searching: false,
                processing: true,
                paging : true,
                language: dtOptions.language,
                ajax : _url,
                // fixedColumns: true,
                rowCallback: function(row, data){
                    if(Object.keys(data).includes("score_tc")){
                        $('td:eq(2)', row).html(parseFloat(data.score_tc).formatMoney(2, ".", ","));
                        $('td:eq(3)', row).html(parseFloat(data.score_com).formatMoney(2, ".", ","));
                    }
                    if(Object.keys(data).includes("score")){
                        $('td:eq(2)', row).html(parseFloat(data.score).formatMoney(2, ".", ","));
                    }
                },
                columns: [
                    @foreach ($tenderData[$type]['fields3'] as $field)
                    {data: '{{$field}}', name: '{{$field}}', "width": {{$field == 'vendor_name' ? 200 : 100}}},
                    @endforeach
                    {data: 'status', name: 'status', "width": 50, className: 'text-center'},
                ],
                columnDefs:[
                    {
                        "render": function ( data, type, row, dt ) {
                            var column = dt.settings.aoColumns[dt.col].data;
                            switch(column){
                                case 'status':
                                    @if($enableBtnFinish && !in_array($tender->commercial_approval_status,[\App\Enums\TenderSubmissionEnum::STATUS_ITEM[2]]))
                                    let _tpl = data;
                                    if(type==='display'){
                                        let statusPassed = "{{\App\Models\TenderVendorSubmission::STATUS[3]}}";
                                        let statusNotPassed = "{{\App\Models\TenderVendorSubmission::STATUS[4]}}";
                                        _tpl = '<div class="btn-group btn-group-toggle" data-toggle="buttons">' +
                                            '<label class="btn btn-outline-danger '+(data == statusNotPassed ? 'active' : '')+'">' +
                                                '<input type="radio" name="evaluate" value="'+statusNotPassed+'" autocomplete="off"> {{__('common.not_passed')}}' +
                                            '</label>' +
                                            '<label class="btn btn-outline-success '+(data == statusPassed ? 'active' : '')+'">' +
                                                '<input type="radio" name="evaluate" value="'+statusPassed+'" autocomplete="off"> {{__('common.passed')}}' +
                                            '</label>' +
                                        '</div>';
                                    }
                                    return _tpl;
                                    @else
                                    return renderStatus(data, type, row, dt)
                                    @endif

                                default:
                                    return data;
                            }
                        },
                        "targets": "_all"
                    },
                    { width: 200, "targets": ['submission_date'] },
                    { className: 'text-center', "targets": ['score'] }
                ],
            };
            //## Initilalize Datatables
            SELF.table = $('#dt-evaluation-vendor').DataTable(options);
            $("#evaluation-content .page_numbers").ready(function () {
                $("#dt-evaluation-vendor_paginate").appendTo($("#evaluation-content .page_numbers"));
                $("#dt-evaluation-vendor_info").css("padding", ".375rem .75rem").appendTo($("#evaluation-content .page_numbers"));
            });
        },
        initTableItem : function(isTechnical){
            let SELF = this;
            let dtOptions = getDTOptions();
            let _url = "{{ route('tender.dataItem', ['id' => $id, 'type' => $type]) }}";
            let _columns = [
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
                {data: 'description_vendor', name: 'description_vendor', "width": 150},
                {
                    data: 'qty_vendor', name: 'qty_vendor', "width": 75,
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
                        if(row.item_category == 0){
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
                        if(row.item_category != 0){
                            total = parseFloat(row.total_overall_limit_vendor) + parseFloat(row.additional_cost);
                        }
                        return formatCurrency(total, row.currency_code_vendor);
                    }
                },
            ];
            if(isTechnical != null && isTechnical == true) {
                _columns = [
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
                    {data: 'description_vendor', name: 'description_vendor', "width": 150},
                    {
                        data: 'qty_vendor', name: 'qty_vendor', "width": 30,
                        render: function (data, type, row, meta) {
                            return formatQty(data);
                        }
                    },
                ];
            }
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
                    // rightColumns: 1
                },
                orderFixed: [0, 'asc'],
                rowsGroup: [
                    'description:name',
                    'description:name',
                    'number:name',
                    'line_number:name',
                    'product_code:name',
                    'product_group_code:name'
                ],
                columns: _columns,
            };
            if(SELF.tableItem != null){
                SELF.tableItem.destroy();
            }
            SELF.tableItem = $('#dt-evaluation-items').DataTable(options);
        },
        initTableSum : function(isTechnical){
            let SELF = this;
            let dtOptions = getDTOptions();
            let _url = "{{ route('tender.dataItem', ['id' => $id, 'type' => $type]) }}";
            let _columns = [
                {data: 'vendor_code', name: 'vendor_code', "width": 50},
                {data: 'vendor_name', name: 'vendor_name', "width": 275},
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
                        // if(!isNaN(row.total_additional_cost)){
                        //     total += parseFloat(row.total_additional_cost);
                        // }
                        return formatCurrency(total, row.currency_code_vendor);
                    }
                },
                {
                    data: 'total_overall_limit_vendor', name: 'total_overall_limit_vendor', "width": 100,
                    render: function (data, type, row, meta) {
                        let total = !isNaN(data) ? parseFloat(data) : 0;
                        // if(!isNaN(row.total_additional_cost)){
                        //     total += parseFloat(row.total_additional_cost);
                        // }
                        return formatCurrency(total, row.currency_code_vendor);
                    }
                },
                {data: 'currency_code_vendor', name: 'currency_code_vendor',"width": 75},
            ];
            if(isTechnical != null && isTechnical == true){
                _columns = [
                    {data: 'vendor_code', name: 'vendor_code', "width": 50},
                    {data: 'vendor_name', name: 'vendor_name', "width": 275},
                    {
                        data: 'total_qty_vendor', name: 'total_qty_vendor', "width": 100,
                        render: function (data, type, row, meta) {
                            let total = !isNaN(data) ? parseFloat(data) : 0;
                            return parseFloat(total).formatMoney(3, ".", ",");
                        }
                    },
                ];
            }
            let options = {
                deferRender: dtOptions.deferRender,
                rowId: dtOptions.rowId,
                lengthChange: false,
                searching: false,
                processing: true,
                paging: false,
                // fixedColumns: true,
                language: dtOptions.language,
                ajax : _url + '?action_type=summary-items',
                columns: _columns,
            };
            if(SELF.tableSummary != null){
                SELF.tableSummary.destroy();
            }
            SELF.tableSummary = $('#dt-evaluation-summary').DataTable(options);
        },

        reload : function(){
            let SELF = this;
            SELF.table.ajax.reload();
            SELF.tableItem.ajax.reload();
            SELF.tableSummary.ajax.reload();

            SELF.table.columns.adjust().draw();
            SELF.tableItem.columns.adjust().draw();
            SELF.tableSummary.columns.adjust().draw();
        }
    };
    var EvaluationNote = {
        data : null,
        loadData : function(noteType){
            let SELF = this;
            let selector = '#popup-evaluation .modal-content';
            let _url = "{{ route('tender.dataItem', ['id' => $id, 'type' => $type]) }}?action_type=evaluation-notes"
            if(SELF.data == null || SELF.data.length == 0){
                $.ajax({
                    url : _url + '&note_type='+noteType,
                    type : 'GET',
                    contentType: "application/json; charset=utf-8",
                    dataType: "json",
                    beforeSend: function( xhr ) {
                        Loading.Show(selector);
                    }
                }).done(function(response, textStatus, jqXhr) {
                    if(response.success){
                        SELF.data = response.data;
                        SELF.renderData(SELF.data);
                    }
                }).fail(defaultAjaxFail)
                .always(function(jqXHR, textStatus, errorThrown) {
                    Loading.Hide(selector);
                });
            }else{
                SELF.renderData(SELF.data);
            }
        },
        renderData : function(data){
            if(data){
                $('#popup-evaluation textarea[name="evaluation_notes"]').val(data.notes);
            }
        }
    };
    var TableScoring = {
        table : null,
        initTable : function(vendorId){
            let SELF = this;
            let dtOptions = getDTOptions();
            let _url = "{{ route('tender.dataItem', ['id' => $id, 'type' => $type]) }}?action_type=submission-scoring&stage_type="+TabSelected.stageType;
            let options = {
                deferRender: dtOptions.deferRender,
                rowId: dtOptions.rowId,
                lengthChange: false,
                searching: false,
                processing: true,
                "paging":   false,
                "ordering": false,
                "info":     false,
                language: dtOptions.language,
                ajax : _url + '&vendor_id='+vendorId,
                responsive: true,
                drawCallback: function(settings){
                    initInputScore();
                },
                columns: [
                    {
                        data: 'id', name: 'id',"width": 50,"className": 'text-center',
                        render: function (data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {data: 'criteria', name: 'criteria'},
                    {
                        data: 'weight', name: 'weight',"width": 100,
                        render: function (data, type, row, meta) {
                            return (data) ? (formatPercentage(data) + ' %') : '';
                        }
                    },
                    {
                        data: 'score', name: 'scoring',"width": 100,
                        render : function(data, type, row, meta){
                            data = data ? formatScore(data) : '';
                            if(TabSelected.editable){
                                return '<input type="number" class="form-control form-control-sm input-score" min="0" max="100" name="row-'+row.id+'" value="'+data+'" />';
                            }
                            return data;
                        }
                    },
                ],
            };

            if(SELF.table != null){
                SELF.table.destroy();
            }
            SELF.table = $('#dt-submission-scoring').DataTable(options);
        },
    };
    submit = function(data, callback, selector){
        let _url = "{{ route('tender.save', ['id'=>$id, 'type'=>$type]) }}";
        $.ajax({
            url : _url,
            type : 'POST',
            data : JSON.stringify(data),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            beforeSend: function( xhr ) {
                Loading.Show(selector);
            }
        }).done(function(response, textStatus, jqXhr) {
            if(response.success){
                if(response.sap_result){
                    if(typeof callback == 'function') return callback(response);
                }else{
                    if(typeof callback == 'function') callback(response);
                    showAlert("Document saved.", "success", 3000);
                }
            }else{
                showAlert("Document not saved.", "danger", 3000);
            }
        }).fail(defaultAjaxFail)
        .always(function(jqXHR, textStatus, errorThrown) {
            Loading.Hide(selector);
        });
    }
    renderStatus = function(data, type, row, dt){
        if(row.status == "{{\App\Models\TenderVendorSubmission::STATUS[3]}}"){
            // return '<span class="badge badge-pill badge-success">'+row.status_text+'</span>';
            return '<p class="text-success" style="margin-bottom: 0;">'+row.status_text+'</p>';
        }else if(row.status == "{{\App\Models\TenderVendorSubmission::STATUS[4]}}"){
            return '<p class="text-danger" style="margin-bottom: 0;">'+row.status_text+'</p>';
        }else{
            return '<p class="text-secondary" style="margin-bottom: 0;">'+row.status_text+'</p>';
        }
    }
    renderDeleteFlg = function(data, type, row, dt){
        let _tpl = data;
        if(data == 'x' || data == 'X'){
            _tpl = '<i class="fa fa-check"></i>';
        }
        return _tpl;
    }
    renderAwardingStatus = function(data, type, row, dt){
        switch(row.awarding_status){
            case "{{\App\Models\TenderVendorAwarding::STATUS[1]}}":
                return '<p class="text-success" style="margin-bottom: 0;">'+row.awarding_status_text+'</p>';
                break;
            case "{{\App\Models\TenderVendorAwarding::STATUS[2]}}":
                return '<p class="text-danger" style="margin-bottom: 0;">'+row.awarding_status_text+'</p>';
                break;
            default:
                return '<p class="text-secondary" style="margin-bottom: 0;">'+row.awarding_status_text+'</p>';
                break;
        }
    }
    initLoad = function(){
        $('button.btn_next_flow').click(function(){
            onClickNext();
        });
        $('#popup-evaluation .btn-save').click(function(e){
            let data = {
                id : EvaluationNote.data ? EvaluationNote.data.id : null,
                action_type : 'save-evaluation-notes',
                notes : $('#popup-evaluation textarea[name="evaluation_notes"]').val() || '',
                note_type : 1,
            };
            submit(data, function(response){
                $('#popup-evaluation').modal('hide');
                EvaluationNote.data = response.data;
            });
            return false;
        });
        $('#popup-comments .btn-save').click(function(e){
            let data = {
                action_type : 'save-comments',
                stage_type : TabSelected.stageType,
                to : TabSelected.selectedRow.vendor_code,
                comments : $('#popup-comments textarea[name="comments"]').val() || '',
            };
            submit(data, function(response){
                $('#popup-comments textarea[name="comments"]').val('');
                TenderComments.data[TabSelected.selectedRow.vendor_code] = response.data;
                TenderComments.loadData(TabSelected.selectedRow.vendor_code, TabSelected.stageType);
                $('#popup-comments .message-list').animate({ scrollTop: 10000 }, 500);
            });
            return false;
        });
        $('#popup-scoring .btn-save').click(function(e){
            let _scores = TableScoring.table.rows().data();
            let params = [];
            for(let ix=0;ix<_scores.length;ix++) {
                let score = $('input[name="row-'+_scores[ix].id+'"]').val();
                if(score > 100){
                    showAlert("Max score is 100.", "warning", 3000);
                    return false;
                }
                params.push({
                    'tender_number' : _scores[ix].tender_number,
                    'weight_id' : _scores[ix].line_id,
                    'vendor_id' : _scores[ix].vendor_id,
                    'vendor_code' : _scores[ix].vendor_code,
                    'score' : $('input[name="row-'+_scores[ix].id+'"]').val(),
                    'weight' : parseInt(_scores[ix].weight),
                });
            }
            let data = {
                action_type : 'save-scoring',
                stage_type : TabSelected.stageType,
                scores : params,
            };
            submit(data, function(response){
                TableScoring.table.ajax.reload();
                $('#popup-scoring').modal('hide');
            });
            return false;
        });
        $('button.btn_evaluate_note').on('click',function(e){
            e.preventDefault();
            $('#popup-evaluation').modal('show');
        });
        $('#popup-history').on("shown.bs.modal", function () {
            let _url = "{{ route('tender.dataItem', ['id' => $id, 'type' => $type]) }}?action_type=submission-history";
            _url += '&stage_type=' + TabSelected.stageType;
            TableHistory.initTable(TabSelected.selectedRow.vendor_id, _url);
        });
        $('#popup-evaluation').on("shown.bs.modal", function () {
            EvaluationNote.loadData(1); // load evaluation notes
        });
        $('#popup-scoring').on("shown.bs.modal", function () {
            TableScoring.initTable(TabSelected.selectedRow.vendor_id);
            if(TabSelected.editable){
                $('#popup-scoring .modal-footer').show();
            }else{
                $('#popup-scoring .modal-footer').hide();
            }
        });
        // btn_print
        $('#btn_print').on("click", function () {
            let _url = "{{ route('tender.print', ['id' => $id, 'type' => $type]) }}";
            $(this).attr('href', _url);
        });
        $('#btn_print_tbe').on("click", function () {
            let _url = "{{ route('tender.print', ['id' => $id, 'type' => $type, 'print' => 'tbe']) }}";
            $(this).attr('href', _url);
        });
        $('#btn_print_cbe').on("click", function () {
            let _url = "{{ route('tender.print', ['id' => $id, 'type' => $type, 'print' => 'cbe']) }}";
            $(this).attr('href', _url);
        });
        $('.btn_print_nbe').on("click", function () {
            let _url = "{{ route('tender.print', ['id' => $id, 'type' => $type, 'print' => 'nbe']) }}";
            $(this).attr('href', _url);
        });
        $('#dt-evaluation-vendor tbody').on('click','input[name="evaluate"]', function(e){
            e.preventDefault();
            let dtrow = TabEvaluation.table.row($(this).parents('tr')).data();
            let data = {
                'action_type' : 'evaluate-submission',
                'id' : dtrow.id,
                'vendor_id' : dtrow.vendor_id,
                'status': $(this).val(),
            };
            submit(data, function(){
                $('#delete_modal .close').click();
            }, $(e.target).parents('td'));
        });

        @if(isset(\App\Enums\TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE[$type]) && in_array(\App\Enums\TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE[$type],[2,4]))
        $('button.btn_finish').on('click',function(e){
            e.preventDefault();
            $('#delete_modal .modal-title').text("{{__('tender.process.btn_evaluation_finish')}}");
            let _body = $('<div class="alert alert-warning" role="alert">{{__('tender.process.message_start_bidding')}}</div>')
            $('#delete_modal .modal-body').html(_body);
            $('#btn_delete_modal').click();
            $('#delete_modal #btn_confirm').off('click').on('click', function () {
                let _url = "{{ route('tender.save', ['id'=>$id, 'type' => $type]) }}";
                let params = {
                    'action_type': 'commercialSignature',
                    'subaction': 'submit'
                };
                $.ajax({
                    url : _url,
                    type : 'POST',
                    data : JSON.stringify(params),
                    contentType: "application/json; charset=utf-8",
                    dataType: "json",
                    beforeSend: function( xhr ) {
                        Loading.Show();
                    }
                }).done(function(response, textStatus, jqXhr) {
                    if(response.success){
                        $('#delete_modal .close').click();
                        refreshPopupApprovalSubmitted();
                        $('button.btn_finish').prop('disabled',true);
                        Loading.Show();
                        location.href = "{{ route('tender.save', ['id'=>$id, 'type' => $type]) }}#evaluation";
                        location.reload();
                        showAlert("Document saved.", "success", 3000);
                    }else{
                        showAlert("Document not saved.", "danger", 3000);
                    }
                }).fail(defaultAjaxFail)
                .always(function(jqXHR, textStatus, errorThrown) {
                    Loading.Hide();
                });
            });
        });
        @else
        $('button.btn_finish').on('click',function(e){
            e.preventDefault();
            $('#delete_modal .modal-title').text("{{__('tender.process.btn_evaluation_finish')}}");
            let _body = $('<div class="alert alert-warning" role="alert">{{__('tender.process.message_start_bidding')}}</div>')
            $('#delete_modal .modal-body').html(_body);
            $('#btn_delete_modal').click();
            $('#delete_modal #btn_confirm').off('click').on('click', function () {
                let data = {action_type : '{{\App\Enums\TenderSubmissionEnum::FLOW_STATUS[6]}}'};
                submit(data, function(){
                    $('#delete_modal .close').click();
                    Loading.Show();
                    location.href = "{{ route('tender.save', ['id'=>$id, 'type' => $type]) }}#evaluation";
                    location.reload();
                });
                return false;
            });
        });
        @endif
        // $('#btn_item_detail').click(function(e){
        //     e.preventDefault();
        // });
    };
    var initTab = function(TabID){
        let queryParams = new URLSearchParams(window.location.search);
        var urlParams = window.location.href.split('#');
        if(urlParams && urlParams.length > 1){
            $( '#' + TabID +' li > a#' + urlParams[1] + '-tab.nav-link').tab('show');
        }else if(queryParams && queryParams.get('tab') != ''){
            $( '#' + TabID +' li > a#' + queryParams.get('tab') + '-tab.nav-link').tab('show');
        }else{
            $(Tabs[0]).tab('show');
            if(TabOverview.table == null){
                TabOverview.initTable();
            }
        }
        ItemDetailPage.init();
        $('#formItemDetail_modal').on("shown.bs.modal", function () {
            try{
                ItemDetailPage.resetForm();
                ItemDetailPage.reloadTable(TabSelected.selectedRow.vendor_id);
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
                    FormCostPage.reloadTable(TabSelected.selectedRow.vendor_id);
                }catch{}
            });
        @endif
    };
</script>
@include('tender.form.tender_process.items')
