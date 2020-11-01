@extends('layouts.one_column')

@section('contentheader')
<div class="card-header-left">
    <span class="heading-title"><i class="fa fa-list mr-2"></i>Buyer Management</span>
</div>
<div class="card-header-right">
    <div class="button-group">
        <button id="btn_create" class="btn btn-sm btn-success" data-toggle="modal" data-target="#frmadd_modal" data-backdrop="static" data-keyboard="false"><i class="fas fa-plus mr-2" aria-hidden="true"></i>{{__('common.new_entry')}}</button>
    </div>
</div>
@endsection

@section('contentbody')
<div class="has-footer">
    <div class="card-fixed">
        <div class="col-12">
            <table id="datatable_serverside" class="table table-bordered table-striped table-vcenter">
                <thead>
                    <tr>
                        <th class="action">{{__('homepage.action')}}</th>
                        @foreach ($fields as $field)
                        <th id="{{$field}}">{{__('homepage.'.$field)}}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>

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
<?php
    $modal1 = [
        'title'=> __("homepage.create_new_entry"),
        'contents'=>'',
        'form_layout'=>'admin.buyer.form_add',
        'form_name'=>'frmadd',
    ];
?>
@include('layouts.modal_common',$modal1)
@include('layouts.modal_delete')
@endsection

