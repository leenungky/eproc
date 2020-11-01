@extends('tender.show')

@section('contentheader')
@php
    $showVendorRespond = ($type != 'items') ? true : false;
    $backTo = ($type != 'items') ? '#technical' : '';
    // check enable edit item
    $statusEnums = \App\Enums\TenderSubmissionEnum::FLOW_STATUS;
    $workflowValues = explode('-',$tender->workflow_values);
    $inWorkflowValues = ['process_tender_evaluation','process_technical_evaluation'];
    if($isVendor && in_array($workflowValues[0], $inWorkflowValues) && count($workflowValues) == 3){
        $editable = false;
        if($workflowValues[2] == $statusEnums[1] || $workflowValues[2]==$statusEnums[3]){
            $editable = true;
        }
    }else{
        // $editable = ($type != 'items') ? $isVendor : $editable;
        $editable = ($type != 'items') ? false : $editable;
    }
@endphp
<ul class="nav nav-tabs" id="detailSpecificationTab" role="tablist">
    @foreach ($categories as $cat)
    <li class="nav-item" id="spec-{{$cat['id']}}-li"
        @if($editable && $canCreate) title="Double click for more action" @endif>
        <a class="nav-link" id="spec-{{$cat['id']}}-tab" data-toggle="tab" href="#spec-{{$cat['id']}}" role="tab"
            aria-controls="spec-{{$cat['id']}}" aria-selected="true">{{ $cat['category_name'] }}
        </a>
    </li>
    @endforeach
    @if($editable && $canCreate)
    <li class="nav-item add-new" id="spec-new-li" style="cursor: pointer;">
        <a class="nav-link add-new disabled" id="spec-new-tab" data-toggle="tab" href="#spec-new" role="tab"
            aria-controls="spec-new" aria-selected="true"><i class="fa fa-plus-circle"></i>
        </a>
    </li>
    @endif
</ul>
@endsection
@section('contentbody')
<div class="">
    <div class="row" style="padding: 4px 20px 4px;">
        <div class="col-sm-12">
            {{-- <div class="float-left">
                <span class="heading-title" style="text-transform: uppercase;color: rgba(13,27,62,0.7);font-weight: bold;font-size: .88rem;">{{__('tender.'.$type)}}</span>
            </div> --}}
            <div class="float-right">
                @if($editable && ($canCreate || $canUpdate) && $showVendorRespond == false)
                <button id="btn_create_document" class="btn btn-sm btn-success ml-2"
                    data-toggle="modal" data-target="#formItemSpecification_modal"
                    data-backdrop="static" data-keyboard="false">
                    <i class="fas fa-plus mr-2" aria-hidden="true"></i>{{__('tender.new_entry')}}
                </button>
                @endif
            </div>
        </div>
    </div>
    <div class="tab-content" id="detailSpecificationContent">
        @foreach ($categories as $cat)
        <div class="tab-pane fade" id="spec-{{$cat['id']}}" role="tabpanel" aria-labelledby="pre_qualification_weight-tab">
            <div class="tab-body">
                <div class="has-footer has-tab" style="padding: 0">
                    <div class="col-12">
                        <table id="dt-spec-{{$cat['id']}}" class="table table-sm table-bordered table-striped table-vcenter table-ellipsis">
                        </table>
                    </div>
                </div>
                <div class="app-footer">
                    <div class="app-footer__inner">
                        <div class="app-footer-left">
                            <div class="page_numbers" style="display:inherit"></div>
                        </div>
                        <div class="app-footer-left button-detail">
                            <a class="btn btn_back_to btn-link mr-2" href="{{ route('tender.show', ['id' => $id, 'type' => $type]) . $backTo}}">
                                <i class="fa fa-arrow-left"></i> {{__('tender.item_specification.btn_back_item')}}
                            </a>
                        </div>

                        {{-- <div class="app-footer-right">
                            <button class="btn_next_flow btn btn-primary">
                                {{__('tender.next')}} <i class="fa fa-arrow-right"></i></button>
                        </div> --}}
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection

@section('footer')
@endsection

@section('modals')
@php
    $template1 = [
        'title'=> __('tender.item_specification.title'),
        'contents'=>'',
        'form_layout'=>'tender.form.item_specification.template.template1',
        'form_name'=>'formTemplate1',
    ];
    $template2 = [
        'title'=> __('tender.item_specification.title'),
        'contents'=>'',
        'form_layout'=>'tender.form.item_specification.template.template2',
        'form_name'=>'formTemplate2',
    ];
