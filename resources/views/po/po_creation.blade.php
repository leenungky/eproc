@extends('tender.show')

@section('contentheader')
<div class="card-header-left">
    <span class="heading-title">{{__('tender.'.$type)}}</span>
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
        <div class="row" style="padding:0 10px;">
            <div class="col-sm-12">
                <div class="float-left">
                    <span class="heading-title" style="text-transform: uppercase;color: rgba(13,27,62,0.7);font-weight: bold;font-size: .88rem;">PO LIST</span>
                </div>
            </div>
        </div>
        <br/><br/>
        <div class="row" style="padding:0 10px;">
            <div class="col-md-12">
                <table id="datatable_serverside" class="table table-bordered table-striped table-vcenter table-wrap">
                    <thead>
                        <tr>
                            {{-- <th></th> --}}
                            <th></th>
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


<div class="app-footer">
    <div class="app-footer__inner">
        <div class="app-footer-left">
            <div id="page_numbers" style="display:inherit"></div>
        </div>
        <div class="app-footer-right">
           {{-- <ul class="nav">
                <li id="action_group" class="nav-item">
                    <button id="btn_next_flow" class="btn btn-primary">
                        {{__('tender.next')}} <i class="fa fa-arrow-right"></i></button>
                </li>
            </ul>--}}
        </div>
    </div>
</div>
@endsection

@section('footer')
@endsection

@section('modals')
@include('layouts.modal_delete')
@endsection

@section('modules-scripts')
@include('layouts.datatableoption')

