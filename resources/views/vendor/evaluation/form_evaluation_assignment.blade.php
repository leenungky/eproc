<input type="hidden" id="id" name="id" value=""/>
<div class="form-group row mb-2">
    <label for="criteria_id" class="col-3 col-form-label text-right">{{__('homepage.criteria_name')}}</label>
    <div class="col-9">
        <select id="criteria_id" name="criteria_id" class="custom-select custom-select-sm" required="required">
            <option value="">-- Choose Criteria --</option>
            @foreach($criterias as $key=>$value)
            <option value="{{$key}}">{{$value}}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="form-group row mb-2">
    <label for="weighting" class="col-3 col-form-label text-right">{{__('homepage.weighting')}}</label>
    <div class="col-9">
        <input type="number" min="1" max="100" id="weighting" name="weighting" placeholder="{{__('homepage.weighting')}}" class="form-control form-control-sm" required>
        <span>{{__('homepage.maximum_allowed_weight')}}: <span id="max_weighting">100</span></span>
    </div>
</div>
<div class="form-group row mb-2">
    <label for="minimum_score" class="col-3 col-form-label text-right">{{__('homepage.minimum_score')}}</label>
    <div class="col-9">
        <input type="number" min="0" max="100" id="minimum_score" name="minimum_score" placeholder="{{__('homepage.minimum_score')}}" class="form-control form-control-sm" required>
    </div>
</div>
<div class="form-group row mb-2">
    <label for="maximum_score" class="col-3 col-form-label text-right">{{__('homepage.maximum_score')}}</label>
    <div class="col-9">
        <input type="number" min="0" max="100" id="maximum_score" name="maximum_score" placeholder="{{__('homepage.maximum_score')}}" class="form-control form-control-sm" required>
    </div>
</div>
