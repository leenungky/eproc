

@section('contentbody')
<div class="tab">
    <ul class="nav nav-tabs" id="prequalification-tab" role="tablist">
        <li class="nav-item" id="overview-li">
            <a class="nav-link" id="overview-tab" data-toggle="tab" href="#overview-content" role="tab"
                aria-controls="overview" aria-selected="true">{{__('tender.process.tab_title_overview')}}</a>
        </li>
        <li class="nav-item" id="document-li">
            <a class="nav-link @if(!$docTabEnable) disabled @endif" id="document-tab" data-toggle="tab" href="#document-content" role="tab"
                aria-controls="document" aria-selected="true">{{__('tender.process.tab_title_document')}}</a>
        </li>
        <li class="nav-item" id="evaluation-li">
            <a class="nav-link @if(!$evalTabEnable) disabled @endif" id="evaluation-tab" data-toggle="tab" href="#evaluation-content" role="tab"
                aria-controls="evaluation" aria-selected="true">{{__('tender.process.tab_title_evaluation')}}</a>
        </li>
    </ul>

    <div class="tab-content" id="tab-prequalification">
        <div class="tab-pane fade" id="overview-content" role="tabpanel" aria-labelledby="pre_qualification_weight-tab">
            @include('tender.form.prequalification.admin_tab_overview')
        </div>
        <div class="tab-pane fade" id="document-content" role="tabpanel" aria-labelledby="offer_weight-tab">
            @include('tender.form.prequalification.admin_tab_document')
        </div>
        <div class="tab-pane fade" id="evaluation-content" role="tabpanel" aria-labelledby="offer_weight-tab">
            @include('tender.form.prequalification.admin_tab_evaluation')
        </div>
    </div>
</div>
@endsection

