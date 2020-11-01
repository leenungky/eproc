<input type="hidden" name="vendor_profile_id" value="{{ $vendor->id }}"/>
<input type="hidden" id="profileId" name="id" value=""/>
<input type="hidden" id="editType" name="edit_type" value=""/>
<div class="form-group row mb-2">
    <div class="col-6">
        <div class="form-group row mb-2">
            <label for="equipment_type" class="col-4 col-form-label text-right">{{__('homepage.equipment_type')}}</label>
            <div class="col-8">
                <select id="equipment_type" name="equipment_type" class="custom-select custom-select-sm" required="required">
                    @foreach($equipmentTypes as $key=>$value)
                    <option value="{{$key}}">{{$value}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group row mb-2">
            <label for="total_qty" class="col-4 col-form-label text-right">{{__('homepage.total_qty')}}</label>
            <div class="col-8">
                <input type="text" id="total_qty" name="total_qty" placeholder="{{__('homepage.total_qty')}}" required="required" class="form-control form-control-sm">
            </div>
        </div>
        <div class="form-group row mb-2">
            <label for="measurement" class="col-4 col-form-label text-right">{{__('homepage.measurement')}}</label>
            <div class="col-8">
                <input type="text" id="measurement" name="measurement" placeholder="{{__('homepage.measurement')}}" required="required" class="form-control form-control-sm" maxlength="{{ Config::get('tables.vendor_profile_tools.measurement') }}">
            </div>
        </div>
        <div class="form-group row mb-2">
            <label for="brand" class="col-4 col-form-label text-right">{{__('homepage.brand')}}</label>
            <div class="col-8">
                <input type="text" id="brand" name="brand" placeholder="{{__('homepage.brand')}}" required="required" class="form-control form-control-sm" maxlength="{{ Config::get('tables.vendor_profile_tools.brand') }}">
            </div>
        </div>
    </div>
    <div class="col-6">
        <div class="form-group row mb-2">
            <label for="condition" class="col-4 col-form-label text-right">{{__('homepage.condition')}}</label>
            <div class="col-8">
                <input type="text" id="condition" name="condition" placeholder="{{__('homepage.condition')}}" required="required" class="form-control form-control-sm" maxlength="{{ Config::get('tables.vendor_profile_tools.condition') }}">
            </div>
        </div>
        <div class="form-group row mb-2">
            <label for="location" class="col-4 col-form-label text-right">{{__('homepage.location')}}</label>
            <div class="col-8">
                <textarea id="location" name="location" placeholder="{{__('homepage.location')}}" required="required" class="form-control form-control-sm" maxlength="{{ Config::get('tables.vendor_profile_tools.location') }}"></textarea>
            </div>
        </div>
        <div class="form-group row mb-2">
            <label for="manufacturing_date" class="col-4 col-form-label text-right">{{__('homepage.manufacturing_date')}}</label>
            <div class="col-8">
                <input type="text" id="manufacturing_date" name="manufacturing_date" placeholder="{{__('homepage.manufacturing_date')}}" required="required" class="form-control form-control-sm datetimepicker-input date" data-toggle="datetimepicker" data-target="#manufacturing_date">
            </div>
        </div>
        <div class="form-group row mb-2">
            <label for="ownership" class="col-4 col-form-label text-right">{{__('homepage.ownership')}}</label>
            <div class="col-8">
                <input type="text" id="ownership" name="ownership" placeholder="{{__('homepage.ownership')}}" required="required" class="form-control form-control-sm" maxlength="{{ Config::get('tables.vendor_profile_tools.ownership') }}">
            </div>
        </div>
    </div>
</div>