@extends('tender.show')

@section('contentbody')
<div class="has-footer" style="padding: 0.5rem 1rem 1rem 0.5rem">
    <fieldset id="frmParameter_fieldset">
    <form id="frmParameter" class="was-validated" enctype="multipart/form-data">
        <div class="form-group row mb-2">
            <label for="text" class="col-3 col-form-label text-right">{{__('tender.tender_number')}}</label>
            <div class="col-9">
                <input id="id" name="id" type="hidden" value="{{$tender->id}}">
                <input id="tender_number" name="tender_number" placeholder="{{__('tender.tender_number')}}" type="text" required="required" class="form-control form-control-sm"
                        value="{{$tender->tender_number}}" readonly
                    >
            </div>
        </div>
        <div class="form-group row mb-2">
            <label for="text" class="col-3 col-form-label text-right">{{__('tender.title')}}</label>
            <div class="col-9">
                <input id="title" name="title" placeholder="{{__('tender.title')}}" type="text" required="required" class="form-control form-control-sm"
                        value="{{$tender->title}}"
                    >
            </div>
        </div>
        <div class="form-group row mb-2">
            <label for="text" class="col-3 col-form-label text-right">{{__('tender.purchase_organization')}}</label>
            <div class="col-9">
                <select id="purchase_org_id" name="purchase_org_id" required="required" class="custom-select custom-select-sm">
                    @foreach ($purchOrgs as $org)
                    <option value="{{$org->id}}" @if($org->id==$tender->purchase_org_id) selected @endif>{{$org->org_code}} - {{$org->description}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group row mb-2">
            <label for="text" class="col-3 col-form-label text-right">{{__('tender.purchase_group')}}</label>
            <div class="col-9">
                <select id="purchase_group_id" name="purchase_group_id" required="required" class="custom-select custom-select-sm">
                    @foreach ($purchGroups as $group)
                    <option value="{{$group->id}}" @if($group->id==$tender->purchase_group_id) selected @endif>{{$group->group_code}} - {{$group->description}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        {{-- <div class="form-group row mb-2">
            <label for="text" class="col-3 col-form-label text-right">{{__('tender.plant')}}</label>
            <div class="col-9">
                <select id="plant_id" name="plant_id" required="required" class="custom-select custom-select-sm">
                    @foreach ($plants as $plant)
                    <option value="{{$plant->id}}" @if($plant->id==$tender->plant_id) selected @endif>{{$plant->name}}</option>
                    @endforeach
                </select>
            </div>
        </div> --}}
        <div class="form-group row mb-2">
            <label for="text" class="col-3 col-form-label text-right">{{__('tender.incoterm')}}</label>
            <div class="col-9">
                <select id="incoterm" name="incoterm" required="required" class="custom-select custom-select-sm">
                    @foreach ($incotermOptions as $key=>$value)
                    <option value="{{$key}}" @if($key==$tender->incoterm) selected @endif>{{__($value)}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group row mb-2">
            <label for="text" class="col-3 col-form-label text-right">{{__('tender.location')}}</label>
            <div class="col-9">
                <textarea id="location" name="location" placeholder="{{__('tender.location')}}" required="required" class="form-control form-control-sm">{{$tender->location}}</textarea>
            </div>
        </div>
        <div class="form-group row mb-2">
            <label for="text" class="col-3 col-form-label text-right">{{__('tender.method')}}</label>
            <div class="col-9">
                <select id="tender_method" name="tender_method" class="custom-select custom-select-sm" required="required">
                    <?php
                    $sel_tender_method = "SELECTION";
                    if (isset($tender->tender_method)){
                        $sel_tender_method = $tender->tender_method;
                    }
                    ?>
                    @foreach($tenderMethod as $key=>$value)
                    <option value="{{$key}}" @if($key==$sel_tender_method) selected  @endif>{{__('tender.'.$value)}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group row mb-2">
            <label for="text" class="col-3 col-form-label text-right">{{__('tender.buyer')}}</label>
            <div class="col-9">
                <input id="buyer" name="buyer" placeholder="{{__('tender.buyer')}}" type="text" required="required" class="form-control form-control-sm"
                    value="{{$tender->buyer}}" readonly
                >
            </div>
        </div>
        <div class="form-group row mb-2">
            <label for="text" class="col-3 col-form-label text-right">{{__('tender.prequalification')}}</label>
            <div class="col-9">
                <?php
                    $sel_prequalification = 0;
                    if ($tender->prequalification==1)
                        $sel_prequalification = 1
                ?>
                <select id="prequalification" name="prequalification" class="custom-select custom-select-sm" required="required">
                    <option value="1"{{$sel_prequalification==1?' selected':''}}>{{__('tender.yes')}}</option>
                    <option value="0"{{$sel_prequalification==0?' selected':''}}>{{__('tender.no')}}</option>
                </select>
            </div>
        </div>
        <div class="form-group row mb-2" hidden>
            <label for="text" class="col-3 col-form-label text-right">{{__('tender.eauction')}}</label>
            <div class="col-9">
                <select id="eauction" name="eauction" class="custom-select custom-select-sm" required="required">
                    <option value="1"{{$tender->eauction==1?' selected':''}}>{{__('tender.yes')}}</option>
                    <option value="0"{{$tender->eauction==0?' selected':''}}>{{__('tender.no')}}</option>
                </select>
            </div>
        </div>
        <div class="form-group row mb-2">
            <label for="text" class="col-3 col-form-label text-right">{{__('tender.submission_method')}}</label>
            <div class="col-9">
                <select id="submission_method" name="submission_method" required="required" class="custom-select custom-select-sm">
                    @foreach ($submissionMethod as $key=>$value)
                    <option value="{{$key}}" @if($key==$tender->submission_method) selected @endif>{{__('tender.'.$value)}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group row mb-2">
            <label for="text" class="col-3 col-form-label text-right">{{__('tender.evaluation_method')}}</label>
            <div class="col-9">
                @foreach ($evaluationMethod as $key=>$value)
                <div class="custom-control custom-radio custom-control-inline">
                    <input name="evaluation_method" id="evaluation_method_{{$key}}" type="radio" class="custom-control-input" value="{{$key}}" required="required" @if($key==$tender->evaluation_method) checked @endif>
                    <label for="evaluation_method_{{$key}}" class="custom-control-label">{{__('tender.'.$value)}}</label>
                </div>
                @endforeach
            </div>
        </div>
        <div class="form-group row mb-2">
            <label for="text" class="col-3 col-form-label text-right">{{__('tender.bid_bond')}}</label>
            <div class="col-9">
                <div class="custom-control custom-radio custom-control-inline">
                    <input name="bid_bond" id="bid_bond_0" type="radio" class="custom-control-input" value="1" required="required" @if(1==$tender->bid_bond) checked @endif>
                    <label for="bid_bond_0" class="custom-control-label">{{__('tender.yes')}}</label>
                </div>
                <div class="custom-control custom-radio custom-control-inline">
                    <input name="bid_bond" id="bid_bond_1" type="radio" class="custom-control-input" value="0" required="required" @if(0==$tender->bid_bond) checked @endif>
                    <label for="bid_bond_1" class="custom-control-label">{{__('tender.no')}}</label>
                </div>
            </div>
        </div>
        <div class="form-group row mb-2">
            <label for="text" class="col-3 col-form-label text-right">{{__('tender.tkdn')}}</label>
            <div class="col-9 row">
                <div class="col-3">
                    <div class="custom-control custom-radio custom-control-inline">
                        <input name="tkdn_option" id="tkdn_option_0" type="radio" class="custom-control-input" value="1" required="required" @if(1===$tender->tkdn_option) checked @endif>
                        <label for="tkdn_option_0" class="custom-control-label">{{__('tender.yes')}}</label>
                    </div>
                    <div class="custom-control custom-radio custom-control-inline">
                        <input name="tkdn_option" id="tkdn_option_1" type="radio" class="custom-control-input" value="0" required="required" @if(0===$tender->tkdn_option) checked @endif>
                        <label for="tkdn_option_1" class="custom-control-label">{{__('tender.no')}}</label>
                    </div>
                </div>
                <div class="col-9">
                    <select id="tkdn" name="tkdn" required="required" class="custom-select custom-select-sm" @if(0===$tender->tkdn_option) disabled @endif>
                        <option value="">-- Choose Option --</option>
                        @foreach ($tkdnOptions as $key=>$value)
                        <option value="{{$key}}" @if($key==$tender->tkdn) selected @endif>{{__('tender.'.$value)}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        {{--
        <!-- <div class="form-group row mb-2">
            <label for="text" class="col-3 col-form-label text-right">{{__('tender.down_payment')}}</label>
            <div class="col-9 row">
                <div class="col-3">
                    <div class="custom-control custom-radio custom-control-inline">
                        <input name="down_payment" id="down_payment_0" type="radio" class="custom-control-input" value="1" required="required" @if(1===$tender->down_payment) checked @endif>
                        <label for="down_payment_0" class="custom-control-label">{{__('tender.yes')}}</label>
                    </div>
                    <div class="custom-control custom-radio custom-control-inline">
                        <input name="down_payment" id="down_payment_1" type="radio" class="custom-control-input" value="0" required="required" @if(0===$tender->down_payment) checked @endif>
                        <label for="down_payment_1" class="custom-control-label">{{__('tender.no')}}</label>
                    </div>
                </div>
                <div class="col-9">
                    <div class="input-group input-group-sm">
                        <input name="down_payment_percentage" id="down_payment_percentage" placeholder="{{__('tender.percentage')}}" type="number" class="form-control form-control-sm" value="{{$tender->down_payment_percentage ?? ''}}" required="required" disabled>
                        <div class="input-group-append">
                            <div class="input-group-text">%</div>
                        </div>
                    </div>
                </div>
            </div>
        </div> -->
        <!-- <div class="form-group row mb-2">
            <label for="text" class="col-3 col-form-label text-right">{{__('tender.retention')}}</label>
            <div class="col-9 row">
                <div class="col-sm-3">
                    <div class="custom-control custom-radio custom-control-inline">
                        <input name="retention" id="retention_0" type="radio" class="custom-control-input" value="1" required="required" @if(1===$tender->retention) checked @endif>
                        <label for="retention_0" class="custom-control-label">{{__('tender.yes')}}</label>
                    </div>
                    <div class="custom-control custom-radio custom-control-inline">
                        <input name="retention" id="retention_1" type="radio" class="custom-control-input" value="0" required="required" @if(0===$tender->retention) checked @endif>
                        <label for="retention_1" class="custom-control-label">{{__('tender.no')}}</label>
                    </div>
                </div>
                <div class="col-sm-9">
                    <div class="input-group input-group-sm">
                        <input name="retention_percentage" id="retention_percentage" placeholder="{{__('tender.percentage')}}" type="number" class="form-control form-control-sm" value="{{$tender->retention_percentage ?? ''}}" required="required" disabled>
                        <div class="input-group-append">
                            <div class="input-group-text">%</div>
                        </div>
                    </div>
                </div>
            </div>
        </div> -->
        --}}
        <div class="form-group row mb-2">
            <label for="text" class="col-3 col-form-label text-right">{{__('tender.winning_method')}}</label>
            <div class="col-9">
                @foreach ($winningMethod as $key=>$value)
                <div class="custom-control custom-radio custom-control-inline">
                    <input name="winning_method" id="winning_method{{$key}}" type="radio" class="custom-control-input" value="{{$key}}" required="required" @if($key==$tender->winning_method) checked @endif>
                    <label for="winning_method{{$key}}" class="custom-control-label">{{__('tender.'.$value)}}</label>
                </div>
                @endforeach
            </div>
        </div>

        <div class="form-group row mb-2 c_conditional_type">
            <label for="text" class="col-3 col-form-label text-right">{{__('tender.conditional_type')}}</label>
            <div class="col-9">
                @foreach ($conditionalType as $key=>$value)
                <div class="custom-control custom-radio custom-control-inline">
                    <input name="conditional_type" id="conditional_type{{$key}}" type="radio" class="custom-control-input"
                        value="{{$key}}" required="required" @if($key==$tender->conditional_type) checked @endif>
                    <label for="conditional_type{{$key}}" class="custom-control-label">{{__('tender.'.$value)}}</label>
                </div>
                @endforeach
            </div>
        </div>

        <div class="form-group row mb-2">
            <label for="text" class="col-3 col-form-label text-right">{{__('tender.validity_quotation')}}</label>
            <div class="col-9">
                @foreach ($validityOptions as $key=>$value)
                <div class="custom-control custom-radio custom-control-inline">
                    <input name="validity_quotation_radio" id="validity_quotation{{$key}}" type="radio" class="custom-control-input"
                        value="{{$key}}" required="required"
                        @if($key==$tender->validity_quotation) checked @endif
                        @if(!isset($tender->validity_quotation) && $value=='30') checked @endif>
                    <label for="validity_quotation{{$key}}" class="custom-control-label">{{__('tender.'.$value)}}</label>
                </div>
                @endforeach
                <div class="custom-control custom-radio custom-control-inline">
                    <input name="validity_quotation_radio" id="validity_quotation-1" type="radio" class="custom-control-input" value="-1"
                    @if(isset($tender->validity_quotation) && !in_array($tender->validity_quotation, $validityOptions->toArray())) checked @endif required="required">
                    <label for="validity_quotation-1" class="custom-control-label">&nbsp;</label>
                    <input name="validity_quotation" id="validity_quotation" type="number" class="form-control form-control-sm" value="{{$tender->validity_quotation ?? 30}}" required="required" 
                    @if(!isset($tender->validity_quotation) || in_array($tender->validity_quotation, $validityOptions->toArray())) readonly @endif >
                </div>
            </div>
        </div>
        <div class="form-group row mb-2">
            <label for="text" class="col-3 col-form-label text-right">{{__('tender.bid_visibility')}}</label>
            <div class="col-9">
                @foreach ($bidVisibility as $key=>$value)
                <div class="custom-control custom-radio custom-control-inline">
                    <input name="visibility_bid_document" id="bid_visibility{{$key}}" type="radio" class="custom-control-input" value="{{$key}}" required="required" @if($key==$tender->visibility_bid_document) checked @endif>
                    <label for="bid_visibility{{$key}}" class="custom-control-label">{{__('tender.'.$value)}}</label>
                </div>
                @endforeach
            </div>
        </div>
        <div class="form-group row mb-2">
            <label for="text" class="col-3 col-form-label text-right">{{__('tender.aanwijzing')}}</label>
            <div class="col-9">
                <div class="custom-control custom-radio custom-control-inline">
                    <input name="aanwijzing" id="aanwijzing_0" type="radio" class="custom-control-input" value="1" required="required" @if(1===$tender->aanwijzing) checked @endif>
                    <label for="aanwijzing_0" class="custom-control-label">{{__('tender.yes')}}</label>
                </div>
                <div class="custom-control custom-radio custom-control-inline">
                    <input name="aanwijzing" id="aanwijzing_1" type="radio" class="custom-control-input" value="0" required="required" @if(0===$tender->aanwijzing) checked @endif>
                    <label for="aanwijzing_1" class="custom-control-label">{{__('tender.no')}}</label>
                </div>
            </div>
        </div>
        <div class="form-group row mb-2">
            <label for="text" class="col-3 col-form-label text-right">{{__('tender.scope_of_work')}}</label>
            <div class="col-9">
                <textarea id="scope_of_work" name="scope_of_work" placeholder="{{__('tender.scope_of_work')}}" class="form-control form-control-sm">{{$tender->scope_of_work}}</textarea>
            </div>
        </div>
        {{-- <div class="form-group row mb-2">
            <label for="text" class="col-3 col-form-label text-right">{{__('tender.note_to_vendor')}}</label>
            <div class="col-9">
                <textarea id="note_to_vendor" name="note_to_vendor" placeholder="{{__('tender.note_to_vendor')}}" class="form-control form-control-sm">{{$tender->note_to_vendor}}</textarea>
            </div>
        </div> --}}
        <div class="form-group row mb-2">
            <label for="text" class="col-3 col-form-label text-right">{{__('tender.note')}}</label>
            <div class="col-9">
                <textarea id="note" name="note" placeholder="{{__('tender.note')}}" class="form-control form-control-sm">{{$tender->note}}</textarea>
            </div>
        </div>
    </form>
    </fieldset>
</div>
@endsection