@section('modules-scripts')
@parent
<script type="text/javascript">
var table;
require(["datatablesb4","dt.plugin.select",'datetimepicker'], function(datetimepicker){
    var Tabs = $('#prequalification-tab li > a.nav-link');
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
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
                ajax : "{{ route('tender.dataItem', ['id' => $id, 'type' => $type]) }}",
                columns: [
                    @foreach ($tenderData['process_prequalification']['fields2'] as $field)
                    {data: '{{$field}}', name: '{{$field}}', "width": {{$field == 'vendor_name' ? 0 : 100}}},
                    @endforeach
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
    var TabDocument = {
        table : null,
        selectedRow: null,
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
                ajax : "{{ route('tender.dataItem', ['id' => $id, 'type' => $type]) }}",
                fixedColumns: true,
                columns: [
                    @foreach ($tenderData['process_prequalification']['fields2'] as $field)
                    {data: '{{$field}}', name: '{{$field}}', "width": {{$field == 'vendor_name' ? 0 : 100}}},
                    @endforeach
                ],
                columnDefs:[
                    {
                        "render": function ( data, type, row, dt ) {
                            var column = dt.settings.aoColumns[dt.col].data;
                            let tmp = data;
                            switch(column){
                                case 'status':
                                return renderStatus(data, type, row, dt)
                                case 'submission_date':
                                    let _tpl = '<a class="btn btn-link float-left openDetail" href="#">'+data+'</a>';
                                    return _tpl;
                                default:
                                    return data;
                            }
                        },
                        "targets": "_all"
                    },
                    { width: 200, "targets": ['submission_date'] }
                ],
            };
            //## Initilalize Datatables
            SELF.table = $('#dt-document-vendor').DataTable(options);
            $("#document-content .page_numbers").ready(function () {
                $("#dt-document-vendor_paginate").appendTo($("#document-content .page_numbers"));
                $("#dt-document-vendor_info").css("padding", ".375rem .75rem").appendTo($("#document-content .page_numbers"));
            });
        },
    };
    var TabEvaluation = {
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
                ajax : "{{ route('tender.dataItem', ['id' => $id, 'type' => $type]) }}",
                fixedColumns: true,
                columns: [
                    @foreach ($tenderData['process_prequalification']['fields3'] as $field)
                    {data: '{{$field}}', name: '{{$field}}', "width": {{$field == 'vendor_name' ? 0 : 100}}},
                    @endforeach
                    {data: 'status', name: 'status', "width": 250, className: 'text-center'},
                ],
                columnDefs:[
                    {
                        "render": function ( data, type, row, dt ) {
                            var column = dt.settings.aoColumns[dt.col].data;
                            switch(column){
                                case 'status':
                                    @if($statusProcess == 'opened-pq' && $canFinish)
                                    let statusPassed = "{{\App\Models\TenderVendorSubmission::STATUS[3]}}";
                                    let statusNotPassed = "{{\App\Models\TenderVendorSubmission::STATUS[4]}}";
                                    let _tpl = '<div class="btn-group btn-group-toggle" data-toggle="buttons">' +
                                        '<label class="btn btn-outline-danger '+(data == statusNotPassed ? 'active' : '')+'">' +
                                            '<input type="radio" name="evaluate" value="'+statusNotPassed+'" autocomplete="off"> {{__('tender.process_status.not_passed')}}' +
                                        '</label>' +
                                        '<label class="btn btn-outline-success '+(data == statusPassed ? 'active' : '')+'">' +
                                            '<input type="radio" name="evaluate" value="'+statusPassed+'" autocomplete="off"> {{__('tender.process_status.passed')}}' +
                                        '</label>' +
                                    '</div>';
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
    };
    var TableDetail = {
        table : null,
        initTable : function(data){
            let SELF = this;
            let dtOptions = getDTOptions();
            let _url = "{{ route('tender.dataItem', ['id' => $id, 'type' => $type]) }}?action_type=submission-detail-admin"
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
                                @if($statusProcess == 'opened-pq')
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
                                @else
                                if(row.status == "{{\App\Models\TenderVendorSubmissionDetail::STATUS[3]}}"){
                                    return '<p class="text-success">'+row.status_text+'</p>';
                                }else if(row.status == "{{\App\Models\TenderVendorSubmissionDetail::STATUS[4]}}"){
                                    return '<p class="text-danger">'+row.status_text+'</p>';
                                }else{
                                    return '<p class="text-secondary">'+row.status_text+'</p>';
                                }
                                @endif
                                return row.status;
                            }
                            return '';
                        }
                    },
                ],
            };
            if(SELF.table != null){
                SELF.table.destroy();
            }
            SELF.table = $('#dt-bid-doc-requirement').DataTable(options);
        },
    };
    var EvaluationNote = {
        data : null,
        loadData : function(noteType){
            let SELF = this;
            let selector = '#popup-evaluation .modal-content';
            let _url = "{{ route('tender.dataItem', ['id' => $id, 'type' => $type]) }}?action_type=evaluation-notes"
            if(SELF.data == null || SELF.data.length == 0){
                $.ajax({
                    url : _url + '&note_type='+noteType+'&stage_type=1',
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
            let _url = "{{ route('tender.dataItem', ['id' => $id, 'type' => $type]) }}?action_type=submission-scoring"
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
                            return data + ' %';
                        }
                    },
                    {
                        data: 'score', name: 'scoring',"width": 100,
                        @if($statusProcess == 'opened-pq')
                        render : function(data, type, row, meta){
                            return '<input type="number" class="form-control form-control-sm input-score" min="0" max="100" name="row-'+row.id+'" value="'+data+'" />';
                        }
                        @endif
                    },
                ],
            };
            if(SELF.table != null){
                SELF.table.destroy();
            }
            SELF.table = $('#dt-submission-scoring').DataTable(options);
        },
    };

    $('button.btn_next_flow').click(function(){
        onClickNext();
    });
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        if(e.target.id == 'overview-tab'){
            if(TabOverview.table == null){
                TabOverview.initTable();
            }else{
                console.log('draw');
                TabOverview.table.draw();
                // TabOverview.table.columns.adjust()
                //     .fixedColumns().relayout();
            }
        }
        if(e.target.id == 'document-tab'){
            if(TabDocument.table == null){
                TabDocument.initTable();
            }
            $('#btn_back_to').trigger('click');
        }
        if(e.target.id == 'evaluation-tab'){
            if(TabEvaluation.table == null){
                TabEvaluation.initTable();
            }else{
                TabEvaluation.table.ajax.reload();
            }
        }
    });
    $('#dt-document-vendor tbody').on('click','.openDetail', function(e){
        e.preventDefault();
        let dtrow = TabDocument.table.row($(this).parents('tr')).data();
        TabDocument.selectedRow = dtrow;
        $('#card-submission').hide();
        $('#document-content .app-footer-left.page-number').hide();
        $('#card-submission-detail').show();
        $('#document-content .button-detail').show();
        $('#document-content .button-header').hide();

        TableDetail.initTable(dtrow);
        $('.vendor-title').text(dtrow.vendor_name);
        TenderComments.loadData(TabDocument.selectedRow.vendor_code);
    });
    $('#btn_back_to').on('click',function(e){
        e.preventDefault();
        $('#card-submission').show();
        $('#document-content .app-footer-left.page-number').show();
        $('#card-submission-detail').hide();
        $('#document-content .button-detail').hide();
        $('#document-content .button-header').show();
        TabDocument.selectedRow = null;

    });
    $('#btn_log').on('click',function(e){
        e.preventDefault();
        $('#popup-history').modal('show');
    });
    $('button.btn_evaluate_note').on('click',function(e){
        console.log('btn_evaluate_note');
        e.preventDefault();
        $('#popup-evaluation').modal('show');
    });
    $('button#btn_comment').on('click',function(e){
        e.preventDefault();
        $('#popup-comments textarea[name="comments"]').val('');
        $('#popup-comments').modal('show');
        $('#popup-comments .message-list').animate({ scrollTop: 10000 }, 500);
    });
    $('button#btn_scoring').on('click',function(e){
        e.preventDefault();
        $('#popup-scoring').modal('show');
    });


    $('#popup-history').on("shown.bs.modal", function () {
        TableHistory.initTable(TabDocument.selectedRow.vendor_id);
    });
    $('#popup-evaluation').on("shown.bs.modal", function () {
        EvaluationNote.loadData(1); // load evaluation notes
    });
    $('#popup-scoring').on("shown.bs.modal", function () {
        TableScoring.initTable(TabDocument.selectedRow.vendor_id);
    });

    // btn_print
    $('#btn_print').on("click", function () {
        let _url = "{{ route('tender.print', ['id' => $id, 'type' => $type]) }}";
        $(this).attr('href', _url);
    });

    @if($statusProcess == 'registration')
    $('button.btn_start_flow').click(function(){
        $('#delete_modal .modal-title').text("{{__('tender.'.$type)}}");
        let _body = $('<div class="alert alert-warning" role="alert">{{__('tender.process.message_start_bidding')}}</div>')
        $('#delete_modal .modal-body').html(_body);
        $('#btn_delete_modal').click();
        $('#delete_modal #btn_confirm').off('click').on('click', function () {
            let data = {action_type : '{{\App\Enums\TenderSubmissionEnum::FLOW_STATUS[1]}}'};
            submit(data, function(){
                $('#delete_modal .close').click();
                location.href = "{{ route('tender.save', ['id'=>$id, 'type' => $type]) }}";
            })
            return false;
        });
    });
    @endif
    @if($statusProcess == 'started-pq')
    $('button.btn_open_flow').click(function(){
        $('#delete_modal .modal-title').text("{{__('tender.'.$type)}}");
        let _body = $('<div class="alert alert-warning" role="alert">{{__('tender.process.message_start_bidding')}}</div>')
        $('#delete_modal .modal-body').html(_body);
        $('#btn_delete_modal').click();
        $('#delete_modal #btn_confirm').off('click').on('click', function () {
            @if($workflowValues == \App\Enums\TenderSubmissionEnum::FLOW_STATUS[3])
            let data = {action_type : '{{\App\Enums\TenderSubmissionEnum::FLOW_STATUS[4]}}'};
            @else
            let data = {action_type : '{{\App\Enums\TenderSubmissionEnum::FLOW_STATUS[2]}}'};
            @endif
            submit(data, function(){
                $('#delete_modal .close').click();
                location.href = "{{ route('tender.save', ['id'=>$id, 'type' => $type]) }}#document";
                location.reload();
            });
            return false;
        });
    });
    @endif

    @if($statusProcess == 'opened-pq')
    $('#dt-bid-doc-requirement tbody').on('click','input[name="evaluate"]', function(e){
        e.preventDefault();
        let dtrow = TableDetail.table.row($(this).parents('tr')).data();
        let data = {
            'action_type' : 'evaluate-submission-detail',
            'id' : dtrow.id,
            'status': $(this).val(),
        };
        submit(data, function(){
            $('#delete_modal .close').click();
        }, $(e.target).parents('td'));
    });
    $('button.btn_request_resubmission').on('click',function(e){
        e.preventDefault();
        $('#delete_modal .modal-title').text("{{__('tender.'.$type)}}");
        let _body = $('<div class="alert alert-warning" role="alert">{{__('tender.process.message_start_bidding')}}</div>')
        $('#delete_modal .modal-body').html(_body);
        $('#btn_delete_modal').click();
        $('#delete_modal #btn_confirm').off('click').on('click', function () {
            let data = {action_type : '{{\App\Enums\TenderSubmissionEnum::FLOW_STATUS[3]}}'};
            submit(data, function(){
                $('#delete_modal .close').click();
                location.href = "{{ route('tender.save', ['id'=>$id, 'type' => $type]) }}#document";
                location.reload();
            });
            return false;
        });
    });
    $('#dt-evaluation-vendor tbody').on('click','input[name="evaluate"]', function(e){
        e.preventDefault();
        let dtrow = TabEvaluation.table.row($(this).parents('tr')).data();
        let data = {
            'action_type' : 'evaluate-submission',
            'id' : dtrow.id,
            'status': $(this).val(),
        };
        submit(data, function(){
            $('#delete_modal .close').click();
        }, $(e.target).parents('td'));
    });
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
                location.href = "{{ route('tender.save', ['id'=>$id, 'type' => $type]) }}#evaluation";
                location.reload();
            });
            return false;
        });
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
    $('#popup-scoring .btn-save').click(function(e){
        let _scores = TableScoring.table.rows().data();
        let params = [];
        // let totalScore = 0;
        for(let ix=0;ix<_scores.length;ix++) {
            let score = $('input[name="row-'+_scores[ix].id+'"]').val();
            // totalScore += score;
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
            scores : params,
        };
        submit(data, function(response){
            TableScoring.table.ajax.reload();
            $('#popup-scoring').modal('hide');
        });
        return false;
    });
    @endif
    $('#popup-comments .btn-save').click(function(e){
        let data = {
            action_type : 'save-comments',
            to : TabDocument.selectedRow.vendor_code,
            comments : $('#popup-comments textarea[name="comments"]').val() || '',
        };
        submit(data, function(response){
            $('#popup-comments textarea[name="comments"]').val('');
            TenderComments.data[TabDocument.selectedRow.vendor_code] = response.data;
            TenderComments.loadData(TabDocument.selectedRow.vendor_code);
            $('#popup-comments .message-list').animate({ scrollTop: 10000 }, 500);
        });
        return false;
    });
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
    renderStatus = function(data, type, row, dt){
        if(row.status == "{{\App\Models\TenderVendorSubmission::STATUS[3]}}"){
            return '<p class="text-success" style="margin-bottom: 0;">'+row.status_text+'</p>';
        }else if(row.status == "{{\App\Models\TenderVendorSubmission::STATUS[4]}}"){
            return '<p class="text-danger" style="margin-bottom: 0;">'+row.status_text+'</p>';
        }else{
            return '<p class="text-secondary" style="margin-bottom: 0;">'+row.status_text+'</p>';
        }
    }
    initLoad = function(){
        var urlParams = window.location.href.split('#');
        if(urlParams && urlParams.length > 1){
            $('#prequalification-tab li > a#' + urlParams[1] + '-tab.nav-link').tab('show');
        }else{
            $(Tabs[0]).tab('show');
            if(TabOverview.table == null){
                TabOverview.initTable();
            }
        }
    };
    initLoad();
});
</script>
@endsection

