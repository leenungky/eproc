<input type="hidden" name="vendor_profile_id" value="{{ $vendor->id }}"/>
<input type="hidden" id="profileId" name="id" value=""/>
<input type="hidden" id="editType" name="edit_type" value=""/>
<div class="page1 display-block">
<div class="form-group row mb-2">
    <label for="classification" class="col-4 col-form-label text-right">{{__('homepage.classification')}}</label>
    <div class="col-8">
        <select id="classification" name="classification" class="custom-select custom-select-sm" required="required">
            @foreach($classifications as $key=>$value)
            <option value="{{$key}}">{{$key.' - '.$value}}</option>
            @endforeach
        </select>
    </div>
</div>
{{--
<div class="form-group row mb-2">
    <label for="sub_classification" class="col-4 col-form-label text-right">{{__('homepage.sub_classification')}}</label>
    <div class="col-8">
        <select id="sub_classification" name="sub_classification" class="custom-select custom-select-sm" required="required">
            @foreach($subclassifications as $key=>$value)
            <option value="{{$key}}">{{$value}}</option>
            @endforeach
        </select>
    </div>
</div>
--}}
<div class="form-group row mb-2"></div>
<div class="form-group row mb-2"></div>

<div class="form-group row mb-2">
    <label for="project_name" class="col-4 col-form-label text-right">{{__('homepage.project_name')}}</label>
    <div class="col-8">
        <input type="text" id="project_name" name="project_name" placeholder="{{__('homepage.project_name')}}" required="required" class="form-control form-control-sm" maxlength="{{ Config::get('tables.vendor_profile_experience.project_name') }}">
    </div>
</div>
<div class="form-group row mb-2">
    <label for="project_location" class="col-4 col-form-label text-right">{{__('homepage.project_location')}}</label>
    <div class="col-8">
        <input type="text" id="project_location" name="project_location" placeholder="{{__('homepage.project_location')}}" required="required" class="form-control form-control-sm" maxlength="{{ Config::get('tables.vendor_profile_experience.project_location') }}">
    </div>
</div>
<div class="form-group row mb-2">
    <label for="contract_owner" class="col-4 col-form-label text-right">{{__('homepage.contract_owner')}}</label>
    <div class="col-8">
        <input type="text" id="contract_owner" name="contract_owner" placeholder="{{__('homepage.contract_owner')}}" required="required" class="form-control form-control-sm" maxlength="{{ Config::get('tables.vendor_profile_experience.contract_owner') }}">
    </div>
</div>

<div class="form-group row mb-2"></div>
<div class="form-group row mb-2"></div>

