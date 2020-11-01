<input type="hidden" name="applicant_id" value="{{ $applicant->id }}"/>
<div class="form-group row mb-2">
    <label for="classification" class="col-4 col-form-label text-right">{{__('homepage.classification')}}</label>
    <div class="col-8">
        <select id="classification" name="classification" class="custom-select custom-select-sm" required="required">
            @foreach($classifications as $key=>$value)
            <option value="{{$key}}">{{$value}}</option>
            @endforeach
        </select>
    </div>
</div>
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

<div class="form-group row mb-2"></div>
<div class="form-group row mb-2"></div>

<div class="form-group row mb-2">
    <label for="project_name" class="col-4 col-form-label text-right">{{__('homepage.project_name')}}</label>
    <div class="col-8">
        <input type="text" id="project_name" name="project_name" placeholder="{{__('homepage.project_name')}}" required="required" class="form-control form-control-sm">
    </div>
</div>
<div class="form-group row mb-2">
    <label for="project_location" class="col-4 col-form-label text-right">{{__('homepage.project_location')}}</label>
    <div class="col-8">
        <input type="text" id="project_location" name="project_location" placeholder="{{__('homepage.project_location')}}" required="required" class="form-control form-control-sm">
    </div>
</div>
<div class="form-group row mb-2">
    <label for="contract_owner" class="col-4 col-form-label text-right">{{__('homepage.contract_owner')}}</label>
    <div class="col-8">
        <input type="text" id="contract_owner" name="contract_owner" placeholder="{{__('homepage.contract_owner')}}" required="required" class="form-control form-control-sm">
    </div>
</div>

<div class="form-group row mb-2"></div>
<div class="form-group row mb-2"></div>

