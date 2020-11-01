<div class="page1 display-block">
    <input type="hidden" name="vendor_profile_id" value="{{ $vendor->id }}"/>
    <input type="hidden" id="profileId" name="id" value=""/>
    <input type="hidden" id="editType" name="edit_type" value=""/>
    <div class="form-group-sm row">
        <label for="full_name" class="col-form-label text-right col-sm-4">{{ __('homepage.full_name') }}<span class="font-danger">*</span></label>
        <div class="col-sm-8">
            <input type="text" name="full_name" class="form-control form-control-sm" id="full_name" placeholder="{{ __('homepage.full_name') }}" maxlength="{{ Config::get('tables.vendor_profile_shareholders.full_name') }}" />
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
        <label for="share_percentage" class="col-form-label text-right col-sm-4">{{ __('homepage.share_percentage') }}<span class="font-danger">*</span></label>
        <div class="col-sm-8">
            <input type="number" name="share_percentage" class="form-control form-control-sm" id="share_percentage" placeholder="{{ __('homepage.share_percentage') }}" />
        </div>
    </div>
    <div class="form-group-sm row">
        <label for="email" class="col-form-label text-right col-sm-4">{{ __('homepage.email') }}<span class="font-danger">*</span></label>
        <div class="col-sm-8">
            <input type="email" name="email" class="form-control form-control-sm" id="email" placeholder="{{ __('homepage.email') }}" maxlength="{{ Config::get('tables.vendor_profile_shareholders.email') }}" />
        </div>
    </div>
    <div class="form-group-sm row">
        <label for="ktp_number" class="col-form-label text-right col-sm-4">{{ __('homepage.ktp_number') }}<span class="font-danger">*</span></label>
        <div class="col-sm-8">
            <input type="text" name="ktp_number" class="form-control form-control-sm" id="ktp_number" placeholder="{{ __('homepage.ktp_number') }}" maxlength="{{ Config::get('tables.vendor_profile_shareholders.ktp_number') }}" />
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
            <td colspan="2" class="text-center pad1x" style="padding: 8px;"><a id="ktp_attachment_filename" target="_blank"></a></td>
            </tr>
        </tbody>
    </table>
    <div class="form-group-sm row">
        <div class="col-sm-12">
            <input type="file" name="ktp_attachment" class="form-control form-control-sm" id="ktp_attachment" placeholder="{{ __('homepage.attachment') }}" />
        </div>
    </div>
</div>
