<div class="page1 display-block">
    <input type="hidden" name="applicant_id" value="{{ $applicant->id }}"/>
    <div class="form-group-sm row">
        <label for="username" class="col-form-label-sm text-right col-sm-4">{{ __('homepage.username') }}<span class="font-danger">*</span></label>
        <div class="col-sm-8">
            <input type="text" name="username" maxlength="8" class="form-control form-control-sm" id="username" placeholder="{{ __('homepage.username') }}">
        </div>
    </div>
    <div class="form-group-sm row">
        <label for="fullname" class="col-form-label-sm text-right col-sm-4">{{ __('homepage.fullname') }}<span class="font-danger">*</span></label>
        <div class="col-sm-8">
            <input type="text" name="fullname" class="form-control form-control-sm" id="fullname" placeholder="{{ __('homepage.fullname') }}" />
        </div>
    </div>
    <div class="form-group-sm row">
        <label for="email" class="col-form-label-sm text-right col-sm-4">{{ __('homepage.email') }}<span class="font-danger">*</span></label>
        <div class="col-sm-8">
            <input type="text" name="email" class="form-control form-control-sm" id="email" placeholder="{{ __('homepage.email') }}" />
        </div>
    </div>
    <div class="form-group-sm row">
        <label for="phone_number" class="col-form-label-sm text-right col-sm-4">{{ __('homepage.phone_number') }}<span class="font-danger">*</span></label>
        <div class="col-sm-8">
            <input type="text" name="phone_number" class="form-control form-control-sm" id="phone_number" placeholder="{{ __('homepage.phone_number') }}" />
        </div>
    </div>
    <div class="form-group-sm row">
        <label for="primary_data" class="col-form-label-sm text-right col-sm-4">{{ __('homepage.primary_data') }}<span class="font-danger">*</span></label>
        <div class="col-sm-8">
            <input type="text" name="primary_data" class="form-control form-control-sm" id="primary_data" placeholder="{{ __('homepage.primary_data') }}" />
        </div>
    </div>
</div>