<div class="form-group row mb-2">
    <div class="col-6">
        <div class="form-group row mb-2">
            <label for="address" class="col-4 col-form-label text-right">{{__('homepage.address')}}</label>
            <div class="col-8">
                <textarea id="address" rows="3" name="address" placeholder="{{__('homepage.address')}}" required="required" class="form-control form-control-sm"></textarea>
            </div>
        </div>
        <div class="form-group row mb-2">
            <label for="country" class="col-4 col-form-label text-right">{{__('homepage.country')}}</label>
            <div class="col-8">
                <select id="country" name="country" class="custom-select custom-select-sm" required="required">
                    @foreach($countries as $key=>$value)
                    <option value="{{$key}}">{{$value}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group row mb-2">
            <label for="province" class="col-4 col-form-label text-right">{{__('homepage.province')}}</label>
            <div class="col-8">
                <select id="province" name="province" class="custom-select custom-select-sm" required="required">
                    @foreach($provinces as $key=>$value)
                    <option value="{{$key}}">{{$value}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group row mb-2">
            <label for="city" class="col-4 col-form-label text-right">{{__('homepage.city')}}</label>
            <div class="col-8">
                <select id="city" name="city" class="custom-select custom-select-sm" required="required">
                    @foreach($cities as $key=>$value)
                    <option value="{{$key}}">{{$value}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group row mb-2">
            <label for="sub_district" class="col-4 col-form-label text-right">{{__('homepage.sub_district')}}</label>
            <div class="col-8">
                <select id="sub_district" name="sub_district" class="custom-select custom-select-sm" required="required">
                    @foreach($subdistricts as $key=>$value)
                    <option value="{{$key}}">{{$value}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group row mb-2">
            <label for="postal_code" class="col-4 col-form-label text-right">{{__('homepage.postal_code')}}</label>
            <div class="col-8">
                <input type="text" id="postal_code" name="postal_code" placeholder="{{__('homepage.postal_code')}}" required="required" class="form-control form-control-sm">
            </div>
        </div>
    </div>
    <div class="col-6">
        <div class="form-group row mb-2">
            <label for="contact_person" class="col-4 col-form-label text-right">{{__('homepage.contact_person')}}</label>
            <div class="col-8">
                <input type="text" id="contact_person" name="contact_person" placeholder="{{__('homepage.contact_person')}}" required="required" class="form-control form-control-sm">
            </div>
        </div>
        <div class="form-group row mb-2">
            <label for="phone_number" class="col-4 col-form-label text-right">{{__('homepage.phone_number')}}</label>
            <div class="col-8">
                <input type="text" id="phone_number" name="phone_number" placeholder="{{__('homepage.phone_number')}}" required="required" class="form-control form-control-sm">
            </div>
        </div>
        <div class="form-group row mb-2">
            <label for="contract_number" class="col-4 col-form-label text-right">{{__('homepage.contract_number')}}</label>
            <div class="col-8">
                <input type="text" id="contract_number" name="contract_number" placeholder="{{__('homepage.contract_number')}}" required="required" class="form-control form-control-sm">
            </div>
        </div>
        <div class="form-group row mb-2">
            <label for="valid_from_date" class="col-4 col-form-label text-right">{{__('homepage.valid_from_date')}}</label>
            <div class="col-8">
                <input type="text" id="valid_from_date" name="valid_from_date" placeholder="{{__('homepage.valid_from_date')}}" required="required" class="form-control form-control-sm">
            </div>
        </div>
        <div class="form-group row mb-2">
            <label for="valid_thru_date" class="col-4 col-form-label text-right">{{__('homepage.valid_thru_date')}}</label>
            <div class="col-8">
                <input type="text" id="valid_thru_date" name="valid_thru_date" placeholder="{{__('homepage.valid_thru_date')}}" required="required" class="form-control form-control-sm">
            </div>
        </div>
        <div class="form-group row mb-2">
            <label for="currency" class="col-4 col-form-label text-right">{{__('homepage.currency')}}</label>
            <div class="col-8">
                <select id="currency" name="currency" class="custom-select custom-select-sm" required="required">
                    @foreach($currencies as $key=>$value)
                    <option value="{{$key}}">{{$value}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group row mb-2">
            <label for="contract_value" class="col-4 col-form-label text-right">{{__('homepage.contract_value')}}</label>
            <div class="col-8">
                <input type="text" id="contract_value" name="contract_value" placeholder="{{__('homepage.contract_value')}}" required="required" class="form-control form-control-sm">
            </div>
        </div>
    </div>
</div>

<div class="form-group row mb-2"></div>
<div class="form-group row mb-2"></div>

<div class="form-group row mb-2">
    <div class="col-6">
        <div class="form-group row mb-2">
            <label for="bast_date" class="col-4 col-form-label text-right">{{__('homepage.bast_date')}}</label>
            <div class="col-8">
                <input type="text" id="bast_date" name="bast_date" placeholder="{{__('homepage.bast_date')}}" required="required" class="form-control form-control-sm">
            </div>
        </div>
        <div class="form-group row mb-2">
            <label for="bast_no" class="col-4 col-form-label text-right">{{__('homepage.bast_no')}}</label>
            <div class="col-8">
                <input type="text" id="bast_no" name="bast_no" placeholder="{{__('homepage.bast_no')}}" required="required" class="form-control form-control-sm">
            </div>
        </div>
        <div class="form-group row mb-2">
            <label for="bast_attachment" class="col-4 col-form-label text-right">{{__('homepage.bast_attachment')}}</label>
            <div class="col-8">
                <div class="custom-file">
                    <input type="file" id="bast_attachment" name="bast_attachment" required="required" class="custom-file-input custom-file-input-sm">
                    <label id="bast_attachment_label" class="custom-file-label" for="bast_attachment">{{__('homepage.bast_attachment')}}</label>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6">
        <div class="form-group row mb-2">
            <label for="testimony" class="col-4 col-form-label text-right">{{__('homepage.testimony')}}</label>
            <div class="col-8">
                <input type="text" id="testimony" name="testimony" placeholder="{{__('homepage.testimony')}}" required="required" class="form-control form-control-sm">
            </div>
        </div>
        <div class="form-group row mb-2">
            <label for="testimony_attachment" class="col-4 col-form-label text-right">{{__('homepage.testimony_attachment')}}</label>
            <div class="col-8">
                <div class="custom-file">
                    <input type="file" id="testimony_attachment" name="testimony_attachment" required="required" class="custom-file-input custom-file-input-sm">
                    <label id="testimony_attachment_label" class="custom-file-label" for="testimony_attachment">{{__('homepage.testimony_attachment')}}</label>
                </div>
            </div>
        </div>
    </div>
</div>