<script type="text/javascript">
   require(["datatables.net-bs4","dt.plugin.select",'datatables.fixed-column'], function () {
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

        var DTMain = {
            table : null,
            init : function(elmId){
                let SELF = this;
                let options = getDTOptions();
                options.ajax.url = "/po/data/{{$tenderData['tender_id']}}?isTender=true";
                // options.scrollY = "300px";
                // options.scrollX = true;
                // options.scrollCollapse = true;
                // options.fixedColumns = {
                //     leftColumns: 2,
                // };

                options.columns=[
                    // {
                    //     "data": null,
                    //     render: function (data, type, row, meta) {
                    //         return '';
                    //     },
                    //     orderable: false,
                    //     className: 'select-checkbox text-center',
                    // },
                    {
                        data: 'id', name: 'id',
                        orderable: false,
                        "render": function ( data, type, row ) {
                            if (!row.deleted_at && row.eproc_po_status=="draft"){
                                return '<a href="" title="delete data" class="col-action col-edit deleteServerside mr-2" ><i class="fa fa-trash"></i></a>';
                            }else{
                                return "";
                            }
                        },
                        "className": 'text-center',
                    },
                    @foreach ($tenderData['tender_'.$type]['fields'] as $field)
                        {data: '{{$field}}', name: '{{$field}}',
                            "render":function(data, type, row){
                                // console.log(row);
                                return '<a href=\'/po/' + row.tender_number + '/' + row.vendor_code + '/po_creation_detail?eproc_po_number=' + row.eproc_po_number + '\' class="col-action col-edit mr-2">' + (data ? data : "") + '</a>';
                            },
                        },
                    @endforeach
                ];

                options.initComplete= function () {

                    var api = this.api();
                    api.on('select', function (e, dt, type, indexes) {
                        $("tr").removeClass("selected");
                    });
                }

                SELF.table = $('#' + elmId).DataTable(options);
                // action column
                $('#' + elmId + ' tbody').on('click','.deleteServerside', function(e){
                    e.preventDefault();
                    let dtrow = SELF.table.row($(this).parents('tr')).data();
                    // console.log(dtrow);
                    let info = dtrow.eproc_po_number+'-'+dtrow.vendor_code+' '+dtrow.vendor_name;

                    $('#delete_modal .modal-title').text("Delete "+info);
                    $('#delete_modal .modal-body').text("Are you sure to delete "+info+"?");
                    $('#btn_delete_modal').click();
                    $('#delete_modal #btn_confirm').off('click').on('click', function () {
                        $.ajax({
                            type: 'DELETE',
                            url: "{{ route('po.deletepo') }}/"+dtrow.id,
                        }).done(function(data) {
                            showAlert("PR "+info+" deleted.", "success", 3000);
                            setTimeout(() => {
                                $('#delete_modal .close').click();
                                DTMain.table.draw(false);
                            }, 1000);
                        });
                        return false;
                    });
                });
            }
        };

        var DTSelected = {
            table : null,
            init : function(elmId){

                let SELF = this;
                let options = getDTOptions();
                let previewOptions = {
                    deferRender: options.deferRender,
                    rowId: options.rowId,
                    columnDefs: options.columnDefs,
                    language: options.language,
                    columns: [
                        {
                            data: 'id', name: 'id',
                            orderable: false,
                            "render": function ( data, type, row ) {
                                return '<a href="" class="col-action col-edit deletePreviewItem mr-2" ><i class="fa fa-trash"></i></a>';
                            },
                            "className": 'text-center',
                        },
                        @foreach ($tenderData['tender_'.$type]['fields'] as $field)
                        {data: '{{$field}}', name: '{{$field}}'},
                        @endforeach
                    ],

                };


                if(SELF.table == null){
                    SELF.table = $('#' + elmId).DataTable(previewOptions);
                }

                // action column
                $('#' + elmId + ' tbody').on('click','.deletePreviewItem', function(e){
                    e.preventDefault();
                    let dtrow = SELF.table.row($(this).parents('tr')).data();
                    var id = dtrow.id; // $(obj).data('id');
                    selectedRows = _.without(selectedRows, '#'+id);
                    selectedData = _.filter(selectedData,function(idx){
                        return idx.id!=id;
                    });
                    DTMain.table.rows('#'+id).deselect();
                    toggleButtons();
                });
            },
            refresh : function(){
                this.table.reload();
            }
        };

        var togglePreview = function(){
            state_preview = !state_preview;
            $('#datatable_serverside_wrapper, #datatable_preview_wrapper').hide();
            $('#select_group, #preview_group').hide();
            if(state_preview){
                $('#datatable_preview_wrapper,#datatable_preview').show();
                $('#preview_group').show();
            }else{
                $('#datatable_serverside_wrapper').show();
                $('#select_group').show();
                // DTSelected.refresh();
            }
        };
        var toggleButtons = function(){
            $('#btn_delete_choices, #btn_preview, #btn_create_tender').attr('disabled',selectedRows.length<=0);
            $('#selected_length').text(selectedRows.length);
            DTSelected.table.clear().rows.add(selectedData).draw(false);
        };
        changeSelectedQty = function(obj){
            var id = $(obj).data('id');
            var idx = 0;
            var found = false;
            while(!found && idx<selectedData.length){
                if(selectedData[idx].id==id){
                    found = true;
                }else{
                    idx++;
                }
            }
            if(found){
                let maxQty = Number($(obj).attr('max'));
                if(Number(obj.value) > maxQty){
                    showAlert("Max QTY is "+maxQty, "warning", 3000);
                    $(obj).val(selectedData[idx].qty);
                    return;
                }if(Number(obj.value) <= 0){
                    showAlert("Min QTY is 1", "warning", 3000);
                    $(obj).val(selectedData[idx].qty);
                    return;
                }else{
                    selectedData[idx].qty = obj.value;
                }
            }
        };

        DTMain.init('datatable_serverside');
        // table = DTMain.table;
        // DTSelected.init('datatable_preview');
        $('#datatable_preview_wrapper').hide();

        $('#btn_delete_choices').click(function(){
            selectedRows = [];
            selectedData = [];
            table.rows().deselect();
        });

        $('input[name="eauction"]').val(0);

        $('#btn_preview').click(function(){
            togglePreview();
        });
        $('#btn_back_select').click(function(){
            togglePreview();
        });
        $('#btn_create_tender').click(function(){
            $('#frmtender_fieldset').attr("disabled",false);
        });

        $('#frmtender-save').click(function(){
            let forms = $('#frmtender')[0];
            var frmData = $('#frmtender').serializeArray();
            frmData.push({name:'items',value:JSON.stringify(selectedData)});
            if (forms.checkValidity() === true) {
                $('#frmtender_fieldset').attr("disabled",true);
                $.ajax({
                    url : "{{ route('tender.draft') }}",
                    type : 'POST',
                    data : frmData,
                    beforeSend: function( xhr ) {
                        Loading.Show();
                    }
                }).done(function(response, textStatus, jqXhr) {
                    // console.log(response);
                    if(response.success){
                        $('#frmtender')[0].reset();
                        showAlert("Draft Tender "+response.data.number+" saved.", "success", 3000);
                        setTimeout(() => {
                            $('#frmtender_modal .close').click();
                            location.href="{{ route('tender.list') }}/"+response.data.id;
                        }, 1000);
                    }else{
                        showAlert("Draft Tender save failed.", "danger", 3000);
                        $('#frmtender_fieldset').attr("disabled",false);
                    }
                }).fail(function(jqXHR, textStatus, errorThrown) {
                    $('#frmtender_fieldset').attr("disabled",false);
                }).always(function(jqXHR, textStatus, errorThrown) {
                    Loading.Hide();
                });
            }
        });

});
</script>
@endsection
