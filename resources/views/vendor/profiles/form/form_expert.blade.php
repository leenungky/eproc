<input type="hidden" name="vendor_profile_id" value="{{ $vendor->id }}"/>
<input type="hidden" id="profileId" name="id" value=""/>
<input type="hidden" id="editType" name="edit_type" value=""/>
<div class="page1 display-block">
<div class="form-group row mb-2">
    <div class="col-6">
        <div class="form-group row mb-2">
            <label for="full_name" class="col-4 col-form-label text-right">{{__('homepage.full_name')}}</label>
            <div class="col-8">
                <input type="text" id="full_name" name="full_name" placeholder="{{__('homepage.full_name')}}" required="required" class="form-control form-control-sm" maxlength="{{ Config::get('tables.vendor_profile_experts.full_name') }}">
            </div>
        </div>
        <div class="form-group row mb-2">
            <label for="date_of_birth" class="col-4 col-form-label text-right">{{__('homepage.date_of_birth')}}</label>
            <div class="col-8">
                <input type="text" id="date_of_birth" name="date_of_birth" placeholder="{{__('homepage.date_of_birth')}}" required="required" class="form-control form-control-sm datetimepicker-input date" data-toggle="datetimepicker" data-target="#date_of_birth">
            </div>
        </div>
        <div class="form-group row mb-2">
            <label for="education" class="col-4 col-form-label text-right">{{__('homepage.education')}}</label>
            <div class="col-8">
                <input type="text" id="education" name="education" placeholder="{{__('homepage.education')}}" required="required" class="form-control form-control-sm" maxlength="{{ Config::get('tables.vendor_profile_experts.full_name') }}">
            </div>
        </div>
        <div class="form-group row mb-2">
            <label for="university" class="col-4 col-form-label text-right">{{__('homepage.university')}}</label>
            <div class="col-8">
                <input type="text" id="university" name="university" placeholder="{{__('homepage.university')}}" required="required" class="form-control form-control-sm" maxlength="{{ Config::get('tables.vendor_profile_experts.full_name') }}">
            </div>
        </div>
        <div class="form-group row mb-2">
            <label for="experts_university" class="col-4 col-form-label text-right">{{__('homepage.experts_university')}}</label>
            <div class="col-8">
                <input type="text" id="experts_university" name="experts_university" placeholder="{{__('homepage.experts_university')}}" required="required" class="form-control form-control-sm" maxlength="{{ Config::get('tables.vendor_profile_experts.full_name') }}">
            </div>
        </div>
        <div class="form-group row mb-2">
            <label for="major" class="col-4 col-form-label text-right">{{__('homepage.major')}}</label>
            <div class="col-8">
                <input type="text" id="major" name="major" placeholder="{{__('homepage.major')}}" required="required" class="form-control form-control-sm" maxlength="{{ Config::get('tables.vendor_profile_experts.full_name') }}">
            </div>
        </div>
    </div>
    <div class="col-6">
        <div class="form-group row mb-2">
            <label for="ktp_number" class="col-4 col-form-label text-right">{{__('homepage.ktp_number')}}</label>
            <div class="col-8">
                <input type="text" id="ktp_number" name="ktp_number" placeholder="{{__('homepage.ktp_number')}}" required="required" class="form-control form-control-sm" maxlength="{{ Config::get('tables.vendor_profile_experts.full_name') }}">
            </div>
        </div>
        <div class="form-group row mb-2">
            <label for="address" class="col-4 col-form-label text-right">{{__('homepage.address')}}</label>
            <div class="col-8">
                <textarea id="address" name="address" placeholder="{{__('homepage.address')}}" required="required" class="form-control form-control-sm" maxlength="{{ Config::get('tables.vendor_profile_experts.address') }}"></textarea>
            </div>
        </div>
        <div class="form-group row mb-2">
            <label for="job_experience" class="col-4 col-form-label text-right">{{__('homepage.job_experience')}}</label>
            <div class="col-8">
                <textarea id="job_experience" name="job_experience" placeholder="{{__('homepage.job_experience')}}" required="required" class="form-control form-control-sm" maxlength="{{ Config::get('tables.vendor_profile_experts.job_experience') }}"></textarea>
            </div>
        </div>
        <div class="form-group row mb-2">
            <label for="years_experience" class="col-4 col-form-label text-right">{{__('homepage.years_experience')}}</label>
            <div class="col-8">
                <input type="text" id="years_experience" name="years_experience" placeholder="{{__('homepage.years_experience_number')}}" required="required" class="form-control form-control-sm" oninput='this.value = this.value.replace(/[^0-9]/g, "");'>
            </div>
        </div>
        <div class="form-group row mb-2">
            <label for="certification_number" class="col-4 col-form-label text-right">{{__('homepage.certification_number')}}</label>
            <div class="col-8">
                <input type="text" id="certification_number" name="certification_number" placeholder="{{__('homepage.certification_number')}}" required="required" class="form-control form-control-sm" maxlength="{{ Config::get('tables.vendor_profile_experts.certification_number') }}">
            </div>
        </div>
    </div>
</div>
</div>

<div class="page2 display-none">
    <table  class="table table-sm table-striped table-bordered">
        <thead>
            <tr>
                <th>{{ __('homepage.attachment') }}</th>
                <th>{{ __('homepage.action') }}</th>
            </tr>
        </thead>
        <tbody>
            <!-- <tr>
                <td colspan="2" class="text-center pad1x" style="padding: 8px; background-color: #fff3f3;"><i>{{ __('homepage.you_should_upload_related_document') }}</i></td>
            </tr> -->
            <tr>
            <td colspan="2" class="text-center pad1x" style="padding: 8px;"><a id="attachment_filename" target="_blank"></a></td>
            </tr>
        </tbody>
    </table>
    <div class="form-group-sm row">
        <div class="col-sm-12">
            <input type="file" name="attachment" class="form-control form-control-sm" id="attachment" placeholder="{{ __('homepage.attachment') }}" />
        </div>
    </div>
</div>
