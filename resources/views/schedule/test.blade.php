@extends('layouts.one_column')

@section('contentheader')
<meta name="csrf-token" content="{{ csrf_token() }}" />
<div class="card-header-left">
    <span class="heading-title">
    <i class="fa fa-pencil-square-o" aria-hidden="true"></i>&nbsp;Testing Performa
    </span>
</div>
<div class="card-header-right">
</div>
@endsection

@section('contentbody')
<button id="start">Start</button>
<button id="stop">Stop</button>
<div class="load" style="display:none">
    loader ............
</div>
@endsection


@section('modules-scripts')
<script type="text/javascript">
    require(["metisMenu"],function(){
        var is_do_loop = false;
        //var domain = 'http://eproctimas.local:8082';
        var domain = 'https://eprocdev.timas.com'
        $("#start").click(function(){     
            is_do_loop = true;       
            $(".load").show();
            var i = 0;
            var until = 60;
            do {
                prosessdata(domain,is_do_loop, i, until);
                if (i==(until-1) ){
                    console.log("looping selesai");
                    is_do_loop=false;
                }           
                i++;                
            }            
            while ((i < until) && (is_do_loop));
        });

        $("#stop").click(function(){
            $(".load").hide();
            console.log('stop');
            id_do_loop = false;
            console.log(id_do_loop);
        });
    });

    function prosessdata(domain,is_do_loop, i, until){
        $.post(domain + "/login",{userid: "admin", password: "password"}, function(data){
            console.log(is_do_loop + ", The number is " + i + '==' + until);
            if (i>=(until-5)){
                $(".load").hide();
            }
        });
        $.post(domain + "/announcement/data-table/open",
        {"draw":"1","columns":[{"data":"id","name":"id","searchable":"true","orderable":"false","search":{"value":null,"regex":"false"}},{"data":"tender_number","name":"tender_number","searchable":"true","orderable":"true","search":{"value":null,"regex":"false"}},{"data":"title","name":"title","searchable":"true","orderable":"true","search":{"value":null,"regex":"false"}},{"data":"scope_of_work","name":"scope_of_work","searchable":"true","orderable":"true","search":{"value":null,"regex":"false"}},{"data":"tender_method","name":"tender_method","searchable":"true","orderable":"true","search":{"value":null,"regex":"false"}},{"data":"winning_method","name":"winning_method","searchable":"true","orderable":"true","search":{"value":null,"regex":"false"}},{"data":"submission_method","name":"submission_method","searchable":"true","orderable":"true","search":{"value":null,"regex":"false"}},{"data":"status","name":"status","searchable":"true","orderable":"true","search":{"value":null,"regex":"false"}},{"data":"workflow_status","name":"workflow_status","searchable":"true","orderable":"true","search":{"value":null,"regex":"false"}},{"data":"created_at","name":"created_at","searchable":"true","orderable":"true","search":{"value":null,"regex":"false"}},{"data":"evaluation_method","name":"evaluation_method","searchable":"true","orderable":"true","search":{"value":null,"regex":"false"}},{"data":"internal_organization","name":"internal_organization","searchable":"true","orderable":"true","search":{"value":null,"regex":"false"}},{"data":"purchase_organization","name":"purchase_organization","searchable":"true","orderable":"true","search":{"value":null,"regex":"false"}}],"order":[{"column":"0","dir":"asc"}],"start":"0","length":"10","search":{"value":null,"regex":"false"}}, 
            function(data){
            console.log(is_do_loop + ", The number is " + i + '==' + until);
            if (i>=(until-5)){
                $(".load").hide();
            }
        });  
    }
</script>
@endsection