@extends('layouts.one_column')

@php ($formName = 'frmsanction')

@section('contentheader')
<div class="card-header-left">
    <span class="heading-title">
        <i class="fa fa-list mr-2"></i>{{__('navigation.sanction')}}
    </span>
</div>
<div class="card-header-right">
</div>
@endsection

@section('contentbody')
<div style="margin:-1rem">
    @if(auth()->user()->user_type!='vendor')
    <div class="form-group row mt-2 mb-2">
        <label for="vendor" class="col-3 col-form-label text-right">{{__('homepage.vendor_name')}}</label>
        <div class="col-4">
            <select id="vendor" name="vendor" class="form-control form-control-sm"></select>
        </div>
    </div>
    @endif
    <div class="mb-3 details" hidden>
        <div class="col-sm-12 mt-lg-3 mb-lg-2">
            <h3><span id="partner_name">&nbsp;</span> <span style="font-size:small" id="purchase_org_description"></span></h3>
            <p>{{ __('homepage.register_number') }} : <span id="partner_register_number"></span></p>
        </div>
        <div class="button-group ml-2">
            <button hidden id="btn_create" class="btn btn-sm btn-success" data-toggle="modal" data-target="#{{$formName}}_modal" data-backdrop="static" data-keyboard="false"><i class="fas fa-plus mr-2" aria-hidden="true"></i>{{__('common.new_entry')}}</button>
        </div>
        <table id="current" class="table table-sm table-bordered table-striped table-vcenter">
            <thead>
                <tr>
                    <th>{{__('homepage.action')}}</th>
                    @foreach ($fields as $field)
                    <th>{{__('homepage.'.$field)}}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>

    @if(auth()->user()->user_type!='vendor')
    <div class="details" hidden>
        <h4 class='col-sm-12'>{{__('homepage.comments_history')}}</h4>
        <table id="comment-history" class="table table-sm table-bordered table-striped table-vcenter">
            <thead>
                <tr>
                    <th>{{__('homepage.action')}}</th>
                    @foreach ($commentHistoryFields as $field)
                    <th>{{__('homepage.'.$field)}}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
    @endif
</div>
@endsection

@if(auth()->user()->user_type!='vendor')
@if($isBuyerActive && auth()->user()->can('vendor_sanction_modify'))
@section('modals')
<?php
    $modal1 = [
        'title'=> __('common.new_entry'),
        'contents'=>'',
        'form_layout'=>'vendor.sanction.form_sanction',
        'form_name'=>$formName,
    ];
?>
@include('layouts.modal_common',$modal1)
@endsection
@endif
@endif