@endphp
@include('tender.form.item_specification.modal_form',$template1)
@include('tender.form.item_specification.modal_form',$template2)
@include('tender.form.item_specification.modal_action')
@include('layouts.modal_delete')
@endsection

@section('modules-scripts')
@parent
@include('layouts.datatableoption')

<script type="text/javascript">
var Categories = {!! json_encode($categories) !!};
var _urlGet = "{{ route('tender.dataItem', ['id' => $id, 'type' => $type, 'action' => 'detail-specification']) }}";
var _urlSave = "{{ route('tender.save', ['id' => $id, 'type' => $type]) }}";
var _urlDelete = "{{ route('tender.show', ['id' => $id, 'type' => $type]) }}";
var TabSelected=null;
</script>
@include('tender.form.item_specification.item_specification')
<script type="text/javascript">
require(["datatablesb4","dt.plugin.select",'datetimepicker'], function () {
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
    $(document).ready(function(){
        var Tabs = $('#detailSpecificationTab li > a.nav-link');
        var ItemSpec = new ItemSpecification({
            source : Categories,
            enable : {{ ($editable && ($canCreate || $canUpdate)) ? 'true' : 'false'}},
            canUpdate : {{ $canUpdate ? 'true' : 'false'}},
            canDelete : {{ $canDelete ? 'true' : 'false'}},
            showVendorRespond : {{ $showVendorRespond ? 'true' : 'false'}},
            vendorId : '{{$vendorId ?? 0}}',
            // formSelector : 'formItemSpecification',
        });
        var init = function(){
            $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                if(ItemSpec.tabs != null && ItemSpec.tabs.length > 0){
                    TabSelected = null;
                    for(let ix in ItemSpec.tabs){
                        if(ItemSpec.tabs[ix].key == e.target.id){
                            TabSelected = ItemSpec.tabs[ix].item;
                        }
                    }
                }
                // console.log(TabSelected);
                if(TabSelected != null){
                    ItemSpec.tabSelected = TabSelected;
                    if(TabSelected.table == null){
                        TabSelected.initTableItem();
                    }else{
                        TabSelected.table.ajax.reload();
                        TabSelected.table.columns.adjust().draw();
                    }
                }
            });
            @if($editable && ($canCreate || $canUpdate))
                @if($isVendor == false)
                $('#detailSpecificationTab li.add-new').click(function(e){
                    e.preventDefault();

                    $('#form_action_category input[name="id"]').val('');
                    $('#form_action_category input[name="category_name"]').val('');
                    $('#form_action_category select[name="template_id"]').val(1);
                    $('#action_modal .btn-delete').hide();
                    $('#action_modal').modal('show');
                });
                $( "#detailSpecificationTab li" ).dblclick(function() {
                    $('#form_action_category input[name="id"]').val(TabSelected.category.id);
                    $('#form_action_category input[name="category_name"]').val(TabSelected.category.category_name);
                    $('#form_action_category select[name="template_id"]').val(TabSelected.category.template_id);
                    $('#action_modal .btn-delete').show();
                    $('#action_modal').modal('show');
                });
                $('#action_modal .btn-save').click(function(e){
                    if ($('#form_action_category')[0].checkValidity()) {
                        // let params = SELF.tabSelected.formParams();
                        submit({
                            'id' : $('#form_action_category input[name="id"]').val(),
                            'category_name' : $('#form_action_category input[name="category_name"]').val(),
                            'template_id' : $('#form_action_category select[name="template_id"]').val(),
                        }, function(){
                            $('#action_modal').modal('hide');
                            location.href = "{{ route('tender.show', ['id'=>$id, 'type' => $type, 'action' => 'detail-specification']) }}";
                            location.reload(true);
                        });
                    }else{
                        showAlert("Please complete the form", "warning");
                    }
                });
                $('#action_modal .btn-delete').click(function(e){
                    let rowinfo = TabSelected.category.category_name;
                    $('#action_modal').modal('hide');
                    $('#delete_modal .modal-title').text("Delete "+rowinfo);
                    $('#delete_modal .modal-body').text("Are you sure to delete "+rowinfo+"?");
                    $('#btn_delete_modal').click();
                    $('#delete_modal #btn_confirm').off('click').on('click', function () {
                        $.ajax({
                            type: 'DELETE',
                            url : _urlDelete + '/'+TabSelected.category.id+'?action=detail-specification&type=1',
                            cache : false,
                            beforeSend: function( xhr ) {
                                Loading.Show();
                            }
                        }).done(function(response) {
                            if(response.success){
                                $('#delete_modal .close').click();
                                showAlert(rowinfo+" deleted", "success", 3000);
                                location.href = "{{ route('tender.show', ['id'=>$id, 'type' => $type, 'action' => 'detail-specification']) }}";
                                location.reload(true);
                            }else{
                                showAlert(rowinfo+" not deleted", "danger", 3000);
                            }
                        }).always(function(jqXHR, textStatus, errorThrown) {
                            Loading.Hide();
                        });
                        return false;
                    });
                });
                @endif

            $('#formTemplate1_modal, #formTemplate2_modal').on("shown.bs.modal", function () {
                $(TabSelected.formSelector+'_modal .modal-title').text(TabSelected.category.category_name);
                if(TabSelected.category.template_id == 1){
                    $(TabSelected.formSelector + ' input[name="id"]').attr('disabled',TabSelected.showVendorRespond );
                    $(TabSelected.formSelector + ' input[name="category_id"]').attr('disabled',TabSelected.showVendorRespond );
                    $(TabSelected.formSelector + ' textarea[name="description"]').attr('disabled',TabSelected.showVendorRespond );
                    $(TabSelected.formSelector + ' textarea[name="requirement"]').attr('disabled',TabSelected.showVendorRespond );
                    $(TabSelected.formSelector + ' textarea[name="reference"]').attr('disabled',TabSelected.showVendorRespond );
                    $(TabSelected.formSelector + ' inptextareaut[name="data"]').attr('disabled',!TabSelected.showVendorRespond );
                    $(TabSelected.formSelector + ' textarea[name="respond"]').attr('disabled',!TabSelected.showVendorRespond );
                    if(TabSelected.showVendorRespond == true){
                        $(TabSelected.formSelector + ' textarea[name="data"]').parents('.form-group').show();
                        $(TabSelected.formSelector + ' textarea[name="respond"]').parents('.form-group').show();
                    }else{
                        $(TabSelected.formSelector + ' textarea[name="data"]').parents('.form-group').hide();
                        $(TabSelected.formSelector + ' textarea[name="respond"]').parents('.form-group').hide();
                    }
                }else if (TabSelected.category.template_id == 2){
                    $(TabSelected.formSelector + ' input[name="id"]').attr('disabled',TabSelected.showVendorRespond );
                    $(TabSelected.formSelector + ' input[name="category_id"]').attr('disabled',TabSelected.showVendorRespond );
                    $(TabSelected.formSelector + ' textarea[name="requirement"]').attr('disabled',TabSelected.showVendorRespond );
                    $(TabSelected.formSelector + ' textarea[name="data"]').attr('disabled',!TabSelected.showVendorRespond );
                    $(TabSelected.formSelector + ' textarea[name="respond"]').attr('disabled',!TabSelected.showVendorRespond );
                    if(TabSelected.showVendorRespond == true){
                        $(TabSelected.formSelector + ' textarea[name="data"]').parents('.form-group').show();
                        $(TabSelected.formSelector + ' textarea[name="respond"]').parents('.form-group').show();
                    }else{
                        $(TabSelected.formSelector + ' textarea[name="data"]').parents('.form-group').hide();
                        $(TabSelected.formSelector + ' textarea[name="respond"]').parents('.form-group').hide();
                    }
                }
            });
            $("#btn_create_document").click(function(){
                TabSelected.resetForm();
                $(TabSelected.formSelector+'_modal').modal('show');
            });
            @endif
        }
        var submit = function(params, callback){
            let SELF = this;
            $.ajax({
                url : _urlSave + '?action=detail-specification&type=1',
                type : 'POST',
                data : JSON.stringify(params),
                contentType: "application/json; charset=utf-8",
                dataType: "json",
                beforeSend: function( xhr ) {
                    Loading.Show();
                }
            }).done(function(response, textStatus, jqXhr) {
                if(response.success){
                    if(typeof callback == 'function'){
                        callback();
                    }
                    showAlert("Data saved.", "success", 3000);
                }else{
                    showAlert("Data not saved.", "danger", 3000);
                }
            }).fail(defaultAjaxFail)
            .always(function(jqXHR, textStatus, errorThrown) {
                Loading.Hide();
            });
        }

        init();
        ItemSpec.init();
        // select first tab
        $(Tabs[0]).tab('show');


    });
});
</script>
@endsection
