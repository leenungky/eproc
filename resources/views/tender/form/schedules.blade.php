@extends('tender.show')

@section('contentbody')
@if($editable)
    @include('tender.form.schedules_edit')
@else
    @include('tender.form.schedules_view')
@endif
</div>

@endsection

@section('footer')
<div class="app-footer">
    <div class="app-footer__inner">
        <div class="app-footer-left">
            <div id="page_numbers" style="display:inherit"></div>
        </div>
        <div class="app-footer-right">
            <a id="btn_print" target="_blank" href="" class="btn btn-outline-secondary mr-2"><i class="fa fa-file-pdf"></i> {{__('common.print_workplan')}}</a>
            {{-- <button id="btn_proposed_vendors" class="btn btn-outline-secondary mr-2">
                <i class="fa fa-list"></i> {{__('tender.proposed_vendors')}}</button> --}}
            @if($editable)
                @php
                    $enable = false;
                    if(count($tenderData['schedules']) > 0 && count($tenderData['signatures']) > 0){
                        $enable = true;
                    }
                @endphp
                @if($isChanged)
                    <button id="btn_submit_flow" class="btn btn-warning" @if(!$enable) disabled @endif>
                        <i class="fa fa-save"></i> {{__('common.change') }}</button>
                @elseif($tender->action_status == \App\Enums\TenderStatusEnum::ACT_NEW && $tender->public_status == \App\Enums\TenderStatusEnum::PUBLIC_STATUS[1])
                    <button id="btn_submit_flow" class="btn btn-success" @if(!$enable) disabled @endif>
                        <i class="fa fa-paper-plane"></i> {{__('common.submit')}} <i class="fa fa-arrow-right"></i></button>
                @else
                    <button id="btn_next_flow" class="btn btn-primary">{{__('tender.next')}} <i class="fa fa-arrow-right"></i></button>
                @endif
            @else
                @if(!Auth::user()->isVendor())<button id="btn_next_flow" class="btn btn-primary">{{__('tender.next')}} <i class="fa fa-arrow-right"></i></button>@endif
            @endif
        </div>
    </div>
</div>
@endsection


@section('modals')
@include('layouts.modal_delete')
@endsection

