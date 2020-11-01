@extends('layouts.one_column')

@php
    $tableWidth = [ 'description' => 275,'requisitioner_desc'=> 275,'plant_name' => 275,'cost_desc' => 275,'storage_loc_name' => 275,'qty' => 200];
@endphp
@section('contentheader')
<div class="card-header-left">
    <span class="heading-title"><i class="fa fa-list mr-1"></i> {{ __('purchaserequisition.title') }} - {{ __('purchaserequisition.subheading') }}</span>
</div>

<div class="card-header-right">
    <div class="button-group">
        <button id="sync-sap-data" class="btn btn-sm btn-warning"
            data-keyboard="false"><i class="fas fa-sync-alt mr-2"></i>{{__('common.sync_sap_pr_list')}}</button>
    </div>
</div>
@endsection

@section('contentbody')
    <div class="has-footer">
        <div class="card-fixed">
            @if (session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
            @endif

            <table id="datatable_serverside" class="table table-bordered table-striped table-vcenter table-wrap">
                <thead>
                    <tr>
                        <th></th>
                        <th>{{__('purchaserequisition.action')}}</th>
                        @foreach ($fields as $field)
                        <th>{{__('purchaserequisition.'.$field)}}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
            <table id="datatable_preview" class="table table-form table-bordered table-striped table-vcenter table-wrap" style="display:none">
                <thead>
                    <tr>
                        <th>{{__('purchaserequisition.action')}}</th>
                        @foreach ($fields as $field)
                        <th>{{__('purchaserequisition.'.$field)}}</th>
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
            <div id="page_numbers"></div>
        </div>
        <div class="app-footer-right">
            <ul class="nav">
                <li class="nav-item">
                    <div class="badge badge-focus mr-2 ml-0 mt-2">
                        <span id="selected_length">0</span> {{__('purchaserequisition.selected')}}
                    </div>
                </li>
                <li id="select_group" class="nav-item">
                    <button id="btn_delete_choices" class="btn btn-sm btn-danger mr-2" disabled>{{__('purchaserequisition.reset')}}</button>
                    <button id="btn_preview" class="btn btn-sm btn-success mr-2" disabled>{{__('purchaserequisition.next')}}</button>
                </li>
                <li id="preview_group" class="nav-item" style="display:none">
                    <button id="btn_back_select" class="btn btn-sm btn-warning mr-2">{{__('purchaserequisition.back')}}</button>
                    <button id="btn_create_tender" class="btn btn-sm btn-success" data-toggle="modal" data-target="#frmtender_modal" data-backdrop="static" data-keyboard="false" disabled>{{__('purchaserequisition.create_draft')}}</button>
                </li>
            </ul>
        </div>
    </div>
</div>
@endsection

@section('modals')
<?php
    $modal1 = [
        'title'=> __("purchaserequisition.create_draft"),
        'contents'=>'',
        'form_layout'=>'purchase_requisition.form_tender_draft',
        'form_name'=>'frmtender',
    ]
?>
@include('layouts.modal_common',$modal1)
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

    require(["datatables.net-bs4","dt.plugin.select",'datatables.fixed-column','autonumeric'], function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var DTMain = {
            table : null,
            init : function(elmId){
                let SELF = this;
                let options = getDTOptions();
                options.ajax.url = "{{ route('pr.data') }}";
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
                            return '<a href="" class="col-action col-edit deleteServerside mr-2" ><i class="fa fa-trash"></i></a>';
                        },
                        "className": 'text-center',
                    },
                    @foreach ($fields as $key=>$field)
                        {data: '{{$field}}', name: '{{$field}}', @if(!empty($tableWidth[$field])) width : {{$tableWidth[$field]}} @endif},
                    @endforeach
                ];
                options.initComplete= function () {
                    var tr = document.createElement("tr");
                    var api = this.api();
                    $('#datatable_serverside thead th').each(function (id, el) {
                        var th = document.createElement("th");
                        var title = $(this).text();
                        // if (id == $('#datatable_serverside thead th').length - 1) {
                        if (id == 0) {
                            $(document.createElement("input"))
                                .attr({
                                    id:    'myCheckbox',
                                    type:  'checkbox'
                                })
                                .addClass('form form-control')
                                .appendTo(th)
                                .on("click", function(e){
                                    if ($(this).is( ":checked" )) {
                                        var len = DTMain.table.rows().count();
                                        for(var i=0;i<len;i++){
                                            DTMain.table.row(i).select();
                                        }
                                    } else {
                                        var len = DTMain.table.rows().count();
                                        for(var i=0;i<len;i++){
                                            DTMain.table.row(i).deselect();
                                        }
                                    }
                                })
                        } else if(id == 1){
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
                        if (dt.data().id != undefined) {
                            var id = "#" + dt.data().id;
                            if(selectedRows.indexOf(id)==-1){
                                selectedRows.push(id);
                                selectedData.push(dt.data());
                                toggleButtons();
                            }
                        }
                    });
                    api.on('deselect', function (e, dt, type, indexes) {
                        var id = "#" + dt.data().id;
                        selectedRows = _.without(selectedRows, id);
                        selectedData = _.filter(selectedData,function(idx){
                            return idx.id!=dt.data().id;
                        });
                        toggleButtons();
                    });
                    api.on('draw.dt', function () {
                        api.rows(_.uniq(selectedRows)).select();
                        $('#myCheckbox').prop('checked',api.rows(_.uniq(selectedRows)).count()==api.rows().count());
                    });
                    api.on('user-select', function (e, dt, type, cell, originalEvent) {
                        if(cell.index().column==1){
                            e.preventDefault();
                        }
                    });
                };
                SELF.table = $('#' + elmId).DataTable(options);
                // action column
                $('#' + elmId + ' tbody').on('click','.deleteServerside', function(e){
                    e.preventDefault();
                    let dtrow = SELF.table.row($(this).parents('tr')).data();
                    let info = dtrow.number+'-'+dtrow.line_number+' '+dtrow.description;

                    $('#delete_modal .modal-title').text("Delete "+info);
                    $('#delete_modal .modal-body').text("Are you sure to delete "+info+"?");
                    $('#btn_delete_modal').click();
                    $('#delete_modal #btn_confirm').off('click').on('click', function () {
                        $.ajax({
                            type: 'DELETE',
                            url: "{{ route('pr.list') }}/"+dtrow.id,
                        }).done(function(data) {
                            showAlert("PR "+info+" deleted.", "danger", 3000);
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
                    drawCallback: function(settings){
                        initInputQty();
                    },
                    columns: [
                        {
                            data: 'id', name: 'id',
                            orderable: false,
                            "render": function ( data, type, row ) {
                                return '<a href="" class="col-action col-edit deletePreviewItem mr-2" ><i class="fa fa-trash"></i></a>';
                            },
                            "className": 'text-center',
                        },
                        @foreach ($fields as $key=>$field)
                        {data: '{{$field}}', name: '{{$field}}', @if(!empty($tableWidth[$field])) width : {{$tableWidth[$field]}} @endif},
                        @endforeach
                    ],
                    columnDefs:[
                        {
                            "render": function ( data, type, row ) {
                                if(row.item_category == 0){
                                    return '<input class="form-control form-control-sm input-qty" type="number" step="0.001" min="0" max="'+data+'" ' +
                                        'data-id="'+row.id+'" onChange="changeSelectedQty(this)" value="'+data+'"  style="width: 150px;"/>';
                                }
                                return data;
                            },
                            // 'width' : 100,
                            className : 'td-value',
                            "targets": 8
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
                // DTSelected.refresh();
            }
        };
        var toggleButtons = function(){
            $('#btn_delete_choices, #btn_preview, #btn_create_tender').attr('disabled',selectedRows.length<=0);
            $('#selected_length').text(selectedRows.length);
            DTSelected.table.clear().rows.add(selectedData).draw(false);
        };
        changeSelectedQty = function(obj){
            var objValue = getAutonumricValue($(obj)); // get value from format autonumric

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
                if(Number(objValue) > maxQty){
                    showAlert("Max QTY is "+maxQty, "warning", 3000);
                    $(obj).val(formatQty(selectedData[idx].qty));
                    return;
                }if(Number(objValue) <= 0){
                    showAlert("Min QTY is 1", "warning", 3000);
                    $(obj).val(formatQty(selectedData[idx].qty));
                    return;
                }else{
                    selectedData[idx].qty = objValue;
                }
            }
        };

        DTMain.init('datatable_serverside');
        table = DTMain.table;
        DTSelected.init('datatable_preview');

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
