<div class="page1 display-block">
    <input type="hidden" name="vendor_profile_id" value="{{ $vendor->id }}"/>
    <input type="hidden" id="profileId" name="id" value=""/>
    <input type="hidden" id="editType" name="edit_type" value=""/>
    <div class="form-group-sm row">
        <label for="board_type" class="col-form-label text-right col-sm-4">{{ __('homepage.board_type') }}<span class="font-danger">*</span></label>
        <div class="col-sm-8">
            <select id="board_type" name="board_type" class="custom-select custom-select-sm">
                <option value=""> -- Select -- </option>
                <option value="BOD (Board of Director)">BOD (Board of Director)</option>
                <option value="BOC (Board of Commisioner)">BOC (Board of Commisioner)</option>
            </select>
        </div>
    </div>
    <div class="form-group-sm row">
        <label for="is_person_company_shareholder" class="col-4 col-form-label text-right">{{__('homepage.is_the_person_listed_as_company_shareholders')}}</label>
        <div class="col-8">
            <select id="is_person_company_shareholder" name="is_person_company_shareholder" class="custom-select custom-select-sm" required="required">
                <option value=""> -- Select -- </option>
                <option value="1">{{__('homepage.yes')}}</option>
                <option value="0">{{__('homepage.no')}}</option>
            </select>
        </div>
    </div>
    <div class="form-group-sm row">
        <label for="full_name" class="col-form-label text-right col-sm-4">{{ __('homepage.fullname') }}<span class="font-danger">*</span></label>
        <div class="col-sm-8">
            <input type="text" name="full_name" class="form-control form-control-sm" id="full_name" placeholder="{{ __('homepage.fullname') }}" maxlength="{{ Config::get('tables.vendor_profile_bodbocs.full_name') }}" />
        </div>
    </div>
    <div class="form-group-sm row">
        <label for="nationality" class="col-form-label text-right col-sm-4">{{ __('homepage.nationality') }}<span class="font-danger">*</span></label>
        <div class="col-sm-8">
            <select id="nationality" name="nationality" class="custom-select custom-select-sm" required="required">
                <option value=""> -- Select -- </option>
                <option value="WNI">{{__('homepage.wni')}}</option>
                <option value="WNA">{{__('homepage.wna')}}</option>
            </select>
        </div>
    </div>
    <div class="form-group-sm row">
        <label for="position" class="col-form-label text-right col-sm-4">{{ __('homepage.position') }}<span class="font-danger">*</span></label>
        <div class="col-sm-8">
            <input type="text" name="position" class="form-control form-control-sm" id="position" placeholder="{{ __('homepage.position') }}" maxlength="{{ Config::get('tables.vendor_profile_bodbocs.position') }}" />
        </div>
    </div>
    <div class="form-group-sm row">
        <label for="email" class="col-form-label text-right col-sm-4">{{ __('homepage.email') }}<span class="font-danger">*</span></label>
        <div class="col-sm-8">
            <input type="email" name="email" class="form-control form-control-sm" id="email" placeholder="{{ __('homepage.email') }}" maxlength="{{ Config::get('tables.vendor_profile_bodbocs.email') }}" />
        </div>
    </div>
    <div class="form-group-sm row">
        <label for="phone_number" class="col-form-label text-right col-sm-4">{{ __('homepage.phone_number') }}<span class="font-danger">*</span></label>
        <div class="col-sm-8">
            <input type="text" name="phone_number" class="form-control form-control-sm" id="phone_number" placeholder="{{ __('homepage.phone_number') }}" maxlength="{{ Config::get('tables.vendor_profile_bodbocs.phone_number') }}" />
        </div>
    </div>
    <div class="form-group-sm row">
        <label for="company_head" class="col-form-label text-right col-sm-4">{{ __('homepage.company_head') }}<span class="font-danger">*</span></label>
        <div class="col-sm-8">
            <div class="custom-control custom-checkbox mb-2 mt-1">
                <input type="checkbox" class="custom-control-input" id="company_head" name="company_head">
                <label class="custom-control-label" for="company_head">{{ __('homepage.yes') }}</label>
            </div>
        </div>
    </div>
</div>
