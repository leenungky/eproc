@extends('tender.show')

@section('contentbody')
@foreach ($tenderData['signatures'] as $k => $appr)
@php
$colorClass = 'secondary';
if($appr->status == 'approved'){
    $colorClass = 'success';
}else if($appr->status == 'rejected'){
    $colorClass = 'danger';
}
@endphp
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
        <div id="frmApproval" class="form-view col-sm-12">
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
@endforeach
@endsection

