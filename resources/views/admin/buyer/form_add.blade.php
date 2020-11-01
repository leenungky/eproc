<input id="id" name="id" type="hidden">

<div class="form-group row mb-2">
    <label for="user_id" class="col-3 col-form-label text-right">{{__('homepage.userid')}}</label>
    <div class="col-9">
        <select class="form-control form-control-sm" id="user_id" name="user_id" required>
            <option value="">-- Choose User --</option>
            @foreach($users as $user)
            <option value="{{$user->id}}">{{$user->userid}}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="form-group row mb-2">
    <label for="buyer_name" class="col-3 col-form-label text-right">{{__('homepage.name')}}</label>
    <div class="col-9">
        <input id="buyer_name" name="buyer_name" placeholder="{{__('homepage.name')}}" type="text" required="required" class="form-control form-control-sm">
    </div>
</div>

<div class="form-group-sm row mb-2">
    <label for="valid_from_date" class="col-form-label text-right col-sm-3">{{ __('homepage.valid_from_date') }}</label>
    <div class="col-sm-9">
        <input type="text" name="valid_from_date" class="form-control form-control-sm date" id="valid_from_date" placeholder="{{ __('homepage.valid_from_date') }}" data-toggle="datetimepicker" data-target="#valid_from_date" required/>
    </div>
</div>

<div class="form-group-sm row mb-2">
    <label for="valid_thru_date" class="col-form-label text-right col-sm-3">{{ __('homepage.valid_thru_date') }}</label>
    <div class="col-sm-9">
        <input type="text" name="valid_thru_date" class="form-control form-control-sm date" id="valid_thru_date" placeholder="{{ __('homepage.valid_thru_date') }}" data-toggle="datetimepicker" data-target="#valid_thru_date" required/>
    </div>
</div>

<div class="form-group row mb-2">
    <label for="purch_org_id" class="col-3 col-form-label text-right">{{__('homepage.purch_org')}}</label>
    <div class="col-9">
        @foreach($purchOrgs as $org)
        <div class="custom-control custom-checkbox">
            <input type="checkbox" class="custom-control-input" id="purch_org_id{{$org->id}}" name="purch_org_id[]" value="{{$org->id}}">
            <label class="custom-control-label" for="purch_org_id{{$org->id}}">{{$org->description}}</label>
        </div>
        @endforeach
    </div>
</div>

<div class="form-group row mb-2">
    <label for="purch_group_id" class="col-3 col-form-label text-right">{{__('homepage.purch_group')}}</label>
    <div class="col-9">
        @foreach($purchGroups as $group)
        <div class="custom-control custom-checkbox">
            <input type="checkbox" class="custom-control-input" id="purch_group_id{{$group->id}}" name="purch_group_id[]" value="{{$group->id}}">
            <label class="custom-control-label" for="purch_group_id{{$group->id}}">{{$group->description}}</label>
        </div>
        @endforeach
    </div>
</div>
