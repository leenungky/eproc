@extends('tender.show')

@section('contentheader')
<div class="card-header-left">
    <span class="heading-title">{{__('tender.'.$type)}}</span>
</div>
@if($editable && $canCreate)
<div class="card-header-right">
    <button id="btn_create_document" class="btn btn-sm btn-success ml-2" data-toggle="modal"
        data-target="#frmproposedvendor_modal" data-backdrop="static"
        data-keyboard="false"><i class="fas fa-plus mr-2" aria-hidden="true"></i>{{__('tender.new_entry')}}</button>
</div>
@endif
@endsection

@section('contentbody')
<div class="has-footer" style="padding: 0">
    @if (session('status'))
    <div class="alert alert-success" role="alert">
        {{ session('status') }}
    </div>
    @endif
    <div class="col-12">
        <table id="datatable_serverside" class="table table-sm table-bordered table-striped table-vcenter table-wrap">
            <thead>
                <tr>
                    <th>{{__('purchaserequisition.action')}}</th>
                    @foreach ($tenderData['tender_vendors']['fields'] as $field)
                        <th class="{{$field}}">{{__('homepage.'.$field)}}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>
<div class="app-footer">
    <div class="app-footer__inner">
        <div class="app-footer-left">
            <div id="page_numbers" style="display:inherit"></div>
        </div>
        <div class="app-footer-right">
            <ul class="nav">
                <li id="action_group" class="nav-item">
                    <button id="btn_next_flow" class="btn btn-primary">
                        {{__('tender.next')}} <i class="fa fa-arrow-right"></i></button>
                </li>
            </ul>
        </div>
    </div>
</div>
@endsection

@section('footer')
@endsection

@section('modals')
<?php
    $modal1 = [
        'title'=> __('tender.'.$type),
        'contents'=>'',
        'form_layout'=>'tender.form.form_proposed_vendor',
        'form_name'=>'frmproposedvendor',
    ];
?>
@include('layouts.modal_common',$modal1)
@include('layouts.modal_delete')
@endsection

@section('modules-scripts')
@parent
@include('layouts.datatableoption')

<script type="text/javascript">
var table;
var isEdit=false;
var uiDatetimeFormat = 'DD.MM.YYYY HH:mm';
var dbDatetimeFormat = 'YYYY-MM-DD HH:mm:ss';
var frmId = '#frmproposedvendor';
var selectedRows = [];
var selectedData = [];