@section('modules-scripts')
@include('layouts.datatableoption')
<script type="text/javascript">
var table;
require(["jquery","datatablesb4","select2","moment"], function () {
require(["datetimepicker"], function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $("#page_numbers").ready(function () {
        $("#datatable_serverside_paginate").appendTo($("#page_numbers"));
        $("#page_numbers").append('<input id="input-page" class="form-control form-control-sm" style="width:70px;" type="number" min="1">')
        $("#datatable_serverside_info").css("padding", ".375rem .75rem").appendTo($("#page_numbers"));
        $('#input-page').keypress(function (event) {
            var keycode = (event.keyCode ? event.keyCode : event.which);
            if (keycode == '13') {
                table.page($(this).val() * 1 - 1).draw(false);
            }
        });
    });

    var options = getDTOptions();
    options.ajax.url = "{{ route('buyer.data') }}";
    options.select=undefined;
    options.columns=[
        {data: 'id', name: 'id'},
        @foreach ($fields as $field)
        {data: '{{$field}}', name: '{{$field}}'},
        @endforeach
    ];
    options.columnDefs=[
        {
            "render": function ( data, type, row ) {
                return ''+
                    '<a onClick="edit(this)" class="mr-2" style="cursor:pointer" data-id="'+data+'" data-label="'+row.buyer_name+'"><i class="fa fa-edit"></i></a>'+
                    '<a onClick="deleteServerside(this)" style="cursor:pointer" data-id="'+data+'" data-label="'+row.buyer_name+'"><i class="fa fa-trash"></i></a>'+
                '';
            },
            "className": 'text-center',
            "targets": 'action'
        },{
            "render": function ( data, type, row, dt ) {
                var column = dt.settings.aoColumns[dt.col].data;
                switch(column){
                    case 'valid_from_date':
                    case 'valid_thru_date':
                        return moment(data).format(uiDateFormat);
                    break;
                    case 'purch_org':
                    case 'purch_group':
                        return data.map(function(item){
                            return '<span class="badge badge-secondary">'+item.description+'</span>';
                        }).join("<br>");
                    break;
                    default:
                        return data;
                    break;
                }
            },
            "targets": "_all"
        },
    ];

    CustomDTtOptions.FilterColumn = function(_this, id,el){
        var th = $('<th class="th-filter-column"></th>');
        var title = $(_this).text();
        if (id != 0) {
            let field = $(el).attr('id');
            switch(field){
                case 'valid_from_date' :
                case 'valid_thru_date' :
                    let createdAt = $(
                        '<input type="text" id="f-'+field+'-at" class="form-control form-control-sm datetimepicker-input date"' +
                        ' name="f-'+field+'-at" data-toggle="datetimepicker" data-target="#f-'+field+'-at"  />'
                    );
                    createdAt
                    .appendTo(th)
                    .on("change.datetimepicker", function () {
                        let _date = (this.value && this.value!= '') ? moment(this.value, uiDateFormat).format(dbDateFormat) : ''
                        table.column(id).search(_date).draw();
                    }).datetimepicker({
                        format: uiDateFormat,
                        icons : {
                            clear: 'fa fa-trash',
                        },
                        buttons: {
                            showClear: true,
                        }
                    });
                    break;
                case 'purch_org' :
                    break;
                case 'purch_group' :
                    break;
                default :
                    $(document.createElement("input"))
                        .addClass('form-control form-control-sm')
                        .appendTo(th)
                        .on("keyup", function () {
                            let SELF = this;
                            if(CustomDTtOptions.searchTimeout != undefined) {
                                clearTimeout(CustomDTtOptions.searchTimeout);
                            }
                            CustomDTtOptions.searchTimeout = setTimeout(function() {
                                CustomDTtOptions.searchTimeout = undefined;
                                table.column(id).search(SELF.value).draw();
                            }, 500);
                        }
                    );
                    break;
            }

        }
        return th;
    };
    options.initComplete = CustomDTtOptions.InitComplete;


    table = $('#datatable_serverside').DataTable(options);
    $('.date').datetimepicker({
        format: uiDateFormat,
    });
    $("#valid_from_date").off("change.datetimepicker").on("change.datetimepicker", function (e) {
        $('#valid_thru_date').datetimepicker('minDate', e.date);
    });

    $('#btn_create').click(function(){
        $('#id').val('');
        $('#user_id').val('');
        $('#buyer_name').val('').focus();
        $('input[id*="purch_org_id"]').attr('checked',false);
        $('input[id*="purch_group_id"]').attr('checked',false);
        $('#valid_from_date').val('');
        $('#valid_thru_date').val('');
    });

    $('#frmadd-save').off('click').on('click',function(){
        let frmId = '#frmadd';
        if ($(frmId)[0].checkValidity()) {
            let frmData = parseFormData('frmadd');
            $(frmId+'_fieldset').attr("disabled",true);
            $.ajax({
                url : "{{ route('buyer.store') }}",
                type : 'POST',
                data : frmData,
                cache : false,
                processData: false,
                contentType: false,
            }).done(function(response, textStatus, jqXhr) {
                if(response.success){
                    showAlert("Buyer "+$(frmId+' #buyer_name').val()+" saved.", "success", 3000);
                    setTimeout(() => {
                        table.draw(false);
                        $(frmId+'_modal .close').click();
                        $(frmId)[0].reset();
                        $(frmId+'_fieldset').attr("disabled",false);
                    }, 1000);

                }else{
                    showAlert("Buyer not saved.", "danger", 3000);
                    $(frmId+'_fieldset').attr("disabled",false);
                }

            }).fail(function(jqXHR, textStatus, errorThrown) {
                showAlert("Buyer not saved. "+jqXHR.responseJSON.message, "danger", 3000);
                $(frmId+'_fieldset').attr("disabled",false);
            });
        }else{
            showAlert("Please complete the form", "danger");
        }
        return false;
    });
});
});
function edit(obj){
    var data = table.row('#'+$(obj).data('id')).data();
    $('#id').val(data.user_id);
    $('#buyer_name').val(data.buyer_name).focus();
    $('#user_id').val(data.user_id);
    $.each(data.purch_org,function(key,val){
        $('#purch_org_id'+val.id).attr('checked',true);
    })
    $.each(data.purch_group,function(key,val){
        $('#purch_group_id'+val.id).attr('checked',true);
    })
    $('#valid_from_date').val(moment(data.valid_from_date,dbDateFormat).format(uiDateFormat));
    $('#valid_thru_date').val(moment(data.valid_thru_date,dbDateFormat).format(uiDateFormat));
    $('#frmadd_modal').modal();
}
function deleteServerside(obj){
    var data = table.row('#'+$(obj).data('id')).data();
    $('#delete_modal .modal-title').text("Delete "+$(obj).data('label'));
    $('#delete_modal .modal-body').text("Are you sure to delete "+$(obj).data('label')+"?");
    $('#btn_delete_modal').click();
    $('#delete_modal #btn_confirm').off('click').on('click', function () {
        $.ajax({
            type: 'DELETE',
            url: "{{ route('buyer.list') }}/"+data.user_id,
        }).done(function(data) {
            $('#delete_modal .close').click();
            showAlert("Buyer "+$(obj).data('label')+" deleted.", "danger", 3000);
            table.draw(false);
        });
        return false;
    });
}
</script>
@endsection
