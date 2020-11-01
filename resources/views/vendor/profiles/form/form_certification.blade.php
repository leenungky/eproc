<input type="hidden" name="vendor_profile_id" value="{{ $vendor->id }}"/>
<input type="hidden" id="profileId" name="id" value=""/>
<input type="hidden" id="editType" name="edit_type" value=""/>
<div class="page1 display-block">
<div class="form-group row mb-2">
    <label for="certification_type" class="col-3 col-form-label text-right">{{__('homepage.certification_type')}}</label>
    <div class="col-9">
        <select id="certification_type" name="certification_type" class="custom-select custom-select-sm" required="required">
            @foreach($certifications as $key=>$value)
            <option value="{{$value}}">{{$value}}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="form-group row mb-2">
    <label for="description" class="col-3 col-form-label text-right">{{__('homepage.description')}}</label>
    <div class="col-9">
        <textarea id="description" name="description" placeholder="{{__('homepage.description')}}" required="required" class="form-control form-control-sm" maxlength="{{ Config::get('tables.vendor_profile_certifications.description') }}"></textarea>
    </div>
</div>
<div class="form-group row mb-2">
    <label for="valid_from_date" class="col-3 col-form-label text-right">{{__('homepage.valid_from_date')}}</label>
    <div class="col-9">
        <input type="text" id="valid_from_date" name="valid_from_date" placeholder="{{__('homepage.valid_from_date')}}" required="required" class="form-control form-control-sm datetimepicker-input date" data-toggle="datetimepicker" data-target="#valid_from_date">
    </div>
</div>
<div class="form-group row mb-2">
    <label for="valid_thru_date" class="col-3 col-form-label text-right">{{__('homepage.valid_thru_date')}}</label>
    <div class="col-9">
        <input type="text" id="valid_thru_date" name="valid_thru_date" placeholder="{{__('homepage.valid_thru_date')}}" required="required" class="form-control form-control-sm  datetimepicker-input date" data-toggle="datetimepicker" data-target="#valid_thru_date">
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
            <input type="file" name="attachment" class="form-control form-control-sm" id="attachment" placeholder="{{ __('homepage.attachment') }}" />
        </div>
    </div>
</div>
