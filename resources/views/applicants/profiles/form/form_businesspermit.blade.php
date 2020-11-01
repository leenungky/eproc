<div class="page1 display-block">
    <input type="hidden" name="applicant_id" value="{{ $applicant->id }}"/>
    <div class="form-group-sm row">
        <label for="business_permit_type" class="col-form-label-sm text-right col-sm-4">{{ __('homepage.business_permit_type') }}<span class="font-danger">*</span></label>
        <div class="col-sm-8">
            <input type="text" name="business_permit_type" class="form-control form-control-sm" id="business_permit_type" placeholder="{{ __('homepage.business_permit_type') }}">
        </div>
    </div>
    <div class="form-group-sm row">
        <label for="business_class" class="col-form-label-sm text-right col-sm-4">{{ __('homepage.business_class') }}<span class="font-danger">*</span></label>
        <div class="col-sm-8">
            <input type="text" name="business_class" class="form-control form-control-sm" id="business_class" placeholder="{{ __('homepage.business_class') }}" />
        </div>
    </div>
    <div class="form-group-sm row">
        <label for="business_permit_number" class="col-form-label-sm text-right col-sm-4">{{ __('homepage.business_permit_number') }}<span class="font-danger">*</span></label>
        <div class="col-sm-8">
            <input type="text" name="business_permit_number" class="form-control form-control-sm" id="business_permit_number" placeholder="{{ __('homepage.business_permit_number') }}" />
        </div>
    </div>
    <div class="form-group-sm row">
        <label for="valid_from_date" class="col-form-label-sm text-right col-sm-4">{{ __('homepage.valid_from_date') }}<span class="font-danger">*</span></label>
        <div class="col-sm-8">
            <input type="text" name="valid_from_date" class="form-control form-control-sm" id="valid_from_date" placeholder="{{ __('homepage.valid_from_date') }}" />
        </div>
    </div>
    <div class="form-group-sm row">
        <label for="valid_thru_date" class="col-form-label-sm text-right col-sm-4">{{ __('homepage.valid_thru_date') }}<span class="font-danger">*</span></label>
        <div class="col-sm-8">
            <input type="text" name="valid_thru_date" class="form-control form-control-sm" id="valid_thru_date" placeholder="{{ __('homepage.valid_thru_date') }}" />
        </div>
    </div>
    <div class="form-group-sm row">
        <label for="issued_by" class="col-form-label-sm text-right col-sm-4">{{ __('homepage.issued_by') }}<span class="font-danger">*</span></label>
        <div class="col-sm-8">
            <input type="text" name="issued_by" class="form-control form-control-sm" id="issued_by" placeholder="{{ __('homepage.issued_by') }}" />
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
            <tr>
                <td colspan="2" class="text-center pad1x" style="padding: 8px; background-color: #fff3f3;"><i>{{ __('homepage.you_should_upload_related_document') }}</i></td>
            </tr>
        </tbody>
    </table>
    <div class="form-group-sm row">
        <!--<label for="attachment" class="col-form-label-sm text-right col-sm-4">{{ __('homepage.attachment') }}<span class="font-danger">*</span></label>-->
        <div class="col-sm-12">
            <input type="file" name="business_permit_attachment" class="form-control form-control-sm" id="business_permit_attachment" placeholder="{{ __('homepage.business_permit_attachment') }}" />
        </div>
    </div>
</div>
