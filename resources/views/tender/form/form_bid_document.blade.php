<div class="form-group row mb-2">
    <label for="description" class="col-3 col-form-label text-right">{{__('tender.bidding.fields.description')}}</label>
    <div class="col-9">
        <input id="id" name="id" type="hidden" value="">
        <input id="sequence_done" name="sequence_done" type="hidden" value="false">
        <input id="description" name="description" type="text"
            class="form-control form-control-sm" required>
    </div>
</div>
<div class="form-group row mb-2">
    <label for="stage_type" class="col-3 col-form-label text-right">{{__('tender.bidding.fields.stage_type')}}</label>
    <div class="col-9">
        <select id="stage_type" name="stage_type" class="custom-select custom-select-sm" required>
            <option value="">-- Select Type --</option>
            @foreach($stageTypeOptions as $key=>$value)
            <option value="{{$key}}">{{__('tender.status_stage_2.'.$value)}}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="form-group row mb-2">
    <label for="submission_method" class="col-3 col-form-label text-right">{{__('tender.bidding.fields.submission_method')}}</label>
    <div class="col-9">
        <select id="submission_method_text" class="custom-select custom-select-sm" disabled>
            @foreach($stageTypeOptions as $key=>$value)
            <option value="{{$key}}">{{__('tender.status_submission.'.$value)}}</option>
            @endforeach
        </select>
        <select id="submission_method" name="submission_method" class="custom-select custom-select-sm" required hidden>
            @foreach($stageTypeOptions as $key=>$value)
            <option value="{{$key}}">{{__('tender.status_submission.'.$value)}}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="form-group row mb-2">
    <label for="text" class="col-3 col-form-label text-right">{{__('tender.bidding.fields.is_required')}}</label>
    <div class="col-9">
        <div class="custom-control custom-radio custom-control-inline">
            <input name="is_required" id="is_required_yes" type="radio" class="custom-control-input" value="true"
                required="required">
            <label for="is_required_yes" class="custom-control-label">{{__('tender.yes')}}</label>
        </div>
        <div class="custom-control custom-radio custom-control-inline">
            <input name="is_required" id="is_required_no" type="radio" class="custom-control-input" value="false"
                required="required">
            <label for="is_required_no" class="custom-control-label">{{__('tender.no')}}</label>
        </div>
    </div>
</div>
