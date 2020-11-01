<div class="form-group row mb-2">
    <label for="text" class="col-3 col-form-label text-right">{{__('personnel.userid')}}</label>
    <div class="col-9">
        <input id="userid" name="userid" placeholder="{{__('personnel.userid')}}" type="text" required="required" class="form-control form-control-sm">
    </div>
</div>
<div class="form-group row mb-2">
    <label for="text" class="col-3 col-form-label text-right">{{__('personnel.name')}}</label>
    <div class="col-9">
        <input id="name" name="name" placeholder="{{__('personnel.name')}}" type="text" required="required" class="form-control form-control-sm">
    </div>
</div>
<div class="form-group row mb-2">
    <label for="text" class="col-3 col-form-label text-right">{{__('personnel.roles')}}</label>
    <div class="col-9">
        <div class="input-group">
            <select id="addroles" name="roles[]" class="roles form-control form-control-sm" data-placeholder="{{__('personnel.roles')}}" multiple="multiple" required="required">
                @foreach($roles as $role)
                <option value="{{$role->name}}">{{ucwords(implode(' ',explode('_',$role->name)))}}</option>
                @endforeach
            </select>
            <div class="input-group-append">
                <span id="clear-roles" class="input-group-text" style="cursor:pointer"><i class="fas fa-times"></i></span>
            </div>
        </div>
    </div>
</div>
