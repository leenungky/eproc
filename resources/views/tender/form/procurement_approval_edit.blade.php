@extends('tender.show')

@section('contentbody')
@php
    $prevApprover = null;
@endphp
@foreach ($tenderData['signatures'] as $k => $appr)
@php
$colorClass = 'secondary';
if($appr->status == 'approved'){
    $colorClass = 'success';
}else if($appr->status == 'rejected'){
    $colorClass = 'danger';
}
@endphp
@if($appr->sign_by_id == $approver->sign_by_id && $appr->status!='approved')
<div class="card">
    <div class="card-header">
        <div class="card-header-left">
            <span class="heading-title">
                {{-- <small>{{__('tender.schedule.fields.approved_by')}} : <b>{{ $appr->sign_by }}</b></small> --}}
                <small><b>{{ $appr->order . '. ' .$appr->sign_by }}</b></small>
            </span>
        </div>
        <div class="card-header-right">
            <span class="heading-title">
                <small>{{__('tender.status')}} : <b class="text-{{$colorClass}}">{{ __('tender.schedule_status.'.$appr->status) }}</b></small>
            </span>
        </div>
    </div>
    <div class="card-body" style="padding-top: 20px;">
        @if(is_null($prevApprover) || $prevApprover->status =='approved')
        <form id="frmApproval" class="col-sm-12 needs-validation" novalidate>
            <div class="form-group row mb-1">
                <label for="notes" class="col-3 col-form-label text-right">{{__('tender.schedule.fields.note')}} : </label>
                <div class="col-6">
                    <input type="hidden" name="id" value="{{$appr->id}}" />
                    <textarea name="notes" class="form-control form-control-sm" rows="5" required></textarea>
                </div>
            </div>
            <div class="form-group row mb-1" style="padding-top: 20px">
                <label class="col-3 col-form-label text-right">&nbsp;</label>
                <div class="col-6">
                    <button class="btn btn-rejected btn-danger mr-2" type="submit" >
                        <i class="fa fa-times"></i> {{__('homepage.revise')}}</button>
                    <button class="btn btn-approved btn-success mr-2" type="submit" >
                        <i class="fa fa-check"></i> {{__('common.approve')}}</button>
                </div>
            </div>
        </form>
        @elseif($prevApprover->status == 'rejected')
        <div class="col-sm-12">
            <h5>{{ __('tender.schedule_status.'.$prevApprover->status) }}</h5>
        </div>
        @else
        <div class="col-sm-12">
            <h5>Waiting approval</h5>
        </div>
        @endif
    </div>
</div>
@else
<div class="card">
    <div class="card-header">
        <div class="card-header-left">
            <span class="heading-title">
                {{-- <small>{{__('tender.schedule.fields.approved_by')}} : <b>{{ $appr->sign_by }}</b></small> --}}
                <small><b>{{ $appr->order . '. ' .$appr->sign_by }}</b></small>
            </span>
        </div>
        <div class="card-header-right">
            <span class="heading-title">
                <small>{{__('tender.status')}} : <b class="text-{{$colorClass}}">{{ __('tender.schedule_status.'.$appr->status) }}</b></small>
            </span>
        </div>
    </div>
    <div class="card-body" style="padding-top: 20px;">
        <div id="frmApprovals" class="form-view col-sm-12">
            <div class="form-group row mb-1">
                <label for="text" class="col-3 col-form-label text-right">{{__('tender.schedule.fields.name')}} : </label>
                <div class="col-9">
                    <label class="form-control form-control-sm">{{ $appr->sign_by }}</label>
                </div>
            </div>
            <div class="form-group row mb-1">
                <label for="text" class="col-3 col-form-label text-right">{{__('tender.schedule.fields.position')}} : </label>
                <div class="col-9">
                    <label class="form-control form-control-sm">{{ $appr->position }}</label>
                </div>
            </div>

            @if($appr->status !='draft')
            <div class="form-group row mb-1">
                <label for="text" class="col-3 col-form-label text-right">{{__('tender.schedule.fields.note')}} : </label>
                <div class="col-9">
                    <label class="form-control form-control-sm">{{ $appr->notes }}</label>
                </div>
            </div>
            <div class="form-group row mb-1">
                <label for="text" class="col-3 col-form-label text-right">{{__('tender.schedule.fields.date')}} : </label>
                <div class="col-9">
                    <label class="form-control form-control-sm">{{ $appr->updated_at }}</label>
                </div>
            </div>
            @endif

        </div>
    </div>
</div>
@endif
@php
    $prevApprover = $appr;
@endphp
@endforeach
@endsection


