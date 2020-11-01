@extends('layouts.two_column')

@section('menuheader')
            <a href="{{ route('vendor.evaluation.evaluation') }}" class="text-logo"><i class="fas fa-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;{{ __('navigation.evaluation') }}</a>
@endsection

@section('menubody')
<ul class="vertical-nav-menu">
    @foreach($pages as $page)
        <li class="nav-item{{$type==$page?' mm-active':''}}">
            <a id="page_{{$page}}" class="nav-link{{in_array($page,$availablePages) ? '':' disabled'}}" href="{{route('vendor.evaluation.evaluation_detail',['id'=>$general->id, 'type'=>$page])}}">
                {{__('homepage.'.$page)}}
            </a>
        </li>
    @endforeach
</ul>
@endsection

@section('contentheader')
<div class="card-header-left">
    <span class="heading-title">{{$general->name}}: {{__('homepage.'.$type)}}</span>
</div>
<div class="card-header-right">
@yield('contentheader-right')
</div>
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
