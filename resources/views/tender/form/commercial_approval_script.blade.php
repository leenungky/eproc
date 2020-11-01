<script type="text/javascript">
var refreshPopupApprovalSubmitted;
var submitted;
var rejected;
var buyerOptions;
require(["jquery"], function(){
    //Add Info
    $('.btn_addinfo').click(function(){
        $('#popup-addinfo').modal('show');
    })
    $('#popup-addinfo .btn-save').click(function(e){
        let data = {
            id : $('#popup-addinfo #tender_id').val(),
            action_type : 'save-addinfo',
            client_name : $('#popup-addinfo textarea[name="client_name"]').val() || '',
            project_name : $('#popup-addinfo textarea[name="project_name"]').val() || '',
            remarks : $('#popup-addinfo textarea[name="remarks"]').val() || '',
        };
        submit(data, function(response){
            $('#popup-addinfo').modal('hide');
        }, '#popup-addinfo');
        return false;
    });
    $('#popup-addinfo .btn-cancel, #popup-addinfo .close').click(function(e){
        $('#popup-addinfo textarea[name="client_name"]').val($('#popup-addinfo #client_name_old').val());
        $('#popup-addinfo textarea[name="project_name"]').val($('#popup-addinfo #project_name_old').val());
        $('#popup-addinfo textarea[name="remarks"]').val($('#popup-addinfo #remarks_old').val());
    });
    $('.btn_approval').click(function(){
        refreshPopupApproval();
        $('#popup-approval').modal('show');
        // $('#popup-approval .btn.btn-save').prop('disabled', true);
    })

    //Commercial Approval Setting
    signatures = {!!json_encode($commercialSignatures)!!};
    submitted = {{$tender->commercial_approval_status==\App\Enums\TenderSubmissionEnum::STATUS_ITEM[2]?'true':'false'}};
    rejected = {{$tender->commercial_approval_status==\App\Enums\TenderSubmissionEnum::STATUS_ITEM[4]?'true':'false'}};
    buyerOptions = {!!json_encode($buyerOptions)!!};

    function approvalTypeChanged(){
        let html = '';
        if(this.value==0){
            html+=`
                @if(isset($approvers[0]))
                @foreach($approvers[0] as $k=>$appr)
                <tr>
                    <td>
                    <select name="sign_by_id2_{{$appr->order}}" required class="sign_by custom-select custom-select-sm"
                        data-id=""
                        data-order="{{$appr->order}}"
                        data-type="2"
                        @if($tender->workflow_status != 'tender_process' || $workflowValues[0] != $type || $tender->commercial_approval_status==\App\Enums\TenderSubmissionEnum::STATUS_ITEM[2]) disabled @endif
                        >
                        <option></option>
                        @foreach ($buyerOptions as $key => $val)
                        <option value="{{$val->user_id}}"
                            @if($appr->user_id == $val->user_id) 
                                selected 
                            @endif
                            data-position="{{$val->position}}"
                            >{{$val->buyer_name}}</option>
                        @endforeach
                    </select>
                    </td>
                    <td>
                    <input type="text" name="position2_{{$appr->order}}" class="form-control form-control-sm"
                        value="" required readonly/>
                    </td>
                </tr>
                @endforeach
                @endif
            `;
        }else if(this.value==9){
            html+=`
                @if(isset($approvers[9]))
                @foreach($approvers[9] as $k=>$appr)
                <tr>
                    <td>
                    <select name="sign_by_id2_{{$appr->order}}" required class="sign_by custom-select custom-select-sm"
                        data-id=""
                        data-order="{{$appr->order}}"
                        data-type="2"
                        @if($tender->workflow_status != 'tender_process' || $workflowValues[0] != $type || $tender->commercial_approval_status==\App\Enums\TenderSubmissionEnum::STATUS_ITEM[2]) disabled @endif
                        >
                        <option></option>
                        @foreach ($buyerOptions as $key => $val)
                        <option value="{{$val->user_id}}"
                            @if($appr->user_id == $val->user_id) 
                                selected 
                            @endif
                            data-position="{{$val->position}}"
                            >{{$val->buyer_name}}</option>
                        @endforeach
                    </select>
                    </td>
                    <td>
                    <input type="text" name="position2_{{$appr->order}}" class="form-control form-control-sm"
                        value="" required readonly/>
                    </td>
                </tr>
                @endforeach
                @endif
            `;
        }
        $('#dt-approved-by tbody').html(html);
        $('#popup-approval select.sign_by').change(approvalSelectChanged).change();
    }
    function approvalSelectChanged(){
        let order = $(this).data('order');
        let type = $(this).data('type');
        let positionName = 'position' + type + '_' + order;
        let positionVal = $(this).find(':selected').data('position');
        $('#popup-approval input[name="'+positionName+'"]').val(positionVal);
        $('#popup-approval .btn.btn-save').prop('disabled', false);
    }
    function makePopupApprovalRow(sign){        
        let html = `
            <tr>
                <td>
                <select name="sign_by_id${sign.type}_${sign.order}" required class="sign_by custom-select custom-select-sm"
                    data-id="${sign.id}"
                    data-order="${sign.order}"
                    data-type="${sign.type}"
                    >
                    <option></option>`;

        for(let i=0;i<buyerOptions.length;i++){
            let buyer = buyerOptions[i];
            html+=`<option value="${buyer.user_id}"`+(buyer.user_id==sign.sign_by_id ? ' selected ' : '')+`
                    data-position="${buyer.position}"
                    >${buyer.buyer_name}</option>`;
        }

        html+=`
                </select>
                </td>
                <td>
                <input type="text" name="position${sign.type}_${sign.order}" class="form-control form-control-sm"
                    value="${sign.position}" required readonly/>
                </td>
            </tr>
        `;
        return html;
    }
    function refreshPopupApproval(data){
        if(data){
            signatures = data.signatures;
            submitted = data.tender.commercial_approval_status=='{{\App\Enums\TenderSubmissionEnum::STATUS_ITEM[2]}}';
            rejected = data.tender.commercial_approval_status=='{{\App\Enums\TenderSubmissionEnum::STATUS_ITEM[4]}}';
        }

        prepared = "";
        approved = "";
        for(let i=0;i<signatures.length;i++){
            let sign = signatures[i];
            let row = makePopupApprovalRow(sign);
            if(sign.order==0){
                prepared+=row;
            }else{
                approved+=row;
            }
            $('#popup-approval #dt-proposed-by tbody').html(prepared);
            $('#popup-approval #dt-approved-by tbody').html(approved);
        }
        if(submitted){
            $('#popup-approval .btn-submit').prop('disabled',true);
            $('#popup-approval .btn-save').prop('disabled',true);
            $('#popup-approval select').prop('disabled',true);
            $('#popup-approval input').prop('disabled',true);
        }else if(rejected){
            $('#popup-approval .btn-submit').prop('disabled',true);
            $('#popup-approval .btn-save').prop('disabled',false);
        }else if(signatures.length==0){
            $('#popup-approval .btn-submit').prop('disabled',true);
            $('#popup-approval .btn-save').prop('disabled',true);
        }else{
            $('#popup-approval .btn-submit').prop('disabled',false);
            $('#popup-approval .btn-save').prop('disabled',true);
            $('#popup-approval select').prop('disabled',false);
            $('#popup-approval input').prop('disabled',false);
            $('#popup-approval input[type=radio][name=commercial_approval_type]').change(approvalTypeChanged);
            $('#popup-approval select.sign_by').change(approvalSelectChanged);
        }
    }

    $('#popup-approval input[type=radio][name=commercial_approval_type]').change(approvalTypeChanged);
    $('#popup-approval select.sign_by').change(approvalSelectChanged);

    $('#popup-approval .btn-save').click(function(e){
        let _url = "{{ route('tender.save', ['id'=>$id, 'type' => $type]) }}";
        let signBys = $('#popup-approval select.sign_by');
        let params = {
            'commercial_approval_type': $('#popup-approval input[type=radio][name=commercial_approval_type]:checked').val(),
            'action_type': 'commercialSignature',
            'subaction': 'save'
        };
        for(let ix=0;ix<signBys.length;ix++){
            params[ix] = {
                'id' : $(signBys[ix]).data('id'),
                'type' : parseInt($(signBys[ix]).data('type')),
                'order' : parseInt($(signBys[ix]).data('order')),
                'sign_by_id' : parseInt($(signBys[ix]).val()),
                'status' : 'draft',
            };
        }
        $.ajax({
            url : _url,
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
                refreshPopupApproval(response.data);
                $('#popup-approval .close').click();
            }else{
                showAlert("Document not saved.", "danger", 3000);
            }
        }).fail(defaultAjaxFail)
        .always(function(jqXHR, textStatus, errorThrown) {
            Loading.Hide();
        });
    });

    $('#popup-approval .btn-cancel, #popup-approval .close').click(function(e){
        refreshPopupApproval();
    });
    refreshPopupApproval();
    refreshPopupApprovalSubmitted = function(){
        submitted=true;
        $('#popup-approval .btn-submit').prop('disabled',true);
        $('#popup-approval .btn-save').prop('disabled',true);
        $('#popup-approval select').prop('disabled',true);
        $('#popup-approval input').prop('disabled',true);
    };

    //Commercial Approval Tab
    function loadApprovalTab(){
        let _url = "{{ route('tender.show', ['id'=>$id, 'type' => $type, 'action' => 'commercialApproval']) }}";
        $.ajax({
            url : _url,
            type : 'GET',
            beforeSend: function( xhr ) {
                Loading.Show();
            }
        }).done(function(response, textStatus, jqXhr) {
            if(response.success){
                initApprovalTab(response.data);
            }
        }).fail(defaultAjaxFail)
        .always(function(jqXHR, textStatus, errorThrown) {
            Loading.Hide();
        });
    }
    function initApprovalTab(data){
        let html = "";
        if(data.tender.commercial_approval_status=='draft'){
            $('#approval-content').html(html);
        }else{
            prevApprover = null;
            for(let i=0;i<data.tenderData.signatures.length;i++){
                if(data.approver && data.hasPendingApproval && data.tenderData.signatures[i].sign_by_id=={{Auth::user()->id}}){
                    html+=createCardEdit(data.tenderData.signatures[i],prevApprover);
                }else{
                    html+=createCardView(data.tenderData.signatures[i]);
                }
                prevApprover = data.tenderData.signatures[i];
            }
            $('#approval-content').html(html);
            $('#frmApproval .btn-rejected').click(function(){
                if($('#frmApproval')[0].checkValidity()){
                    let message = "{{__('tender.schedule.modal_message', ['status' => __('tender.schedule_status.rejected')])}}";
                    save('{{\App\Enums\TenderSubmissionEnum::STATUS_ITEM[4]}}', message);
                }
                return false;
            });
            $('#frmApproval .btn-approved').click(function(){
                if($('#frmApproval')[0].checkValidity()){
                    let message = "{{__('tender.schedule.modal_message', ['status' => __('tender.schedule_status.approved')])}}";
                    save('{{\App\Enums\TenderSubmissionEnum::STATUS_ITEM[5]}}', message);
                }
                return false;
            });
        }

    }
    function createCardView(sign,prevApprover){
        let scheduleStatus = {!!json_encode(Lang::get('tender.schedule_status'))!!};
        let color='secondary';
        if(sign.status=='rejected'){
            color='danger';
        }else if(sign.status=='approved'){
            color='success';
        }
        let html = `
        <div class="card">
            <div class="card-header">
                <div class="card-header-left">
                    <span class="heading-title">
                        <small><b>${sign.order}. ${sign.sign_by}</b></small>
                    </span>
                </div>
                <div class="card-header-right">
                    <span class="heading-title">
                        <small>{{__('tender.status')}} : <b class="text-${color}">`+scheduleStatus[sign.status]+`</b></small>
                    </span>
                </div>
            </div>
            <div class="card-body" style="padding-top: 20px;">
                <div id="frmApprovals" class="form-view col-sm-12">
                    <div class="form-group row mb-1">
                        <label for="text" class="col-3 col-form-label text-right">{{__('tender.schedule.fields.name')}} : </label>
                        <div class="col-9">
                            <label class="form-control form-control-sm">${sign.sign_by}</label>
                        </div>
                    </div>
                    <div class="form-group row mb-1">
                        <label for="text" class="col-3 col-form-label text-right">{{__('tender.schedule.fields.position')}} : </label>
                        <div class="col-9">
                            <label class="form-control form-control-sm">${sign.position}</label>
                        </div>
                    </div>`;
        if(sign.status!='draft'){
            html += `
                    <div class="form-group row mb-1">
                        <label for="text" class="col-3 col-form-label text-right">{{__('tender.schedule.fields.note')}} : </label>
                        <div class="col-9">
                            <label class="form-control form-control-sm">${sign.notes}</label>
                        </div>
                    </div>
                    <div class="form-group row mb-1">
                        <label for="text" class="col-3 col-form-label text-right">{{__('tender.schedule.fields.date')}} : </label>
                        <div class="col-9">
                            <label class="form-control form-control-sm">${sign.updated_at}</label>
                        </div>
                    </div>
            `;
        }
        html += `
                </div>
            </div>
        </div>
        `;
        return html;
    }
    function createCardEdit(sign,prevApprover){
        let scheduleStatus = {!!json_encode(Lang::get('tender.schedule_status'))!!};
        let color='secondary';
        if(sign.status=='rejected'){
            color='danger';
        }else if(sign.status=='approved'){
            color='success';
        }
        let html = `
        <div class="card">
            <div class="card-header">
                <div class="card-header-left">
                    <span class="heading-title">
                        <small><b>${sign.order}. ${sign.sign_by}</b></small>
                    </span>
                </div>
                <div class="card-header-right">
                    <span class="heading-title">
                        <small>{{__('tender.status')}} : <b class="text-${color}">`+scheduleStatus[sign.status]+`</b></small>
                    </span>
                </div>
            </div>
            <div class="card-body" style="padding-top: 20px;">
            `;
        if(prevApprover==null || prevApprover.status=='approved'){
            html+=`
                <form id="frmApproval" class="col-sm-12 needs-validation" novalidate>
                    <div class="form-group row mb-1">
                        <label for="notes" class="col-3 col-form-label text-right">{{__('tender.schedule.fields.note')}} : </label>
                        <div class="col-6">
                            <input type="hidden" name="id" value="${sign.id}" />
                            <textarea name="notes" class="form-control form-control-sm" rows="5" required></textarea>
                        </div>
                    </div>
                    <div class="form-group row mb-1" style="padding-top: 20px">
                        <label class="col-3 col-form-label text-right">&nbsp;</label>
                        <div class="col-6">
                            <button class="btn btn-rejected btn-danger mr-2" type="submit" >
                                <i class="fa fa-times"></i> {{__('homepage.revise')}}</button>
                            <button class="btn btn-approved btn-success mr-2" type="submit" >
                                <i class="fa fa-check"></i> {{__('common.approve')}}</button>
                        </div>
                    </div>
                </form>
            `;
        }else if(prevApprover.status == 'rejected'){
            html+=`
                <div class="col-sm-12">
                    <h5>`+scheduleStatus[prevApprover.status]+`</h5>
                </div>
            `;
        }else{
            html+=`
                <div class="col-sm-12">
                    <h5>Waiting approval</h5>
                </div>
            `;
        }
        html+=`
            </div>
        </div>
        `;
        return html;
    }

    $('.nav-tabs a#approval-tab').on('show.bs.tab', function(){
        loadApprovalTab();
    });

    function save(_status, message){
        $('#delete_modal .modal-title').text("{{__('tender.schedule.modal_title')}}");
        $('#delete_modal .modal-body').text(message);
        $('#btn_delete_modal').click();
        $('#delete_modal #btn_confirm').off('click').on('click', function () {
            let params = {
                id : $('#frmApproval input[name="id"]').val(),
                notes : $('#frmApproval textarea[name="notes"]').val(),
                status : _status,
                subaction : _status,
                action_type : 'commercialSignature'
            };

            $.ajax({
                url : "{{ route('tender.save', ['id'=>$id, 'type' => $type]) }}",
                type : 'POST',
                data : JSON.stringify(params),
                contentType: "application/json; charset=utf-8",
                dataType: "json",
                beforeSend: function( xhr ) {
                    Loading.Show();
                }
            }).done(function(response, textStatus, jqXhr) {
                if(response.success){
                    $('#delete_modal .close').click();
                    if(response.next){
                        if(response.approvalData) initApprovalTab(response.approvalData);
                        location.href = "{{ route('tender.save', ['id'=>$id, 'type' => $type]) }}#approval";
                        location.reload();
                    }else{
                        loadApprovalTab();
                    }
                    showAlert("Data saved.", "success", 3000);
                }else{
                    showAlert("Data not saved.", "danger", 3000);
                }
            }).fail(defaultAjaxFail)
            .always(function(jqXHR, textStatus, errorThrown) {
                Loading.Hide();
            });
            return false;
        });
    }
});
</script>
