<div id="action_modal" class="modal fade" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{__('tender.item_specification.title_category')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="form_action_category" name='form_action_category' class="needs-validation" novalidate>
                @csrf
                <div class="modal-body" style="padding-top: 0;padding-bottom: 0">
                    <div class="form-group row mb-2" style="margin-top: 20px;">
                        <input name="id" type="hidden" value="">
                        <label for="category_name" class="col-3 col-form-label text-right">{{__('tender.item_specification.fields.category_name')}}</label>
                        <div class="col-9">
                            <input id="category_name" name="category_name" type="text" required="required"
                                class="form-control form-control-sm">
                        </div>
                    </div>
                    <div class="form-group row mb-2">
                        <label for="template_id" class="col-3 col-form-label text-right">{{__('tender.item_specification.fields.template')}}</label>
                        <div class="col-9">
                            <select id="template_id" name="template_id" class="custom-select custom-select-sm">
                                <option value="1">Technical-Requirement-Reference</option>
                                <option value="2">Requirement</option>
                            </select>
                            <input id="submission_method" name="submission_method" type="hidden" required="required">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary btn-cancel" data-dismiss="modal">{{__('common.cancel')}}</button>
                    @if($canDelete)
                    <button type="button" class="btn btn-sm btn-danger btn-delete" >{{__('common.delete')}}</button>
                    @endif
                    @if($canCreate || $canUpdate)
                    <button type="button" class="btn btn-sm btn-primary btn-save">{{__('common.save')}}</button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>
