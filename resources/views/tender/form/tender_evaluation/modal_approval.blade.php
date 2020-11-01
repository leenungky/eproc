<div id="popup-approval" class="modal fade common-modal" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{__('tender.process.tab_title_approval')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h6 class="panel-title"><strong>{{__('tender.schedule.fields.prepared_by')}}</strong></h6>
                        </div>
                        <div class="panel-body">
                            <table id="dt-proposed-by" class="table table-sm table-bordered table-striped table-vcenter" style="width: 100%">
                                <thead>
                                    <tr>
                                        <th>{{__('tender.schedule.fields.name')}}</th>
                                        <th>{{__('tender.schedule.fields.position')}}</th>
                                    </tr>
                                </thead>
                                @php
                                    $sign = null;
                                    if(count($commercialSignatures) > 0){
                                        $sign = $commercialSignatures->first(function($item) { return $item->type == 1;});
                                        $proposedBy = $sign ? $sign->sign_by_id : null;
                                    }else{
                                        $proposedBy = $tender->createdBy ? $tender->createdBy->id : null;
                                    }
                                @endphp
                                <tbody>
                                    <tr>
                                        <td>
                                            <select name="sign_by_id1_0" required class="sign_by custom-select custom-select-sm"
                                                data-id="{{$sign ? $sign->id : ''}}"
                                                data-order="0"
                                                @if($tender->workflow_status != 'tender_process' || $workflowValues[0] != $type || $tender->commercial_approval_status == \App\Enums\TenderSubmissionEnum::STATUS_ITEM[2]) disabled @endif
                                                data-type="1">
                                                <option></option>
                                                @php $selected=($proposedBy ?? auth()->user()->id); @endphp
                                                @php $position=""; @endphp
                                                @foreach ($buyerOptions as $key => $val)
                                                <option value="{{$val->user_id}}"
                                                    @if($selected == $val->user_id) 
                                                        selected 
                                                        @php $position = !is_null($sign) ? $val->position : ''; @endphp
                                                    @endif
                                                    data-position="{{$val->position}}"
                                                    >{{$val->buyer_name}}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                        <input type="text" name="position1_0" class="form-control form-control-sm"
                                            value="{{$sign ? $sign->position : (!empty($position) ? $position : '')}}" required readonly/>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <hr>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h6 class="panel-title"><strong>{{__('tender.schedule.fields.approved_by')}}</strong></h6>
                            <div style="float:right;margin-top:-28px">
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input name="commercial_approval_type" id="commercial_approval_type_0" type="radio" class="custom-control-input" value="0" required="required"
                                    @if($tender->workflow_status != 'tender_process' || $workflowValues[0] != $type || $tender->commercial_approval_status==\App\Enums\TenderSubmissionEnum::STATUS_ITEM[2]) disabled @endif
                                    @if($tender->commercial_approval_type==='0') checked @endif
                                    >
                                    <label for="commercial_approval_type_0" class="custom-control-label">MATERIAL</label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input name="commercial_approval_type" id="commercial_approval_type_9" type="radio" class="custom-control-input" value="9" required="required"
                                    @if($tender->workflow_status != 'tender_process' || $workflowValues[0] != $type || $tender->commercial_approval_status==\App\Enums\TenderSubmissionEnum::STATUS_ITEM[2]) disabled @endif
                                    @if($tender->commercial_approval_type==='9') checked @endif
                                    >
                                    <label for="commercial_approval_type_9" class="custom-control-label">SERVICE</label>
                                </div>
                            </div>
                        </div>
                        <div class="panel-body">
                            <table id="dt-approved-by" class="table table-sm table-bordered table-striped table-vcenter" style="width: 100%">
                                <thead>
                                    <tr>
                                        <th>{{__('tender.schedule.fields.name')}}</th>
                                        <th>{{__('tender.schedule.fields.position')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                @if($statusProcess != '')
                    <button type="button" class="btn btn-sm btn-secondary btn-cancel" data-dismiss="modal">{{__('common.cancel')}}</button>
                    <button type="button" class="btn btn-sm btn-primary btn-save">{{__('common.save')}}</button>
                @endif
            </div>
        </div>
    </div>
</div>