@section('modules-scripts')
@parent
<script type="text/javascript">
require(['datetimepicker'], function(datetimepicker){
    @if($editable)
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    var Forms = [];

    var FormSchedule = function(elmId){
        var SELF = this;
        this.elmId = '#'+elmId;
        this.init = function(){
            let typeId = this.elmId.replace('#card-','');
            let startDate = $(SELF.elmId+' input[name="start_date"]').data('value');
            let endDate = $(SELF.elmId+' input[name="end_date"]').data('value');
            $('#start_date-' +typeId).datetimepicker({
                sideBySide: true,
                // 'minDate' : moment(), // new Date(),
                format: uiDatetimeFormat,
            });
            $('#end_date-' +typeId).datetimepicker({
                useCurrent: false,
                sideBySide: true,
                format: uiDatetimeFormat,
            });
            $(SELF.elmId+' input[name="start_date"]').val(startDate);
            $(SELF.elmId+' input[name="end_date"]').val(endDate);
            // $(SELF.elmId+' input[name="start_date"]').datetimepicker('minDate', moment());


            $(SELF.elmId+' input[name="start_date"]').on("change.datetimepicker", function (e) {
                $(SELF.elmId+' input[name="end_date"]').datetimepicker('minDate', e.date);
                $(SELF.elmId+' .btn.btn-save').prop('disabled', false)
            });
            $(SELF.elmId+' input[name="end_date"]').on("change.datetimepicker", function (e) {
                $(SELF.elmId+' .btn.btn-save').prop('disabled', false)
            });

            $(SELF.elmId+' .btn.btn-save').on("click", function (e) {
                let form = $(SELF.elmId+' form');
                if (form[0].checkValidity()) {
                    SELF.submit();
                }
            });
        };
        this.validateRow = function(){
            return true;
        };
        this.submit = function(){
            let _url = "{{ route('tender.save', ['id'=>$id, 'type' => $type]) }}";
            let params = {
                'id' : $(SELF.elmId+' input[name="id"]').val(),
                'start_date' : $(SELF.elmId+' input[name="start_date"]').val(),
                'end_date' : $(SELF.elmId+' input[name="end_date"]').val(),
                'type' : $(SELF.elmId+' input[name="type"]').val(),// type
            };
            $.ajax({
                url : _url + '?actionType=schedule',
                type : 'POST',
                data : JSON.stringify(params),
                contentType: "application/json; charset=utf-8",
                dataType: "json",
                beforeSend: function( xhr ) {
                    Loading.Show();
                }
            }).done(function(response, textStatus, jqXhr) {
                if(response.success){
                    $(SELF.elmId+' input[name="id"]').val(response.data.id);
                    $(SELF.elmId+' input[name="s-check"]').prop('checked', true);
                    showAlert("Document saved.", "success", 3000);
                    $(SELF.elmId+' .btn.btn-save').prop('disabled', true);
                    location.href = "{{ route('tender.save', ['id'=>$id, 'type' => $type]) }}";
                }else{
                    showAlert("Document not saved.", "danger", 3000);
                }
            }).fail(defaultAjaxFail)
            .always(function(jqXHR, textStatus, errorThrown) {
                Loading.Hide();
            });
        }
    }

    var FormSignature = function(elmId){
        var SELF = this;
        this.elmId = '#'+elmId;
        this.init = function(){
            let typeId = this.elmId.replace('#card-','');
            $(SELF.elmId+' select').on("change", function (e) {
                let order = $(this).data('order');
                let type = $(this).data('type');
                let positionName = 'position' + type + '_' + order;
                let positionVal = $(this).find(':selected').data('position');
                $(SELF.elmId+' input[name="'+positionName+'"]').val(positionVal);
                $(SELF.elmId+' .btn.btn-save').prop('disabled', false);
            });

            $(SELF.elmId+' .btn.btn-save').on("click", function (e) {
                let form = $(SELF.elmId+' form');
                if (form[0].checkValidity()) {
                    SELF.submit();
                }
            });
        };
        this.validateRow = function(){
            return true;
        };
        this.submit = function(){
            let _url = "{{ route('tender.save', ['id'=>$id, 'type' => $type]) }}";
            let signBys = $(SELF.elmId+' select.sign_by');
            let params = [];
            for(let ix=0;ix<signBys.length;ix++){
                params.push({
                    'id' : $(signBys[ix]).data('id'),
                    'type' : parseInt($(signBys[ix]).data('type')),
                    'order' : parseInt($(signBys[ix]).data('order')),
                    'sign_by_id' : parseInt($(signBys[ix]).val()),
                    'status' : 'draft',
                });
            }
            $.ajax({
                url : _url + '?actionType=signature',
                type : 'POST',
                data : JSON.stringify(params),
                contentType: "application/json; charset=utf-8",
                dataType: "json",
                beforeSend: function( xhr ) {
                    Loading.Show();
                }
            }).done(function(response, textStatus, jqXhr) {
                if(response.success){
                    showAlert("Document saved.", "success", 3000);
                    location.href = "{{ route('tender.save', ['id'=>$id, 'type' => $type]) }}";
                }else{
                    showAlert("Document not saved.", "danger", 3000);
                }
            }).fail(defaultAjaxFail)
            .always(function(jqXHR, textStatus, errorThrown) {
                Loading.Hide();
            });
        }
    }

    @foreach ($scheduleTypes as $key => $val)
    Forms['{{$val}}'] = new FormSchedule('card-{{$val}}');
    Forms['{{$val}}'].init();
    @endforeach

    Forms['signature'] = new FormSignature('card-signature');
    Forms['signature'].init();
    $('#btn_submit_flow').click(function(){
        $('#delete_modal .modal-title').text("{{__('tender.'.$type)}}");
        let _body = $('<div class="alert alert-warning" role="alert">{{__('tender.submit_message1')}}</div>')
        $('#delete_modal .modal-body').html(_body);
        $('#btn_delete_modal').click();
        $('#delete_modal #btn_confirm').off('click').on('click', function () {
            let _url = "{{ route('tender.save', ['id'=>$id, 'type' => $type]) }}";
            let actionType = "{{$isChanged ? 'changed' : 'submit' }}"
            $.ajax({
                url : _url + '?actionType=' + actionType,
                type : 'POST',
                data : JSON.stringify({sequence_done : true}),
                contentType: "application/json; charset=utf-8",
                dataType: "json",
                beforeSend: function( xhr ) {
                    Loading.Show();
                }
            }).done(function(response, textStatus, jqXhr) {
                if(response.success){
                    showAlert("Document saved.", "success", 3000);
                    $('#delete_modal .close').click();
                    if(response.next!='' && response.next != null){
                        location.href = response.next;
                    }else{
                        location.href = "{{ route('tender.save', ['id'=>$id, 'type' => $type]) }}";
                    }
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
    @endif
    $('#btn_next_flow').click(function(){
        onClickNext();
    });

    $('#btn_proposed_vendors').click(function(){
        location.href = "{{ route('tender.save', ['id'=>$id, 'type' => 'proposed_vendors']) }}";
    });
    $('#btn_print').on("click", function () {
        let _url = "{{ route('tender.print', ['id' => $id, 'type' => $type]) }}";
        $(this).attr('href', _url);
    });

});
</script>
@endsection

