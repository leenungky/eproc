<div class="form-group row">
    <label class="col-4 col-form-label" for="{{$options['id'] ?? 'id'}}">
    {{$attr['label']??'label'}} @if(isset($attr['required']))<span class="font-danger">*</span>@endif
    </label>
    <div class="col-8">
    <textarea @foreach($attr as $k=>$v){{$k}}='{{$v}}' @endforeach>{{$attr['value']??''}}</textarea>
    </div>
</div>
