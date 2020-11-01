<input type="hidden" name="applicant_id" value="{{ $applicant->id }}"/>
<input type="hidden" id="profileId" name="id" value=""/>
<input type="hidden" id="editType" name="edit_type" value=""/>
<div class="form-group row mb-2">
    <label for="tax_document_type" class="col-3 col-form-label text-right">{{__('homepage.tax_document_type')}}</label>
    <div class="col-9">
        <select id="tax_document_type" name="tax_document_type" class="custom-select custom-select-sm" required="required">
            @foreach($taxDocumentTypes as $key=>$value)
            <option value="{{$key}}">{{$value}}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="form-group row mb-2">
    <label for="tax_document_no" class="col-3 col-form-label text-right">{{__('homepage.tax_document_no')}}</label>
    <div class="col-9">
        <input type="text" id="tax_document_no" name="tax_document_no" placeholder="{{__('homepage.tax_document_no')}}" required="required" class="form-control form-control-sm">
    </div>
</div>
<div class="form-group row mb-2">
    <label for="tax_document_date" class="col-3 col-form-label text-right">{{__('homepage.tax_document_date')}}</label>
    <div class="col-9">
        <input type="text" id="tax_document_date" name="tax_document_date" placeholder="{{__('homepage.tax_document_date')}}" required="required" class="form-control form-control-sm date" data-toggle="datetimepicker" data-target="#tax_document_date">
    </div>
</div>
<div class="form-group row mb-2">
    <label for="tax_document_attachment" class="col-3 col-form-label text-right">{{__('homepage.tax_document_attachment')}}</label>
    <div class="col-9">
        <div class="custom-file">
            <input type="file" id="tax_document_attachment" name="tax_document_attachment" required="required" class="custom-file-input custom-file-input-sm">
            <label id="tax_document_attachment_label" class="custom-file-label" for="tax_document_attachment">{{__('homepage.tax_document_attachment')}}</label>
            <a id="tax_document_attachment_filename" target="_blank"></a>
        </div>
    </div>
</div>
