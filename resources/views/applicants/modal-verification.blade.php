<!-- MODAL -->
<div class="modal fade" id="modalVerification" tabindex="-1" role="dialog" aria-labelledby="modalVerificationLabel" style="display: none;" aria-hidden="true">
    <form method="POST" action="{{ route('applicant.approval') }}" class="needs-validation">
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
                        <input type="hidden" name="company_name" value="" readonly=""/>
                        <input type="hidden" name="company_type" value="" readonly=""/>
                        <input type="hidden" name="company_email" value="" readonly=""/>
                        <input type="hidden" name="pic_email" value="" readonly=""/>
                        <input type="hidden" name="npwp" value="" readonly=""/>
                        <input type="hidden" name="vendor_id" value="" readonly=""/>
                        <input type="text" name="status" class="form-control" value="" readonly=""/>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-form-label">{{ __('homepage.remarks') }}</label>
                        <textarea name="remarks" class="form-control" placeholder="Fill remarks" required=true></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" id="btn-submit-approval" class="btn btn-primary">Submit</button>
                </div>
            </div>
        </div>
    </form>
</div>
