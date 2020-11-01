@if(is_array($translate))
@foreach($translate as $k=>$v)
@include('admin.locale.partials_manager',['key'=>$key.']['.$k,'translate'=>$v])
@endforeach
@else
@include('admin.locale.partials_line',['key'=>$key,'translate'=>$translate])
@endif
