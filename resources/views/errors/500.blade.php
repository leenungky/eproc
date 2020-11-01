@extends('layouts.one_column')

@section('contentheader')
500 - Server Error
@endsection

@section('contentbody')
<div class="p-2">
    <div class="alert alert-danger" role="alert">
        Sorry an error occurred on the server
    </div>

    <!-- Ini nanti jadi Dashboard untuk admin maupun untuk vendor. -->
    <a href="javascript:history.back()">Back to previous page</a>
</div>
@endsection
