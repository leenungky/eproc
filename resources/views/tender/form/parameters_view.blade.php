@extends('tender.show')

@section('contentbody')
<div class="has-footer" style="padding: 0.5rem 1rem 1rem 0.5rem">
    <div>
        <h4 class="tender-title">{{$tender->title}} <small class="float-right">{{$tender->tender_number}}</small></h4>
    </div>
    <br/>
    <div id="frmParameter" class="form-view row">
        <div class="col-6">
            <dl class="row">
                <dt class="col-sm-6 text-right">{{__('tender.tender_number')}} :</dt>
                <dd class="col-sm-6">{{$tender->tender_number}}</dd>

                <dt class="col-sm-6 text-right">{{__('tender.title')}} :</dt>
                <dd class="col-sm-6">{{$tender->title}}</dd>

                <dt class="col-sm-6 text-right">{{__('tender.purchase_organization')}} :</dt>
                <dd class="col-sm-6">{{$purchOrgs->where('id', $tender->purchase_org_id)->first()->description ?? ''}}</dd>

                <dt class="col-sm-6 text-right">{{__('tender.purchase_group')}} :</dt>
                <dd class="col-sm-6">{{$purchGroups->where('id', $tender->purchase_group_id)->first()->description ?? ''}}</dd>

                {{-- <dt class="col-sm-6 text-right">{{__('tender.plant')}} :</dt>
                <dd class="col-sm-6">{{$plants->where('id', $tender->plant_id)->first()->name ?? ''}}</dd> --}}

                <dt class="col-sm-6 text-right">{{__('tender.incoterm')}} :</dt>
                <dd class="col-sm-6">{{$tender->incoterm ?? ''}}</dd>

                <dt class="col-sm-6 text-right">{{__('tender.location')}} :</dt>
                <dd class="col-sm-6">{{$tender->location}}</dd>

                <dt class="col-sm-6 text-right">{{__('tender.method')}} :</dt>
                <dd class="col-sm-6">{{$tender->tender_method ? __('tender.'.$tenderMethod->toArray()[$tender->tender_method]) : ''}}</dd>

                <dt class="col-sm-6 text-right">{{__('tender.buyer')}} :</dt>
                <dd class="col-sm-6">{{$tender->buyer ?? ''}}</dd>

                <dt class="col-sm-6 text-right">{{__('tender.prequalification')}} :</dt>
                <dd class="col-sm-6">{{ $tender->prequalification==1 ? __('tender.yes') :__('tender.no') }}</dd>

                <dt class="col-sm-6 text-right">{{__('tender.eauction')}} :</dt>
                <dd class="col-sm-6">{{ $tender->eauction==1 ? __('tender.yes') :__('tender.no') }}</dd>

                <dt class="col-sm-6 text-right">{{__('tender.submission_method')}} :</dt>
                <dd class="col-sm-6">{{$tender->tender_method ? __('tender.'.$submissionMethod->toArray()[$tender->submission_method]) : ''}}</dd>

                <dt class="col-sm-6 text-right">{{__('tender.evaluation_method')}} :</dt>
                <dd class="col-sm-6">{{$tender->tender_method ? __('tender.'.$evaluationMethod->toArray()[$tender->evaluation_method]) : ''}}</dd>

            </dl>
        </div>
        <div class="col-6">
            <dl class="row">
                <dt class="col-sm-4 text-right">{{__('tender.bid_bond')}} :</dt>
                <dd class="col-sm-8">{{ $tender->bid_bond==1 ? __('tender.yes') :__('tender.no') }}</dd>

                <dt class="col-sm-4 text-right">{{__('tender.tkdn')}} :</dt>
                <dd class="col-sm-8">
                    {{ $tender->tkdn_option==1
                        ? __('tender.'.$tkdnOptions->toArray()[$tender->tkdn])
                        :__('tender.no') }}
                </dd>
{{--
                <dt class="col-sm-4 text-right">{{__('tender.down_payment')}} :</dt>
                <dd class="col-sm-8">
                    {{ $tender->down_payment==1
                        ? ($tender->down_payment_percentage ? $tender->down_payment_percentage.' %' : '')
                        :__('tender.no') }}
                </dd>

                <dt class="col-sm-4 text-right">{{__('tender.retention')}} :</dt>
                <dd class="col-sm-8">
                    {{ $tender->retention==1
                        ? ($tender->retention_percentage ? $tender->retention_percentage.' %' : '')
                        :__('tender.no') }}
                </dd>
--}}
                <dt class="col-sm-4 text-right">{{__('tender.winning_method')}} :</dt>
                <dd class="col-sm-8">
                    {{$tender->winning_method ? __('tender.'.$winningMethod->toArray()[$tender->winning_method]) : ''}}
                </dd>

                <dt class="col-sm-4 text-right">{{__('tender.conditional_type')}} :</dt>
                <dd class="col-sm-8">
                    {{$tender->conditional_type ? __('tender.'.$conditionalType->toArray()[$tender->conditional_type]) : ''}}
                </dd>

                <dt class="col-sm-4 text-right">{{__('tender.validity_quotation')}} :</dt>
                <dd class="col-sm-8">
                    {{$tender->validity_quotation ?? ''}}
                </dd>

                <dt class="col-sm-4 text-right">{{__('tender.bid_visibility')}} :</dt>
                <dd class="col-sm-8">
                    {{$tender->visibility_bid_document ? __('tender.'.$bidVisibility->toArray()[$tender->visibility_bid_document]) : ''}}
                </dd>

                <dt class="col-sm-4 text-right">{{__('tender.aanwijzing')}} :</dt>
                <dd class="col-sm-8">{{ $tender->aanwijzing==1 ? __('tender.yes') :__('tender.no') }}</dd>

                <dt class="col-sm-4 text-right">{{__('tender.scope_of_work')}} :</dt>
                <dd class="col-sm-8">
                    {{$tender->scope_of_work ?? ''}}
                </dd>

                {{-- <dt class="col-sm-4 text-right">{{__('tender.note_to_vendor')}} :</dt>
                <dd class="col-sm-8">
                    {{$tender->note_to_vendor ?? ''}}
                </dd> --}}

                <dt class="col-sm-4 text-right">{{__('tender.note')}} :</dt>
                <dd class="col-sm-8">
                    {{$tender->note ?? ''}}
                </dd>
            </dl>
        </div>
    </div>
    </fieldset>
</div>
@endsection
