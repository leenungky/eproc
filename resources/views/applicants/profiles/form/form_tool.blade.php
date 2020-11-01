<input type="hidden" name="applicant_id" value="{{ $applicant->id }}"/>
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
            <label for="total" class="col-4 col-form-label text-right">{{__('homepage.total')}}</label>
            <div class="col-8">
                <input type="text" id="total" name="total" placeholder="{{__('homepage.total')}}" required="required" class="form-control form-control-sm">
            </div>
        </div>
        <div class="form-group row mb-2">
            <label for="measurement" class="col-4 col-form-label text-right">{{__('homepage.measurement')}}</label>
            <div class="col-8">
                <input type="text" id="measurement" name="measurement" placeholder="{{__('homepage.measurement')}}" required="required" class="form-control form-control-sm">
            </div>
        </div>
        <div class="form-group row mb-2">
            <label for="brand" class="col-4 col-form-label text-right">{{__('homepage.brand')}}</label>
            <div class="col-8">
                <input type="text" id="brand" name="brand" placeholder="{{__('homepage.brand')}}" required="required" class="form-control form-control-sm">
            </div>
        </div>
    </div>
    <div class="col-6">
        <div class="form-group row mb-2">
            <label for="condition" class="col-4 col-form-label text-right">{{__('homepage.condition')}}</label>
            <div class="col-8">
                <input type="text" id="condition" name="condition" placeholder="{{__('homepage.condition')}}" required="required" class="form-control form-control-sm">
            </div>
        </div>
        <div class="form-group row mb-2">
            <label for="location" class="col-4 col-form-label text-right">{{__('homepage.location')}}</label>
            <div class="col-8">
                <textarea id="location" name="location" placeholder="{{__('homepage.location')}}" required="required" class="form-control form-control-sm"></textarea>
            </div>
        </div>
        <div class="form-group row mb-2">
            <label for="ownership" class="col-4 col-form-label text-right">{{__('homepage.ownership')}}</label>
            <div class="col-8">
                <input type="text" id="ownership" name="ownership" placeholder="{{__('homepage.ownership')}}" required="required" class="form-control form-control-sm">
            </div>
        </div>
    </div>
</div>