@section('modules-scripts')
@include('layouts.datatableoption')
@include('vendor.profiles.profile_modal_twopage')
<script>
var current;
var table;
var historyTable;
var currentData;
var sanctionTypes = {!!json_encode($sanctionTypes)!!};
var selectedVendor;
require(["jquery","datatablesb4", "bootstrap-fileinput",'moment'], function () {
require(["bootstrap-fileinput-fas","select2","datetimepicker"],function(){
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    let maxUploadSize = parseInt(`{{ config('eproc.upload_file_size') }}`);
    @if(auth()->user()->user_type!='vendor')
        $("#attachment").fileinput({
            'showUpload':false, 
            'previewFileType':'any',
            'theme': 'fas',
            'required': true,
            'maxFileSize' : maxUploadSize
        });
        $(".date").datetimepicker({
            useCurrent: false,
            format:uiDateFormat,
        })
        // $('#valid_from_date').datetimepicker('minDate', moment().format(uiDateFormat));
        $("#valid_from_date").off("change.datetimepicker").on("change.datetimepicker", function (e) {
            $('#valid_thru_date').val("");
            $('#valid_thru_date').datetimepicker('minDate', e.date);
        });

        $('btn_create').click(function(){

        });
        $('#vendor').select2({
            theme: "bootstrap4",
            minimumInputLength:2,
            placeholder: "-- Choose Vendor --",
            ajax: {
                url: "{{route('vendor.vendor_option_list')}}",
                dataType: 'json',
                delay: 500,
                cache:false,
                processResults: function (data) {
                    return {
                        results: data
                    };
                }
            }
        });
        $('#vendor').change(function(){
            selectedVendor = this.value;
            $.ajax({
                type: 'GET',
                url: "{{ route('vendor.sanction') }}/"+this.value,
            }).done(function(data) {
                $('#partner_name').text(data.vendor_name);
                $('#purchase_org_description').text("("+data.purchase_org_description+")");
                $('#partner_register_number').text(data.vendor_code);
                $('#vendor_id').val(data.id);
                $('#sanction_type option[value="GREEN"]').attr('hidden',data.sanction_count==0);
                $('.details').attr('hidden',false);
                $('#pic').val(data.pic);
            });

            if(current) current.destroy();
            let options = getOptions(true);
            options.ajax.url = "{{ route('vendor.sanction') }}/current/"+this.value;
            options.autoWidth=false;
            options.drawCallback = function(settings){
                if(settings.json.data.length==0){
                    $('#btn_create').attr('hidden',false);
                }else{
                    if(settings.json.data.length==1 && settings.json.data[0].status==''){
                        $('#btn_create').attr('hidden',false);
                    }else{
                        $('#btn_create').attr('hidden',true);
                    }
                }
            };
            current = $('#current').DataTable(options);

            // if(table) if(typeof(table.destroy)=='function') table.destroy();
            // let hoptions = getOptions(false);
            // hoptions.ajax.url = "{{ route('vendor.sanction') }}/history/"+this.value;
            // table = $('#history').DataTable(hoptions);
            
            if(historyTable) if(typeof(historyTable.destroy)=='function') historyTable.destroy();
            let choptions = getOptions(false);
            choptions.ajax.url = "{{ route('vendor.sanction') }}/comment-history/"+this.value;
            historyTable = $('#comment-history').DataTable(choptions);
        });
        @if($isBuyerActive && auth()->user()->can('vendor_sanction_modify'))
        $('#{{$formName}}-save').click(function(){
            let frmId = '#{{$formName}}';
            if ($(frmId)[0].checkValidity()) {
                let frmData = parseFormData('{{$formName}}');
                $(frmId+'_fieldset').attr("disabled",true);
                Loading.Show();
                $.ajax({
                    url : "{{ route('vendor.sanction_store') }}",
                    type : 'POST',
                    data : frmData,
                    cache : false,
                    processData: false,
                    contentType: false,
                }).done(function(response, textStatus, jqXhr) {

                    if(response.success){
                        showAlert("Page saved.", "success", 3000);
                        setTimeout(() => {
                            current.draw(false);
                            historyTable.draw(false);
                            $(frmId+'_modal .close').click();
                            $(frmId)[0].reset();
                            $(frmId+'_fieldset').attr("disabled",false);
                        }, 1000);

                    }else{
                        showAlert("Page not saved. "+response.message, "danger", 3000);
                        $(frmId+'_fieldset').attr("disabled",false);
                    }
                    Loading.Hide();

                }).fail(function(jqXHR, textStatus, errorThrown) {
                    $(frmId+'_fieldset').attr("disabled",false);
                    Loading.Hide();
                });

            }else{
                showAlert("Please complete the form", "danger");
            }
        });
        @endif
    @else
        $('#partner_name').text('{{$vendor->vendor_name}}');
        $('#partner_register_number').text('{{$vendor->vendor_code}}');
        let options = getOptions(true);
        options.drawCallback = false;
        options.ajax.url = "{{ route('vendor.sanction') }}/current";
        current = $('#current').DataTable(options);
        $('.details').attr('hidden',false);
    @endif

    moment.updateLocale(moment.locale(), { invalidDate: "" });
});
});
function getOptions(isCurrent){
    var options = getDTOptions();
    options.select=undefined;
    options.autoWidth=false;
    options.columnDefs=[
        @if(auth()->user()->user_type=='vendor')
        {
                "visible": false,
                "targets": 0
        },
        @endif
        {
            "render": function ( data, type, row, dt ) {
                var column = dt.settings.aoColumns[dt.col].data;
                switch(column){
                    case 'sanction_type':
                        let badge = 'badge-primary';
                        if(data=="RED") badge = 'badge-danger';
                        if(data=="YELLOW") badge = 'badge-warning';
                        if(data=="GREEN") badge = 'badge-success';
                        return '<span class="badge '+badge+'">'+data+' ('+sanctionTypes[data]+')</span>';
                    break;
                    case 'status':
                        if(isCurrent){
                            let color = 'info';
                            switch(data){
                                case 'SUBMITTED': color = 'info'; break;
                                case 'REVISE': color = 'warning'; break;
                                case 'APPROVED': color = 'success'; break;
                            }
                            return '<span class="badge badge-'+color+'">'+data+'</span>';
                        }else{
                            return data;
                        }
                    break;
                    case 'sanction_detail':
                        return '<a href="{{$storage}}/'+selectedVendor+'/'+data+'" target="_blank">'+data+'</a>';
                    break;
                    case 'valid_from_date':
                    case 'valid_thru_date':
                        return moment(data,dbDateFormat).format(uiDateFormat);
                    break;
                    case 'activity_date':
                        return moment(data,dbDatetimeFormat).format(uiDatetimeFormat);
                    break;
                    default: 
                        return data; 
                    break;
                }
            },
            "targets": "_all"
        }
    ];
    options.lengthChange = false;
    options.searching = false;
    if(isCurrent){
        options.columns=[
            {data: 'id', name: 'id'},
            @foreach ($fields as $field)
            {data: '{{$field}}', name: '{{$field}}'},
            @endforeach
        ];
        options.paging = false;
        options.columnDefs.unshift({
            "render": function ( data, type, row, dt ) {
                @if(auth()->user()->user_type=='vendor')
                    return '';
                @else
                    let haveWorkflow = dt.settings.json.data.length>1;
                    let rowCurrent = row.status=='APPROVED';
                    let rowCanEdit = row.sanction_type=='GREEN' || row.sanction_type=='YELLOW';
                    @if($isBuyerActive && auth()->user()->can('vendor_sanction_modify'))
                        if(rowCurrent){
                            if(haveWorkflow){
                                return '';
                            }else{
                                if(rowCanEdit){
                                    return ''+
                                        '<a onClick="edit(this)" title="Edit" class="mr-2" style="cursor:pointer" data-id="'+data+'" data-label="'+row.vendor_name+' ['+row.sanction_type+']"><i class="fas fa-edit"></i></a>' +
                                        '<a onClick="blacklist(this)" title="Blacklist" class="mr-2" style="cursor:pointer" data-id="'+data+'" data-label="'+row.vendor_name+' ['+row.sanction_type+']"><i class="fas fa-ban"></i></a>' +
                                    '';
                                }else{
                                    return ''+
                                        '<a onClick="unblacklist(this)" title="Un-blacklist" class="mr-2" style="cursor:pointer" data-id="'+data+'" data-label="'+row.vendor_name+' ['+row.sanction_type+']"><i class="fas fa-check-circle"></i></a>' +
                                    '';
                                }
                            } 
                        }else{
                            return '';
                        }
                    @else
                        return '';
                    @endif
                @endif
            },
            "className": 'text-center',
            "targets": 0
        });
    }else{
        options.columns=[
            {data: 'id', name: 'id'},
            @foreach ($commentHistoryFields as $field)
            @if($field == 'position')
            {data: null, name: 'position',
                render: function(data, type, row){
                    return 'System';
                }
            },
            @else
            {data: '{{$field}}', name: '{{$field}}'},
            @endif
            @endforeach
        ];
        options.order = [[0, 'desc']];
        options.autoWidth = false;
        options.columnDefs.unshift({
            "render": function ( data, type, row, dt ) {
                return '';
            },
            "className": 'text-center',
            'visible': false,
            "targets": 0
        });
    }
    return options;
}
@if(auth()->user()->user_type!='vendor')
@if($isBuyerActive && auth()->user()->can('vendor_sanction_modify'))
var toSAP;
function blacklist(obj){
    $('#sanction_type option[value="GREEN"]').attr('hidden',true);
    $('#sanction_type option[value="YELLOW"]').attr('hidden',true);
    $('#sanction_type option[value="RED"]').attr('hidden',false);
    $('#sanction_type').val('RED');
    toSAP = true;
    change(obj);
}
function edit(obj){
    var data = current.row('#'+$(obj).data('id')).data();
    $('#sanction_type option[value="RED"]').attr('hidden',true);
    $('#sanction_type option[value="GREEN"]').attr('hidden',data['sanction_type']=='GREEN');
    $('#sanction_type option[value="YELLOW"]').attr('hidden',data['sanction_type']=='YELLOW');
    $('#sanction_type').val('');
    toSAP = false;
    change(obj);
}
function unblacklist(obj){
    $('#sanction_type option[value="RED"]').attr('hidden',true);
    $('#sanction_type option[value="GREEN"]').attr('hidden',false);
    $('#sanction_type option[value="YELLOW"]').attr('hidden',false);
    $('#sanction_type').val('');
    toSAP = true;
    change(obj);
}
function change(obj){
    var data = current.row('#'+$(obj).data('id')).data();
    // console.log(data);
    var form = '#{{$formName}}';

    $(form)[0].reset();
    $(form+'_modal .modal-title').text($(obj).data('label'));
    $(form+' #id').val(data.id);
    $.each(data, function(k,v){
        if(k!='sanction_type')
        parseInputData(k,v,'{{$storage}}/'+data.vendor_profile_id);
    });

    $('#{{$formName}}-previous').click();
    if(moment($('#valid_from_date').val(),uiDateFormat)<moment(uiDateFormat)){
        $('#valid_from_date').attr('readonly',true);
        $("#valid_from_date").off("change.datetimepicker");
    }else{
        $('#valid_from_date').attr('readonly',false);
        // $('#valid_from_date').datetimepicker('minDate', moment().format(uiDateFormat));
        $("#valid_from_date").off("change.datetimepicker").on("change.datetimepicker", function (e) {
            $('#valid_thru_date').datetimepicker('minDate', e.date);
        });
    }
    $('#attachment').attr('required',true);

    $(form+'_modal').modal();
}
@endif
@endif
function detail(obj){
    var data = current.row('#'+$(obj).data('id')).data();

}
</script>
@endsection

@section('styles')
@endsection