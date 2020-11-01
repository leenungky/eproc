<input type="hidden" name="applicant_id" value="{{ $applicant->id }}"/>
<div class="form-group row mb-2">
    <label for="certification_type" class="col-3 col-form-label text-right">{{__('homepage.certification_type')}}</label>
    <div class="col-9">
        <select id="certification_type" name="certification_type" class="custom-select custom-select-sm" required="required">
            @foreach($certifications as $key=>$value)
            <option value="{{$key}}">{{$value}}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="form-group row mb-2">
    <label for="description" class="col-3 col-form-label text-right">{{__('homepage.description')}}</label>
    <div class="col-9">
        <textarea id="description" name="description" placeholder="{{__('homepage.description')}}" required="required" class="form-control form-control-sm"></textarea>
    </div>
</div>
<div class="form-group row mb-2">
    <label for="valid_from_date" class="col-3 col-form-label text-right">{{__('homepage.valid_from_date')}}</label>
    <div class="col-9">
        <input type="text" id="valid_from_date" name="valid_from_date" placeholder="{{__('homepage.valid_from_date')}}" required="required" class="form-control form-control-sm">
    </div>
</div>
<div class="form-group row mb-2">
    <label for="valid_thru_date" class="col-3 col-form-label text-right">{{__('homepage.valid_thru_date')}}</label>
    <div class="col-9">
        <input type="text" id="valid_thru_date" name="valid_thru_date" placeholder="{{__('homepage.valid_thru_date')}}" required="required" class="form-control form-control-sm">
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
