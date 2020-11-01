@extends('layouts.one_column')

@section('contentheader')
Welcome back, {{Auth::user()->name}}
@endsection

@section('contentbody')
<div class="p-2">
    @if (session('status'))
        <div class="alert alert-success" role="alert">
            {{ session('status') }}
        </div>
    @endif

    <!-- Ini nanti jadi Dashboard untuk admin maupun untuk vendor. -->
    You are logged in.
</div>
@endsection
@section('footer')
@php $footerText = config('eproc.footer_text') ?? '' @endphp
    @if($footerText!='')
        @include('layouts.footer_transaction')
    @endif
@endsection
