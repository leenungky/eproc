<div class="form-group row">
    <label class="col-3 col-form-label text-right" for="{{$attr['id']??''}}">
    {{$attr['label']??''}} @if(isset($attr['required']))<span class="font-danger">*</span>@endif
    </label>
    <div class="col-9">
    <input @foreach($attr as $k=>$v){{$k}}='{{$v}}' @endforeach placeholder="{{$attr['label']??''}}"/>
    </div>
</div>
