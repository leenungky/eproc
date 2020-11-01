<div class="page1 display-block">
    <input type="hidden" name="vendor_profile_id" value="{{ $vendor->id }}"/>
    <input type="hidden" id="profileId" name="id" value=""/>
    <input type="hidden" id="editType" name="edit_type" value=""/>
    <div class="form-group-sm row">
        <label for="username" class="col-form-label text-right col-sm-4">{{ __('homepage.username') }}</label>
        <div class="col-sm-8">
            <input type="text" name="username" class="form-control form-control-sm" id="username" placeholder="{{ __('homepage.username') }}" maxlength="{{ Config::get('tables.vendor_profile_pics.username') }}" required=""/>
        </div>
    </div>
    <div class="form-group-sm row">
        <label for="full_name" class="col-form-label text-right col-sm-4">{{ __('homepage.fullname') }}</label>
        <div class="col-sm-8">
            <input type="text" name="full_name" class="form-control form-control-sm" id="full_name" placeholder="{{ __('homepage.fullname') }}" maxlength="{{ Config::get('tables.vendor_profile_pics.full_name') }}" required="" />
        </div>
    </div>
    <div class="form-group-sm row">
        <label for="email" class="col-form-label text-right col-sm-4">{{ __('homepage.email') }}</label>
        <div class="col-sm-8">
            <input type="text" name="email" class="form-control form-control-sm" id="email" placeholder="{{ __('homepage.email') }}" maxlength="{{ Config::get('tables.vendor_profile_pics.email') }}" required="" />
        </div>
    </div>
    <div class="form-group-sm row">
        <label for="phone" class="col-form-label text-right col-sm-4">{{ __('homepage.phone_number') }}</label>
        <div class="col-sm-8">
            <input type="text" name="phone" class="form-control form-control-sm" id="phone" placeholder="{{ __('homepage.phone_number') }}" maxlength="{{ Config::get('tables.vendor_profile_pics.phone_number') }}" oninput='this.value=this.value.replace(/[^+0-9]/g, "")' required="" />
        </div>
    </div>
    <div class="form-group-sm row">
        <label for="primary_data" class="col-form-label text-right col-sm-4">{{ __('homepage.primary_data') }}<span class="font-danger">*</span></label>
        <div class="col-sm-8">
            <div class="custom-control custom-checkbox mb-2 mt-1">
                <input type="checkbox" class="custom-control-input" id="primary_data" name="primary_data">
                <label class="custom-control-label" for="primary_data">{{ __('homepage.yes') }}</label>
            </div>
        </div>
    </div>
</div>