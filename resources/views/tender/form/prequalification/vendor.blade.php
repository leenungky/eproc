@section('contentbody')
@php
    $enabled = false;
    $arrStatus = \App\Models\TenderVendorSubmission::STATUS; //[\App\Models\TenderVendorSubmission::STATUS[1],\App\Models\TenderVendorSubmission::STATUS[2]];
    if($statusProcess == 'started-pq' &&
    (empty($submissionData) || in_array($submissionData->status, $arrStatus)))
    {
        $enabled=true;
    }
    // dd($enabled);
@endphp
<div class="tender-content">
    <ul class="nav nav-tabs" id="prequalification-tab" role="tablist">
        <li class="nav-item" id="overview-li">
            <a class="nav-link" id="overview-tab" data-toggle="tab" href="#overview-content" role="tab"
                aria-controls="overview" aria-selected="true">{{__('tender.process.tab_title_overview')}}</a>
        </li>
        <li class="nav-item" id="document-li">
            <a class="nav-link @if($statusProcess == 'registration') disabled @endif"" id="document-tab" data-toggle="tab" href="#document-content" role="tab"
                aria-controls="document" aria-selected="true">{{__('tender.process.tab_title_document')}}</a>
        </li>
    </ul>
    <div class="tab-content" id="tab-prequalification">
        <div class="tab-pane fade" id="overview-content" role="tabpanel" aria-labelledby="pre_qualification_weight-tab">
            @include('tender.form.prequalification.vendor_tab_overview')
        </div>
        <div class="tab-pane fade" id="document-content" role="tabpanel" aria-labelledby="offer_weight-tab">
            @include('tender.form.prequalification.vendor_tab_document')
        </div>
    </div>
</div>
@endsection


