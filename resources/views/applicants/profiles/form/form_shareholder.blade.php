<div class="page1 display-block">
    <input type="hidden" name="applicant_id" value="{{ $applicant->id }}"/>
    <input type="hidden" id="profileId" name="id" value=""/>
    <input type="hidden" id="editType" name="edit_type" value=""/>
    <div class="form-group-sm row">
        <label for="full_name" class="col-form-label-sm text-right col-sm-4">{{ __('homepage.full_name') }}<span class="font-danger">*</span></label>
        <div class="col-sm-8">
            <input type="text" name="full_name" class="form-control form-control-sm" id="full_name" placeholder="{{ __('homepage.full_name') }}">
        </div>
    </div>
    <div class="form-group-sm row">
        <label for="nationality" class="col-form-label-sm text-right col-sm-4">{{ __('homepage.nationality') }}<span class="font-danger">*</span></label>
        <div class="col-sm-8">
            <input type="text" name="nationality" class="form-control form-control-sm" id="deedDate" placeholder="{{ __('homepage.nationality') }}" />
        </div>
    </div>
    <div class="form-group-sm row">
        <label for="share_percentage" class="col-form-label-sm text-right col-sm-4">{{ __('homepage.share_percentage') }} (%)<span class="font-danger">*</span></label>
        <div class="col-sm-8">
            <input type="text" name="share_percentage" class="form-control form-control-sm" id="notaryName" placeholder="{{ __('homepage.share_percentage') }}" />
        </div>
    </div>
    <div class="form-group-sm row">
        <label for="email" class="col-form-label-sm text-right col-sm-4">{{ __('homepage.email') }}<span class="font-danger">*</span></label>
        <div class="col-sm-8">
            <input type="email" name="email" class="form-control form-control-sm" id="email" placeholder="{{ __('homepage.email') }}" />
        </div>
    </div>
    <div class="form-group-sm row">
        <label for="ktp_number" class="col-form-label-sm text-right col-sm-4">{{ __('homepage.ktp_number') }}<span class="font-danger">*</span></label>
        <div class="col-sm-8">
            <input type="text" name="ktp_number" class="form-control form-control-sm" id="ktp_number" placeholder="{{ __('homepage.ktp_number') }}" />
        </div>
    </div>
</div>
<div class="page2 display-none">
    <div class="form-group-sm row">
        <label for="attachment" class="col-form-label-sm text-right col-sm-4">{{ __('homepage.ktp_attachment') }}<span class="font-danger">*</span></label>
        <div class="col-sm-8">
            <input type="file" name="ktp_attachment" class="form-control form-control-sm" id="attachment" placeholder="{{ __('homepage.ktp_attachment') }}" />
        </div>
    </div>
</div>
