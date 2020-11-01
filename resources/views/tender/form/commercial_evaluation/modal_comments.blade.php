<div id="popup-comments" class="modal fade common_modal" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{__('common.comment')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="padding-top: 0;padding-bottom: 0">
                <div class="col-12 no-padding">
                    <div class="row message-list" style="padding-top:10px;padding-left:10px;padding-right:10px; height: 300px;overflow: scroll;overflow-x: auto;background: #f0f1f1;"></div>
                </div>
                @if($statusProcess != '')
                <div class="form-group row mb-2" style="margin-top: 20px;">
                    <div class="col-12">
                        <textarea name="comments" rows="5" @if(!$editable) disabled @endif
                            class="form-control form-control-sm"></textarea>
                    </div>
                </div>
                @endif
            </div>
            <div class="modal-footer">
                @if($statusProcess != '')
                <button type="button" class="btn btn-sm btn-secondary btn-cancel" data-dismiss="modal">{{__('common.cancel')}}</button>
                <button type="button" class="btn btn-sm btn-primary btn-save">{{__('common.send')}}</button>
                @endif
            </div>
        </div>
    </div>
</div>
