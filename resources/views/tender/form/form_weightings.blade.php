<div class="form-group row mb-2">
    <label for="criteria" class="col-3 col-form-label text-right">{{__('tender.bidding.fields.criteria')}}</label>
    <div class="col-9">
        <input id="id" name="id" type="hidden" value="">
        <input id="sequence_done" name="sequence_done" type="hidden" value="false">
        <input id="criteria" name="criteria" placeholder="{{__('tender.bidding.fields.criteria')}}"
            type="text" required="required" class="form-control form-control-sm">
    </div>
</div>
{{-- <div class="form-group row mb-2">
    <label for="order" class="col-3 col-form-label text-right">{{__('tender.bidding.fields.order')}}</label>
    <div class="col-9">
        <input type="text" id="order" name="order" class="form-control form-control-sm"
            placeholder="{{__('tender.bidding.fields.order')}}" />
    </div>
</div> --}}
<div class="form-group row mb-2">
    <label for="submission_method" class="col-3 col-form-label text-right">{{__('tender.bidding.fields.submission_method')}}</label>
    <div class="col-9">
        <select id="submission_method_text" name="submission_method_text" class="custom-select custom-select-sm" disabled>
            @foreach($typeOptions as $key=>$value)
            <option value="{{$key}}">{{__('tender.status_submission.'.$value)}}</option>
            @endforeach
        </select>
        <input id="submission_method" name="submission_method" type="hidden" required="required">
    </div>
</div>
<div class="form-group row mb-2">
    <label for="weight" class="col-3 col-form-label text-right">{{__('tender.bidding.fields.weight')}}</label>
    <div class="col-9">
        <input id="weight" name="weight" placeholder="{{__('tender.bidding.fields.weight')}}"
            type="number" required="required" class="form-control form-control-sm">
    </div>
</div>
{{-- <div class="form-group row mb-2">
    <label for="is_commercial" class="col-3 col-form-label text-right">{{__('tender.bidding.fields.is_commercial')}}</label>
    <div class="col-9">
        <div class="custom-control custom-radio custom-control-inline">
            <input name="is_commercial" id="is_commercial_yes" type="radio" class="custom-control-input" value="true"
                required="required">
            <label for="is_commercial_yes" class="custom-control-label">{{__('tender.yes')}}</label>
        </div>
        <div class="custom-control custom-radio custom-control-inline">
            <input name="is_commercial" id="is_commercial_no" type="radio" class="custom-control-input" value="false"
                required="required">
            <label for="is_commercial_no" class="custom-control-label">{{__('tender.no')}}</label>
        </div>
    </div>
</div> --}}
