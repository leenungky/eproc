<div class="form-group row">
<label class="col-3 col-form-label text-right" for="{{$attr['id']??''}}">
    {{$attr['label']??'label'}} @if(isset($attr['required']))<span class="font-danger">*</span>@endif
    </label>
    <div class="col-9">
    <select @foreach($attr as $k=>$v){{$k}}='{{$v}}' @endforeach>
    @foreach($options as $k=>$v)
    <option value="{{$k}}" @if($k == $attr['value']) selected @endif>{{$v}}</option>
    @endforeach
    </select>
    </div>
</div>
