@extends('layouts.app')

@include('layouts.navigation')

@section('content')
<div class="card card-menu">
    <div class="card-header">
        @yield('menuheader')
    </div>
    <div id="nav-menu-body" class="card-body">
        @yield('menubody')
    </div>
</div>
<div class="card card-content">
    <div class="card-header">
        @yield('contentheader')
    </div>
    @yield('contenttabbody')
    <div class="card-body">
        @yield('contentbody')
    </div>
</div>

@endsection
