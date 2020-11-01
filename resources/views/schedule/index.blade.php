@extends('layouts.one_column')

@section('contentheader')
<meta name="csrf-token" content="{{ csrf_token() }}" />
<div class="card-header-left">
    <span class="heading-title">
    <i class="fa fa-pencil-square-o" aria-hidden="true"></i>&nbsp;Testing schedule
    </span>
</div>
<div class="card-header-right">
</div>
@endsection

@section('contentbody')
<div class="btn">
<button id="doc_expiry">Doc Expiry Test</button>
<button id="sanction_expiry">Sanction Expiry Test</button>
<button id="sanction_start">Sanction Start Test</button>
</div>
<div class="load" style="display:none">
loader ............
</div>
@endsection


@section('modules-scripts')
<script type="text/javascript">
    require(["metisMenu"],function(){
        $("#doc_expiry").click(function(){
            console.log("aaaaa")
            $(".load").show();
            $(".btn").hide();
            $.post( "/schedule/doc_expiry", {"_token": "{{ csrf_token() }}"}, function( data ) {
                if (data.status==200){
                    alert('sukses');
                    $(".load").hide();
                    $(".btn").show();
                }
            });
        });
        $("#sanction_expiry").click(function(){
            $(".load").show();
            $(".btn").hide();
            $.post( "/schedule/sanction_expiry", {"_token": "{{ csrf_token() }}"}, function( data ) {
                if (data.status==200){
                    alert('sukses');
                    $(".load").hide();
                    $(".btn").show();
                }
            });
        });
        $("#sanction_start").click(function(){
            $(".load").show();
            $(".btn").hide();
            $.post( "/schedule/sanction_start", {"_token": "{{ csrf_token() }}"}, function( data ) {
                if (data.status==200){
                    alert('sukses');
                    $(".load").hide();
                    $(".btn").show();
                }
            });
        });     });
</script>
@endsection