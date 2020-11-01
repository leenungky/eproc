@extends('layouts.one_column')

@section('contentheader')
<div class="card-header-left">
    <span class="heading-title"><i class="fa fa-list mr-1"></i>Purchase Order</span>
</div>
@endsection

@section('contentbody')
<div class="has-footer">
    <div class="card-fixed">
        <table id="datatable_serverside" class="table table-bordered table-striped table-vcenter table-wrap">
            <thead>
                <tr>
                        <th></th>
                        <th>{{__('purchaserequisition.action')}}</th>
                        {{--@foreach ($fields as $field)
                        <th>{{__('purchaserequisition.'.$field)}}</th>
                        @endforeach--}}


                    @foreach ($tenderData['tender_po_index']['fields'] as $field)
                        <th>{{__('tender.po.'.$field)}}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('footer')
<div class="app-footer">
    <div class="app-footer__inner">
        <div class="app-footer-left">
            <div id="page_numbers" style="display:inherit"></div>
        </div>
        <div class="app-footer-right">
        </div>
    </div>
</div>
@endsection

@section('modals')
@include('layouts.modal_delete')
@endsection

@section('modules-scripts')
@include('layouts.datatableoption')
<script type="text/javascript">
    var table;
    var tableSelection;
    var selectedRows = [];
    var selectedData = [];
    state_preview = false;

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
                options.ajax.url = "{{ route('po.data') }}";
                // options.scrollY = "300px";
                // options.scrollX = true;
                // options.scrollCollapse = true;
                // options.fixedColumns = {
                //     leftColumns: 2,
                // };

                options.columns=[
                    {
                        "data": null,
                        render: function (data, type, row, meta) {
                            return '';
                        },
                        orderable: false,
                        className: 'select-checkbox text-center',
                    },
                    {
                        data: 'id', name: 'id',
                        orderable: false,
                        "render": function ( data, type, row ) {
                            if (!row.deleted_at){
                                return '<a href="" title="delete data" class="col-action col-edit deleteServerside mr-2" ><i class="fa fa-trash"></i></a>';
                            }else{
                                return "";
                            }    
                        },
                        "className": 'text-center',
                    },
                    @foreach ($tenderData['tender_po_index']['fields'] as $key=>$field)
                    
                        {data: '{{$field}}', name: '{{$field}}',
                        "render":function(data, type, row){
                            @if ($field=="eproc_po_status")
                                var status = data;
                                if (row.deleted_at){
                                    status = 'delete';
                                }
                                return '<a href=\'/po/' + row.tender_number + '/' + row.vendor_code + '/detail?eproc_po_number='+ row.eproc_po_number +'\' class="col-action col-edit mr-2">' + status + '</a>';
                            @else
                                return '<a href=\'/po/' + row.tender_number + '/' + row.vendor_code + '/detail?eproc_po_number='+ row.eproc_po_number +'\' class="col-action col-edit mr-2">' + (data ? data : "") + '</a>';        
                            @endif
                        },
                    },
                    @endforeach
                ];
                options.createdRow= function( row, data, dataIndex ) {
                    if (data.deleted_at){
                        $(row).addClass("bg-warning");
                    }
                };
                options.initComplete= function () {
                    var tr = document.createElement("tr");
                    // $("tr").removeAttr("role");
                    var api = this.api();
                    $('#datatable_serverside thead th').each(function (id, el) {
                        var th = document.createElement("th");
                        var title = $(this).text();
                        // if (id == $('#datatable_serverside thead th').length - 1) {
                        if (id == 0 || id == 1) {
                        } else {
                            $(document.createElement("input"))
                                //.attr("placeholder", title)
                                .addClass('form-control form-control-sm')
                                .appendTo(th)
                                .on("change", function () {
                                    table.column(id).search(this.value).draw();
                            });
                        }
                        $(th).appendTo($(tr));
                    });
                    $(tr).appendTo($('#datatable_serverside thead'));

                    // Function to manage the selected rows, which need to be re-selected when the table is redrawn
                    api.on('select', function (e, dt, type, indexes) {
                        $("tr").removeClass("selected");
                       
                    });
                   
                };
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
                        @foreach ($tenderData['tender_po_index']['fields'] as $key=>$field)
                         {data: '{{$field}}', name: '{{$field}}', @if(!empty($tableWidth[$field])) width : {{$tableWidth[$field]}} @endif},
                        @endforeach
                    ],
                    columnDefs:[
                        {
                            "render": function ( data, type, row ) {
                                if(row.item_category == 0){
                                    return '<input class="form-control form-control-sm" type="number" step="0.001" min="0" max="'+data+'" ' +
                                        'data-id="'+row.id+'" onChange="changeSelectedQty(this)" value="'+data+'"  style="width: 150px;"/>';
                                }
                                return data;
                            },
                            // 'width' : 100,
                            className : 'td-value',
                            "targets": 7
                        }
                    ]
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
                DTSelected.refresh();
            }
        };
        var toggleButtons = function(){
            $('#btn_delete_choices, #btn_preview, #btn_create_tender').attr('disabled',selectedRows.length<=0);
            $('#selected_length').text(selectedRows.length);
            // DTSelected.table.clear().rows.add(selectedData).draw(false);
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
        table = DTMain.table;
        // DTSelected.init('datatable_preview');

        $("#page_numbers").ready(function () {
            $("#datatable_serverside_paginate").appendTo($("#page_numbers"));
            $("#page_numbers").append('<input id="input-page" class="form-control form-control-sm" type="number" min="1" max="1000">')
            $("#datatable_serverside_info").css("padding", ".375rem .75rem").appendTo($("#page_numbers"));
            $('#input-page').keypress(function (event) {
                var keycode = (event.keyCode ? event.keyCode : event.which);
                if (keycode == '13') {
                    table.page($(this).val() * 1 - 1).draw(false);
                }
            });
        });
        $('#datatable_preview_wrapper').hide();

        //## Initialize Buttons
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

        $('#sync-sap-data').click(function(){
            $(this).attr("disabled", 'disabled');
            $(this).find('i.fas').removeClass('fa-sync-alt');
            $(this).find('i.fas').addClass('fa-spinner');
            $(this).find('i.fas').addClass('fa-spin');

            $.ajax({
                url : "{{ route('pr.syncSapData') }}",
                type : 'POST',
            }).done(function(response, textStatus, jqXhr) {
                if(response.success){
                    showAlert(response.message, "success", 3000);
                    setTimeout(() => {
                        location.href = "{{ route('pr.list') }}"
                    }, 2000);
                }else{
                    showAlert(response.message, "danger", 3000);
                }
            }).fail(function(jqXHR, textStatus, errorThrown) {
                showAlert("Sync SAP Data Failed", "danger", 3000);
            }).always(function(){
                $('#sync-sap-data').attr("disabled", false);
                $('#sync-sap-data').find('i.fas').addClass('fa-sync-alt');
                $('#sync-sap-data').find('i.fas').removeClass('fa-spinner');
                $('#sync-sap-data').find('i.fas').removeClass('fa-spin');
            });
        });
    });
</script>
@endsection
