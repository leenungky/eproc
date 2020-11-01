@extends('layouts.two_column')

@section('menuheader')
    <div class="col-sm-12 full-width">
        <div class="row">
            <div class="heading-left">
                {{-- <a href="{{ route('tender.list') }}" class="text-logo"><i class="fas fa-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;{{ Str::upper($tender ? $tender->tender_number : '') }}</a>
                <a href="{{ route('tender.list') }}" class="ico-logo"><i class="fas fa-building icon-heading" aria-hidden="true"></i></a> --}}
                <a href="#" class="text-logo tender-back-list"><i class="fas fa-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;{{ Str::upper($tender ? $tender->tender_number : '') }}</a>
                <a href="#" class="ico-logo tender-back-list"><i class="fas fa-building icon-heading" aria-hidden="true"></i></a>
            </div>
        </div>
    </div>
@endsection

@section('menubody')
@include('tender.tender_submenu')
@endsection
@section('contentheader')
@if(in_array($type,['process_technical_evaluation','process_commercial_evaluation']))
{{__('tender.'.$type.'_'.strtolower($tender->submission_method))}}
@else
{{__('tender.'.$type)}}
@endif
@endsection

@section('styles')
<style>
.vertical-nav-menu .nav-item{
    border-bottom: 1px solid #eee;
}
.vertical-nav-menu .nav-item .disabled{
    color: #ccc;
}
.dataTables_wrapper{
    margin-top: -.5rem;
}
.dataTables_wrapper .row:first-child {
    padding: 0;
}
</style>
@endsection
