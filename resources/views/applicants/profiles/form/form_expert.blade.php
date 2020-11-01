<input type="hidden" name="applicant_id" value="{{ $applicant->id }}"/>
<div class="form-group row mb-2">
    <div class="col-6">
        <div class="form-group row mb-2">
            <label for="full_name" class="col-4 col-form-label text-right">{{__('homepage.full_name')}}</label>
            <div class="col-8">
                <input type="text" id="full_name" name="full_name" placeholder="{{__('homepage.full_name')}}" required="required" class="form-control form-control-sm">
            </div>
        </div>
        <div class="form-group row mb-2">
            <label for="date_of_birth" class="col-4 col-form-label text-right">{{__('homepage.date_of_birth')}}</label>
            <div class="col-8">
                <input type="text" id="date_of_birth" name="date_of_birth" placeholder="{{__('homepage.date_of_birth')}}" required="required" class="form-control form-control-sm">
            </div>
        </div>
        <div class="form-group row mb-2">
            <label for="education" class="col-4 col-form-label text-right">{{__('homepage.education')}}</label>
            <div class="col-8">
                <input type="text" id="education" name="education" placeholder="{{__('homepage.education')}}" required="required" class="form-control form-control-sm">
            </div>
        </div>
        <div class="form-group row mb-2">
            <label for="major" class="col-4 col-form-label text-right">{{__('homepage.major')}}</label>
            <div class="col-8">
                <input type="text" id="major" name="major" placeholder="{{__('homepage.major')}}" required="required" class="form-control form-control-sm">
            </div>
        </div>
        <div class="form-group row mb-2">
            <label for="ktp_number" class="col-4 col-form-label text-right">{{__('homepage.ktp_number')}}</label>
            <div class="col-8">
                <input type="text" id="ktp_number" name="ktp_number" placeholder="{{__('homepage.ktp_number')}}" required="required" class="form-control form-control-sm">
            </div>
        </div>
        <div class="form-group row mb-2">
            <label for="address" class="col-4 col-form-label text-right">{{__('homepage.address')}}</label>
            <div class="col-8">
                <textarea id="address" name="address" placeholder="{{__('homepage.address')}}" required="required" class="form-control form-control-sm"></textarea>
            </div>
        </div>
    </div>
    <div class="col-6">
        <div class="form-group row mb-2">
            <label for="job_experience" class="col-4 col-form-label text-right">{{__('homepage.job_experience')}}</label>
            <div class="col-8">
                <textarea id="job_experience" name="job_experience" placeholder="{{__('homepage.job_experience')}}" required="required" class="form-control form-control-sm"></textarea>
            </div>
        </div>
        <div class="form-group row mb-2">
            <label for="year_experience" class="col-4 col-form-label text-right">{{__('homepage.year_experience')}}</label>
            <div class="col-8">
                <input type="text" id="year_experience" name="year_experience" placeholder="{{__('homepage.year_experience')}}" required="required" class="form-control form-control-sm">
            </div>
        </div>
        <div class="form-group row mb-2">
            <label for="certification" class="col-4 col-form-label text-right">{{__('homepage.certification')}}</label>
            <div class="col-8">
                <input type="text" id="certification" name="certification" placeholder="{{__('homepage.certification')}}" required="required" class="form-control form-control-sm">
            </div>
        </div>
        <div class="form-group row mb-2">
            <label for="certification_number" class="col-4 col-form-label text-right">{{__('homepage.certification_number')}}</label>
            <div class="col-8">
                <input type="text" id="certification_number" name="certification_number" placeholder="{{__('homepage.certification_number')}}" required="required" class="form-control form-control-sm">
            </div>
        </div>
        <div class="form-group row mb-2">
            <label for="attachment" class="col-4 col-form-label text-right">{{__('homepage.attachment')}}</label>
            <div class="col-8">
                <div class="custom-file">
                    <input type="file" id="attachment" name="attachment" required="required" class="custom-file-input custom-file-input-sm">
                    <label id="attachment_label" class="custom-file-label" for="attachment">{{__('homepage.attachment')}}</label>
                </div>
            </div>
        </div>
    </div>
</div>