@section('modules-scripts')
@parent
<script type="text/javascript">
var table;
require(["bootstrap-fileinput-fas"], function(){
    var Tabs = $('#prequalification-tab li > a.nav-link');
    var URL = "{{ route('tender.save', ['id' => $id, 'type' => $type]) }}";
    var URLDatatable = "{{ route('tender.dataItem', ['id' => $id, 'type' => $type]) }}";
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $(document).ajaxStart(function() {
        Loading.Show();
    });
    $(document).ajaxStop(function() {
        Loading.Hide();
    });

    $('button.btn_next_flow').click(function(){
        onClickNext();
    });
    @if($enabled && !$hasDocument)
    $('#btn_new_doc').click(function(){
        requestSubmissionDetail(function(){
            location.href = "{{ route('tender.show', ['id'=>$id, 'type' => $type]) }}#document";
            location.reload(true);
        });
    });
    @endif

    initLoad = function(){
        var urlParams = window.location.href.split('#');
        if(urlParams && urlParams.length > 1){
            $('#prequalification-tab li > a#' + urlParams[1] + '-tab.nav-link').tab('show');
        }else{
            $(Tabs[0]).tab('show');
        }
    };
    requestSubmissionDetail = function(callback){
        $.ajax({
            url : URL,
            type : 'POST',
            data : JSON.stringify({action_type : 'request-submission-detail'}),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            beforeSend: function( xhr ) {
                Loading.Show();
            }
        }).done(function(response, textStatus, jqXhr) {
            if(response.success){
                $('#btn_new_doc').attr('disabled',true);
                if(typeof callback == 'function'){
                    callback();
                }
                showAlert("Document saved.", "success", 3000);
            }else{
                showAlert("Document not saved.", "danger", 3000);
            }
        }).fail(defaultAjaxFail)
        .always(function(jqXHR, textStatus, errorThrown) {
            Loading.Hide();
        });
    };
    function setFileName(file, prefixName){
        // let re = /(?:\.([^.]+))?$/;
        // let ext = re.exec(file.name)[1];
        // let fileName = prefixName.replace(/ /g, '_') + '_{{$id}}';
        // return fileName + '.' + ext
        return file.name;
    }

    @if($hasDocument)
    initTable = function(){
        let dtOptions = getDTOptions();
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
            processing: true,
            ajax : URLDatatable + '?action_type=submission-detail',
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
                            @if($enabled)
                            _tpl += '<a href="" class="btn btn-link float-right delete-document"><i class="fa fa-trash"></i></a>';
                            @endif
                            return '<div>' + _tpl + '</div>';
                        }else{
                            let _tpl = '';
                            @if($enabled)
                            _tpl = '<div class="input-group input-group-sm">' +
                                '<div class="custom-file">' +
                                    '<input type="file" id="attachment-'+row.id +'" class="attachment" name="result_attachment" ' +
                                        'class="custom-file-input custom-file-input-sm" data-id="'+row.id +'">' +
                                    '<label id="attachment-'+row.id +'-label" class="custom-file-label" ' +
                                        'for="attachment-'+row.id +'"></label>' +
                                '</div>' +
                                '<div class="input-group-prepend"><button class="btn btn-sm btn-success upload" data-id="'+row.id +'">Upload</button></div>'+
                            '</div>';
                            @endif
                            return _tpl;
                        }
                    }
                },
            ],
        };
        //## Initilalize Datatables
        table = $('#dt-bid-doc-requirement').DataTable(options);
    };

    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        if(e.target.id == 'document-tab'){
            if(table == null){
                initTable();
            }
            TenderComments.loadData("{{$vendor->vendor_code}}");
        }
    });
    $('#dt-bid-doc-requirement tbody').on('change','.attachment', function(e){
        let id = $(this).data('id');
        let files = $(this).prop('files');
        if(files && files.length > 0){
            $('#attachment-'+id+'-label').text(files[0].name);
        }
    });
    $('#dt-bid-doc-requirement tbody').on('click','.upload', function(e){
        let dtrow = table.row($(this).parents('tr')).data();
        let file = $(this).parents('div.input-group').find('input[type="file"]')[0].files[0];
        if(file){
            let formData = new FormData();
            formData.append('action_type', 'upload-submission-detail');
            if(dtrow.id != null && dtrow.id != ''){
                formData.append('id', dtrow.id);
            }
            formData.append('line_id', dtrow.line_id);
            if(dtrow.order != null && dtrow.order != ''){
                formData.append('order', dtrow.order);
            }
            formData.append('file', file, setFileName(file, dtrow.description +'_'+dtrow.vendor_code));
            $.ajax({
                type: 'POST',
                url: URL,
                enctype: 'multipart/form-data',
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function( xhr ) {
                    Loading.Show();
                }
            }).done(function(response, textStatus, jqXhr) {
                if(response.success){
                    showAlert("Document saved.", "success", 3000);
                    table.ajax.reload();
                }else{
                    showAlert("Document not saved.", "danger", 3000);
                }
            }).fail(defaultAjaxFail)
            .always(function(jqXHR, textStatus, errorThrown) {
                Loading.Hide();
            });
        }else{
            showAlert("Document is required.", "warning", 3000);
        }
    });
    $('#dt-bid-doc-requirement tbody').on('click','.delete-document', function(e){
        e.preventDefault();
        let dtrow = table.row($(this).parents('tr')).data();
        let selectedRow = table.row($(this).parents('tr'));
        dtrow.attachment = '';
        $.ajax({
            url : URL,
            type : 'POST',
            data : JSON.stringify({
                action_type : 'delete-submission-detail',
                id : dtrow.id
            }),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            beforeSend: function( xhr ) {
                Loading.Show();
            }
        }).done(function(response, textStatus, jqXhr) {
            if(response.success){
                dtrow.id = response.data.id;
                selectedRow.data( dtrow ).draw();
                showAlert("Document deleted.", "success", 3000);
            }else{
                showAlert("Document delete failed.", "danger", 3000);
            }
        }).fail(defaultAjaxFail)
        .always(function(jqXHR, textStatus, errorThrown) {
            Loading.Hide();
        });
    });
    $('#btn_delete_draft').click(function(e){
        $('#delete_modal .modal-title').text("Delete Document");
        $('#delete_modal .modal-body').text("Are you sure to delete Document ?");
        $('#btn_delete_modal').click();
        $('#delete_modal #btn_confirm').off('click').on('click', function () {
            $.ajax({
                url : URL,
                type : 'POST',
                data : JSON.stringify({action_type : 'delete-all-submission-detail'}),
                contentType: "application/json; charset=utf-8",
                dataType: "json",
                beforeSend: function( xhr ) {
                    Loading.Show();
                }
            }).done(function(response, textStatus, jqXhr) {
                if(response.success){
                    showAlert("Document saved.", "success", 3000);
                    location.href = "{{ route('tender.show', ['id'=>$id, 'type' => $type]) }}#document";
                    location.reload(true);
                }else{
                    showAlert("Document not saved.", "danger", 3000);
                }
            }).fail(defaultAjaxFail)
            .always(function(jqXHR, textStatus, errorThrown) {
                Loading.Hide();
            });
            return false;
        });
    });
    $('#btn_submit').click(function(e){
        let data = table.rows().data();
        let valid = true;
        for(let ix=0;ix<data.length;ix++){
            if(!data[ix].attachment || data[ix].attachment == ''){
                valid = data[ix].is_required == false;
                break;
            }
        }
        if(valid){
            $('#delete_modal .modal-title').text("Submit Draft");
            $('#delete_modal .modal-body').text("Make sure all documents have been uploaded.");
            $('#btn_delete_modal').click();
            $('#delete_modal #btn_confirm').off('click').on('click', function () {
                $.ajax({
                    url : URL,
                    type : 'POST',
                    data : JSON.stringify({action_type : 'submit-submission-detail'}),
                    contentType: "application/json; charset=utf-8",
                    dataType: "json",
                    beforeSend: function( xhr ) {
                        Loading.Show();
                    }
                }).done(function(response, textStatus, jqXhr) {
                    if(response.success){
                        showAlert("Document saved.", "success", 3000);
                        location.href = "{{ route('tender.show', ['id'=>$id, 'type' => $type]) }}#document";
                        location.reload(true);
                    }else{
                        showAlert("Document not saved.", "danger", 3000);
                    }
                }).fail(defaultAjaxFail)
                .always(function(jqXHR, textStatus, errorThrown) {
                    Loading.Hide();
                });
                return false;
            });
        }else{
            showAlert("Please complete the document.", "danger", 3000);
        }
    });
    $('#btn_resubmit').click(function(e){
        let data = table.rows().data();
        let valid = true;
        for(let ix=0;ix<data.length;ix++){
            if(!data[ix].attachment || data[ix].attachment == ''){
                valid = data[ix].is_required == false;
                break;
            }
        }
        if(valid){
            $('#delete_modal .modal-title').text("Resubmit Document");
            $('#delete_modal .modal-body').text("Make sure all documents have been uploaded.");
            $('#btn_delete_modal').click();
            $('#delete_modal #btn_confirm').off('click').on('click', function () {
                $.ajax({
                    url : URL,
                    type : 'POST',
                    data : JSON.stringify({action_type : 'resubmit-submission-detail'}),
                    contentType: "application/json; charset=utf-8",
                    dataType: "json",
                    beforeSend: function( xhr ) {
                        Loading.Show();
                    }
                }).done(function(response, textStatus, jqXhr) {
                    if(response.success){
                        showAlert("Document saved.", "success", 3000);
                        location.href = "{{ route('tender.show', ['id'=>$id, 'type' => $type]) }}#document";
                        location.reload(true);
                    }else{
                        showAlert("Document not saved.", "danger", 3000);
                    }
                }).fail(defaultAjaxFail)
                .always(function(jqXHR, textStatus, errorThrown) {
                    Loading.Hide();
                });
                return false;
            });
        }else{
            showAlert("Please complete the document.", "danger", 3000);
        }
    });

    @if($tender->visibility_bid_document == 'PUBLIC')
    $('#btn_log').on('click',function(e){
        e.preventDefault();
        $('#popup-history').modal('show');
    });
    @endif
    $('#btn_comment').on('click',function(e){
        e.preventDefault();
        $('#popup-comments textarea[name="comments"]').val('');
        $('#popup-comments').modal('show');
        $('#popup-comments .message-list').animate({ scrollTop: 10000 }, 500);
    });
    $('#popup-comments .btn-save').click(function(e){
        let data = {
            action_type : 'save-comments',
            comments : $('#popup-comments textarea[name="comments"]').val() || '',
        };
        let selector = $('#popup-comments .modal-content');
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
                $('#popup-comments textarea[name="comments"]').val('');
                TenderComments.data['{{$vendor->vendor_code}}'] = response.data;
                TenderComments.loadData('{{$vendor->vendor_code}}');
                $('#popup-comments .message-list').animate({ scrollTop: 10000 }, 500);
            }else{
                showAlert("Document not saved.", "danger", 3000);
            }
        }).fail(defaultAjaxFail)
        .always(function(jqXHR, textStatus, errorThrown) {
            Loading.Hide(selector);
        });
        return false;
    });
    $('#popup-history').on("shown.bs.modal", function () {
        TableHistory.initTable("{{$vendor->id}}");
    });
    @endif
    initLoad();
});
</script>
@endsection

