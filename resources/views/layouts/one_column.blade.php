@extends('layouts.app')

@include('layouts.navigation')

@section('content')
<div class="card">
    <div class="card-header">
        @yield('contentheader')
    </div>

    <div class="card-body">
        @yield('contentbody')
    </div>
</div>
@endsection
