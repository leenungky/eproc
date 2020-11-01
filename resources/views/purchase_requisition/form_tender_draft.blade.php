<div class="form-group row mb-2">
    <label for="text" class="col-3 col-form-label text-right">{{__('tender.title')}}</label>
    <div class="col-9">
        <input id="name" name="name" placeholder="{{__('tender.title')}}" type="text" required="required" class="form-control form-control-sm">
    </div>
</div>
<div class="form-group row mb-2">
    <label for="text" class="col-3 col-form-label text-right">{{__('tender.method')}}</label>
    <div class="col-9">
        <select id="tender_method" name="tender_method" class="custom-select custom-select-sm" required="required">
            @foreach($tenderMethod as $key=>$value)
            <option value="{{$key}}" @if($key=="SELECTION") selected  @endif>{{__('tender.'.$value)}}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="form-group row mb-2">
    <label for="text" class="col-3 col-form-label text-right">{{__('tender.purchase_organization')}}</label>
    <div class="col-9">
        <select id="purchase_org_id" name="purchase_org_id" required="required" class="custom-select custom-select-sm">
            @foreach ($purchOrgs as $org)
            <option value="{{$org->id}}">{{$org->org_code}} - {{$org->description}}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="form-group row mb-2">
    <label for="text" class="col-3 col-form-label text-right">{{__('tender.purchase_group')}}</label>
    <div class="col-9">
        <select id="purchase_group_id" name="purchase_group_id" required="required" class="custom-select custom-select-sm">
            @foreach ($purchGroups as $group)
            <option value="{{$group->id}}">{{$group->group_code}} - {{$group->description}}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="form-group row mb-2">
    <label for="text" class="col-3 col-form-label text-right">{{__('tender.prequalification')}}</label>
    <div class="col-9">
        <div class="custom-control custom-radio custom-control-inline">
            <input name="prequalification" id="prequalification_0" type="radio" class="custom-control-input" value="1" required="required">
            <label for="prequalification_0" class="custom-control-label">{{__('tender.yes')}}</label>
        </div>
        <div class="custom-control custom-radio custom-control-inline">
            <input name="prequalification" id="prequalification_1" type="radio" class="custom-control-input" value="0" checked required="required">
            <label for="prequalification_1" class="custom-control-label">{{__('tender.no')}}</label>
        </div>
    </div>
</div>
<div class="form-group row mb-2" hidden>
    <label for="text" class="col-3 col-form-label text-right">{{__('tender.eauction')}}</label>
    <div class="col-9">
        <div class="custom-control custom-radio custom-control-inline">
            <input name="eauction" id="eauction_0" type="radio" class="custom-control-input" value="1">
            <label for="eauction_0" class="custom-control-label">{{__('tender.yes')}}</label>
        </div>
        <div class="custom-control custom-radio custom-control-inline">
            <input name="eauction" id="eauction_1" type="radio" class="custom-control-input" selected value="0">
            <label for="eauction_1" class="custom-control-label">{{__('tender.no')}}</label>
        </div>
    </div>
</div>
<div class="form-group row mb-2">
    <label for="text" class="col-3 col-form-label text-right">{{__('tender.submission_method')}}</label>
    <div class="col-9">
        @foreach ($submissionMethod as $key=>$value)
        <div class="custom-control custom-radio custom-control-inline">
            <input name="submission_method" id="submission_method_{{$key}}" type="radio" class="custom-control-input" value="{{$key}}" required="required">
            <label for="submission_method_{{$key}}" class="custom-control-label">{{__('tender.'.$value)}}</label>
        </div>
        @endforeach
    </div>
</div>
<div class="form-group row mb-2">
    <label for="text" class="col-3 col-form-label text-right">{{__('tender.evaluation_method')}}</label>
    <div class="col-9">
        @foreach ($evaluationMethod as $key=>$value)
        <div class="custom-control custom-radio custom-control-inline">
            <input name="evaluation_method" id="evaluation_method_{{$key}}" type="radio" class="custom-control-input"
                checked="true"
                value="{{$key}}" required="required">
            <label for="evaluation_method_{{$key}}" class="custom-control-label">{{__('tender.'.$value)}}</label>
        </div>
        @endforeach
    </div>
</div>
