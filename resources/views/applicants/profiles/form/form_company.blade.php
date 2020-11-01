<div class="row">
    <div class="col-sm-12">
        <input type="hidden" name="applicant_id" value="{{ $applicant->id }}"/>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group-sm row">
                    <label for="inputFullname" class="col-form-label-sm text-right col-sm-4">{{ __('homepage.company_name') }}<span class="font-danger">*</span></label>
                    <div class="col-sm-8">
                        <input type="text" name="company_name" class="form-control form-control-sm" id="inputFullname" placeholder="{{ __('homepage.company_name') }}">
                    </div>
                </div>
            </div>
        </div>     
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group-sm row">
                    <label for="selCompanyType" class="col-form-label-sm text-right col-sm-4">{{ __('homepage.company_type') }} <span class="font-danger">*</span></label>
                    <div class="col-sm-8">
                        <select name="company_type_id" id="selCompanyType" class="form-control form-control-sm">
                            <option> -- Select -- </option>
                            <option value="1">PT - Local Vendor</option>
                            <option value="2">CV - Local Vendor</option>
                            <option value="3">Yayasan - Local Vendor</option>
                            <option value="4">Koperasi - Local Vendor</option>
                            <option value="5">Perum - Local Vendor</option>
                            <option value="6">Toko - Local Vendor / Personal Vendor</option>
                            <option value="7">Company - Overseas Vendor</option>
                            <option value="8">Others - Others</option>
                            <option value="9">Perorangan - Mr, Mrs & Personal Vendor</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>     
    </div>
</div>