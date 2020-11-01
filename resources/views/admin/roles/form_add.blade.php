<div class="form-group row mb-2">
    <input id="id" name="id" type="hidden">
    <label for="name" class="col-3 col-form-label text-right">{{__('homepage.role_name')}}</label>
    <div class="col-9">
        <input id="name" name="name" placeholder="{{__('homepage.role_name')}}" type="text" required="required" class="form-control form-control-sm">
    </div>
</div>

<div class="form-group row mb-2">
    <label for="text" class="col-3 col-form-label text-right">{{__('homepage.permission')}}</label>
    <div class="col-9">
        <select class="form-control form-control-sm" id="permissions" name="permissions[]" multiple="multiple">
            <option value="">-- Choose Permission --</option>
            @foreach($permissions as $permission)
            {{-- <option value="{{$permission->name}}">{{ucwords(implode(" ",explode("_",$permission->name)))}}</option> --}}
            <option value="{{$permission->name}}">{{ \App\Helpers\App::transFb('permissions.'.$permission->name, ucwords(implode(" ",explode("_",$permission->name)))) }}</option>
            @endforeach
        </select>
    </div>
</div>

