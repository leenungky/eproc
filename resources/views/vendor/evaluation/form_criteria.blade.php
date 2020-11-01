<input type="hidden" id="id" name="id" value=""/>
<input type="hidden" id="oldweight" value="0"/>
<input type="hidden" id="oldcriteria" value=""/>
<div class="form-group row mb-2">
    <label for="criteria_group_id" class="col-3 col-form-label text-right">{{__('homepage.criteria_group_name')}}</label>
    <div class="col-9">
        <select id="criteria_group_id" name="criteria_group_id" class="custom-select custom-select-sm" required="required">
            <option value="">-- Choose Criteria Group --</option>
            @foreach($criteriaGroups as $group)
            <option value="{{$group->id}}">{{$group->name}}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="form-group row mb-2">
    <label for="name" class="col-3 col-form-label text-right">{{__('homepage.name')}}</label>
    <div class="col-9">
        <input type="text" id="name" name="name" placeholder="{{__('homepage.name')}}" class="form-control form-control-sm" required>
    </div>
</div>
<div class="form-group row mb-2">
    <label for="description" class="col-3 col-form-label text-right">{{__('homepage.description')}}</label>
    <div class="col-9">
        <textarea id="description" name="description" placeholder="{{__('homepage.description')}}" class="form-control form-control-sm" required></textarea>
    </div>
</div>
@if($scoreAssignment)
<div class="form-group row mb-2">
    <label for="weighting" class="col-3 col-form-label text-right">{{__('homepage.weighting')}}</label>
    <div class="col-9">
        <input type="number" min="1" max="100" id="weighting" name="weighting" pattern="[0-9]" placeholder="{{__('homepage.weighting')}}" class="form-control form-control-sm" required>
        <span>{{__('homepage.maximum_allowed_weight')}}: <span id="max_weighting">100</span></span>
    </div>
</div>
<div class="form-group row mb-2">
    <label for="minimum_score" class="col-3 col-form-label text-right">{{__('homepage.minimum_score')}}</label>
    <div class="col-9">
        <input type="number" min="0" max="100" id="minimum_score" name="minimum_score" pattern="[0-9]" placeholder="{{__('homepage.minimum_score')}}" class="form-control form-control-sm" required>
    </div>
</div>
<div class="form-group row mb-2">
    <label for="maximum_score" class="col-3 col-form-label text-right">{{__('homepage.maximum_score')}}</label>
    <div class="col-9">
        <input type="number" min="0" max="100" id="maximum_score" name="maximum_score" pattern="[0-9]" placeholder="{{__('homepage.maximum_score')}}" class="form-control form-control-sm" required>
    </div>
</div>
@endif