require(["datatablesb4","dt.plugin.select",'datetimepicker'], function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $( document ).ready(function(){
        $("#page_numbers").ready(function () {
            $("#datatable_serverside_paginate").appendTo($("#page_numbers"));
            $("#datatable_serverside_info").css("padding", ".375rem .75rem").appendTo($("#page_numbers"));
        });

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
                {
                    data: 'id', name: 'id',"width": 50,"className": 'text-center',
                    //'visible' : @if($editable) true @else false @endif,
                    "render": function ( data, type, row ) {
                        let _tpl = '';
                        if(row.status=='draft'){
                        _tpl += '<a onClick="deleteRow(this)" class="deleteRow" style="cursor:pointer" data-id="'+data+'" data-pr="'+row.tender_number+'"><i class="fa fa-trash"></i></a>';
                        }else{
                        @if($canDelete)
                        _tpl += '<a onClick="deleteRow(this)" class="deleteRow" style="cursor:pointer" data-id="'+data+'" data-pr="'+row.tender_number+'"><i class="fa fa-trash"></i></a>';
                        @endif
                        }
                        return _tpl;
                    },
                },
                {data: 'vendor_code', name: 'vendor_code', width: 90},
                {data: 'vendor_name', name: 'vendor_name', width: 200},
                {data: 'pic_full_name', name: 'pic_full_name', width: 200},
                {data: 'status_text', name: 'status_text'},
                {
                    data: 'vendor_status_text', name: 'vendor_status_text',
                    "render": function ( data, type, row ) {
                        let classBadge = 'badge-danger';
                        if(row.vendor_status == 'active'){
                            classBadge = 'badge-success';
                        }
                        return '<span class="badge badge-pill '+classBadge+'">'+data+'</span>';
                    },
                },
                {data: 'vendor_evaluation_score', name: 'vendor_evaluation_score'},
                {data: 'scope_of_supply1', name: 'scope_of_supply1'},
                {data: 'scope_of_supply2', name: 'scope_of_supply2'},
                {data: 'scope_of_supply3', name: 'scope_of_supply3'},
                {data: 'scope_of_supply4', name: 'scope_of_supply4'},
            ],
        };
        //## Initilalize Datatables
        table = $('#datatable_serverside').DataTable(options);

        var formObj = {
            table : null,
            init : function(){
                $("#vpage_numbers").ready(function () {
                    $("#datatable_vendor_paginate").appendTo($("#vpage_numbers"));
                    $("#datatable_vendor_info").css("padding", ".375rem .75rem").appendTo($("#vpage_numbers"));
                });
                this.initTable();
            },
            initTable : function(){
                let options = getDTOptions();
                let _url = "{{ route('tender.dataVendor') }}";
                options.ajax.url = _url;
                options = Object.assign(options, {
                    processing: true,
                    columns : [
                        {
                            "data": "id",
                            render: function (data, type, row, meta) {
                                return '';
                            },
                            orderable: false,
                            className: 'select-checkbox',
                        },
                        {
                            "data": "id",
                            render: function (data, type, row, meta) {
                                return meta.row + meta.settings._iDisplayStart + 1;
                            }
                        },
                        {data: 'vendor_code', name: 'vendor_code', width: 90},
                        {data: 'vendor_name', name: 'vendor_name', width: 200},
                        {data: 'pic_full_name', name: 'pic_full_name', width: 200},
                        {
                            data: 'vendor_status_text', name: 'vendor_status_text',
                            "render": function ( data, type, row ) {
                                let classBadge = 'badge-danger';
                                if(row.vendor_status == 'active'){
                                    classBadge = 'badge-success';
                                }
                                return '<span class="badge badge-pill '+classBadge+'">'+data+'</span>';
                            },
                        },
                        {data: 'vendor_evaluation_score', name: 'vendor_evaluation_score'},
                        {data: 'scope_of_supply1', name: 'scope_of_supply1'},
                        {data: 'scope_of_supply2', name: 'scope_of_supply2'},
                        {data: 'scope_of_supply3', name: 'scope_of_supply3'},
                        {data: 'scope_of_supply4', name: 'scope_of_supply4'},
                    ],
                    lengthChange: false,
                    searching: false,
                    select: {
                        style:    'multi',
                    },
                    order: [[ 1, 'asc' ]],
                });
                options.initComplete= function () {
                    var tr = document.createElement("tr");
                    var api = this.api();

                    // Function to manage the selected rows, which need to be re-selected when the table is redrawn
                    api.on('select', function (e, dt, type, indexes) {
                        if (dt.data().id != undefined) {
                            var id = "#" + dt.data().id;
                            selectedRows.push(id);
                            selectedData.push(dt.data());
                        }
                    });
                    api.on('deselect', function (e, dt, type, indexes) {
                        var id = "#" + dt.data().id;
                        selectedRows = _.without(selectedRows, id);
                        selectedData = _.filter(selectedData,function(idx){
                            return idx.id!=dt.data().id;
                        });
                    });
                    api.on('draw.dt', function () {
                        api.rows(_.uniq(selectedRows)).select();
                    });
                };
                //## Initilalize Datatables
                this.table = $('#datatable_vendor').DataTable(options);
            },
            reloadTable : function(){
                let SELF = this;
                let isAwarded = $("#f_vendor_has_awarded").is(":checked") ? 1 : 0;
                let _url = "{{ route('tender.dataVendor') }}";
                    _url += '?vendor_code='+$('#f_vendor_code').val();
                    _url += '&vendor_name='+$('#f_vendor_name').val();
                    _url += '&sos='+$('#f_sos').val();
                    _url += '&is_awarded='+isAwarded;
                    SELF.table.ajax.url(_url).load();
            },
            reset: function(){
                if(this.table != null){
                    this.table.rows().deselect();
                }
            }
        }

        $(frmId+' #attachment').change(function(e){
            $(frmId+' #attachment_label').text('');
            if(e.target.files.length>0){
                $(frmId+' #attachment_label').text(e.target.files[0].name);
            }
        });
        $('#btn_next_flow').click(function(){
            onClickNext();
        });

        $(frmId+'_modal').on("hidden.bs.modal", function () {
            resetForm();
        });
        $(frmId+'_modal').on("shown.bs.modal", function () {
            if(formObj.table == null){
                formObj.init();
            }
        });
        $(frmId+'-save').click(function(){
            if(validateSave()){
                submit(function(){
                    $(frmId+'_modal .close').click();
                    table.ajax.reload();
                    resetForm();
                });
            }
        });

        resetForm = function(){
            $(frmId+'')[0].reset();
            formObj.reset();
        };
        editRow = function(obj){
            isEdit = true;
        };
        deleteRow = function(obj){
            let dtrow = table.row('#'+$(obj).data('id')).data();
            let rowinfo = dtrow['vendor_name'];
            $('#delete_modal .modal-title').text("Delete "+rowinfo);
            $('#delete_modal .modal-body').text("Are you sure to delete "+rowinfo+"?");
            $('#btn_delete_modal').click();
            $('#delete_modal #btn_confirm').off('click').on('click', function () {
                $.ajax({
                    type: 'DELETE',
                    url: "{{ route('tender.show',['id'=>$id,'type'=>$type]) }}/"+dtrow['id'],
                    cache : false,
                    beforeSend: function( xhr ) {
                        Loading.Show();
                    }
                }).done(function(response) {
                    if(response.success){
                        $('#delete_modal .close').click();
                        showAlert(rowinfo+" deleted", "success", 3000);
                        table.ajax.reload();
                        resetForm();
                    }else{
                        showAlert(rowinfo+" not deleted", "danger", 3000);
                    }
                }).always(function(jqXHR, textStatus, errorThrown) {
                    Loading.Hide();
                });
                return false;
            });
        };
        validateSave = function(){
            if(selectedRows.length<=0){
                showAlert("Please select one or more data", "warning");
                return false;
            }
            let rowData = table.rows().data();
            if(table.rows().count() > 0){
                let savedVendorCount = table.rows().count();
                for(let x=0;x<selectedData.length;x++){
                    for(let ix=0;ix<savedVendorCount;ix++){
                        if(rowData[ix].vendor_id == selectedData[x].id){
                            showAlert("Duplicate Vendor " + rowData[ix].vendor_name + " ("+rowData[ix].vendor_code+")", "warning");
                            return false;
                        }
                    }
                }
            }
            return true;
        };
        submit = function(callback){
            let slectedRows = selectedData;
            let selectedCount = selectedRows.length;
            let FormData = [];
            for(let ix=0;ix<selectedCount;ix++){
                slectedRows[ix]['tender_vendor_type'] = 1;
                FormData.push(slectedRows[ix]);
            }
            //SUBMIT
            $.ajax({
                url : "{{ route('tender.save', ['id'=>$id, 'type'=>$type]) }}",
                type : 'POST',
                data : JSON.stringify(FormData),
                dataType: 'json',
                contentType: 'application/json',
                beforeSend: function( xhr ) {
                    $(frmId+'_fieldset').attr("disabled",true);
                    Loading.Show();
                }
            }).done(function(response, textStatus, jqXhr) {
                if(response.success){
                    if(typeof callback == 'function'){
                        callback();
                    }
                    showAlert("Data saved.", "success", 3000);
                    selectedRows = [];
                    selectedData = [];
                }else{
                    showAlert("Data not saved.", "danger", 3000);
                }

            }).fail(defaultAjaxFail)
            .always(function(jqXHR, textStatus, errorThrown) {
                $(frmId+'_fieldset').attr("disabled",false);
                Loading.Hide();
            });
        };
        addRow = function(data){
            let row = {
                'id': data.id,
                'tender_number': "{{$tender->tender_number}}",
                'vendor_id': data.vendor_id,
                'vendor_code': data.vendor_code,
                'vendor_name': data.vendor_name,
                'president_director': data.president_director,
                'status': '' ,
                'status_sanction': data.status_sanction,
                'scores': data.scores,
                'score_category': data.score_category,
            }
            if($(frmId+' #id').val()=='') {
                table.row.add(row).draw();
            }else{
                table.rows('#'+$(frmId+' #id').val()).remove().draw();
                table.row.add(row).draw();
            }
        };

        @if($editable)
        let delayTimeout = null;
        $('.filter-change').keyup(function(){
            if (delayTimeout != null) clearTimeout(delayTimeout);
            delayTimeout = setTimeout(function(){
                formObj.reloadTable();
            }, 500);
        });
        $('#f_sos').change(function(){
            formObj.reloadTable();
        });
        $('#f_vendor_has_awarded').change(function(){
            formObj.reloadTable();
        });
        @endif
    }); //document.ready
});
</script>
@endsection
