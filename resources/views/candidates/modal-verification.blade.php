<!-- MODAL -->
<form method="POST" id="form-candidate-approval">
    @csrf
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalVerificationLabel"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="control-label col-form-label">Status</label>
                    <input type="hidden" name="vendor_id" value="" readonly=""/>
                    <input type="hidden" name="vendor_profile_id" value="" readonly=""/>
                    <input type="hidden" name="vendor_group" value="" readonly=""/>
                    <input type="hidden" name="company_type" value="" readonly=""/>
                    <input type="hidden" name="purchase_org" value="" readonly=""/>
                    <input type="hidden" name="purchase_org_description" value="" readonly=""/>
                    <input type="hidden" name="status_key" value="" readonly=""/>
                    <input type="text" name="status" class="form-control" value="" readonly=""/>
                </div>
                <div id="additional-content"></div>
                <div class="form-group">
                    <label class="control-label col-form-label">{{ __('homepage.remarks') }}</label>
                    <textarea name="remarks" class="form-control" placeholder="Fill remarks" required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="btn-confirm-approval" class="btn btn-primary">Submit</button>
            </div>
        </div>
    </div>
</form>
