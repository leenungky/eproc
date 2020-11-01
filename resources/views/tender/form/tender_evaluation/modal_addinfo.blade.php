<div id="popup-addinfo" class="modal fade common_modal" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{__('tender.process.btn_add_info')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="tender_id" name="tender_id" value="{{$tender->id}}">
                <div class="form-group mb-2">
                    <label for="client_name"
                        class="col-form-label text-right">Client</label>
                    <input id="client_name_old" type="hidden" value="{{$tender->client_name}}">
                    <textarea id="client_name" name="client_name" rows="5" @if(!$editable || $statusProcess == '') disabled @endif
                        class="form-control form-control-sm" maxlength="255">{{$tender->client_name}}</textarea>
                </div>
                <div class="form-group mb-2">
                    <label for="project_name"
                        class="col-form-label text-right">Project</label>
                    <input id="project_name_old" type="hidden" value="{{$tender->project_name}}">
                    <textarea id="project_name" name="project_name" rows="5" @if(!$editable || $statusProcess == '') disabled @endif
                        class="form-control form-control-sm" maxlength="255">{{$tender->project_name}}</textarea>
                </div>
                <div class="form-group mb-2">
                    <label for="remarks"
                        class="col-form-label text-right">Remarks</label>
                    <input id="remarks_old" type="hidden" value="{{$tender->remarks}}">
                    <textarea id="remarks" name="remarks" rows="5" @if(!$editable || $statusProcess == '') disabled @endif
                        class="form-control form-control-sm">{{$tender->remarks}}</textarea>
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
