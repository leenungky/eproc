<div class="form-group row mb-2">
    <label for="text" class="col-3 col-form-label text-right">{{__('personnel.userid')}}</label>
    <div class="col-9">
        <input id="userid" name="userid" placeholder="{{__('personnel.userid')}}" type="text" required="required" class="form-control form-control-sm" readonly>
    </div>
</div>
<div class="form-group row mb-2">
    <label for="text" class="col-3 col-form-label text-right">{{__('personnel.name')}}</label>
    <div class="col-9">
        <input id="id" name="id" type="hidden">
        <input id="name" name="name" placeholder="{{__('personnel.name')}}" type="text" required="required" class="form-control form-control-sm">
    </div>
</div>
<div class="form-group row mb-2">
    <label for="text" class="col-3 col-form-label text-right">{{__('personnel.position')}}</label>
    <div class="col-9">
        <input id="position" name="position" placeholder="{{__('personnel.position')}}" type="text" required="required" class="form-control form-control-sm">
    </div>
</div>
<div class="form-group row mb-2">
    <label for="text" class="col-3 col-form-label text-right">{{__('personnel.email')}}</label>
    <div class="col-9">
        <input id="email" name="email" placeholder="{{__('personnel.email')}}" type="email" required="required" class="form-control form-control-sm">
    </div>
</div>
<div class="form-group row mb-2">
    <label for="text" class="col-3 col-form-label text-right">{{__('personnel.status')}}</label>
    <div class="col-9">
        <div class="custom-control custom-radio custom-control-inline">
            <input name="status" id="status_1" type="radio" class="custom-control-input" value="1" required="required"> 
            <label for="status_1" class="custom-control-label">{{__('common.active')}}</label>
        </div>
        <div class="custom-control custom-radio custom-control-inline">
            <input name="status" id="status_0" type="radio" class="custom-control-input" value="0" required="required"> 
            <label for="status_0" class="custom-control-label">{{__('common.non_active')}}</label>
        </div>
    </div>
</div>
<div class="form-group row mb-2">
    <label for="text" class="col-3 col-form-label text-right">{{__('personnel.roles')}}</label>
    <div class="col-9">
        <div class="input-group">
            <select id="editroles" name="roles[]" class="roles form-control form-control-sm" data-placeholder="{{__('personnel.roles')}}" multiple="multiple" required="required">
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
