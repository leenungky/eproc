<div class="page1 display-block">
    <input type="hidden" name="vendor_profile_id" value="{{ $vendor->id }}"/>
    <input type="hidden" id="profileId" name="id" value=""/>
    <input type="hidden" id="editType" name="edit_type" value=""/>
    <div class="form-group-sm row">
        <label for="deed_type" class="col-form-label text-right col-sm-4">{{ __('homepage.deed_type') }}</label>
        <div class="col-sm-8">
            <select id="deed_type" name="deed_type" class="custom-select custom-select-sm" required="">
                <option value=''> -- Select -- </option>
                <option value='Deed of Establishment (Akta Pendirian)'>Deed of Establishment (Akta Pendirian)</option>
                <option value='Last Updated of Company Deed (Akta Perubahan)'>Last Updated of Company Deed (Akta Perubahan)</option>
            </select>
        </div>
    </div>
    <div class="form-group-sm row">
        <label for="deed_number" class="col-form-label text-right col-sm-4">{{ __('homepage.deed_number') }}</label>
        <div class="col-sm-8">
            <input type="text" name="deed_number" class="form-control form-control-sm" id="deed_number" placeholder="{{ __('homepage.deed_number') }}" required=""  maxlength="{{ Config::get('tables.vendor_profile_deeds.deed_number') }}" />
        </div>
    </div>
    <div class="form-group-sm row">
        <label for="deed_date" class="col-form-label text-right col-sm-4">{{ __('homepage.deed_date') }}</label>
        <div class="col-sm-8">
            <input type="text" name="deed_date" class="form-control form-control-sm datetimepicker-input date" id="deed_date" placeholder="{{ __('homepage.deed_date') }}" data-toggle="datetimepicker" data-target="#deed_date" required=""/>
        </div>
    </div>
    <div class="form-group-sm row">
        <label for="notary_name" class="col-form-label text-right col-sm-4">{{ __('homepage.notary_name') }}</label>
        <div class="col-sm-8">
            <input type="text" name="notary_name" class="form-control form-control-sm" id="notary_name" placeholder="{{ __('homepage.notary_name') }}" required=""  maxlength="{{ Config::get('tables.vendor_profile_deeds.notary_name') }}"/>
        </div>
    </div>
    <div class="form-group-sm row">
        <label for="sk_menkumham_number" class="col-form-label text-right col-sm-4">{{ __('homepage.sk_menkumham_number') }}</label>
        <div class="col-sm-8">
            <input type="text" name="sk_menkumham_number" class="form-control form-control-sm" id="sk_menkumham_number" placeholder="{{ __('homepage.sk_menkumham_number') }}" required=""  maxlength="{{ Config::get('tables.vendor_profile_deeds.sk_menkumham_number') }}" />
        </div>
    </div>
    <div class="form-group-sm row">
        <label for="sk_menkumham_date" class="col-form-label text-right col-sm-4">{{ __('homepage.sk_menkumham_date') }}</label>
        <div class="col-sm-8">
            <input type="text" name="sk_menkumham_date" class="form-control form-control-sm datetimepicker-input date" id="sk_menkumham_date" placeholder="{{ __('homepage.sk_menkumham_date') }}" data-toggle="datetimepicker" data-target="#sk_menkumham_date" required=""/>
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
            <input type="file" name="attachment" class="form-control form-control-sm" id="attachment" placeholder="{{ __('homepage.attachment') }}" required="" />
        </div>
    </div>
</div>