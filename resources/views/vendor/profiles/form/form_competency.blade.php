<input type="hidden" name="vendor_profile_id" value="{{ $vendor->id }}"/>
<input type="hidden" id="profileId" name="id" value=""/>
<input type="hidden" id="editType" name="edit_type" value=""/>
<div class="page1 display-block">
<div class="form-group row mb-2">
    <label for="classification" class="col-3 col-form-label text-right">{{__('homepage.classification')}}</label>
    <div class="col-9">
        <select id="classification" name="classification" class="custom-select custom-select-sm" required="required">
            @foreach($classifications as $key=>$value)
            <option value="{{$key}}">{{$key.' - '.$value}}</option>
            @endforeach
        </select>
    </div>
</div>
{{--
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
--}}
<div class="form-group row mb-2">
    <label for="detail_competency" class="col-3 col-form-label text-right">{{__('homepage.detail_competency')}}</label>
    <div class="col-9">
        <textarea id="detail_competency" name="detail_competency" placeholder="{{__('homepage.detail_competency')}}" required="required" class="form-control form-control-sm" maxlength="{{ Config::get('tables.vendor_profile_competencies.detail_competency') }}"></textarea>
    </div>
</div>
<div class="form-group row mb-2">
    <label for="vendor_type" class="col-3 col-form-label text-right">{{__('homepage.vendor_type')}}</label>
    <div class="col-9">
        <select id="vendor_type" name="vendor_type" class="custom-select custom-select-sm" required="required">
            @foreach($vendorTypes as $key=>$value)
            <option value="{{$value}}">{{$value}}</option>
            @endforeach
        </select>
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
