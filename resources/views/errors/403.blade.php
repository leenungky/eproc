@extends('layouts.one_column')

@section('contentheader')
403 - Forbidden
@endsection

@section('contentbody')
<div class="p-2">
    <div class="alert alert-danger" role="alert">
        Sorry, you don't have permission to access / on this page. Please contact administrator
    </div>

    <!-- Ini nanti jadi Dashboard untuk admin maupun untuk vendor. -->
    <a href="javascript:history.back()">Back to previous page</a>
</div>
@endsection
