@extends('layouts.one_column')

@section('contentheader')
404 - Not Available
@endsection

@section('contentbody')
<div class="p-2">
    <div class="alert alert-danger" role="alert">
        Sorry, the page you are looking for is not available. Please contact administrator
    </div>

    <!-- Ini nanti jadi Dashboard untuk admin maupun untuk vendor. -->
    <a href="javascript:history.back()">Back to previous page</a>
</div>
@endsection