<div class="form-group row mb-2">
    <div class="col-6">
        <div class="form-group row mb-2">
            <label for="address" class="col-4 col-form-label text-right">{{__('homepage.address')}}</label>
            <div class="col-8">
                <textarea id="address" rows="3" name="address" placeholder="{{__('homepage.address')}}" required="required" class="form-control form-control-sm" maxlength="{{ Config::get('tables.vendor_profile_experience.address') }}"></textarea>
            </div>
        </div>
        <div class="form-group row mb-2">
            <label for="country" class="col-4 col-form-label text-right">{{__('homepage.country')}}</label>
            <div class="col-8">
                <select id="country" name="country" class="custom-select custom-select-sm" required="true">
                    @foreach($countries as $key=>$value)
                    <option value="{{$key}}">{{$value}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group row mb-2">
            <label for="province" class="col-4 col-form-label text-right country-attr">{{__('homepage.province')}}<span class="font-danger"></span></label>
            <div class="col-8">
                <select id="province" name="province" class="custom-select custom-select-sm">
                </select>
            </div>
        </div>
        <div class="form-group row mb-2">
            <label for="city" class="col-4 col-form-label text-right country-attr">{{__('homepage.city')}}<span class="font-danger"></span></label>
            <div class="col-8">
                <select id="city" name="city" class="custom-select custom-select-sm" disabled>
                </select>
            </div>
        </div>
        <div class="form-group row mb-2">
            <label for="sub_district" class="col-4 col-form-label text-right country-attr">{{__('homepage.sub_district')}}<span class="font-danger"></span></label>
            <div class="col-8">
                <select id="sub_district" name="sub_district" class="custom-select custom-select-sm" disabled>
                </select>
            </div>
        </div>
        <div class="form-group row mb-2">
            <label for="postal_code" class="col-4 col-form-label text-right">{{__('homepage.postal_code')}}</label>
            <div class="col-8">
                <input type="text" id="postal_code" name="postal_code" placeholder="{{__('homepage.postal_code')}}" required="required" class="form-control form-control-sm" maxlength="{{ Config::get('tables.vendor_profile_experience.postal_code') }}">
            </div>
        </div>
    </div>
    <div class="col-6">
        <div class="form-group row mb-2">
            <label for="contact_person" class="col-4 col-form-label text-right">{{__('homepage.contact_person')}}</label>
            <div class="col-8">
                <input type="text" id="contact_person" name="contact_person" placeholder="{{__('homepage.contact_person')}}" required="required" class="form-control form-control-sm" maxlength="{{ Config::get('tables.vendor_profile_experience.contact_person') }}">
            </div>
        </div>
        <div class="form-group row mb-2">
            <label for="phone_number" class="col-4 col-form-label text-right">{{__('homepage.phone_number')}}</label>
            <div class="col-8">
                <input type="text" id="phone_number" name="phone_number" placeholder="{{__('homepage.phone_number')}}" required="required" class="form-control form-control-sm" maxlength="{{ Config::get('tables.vendor_profile_experience.phone_number') }}">
            </div>
        </div>
        <div class="form-group row mb-2">
            <label for="contract_number" class="col-4 col-form-label text-right">{{__('homepage.contract_number')}}</label>
            <div class="col-8">
                <input type="text" id="contract_number" name="contract_number" placeholder="{{__('homepage.contract_number')}}" required="required" class="form-control form-control-sm" maxlength="{{ Config::get('tables.vendor_profile_experience.contract_number') }}">
            </div>
        </div>
        <div class="form-group row mb-2">
            <label for="valid_from_date" class="col-4 col-form-label text-right">{{__('homepage.valid_from_date')}}</label>
            <div class="col-8">
                <input type="text" id="valid_from_date" name="valid_from_date" placeholder="{{__('homepage.valid_from_date')}}" required="required" class="form-control form-control-sm datetimepicker-input date" data-toggle="datetimepicker" data-target="#valid_from_date">
            </div>
        </div>
        <div class="form-group row mb-2">
            <label for="valid_thru_date" class="col-4 col-form-label text-right">{{__('homepage.valid_thru_date')}}</label>
            <div class="col-8">
                <input type="text" id="valid_thru_date" name="valid_thru_date" placeholder="{{__('homepage.valid_thru_date')}}" required="required" class="form-control form-control-sm datetimepicker-input date" data-toggle="datetimepicker" data-target="#valid_thru_date">
            </div>
        </div>
        <div class="form-group row mb-2">
            <label for="currency" class="col-4 col-form-label text-right">{{__('homepage.currency')}}</label>
            <div class="col-8">
                <select id="currency" name="currency" class="custom-select custom-select-sm" required="required">
                    @foreach($currencies as $key=>$value)
                    <option value="{{$key}}">{{$key.' - '.$value}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group row mb-2">
            <label for="contract_value" class="col-4 col-form-label text-right">{{__('homepage.contract_value')}}</label>
            <div class="col-8">
                <input type="text" id="contract_value" name="contract_value" placeholder="{{__('homepage.contract_value')}}" required="required" class="form-control form-control-sm" maxlength="{{ Config::get('tables.vendor_profile_experience.contract_value') }}">
            </div>
        </div>
    </div>
</div>

<div class="form-group row mb-2"></div>
<div class="form-group row mb-2"></div>

<div class="form-group row mb-2">
    <label for="bast_wan_date" class="col-4 col-form-label text-right">{{__('homepage.bast_wan_date')}}</label>
    <div class="col-8">
        <input type="text" id="bast_wan_date" name="bast_wan_date" placeholder="{{__('homepage.bast_wan_date')}}" required="required" class="form-control form-control-sm datetimepicker-input date" data-toggle="datetimepicker" data-target="#bast_wan_date">
    </div>
</div>
<div class="form-group row mb-2">
    <label for="bast_wan_number" class="col-4 col-form-label text-right">{{__('homepage.bast_wan_number')}}</label>
    <div class="col-8">
        <input type="text" id="bast_wan_number" name="bast_wan_number" placeholder="{{__('homepage.bast_wan_number')}}" required="required" class="form-control form-control-sm" maxlength="{{ Config::get('tables.vendor_profile_experience.bast_wan_number') }}">
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
            <td colspan="2" class="text-center pad1x" style="padding: 8px;"><a id="bast_wan_attachment_filename" target="_blank"></a></td>
            </tr>
        </tbody>
    </table>
    <div class="form-group-sm row">
        <div class="col-sm-12">
            <input type="file" name="bast_wan_attachment" class="form-control form-control-sm" id="bast_wan_attachment" placeholder="{{ __('homepage.attachment') }}" />
        </div>
    </div>
</div>
