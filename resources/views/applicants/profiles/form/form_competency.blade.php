<input type="hidden" name="applicant_id" value="{{ $applicant->id }}"/>
<div class="form-group row mb-2">
    <label for="classification" class="col-3 col-form-label text-right">{{__('homepage.classification')}}</label>
    <div class="col-9">
        <select id="classification" name="classification" class="custom-select custom-select-sm" required="required">
            @foreach($classifications as $key=>$value)
            <option value="{{$key}}">{{$value}}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="form-group row mb-2">
    <label for="sub_classification" class="col-3 col-form-label text-right">{{__('homepage.sub_classification')}}</label>
    <div class="col-9">
        <select id="sub_classification" name="sub_classification" class="custom-select custom-select-sm" required="required">
            @foreach($subclassifications as $key=>$value)
            <option value="{{$key}}">{{$value}}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="form-group row mb-2">
    <label for="competency_detail" class="col-3 col-form-label text-right">{{__('homepage.competency_detail')}}</label>
    <div class="col-9">
        <textarea id="competency_detail" name="competency_detail" placeholder="{{__('homepage.competency_detail')}}" required="required" class="form-control form-control-sm"></textarea>
    </div>
</div>
<div class="form-group row mb-2">
    <label for="vendor_type" class="col-3 col-form-label text-right">{{__('homepage.vendor_type')}}</label>
    <div class="col-9">
        <select id="vendor_type" name="vendor_type" class="custom-select custom-select-sm" required="required">
            @foreach($vendorTypes as $key=>$value)
            <option value="{{$key}}">{{$value}}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="form-group row mb-2">
    <label for="attachment" class="col-3 col-form-label text-right">{{__('homepage.attachment')}}</label>
    <div class="col-9">
        <div class="custom-file">
            <input type="file" id="attachment" name="attachment" required="required" class="custom-file-input custom-file-input-sm">
            <label id="attachment_label" class="custom-file-label" for="attachment">{{__('homepage.attachment')}}</label>
        </div>
    </div>
</div>
