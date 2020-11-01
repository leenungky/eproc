@extends('layouts.one_column')

@section('contentheader')
405 - Method Not Allowed
@endsection

@section('contentbody')
<div class="p-2">
    <div class="alert alert-danger" role="alert">
        {{$message}} 
    </div>

    <!-- Ini nanti jadi Dashboard untuk admin maupun untuk vendor. -->
    <a href="javascript:history.back()">Back to previous page</a>
</div>
@endsection
