<input type="hidden" name="vendor_profile_id" value="{{ $vendor->id }}"/>
<input type="hidden" id="profileId" name="id" value=""/>
<input type="hidden" id="editType" name="edit_type" value=""/>

<div class="page1 display-block">
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
    <label for="tax_document_number" class="col-3 col-form-label text-right">{{__('homepage.tax_document_number')}}</label>
    <div class="col-9">
        <input type="text" id="tax_document_number" name="tax_document_number" placeholder="{{__('homepage.tax_document_number')}}" required="required" class="form-control form-control-sm" maxlength="{{ Config::get('tables.vendor_profile_taxes.tax_document_number') }}" />
    </div>
</div>
<div class="form-group row mb-2">
    <label for="issued_date" class="col-3 col-form-label text-right">{{__('homepage.issued_date')}}</label>
    <div class="col-9">
        <input type="text" id="issued_date" name="issued_date" placeholder="{{__('homepage.issued_date')}}" required="required" class="form-control form-control-sm datetimepicker-input date" data-toggle="datetimepicker" data-target="#issued_date">
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
            <td colspan="2" class="text-center pad1x" style="padding: 8px;"><a id="tax_document_attachment_filename" target="_blank"></a></td>
            </tr>
        </tbody>
    </table>
    <div class="form-group-sm row">
        <div class="col-sm-12">
            <input type="file" name="tax_document_attachment" class="form-control form-control-sm" id="tax_document_attachment" placeholder="{{ __('homepage.tax_document_attachment') }}" />
        </div>
    </div>
</div>
