<div class="page1 display-block">
    <input type="hidden" name="applicant_id" value="{{ $applicant->id }}"/>
    <div class="form-group-sm row">
        <label for="board_type" class="col-form-label-sm text-right col-sm-4">{{ __('homepage.board_type') }}<span class="font-danger">*</span></label>
        <div class="col-sm-8">
            <input type="text" name="board_type" class="form-control form-control-sm" id="board_type" placeholder="{{ __('homepage.board_type') }}">
        </div>
    </div>
    <div class="form-group-sm row">
        <label for="is_the_person_listed_as_company_shareholders" class="col-form-label-sm text-right col-sm-4">{{ __('homepage.is_the_person_listed_as_company_shareholders') }}<span class="font-danger">*</span></label>
        <div class="col-sm-8">
            <input type="text" name="is_person_listed" class="form-control form-control-sm" id="is_the_person_listed_as_company_shareholders" placeholder="{{ __('homepage.is_the_person_listed_as_company_shareholders') }}" />
        </div>
    </div>
    <div class="form-group-sm row">
        <label for="fullname" class="col-form-label-sm text-right col-sm-4">{{ __('homepage.fullname') }}<span class="font-danger">*</span></label>
        <div class="col-sm-8">
            <input type="text" name="fullname" class="form-control form-control-sm" id="fullname" placeholder="{{ __('homepage.fullname') }}" />
        </div>
    </div>
    <div class="form-group-sm row">
        <label for="nationality" class="col-form-label-sm text-right col-sm-4">{{ __('homepage.nationality') }}<span class="font-danger">*</span></label>
        <div class="col-sm-8">
            <input type="text" name="nationality" class="form-control form-control-sm" id="nationality" placeholder="{{ __('homepage.nationality') }}" />
        </div>
    </div>
    <div class="form-group-sm row">
        <label for="email" class="col-form-label-sm text-right col-sm-4">{{ __('homepage.email') }}<span class="font-danger">*</span></label>
        <div class="col-sm-8">
            <input type="email" name="email" class="form-control form-control-sm" id="email" placeholder="{{ __('homepage.email') }}" />
        </div>
    </div>
    <div class="form-group-sm row">
        <label for="phone_number" class="col-form-label-sm text-right col-sm-4">{{ __('homepage.phone_number') }}<span class="font-danger">*</span></label>
        <div class="col-sm-8">
            <input type="text" name="phone_number" class="form-control form-control-sm" id="phone_number" placeholder="{{ __('homepage.phone_number') }}" />
        </div>
    </div>
    <div class="form-group-sm row">
        <label for="company_head" class="col-form-label-sm text-right col-sm-4">{{ __('homepage.company_head') }}<span class="font-danger">*</span></label>
        <div class="col-sm-8">
            <input type="text" name="company_head" class="form-control form-control-sm" id="company_head" placeholder="{{ __('homepage.company_head') }}" />
        </div>
    </div>
</div>
