<div class="form-group row mb-2">
    <label for="" class="col-3 col-form-label text-right">{{__('tender.submission_method')}}</label>
    <div class="col-9">
        <input type="text" class="form-control form-control-sm" disabled value="{{__('tender.'.$submissionMethod[$tender->submission_method])}}"/>
    </div>
</div>
<div class="form-group row mb-2">
    <label for="buyer_user_id" class="col-3 col-form-label text-right">{{__('tender.bidding.fields.buyer_name')}}</label>
    <div class="col-9">
        <input id="id" name="id" type="hidden" value="">
        <input id="sequence_done" name="sequence_done" type="hidden" value="false">
        <select id="buyer_user_id" name="buyer_user_id" class="custom-select custom-select-sm" required>
            <option>
            @foreach($buyers as $key=>$value)
            <option value="{{$value->user_id}}">{{$value->buyer_name}}</option>
            @endforeach
        </select>
    </div>
</div>
{{-- <div class="form-group row mb-2">
    <label for="order" class="col-3 col-form-label text-right">{{__('tender.order')}}</label>
    <div class="col-9">
        <input type="text" id="order" name="order" class="form-control form-control-sm" placeholder="{{__('tender.order')}}" />
    </div>
</div> --}}
<div class="form-group row mb-2">
    <label for="stage_type" class="col-3 col-form-label text-right">{{__('tender.bidding.fields.stage_type')}}</label>
    <div class="col-9">
        <select id="stage_type" name="stage_type" class="custom-select custom-select-sm" required>
            <option value="">-- Select Type --</option>
            @if($tender->submission_method!='2S')<option value="2">{{__('tender.'.$submissionMethod[$tender->submission_method])}}</option>@endif
            @foreach($stageTypeOptions as $key=>$value)
            <option value="{{$key}}">{{__('tender.status_stage_2.'.$value)}}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="form-group row mb-2" hidden>
    <label for="submission_method" class="col-3 col-form-label text-right">{{__('tender.bidding.fields.submission_method')}}</label>
    <div class="col-9">
        <select id="submission_method" name="submission_method" class="custom-select custom-select-sm" required>
            @foreach($stageTypeOptions as $key=>$value)
            <option value="{{$key}}">{{__('tender.status_submission.'.$value)}}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="form-group row mb-2">
    <label for="buyer_type_id" class="col-3 col-form-label text-right">{{__('tender.bidding.fields.buyer_type_name')}}</label>
    <div class="col-9">
        <select id="buyer_type_ids" name="buyer_type_ids[]" class="custom-select custom-select-sm" multiple="multiple" required>
            <option>
            @foreach($buyerTypes as $key=>$value)
            <option value="{{$key}}">{{__('tender.permissions.'.$value)}}</option>
            @endforeach
        </select>
    </div>
</div>
