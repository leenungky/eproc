@foreach ($data as $key=>$name)
    @if(is_array($name))
    <div class="form-group row mb-2">
        <label for="{{$key}}" class="col-6 col-form-label">{!!$spacing!!}{{__('homepage.'.$key)}}</label>
        <div class="col-6">&nbsp;</div>
    </div>
    @include('applicants.profiles.partials.finance_form',['data'=>$name, 'spacing'=>$spacing.'&nbsp;&nbsp;&nbsp;&nbsp;'])
    @else
    <div class="form-group row mb-2">
        <label for="{{$name}}" class="col-6 col-form-label">{!!$spacing!!}{{__('homepage.'.$name)}}</label>
        <div class="col-6">
            <input type="text" id="{{$name}}" name="{{$name}}" placeholder="0" class="form-control form-control-sm text-right money">
        </div>
    </div>
    @endif
@endforeach
