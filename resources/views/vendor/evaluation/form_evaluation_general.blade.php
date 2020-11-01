<input type="hidden" id="id" name="id" value="{{$general->id ?? ''}}"/>
<div class="row">
    <div class="col-6">
        <div class="form-group row mb-2">
            <label for="name" class="col-4 col-form-label text-right">{{__('homepage.name')}}</label>
            <div class="col-8">
                <input type="text" id="name" name="name" placeholder="{{__('homepage.name')}}" class="form-control form-control-sm" required value="{{$general->name ?? ''}}">
            </div>
        </div>
        <div class="form-group row mb-2">
            <label for="description" class="col-4 col-form-label text-right">{{__('homepage.description')}}</label>
            <div class="col-8">
                <textarea id="description" name="description" placeholder="{{__('homepage.description')}}" class="form-control form-control-sm" required>{{$general->description ?? ''}}</textarea>
            </div>
        </div>
        <div class="form-group row mb-2">
            <label for="category_id" class="col-4 col-form-label text-right">{{__('homepage.category_name')}}</label>
            <div class="col-8">
                <select id="category_id" name="category_id" class="custom-select custom-select-sm" required="required">
                    <option value="">-- Choose Category --</option>
                    @foreach($scoreCategories as $key=>$value)
                    <option value="{{$key}}"{{isset($general->category_id)?($general->category_id==$key ? ' selected' : '') : ''}}>{{$value}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group row mb-2">
            <label for="year" class="col-4 col-form-label text-right">{{__('homepage.year')}}</label>
            <div class="col-8">
                <input type="number" min="{{date('Y')-5}}" max="{{date('Y')}}" id="year" name="year" placeholder="{{__('homepage.year')}}" class="form-control form-control-sm" required value="{{$general->year ?? date('Y')}}">
            </div>
        </div>
    </div>

    <div class="col-6">
        <div class="form-group row mb-2">
            <label for="criteria_group_id" class="col-4 col-form-label text-right">{{__('homepage.criteria_group_name')}}</label>
            <div class="col-8">
                <select id="criteria_group_id" name="criteria_group_id" class="custom-select custom-select-sm" required="false">
                    <option value="">-- Choose Group --</option>
                    @foreach($criteriaGroups as $group)
                    <option value="{{$group->id}}"{{isset($general->criteria_group_id)?($general->criteria_group_id==$group->id ? ' selected' : '') : ''}}>{{$group->name}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div id="project" class="form-group row mb-2" style="display:none">
            <label for="project_code" class="col-4 col-form-label text-right">{{__('homepage.project')}}</label>
            <div class="col-8">
                <select id="project_code" name="project_code" class="custom-select custom-select-sm" required="false">
                    <option value="">-- Choose Project --</option>
                    @foreach($projects as $project)
                    <option value="{{$project->code}}"{{isset($general->project_code)?($general->project_code==$project->code ? ' selected' : '') : ''}}>{{$project->code.' - '.$project->name}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div id='yearly' style="display:none">
            <div class="form-group row mb-2">
                <label for="start_date" class="col-4 col-form-label text-right">{{__('homepage.start_date')}}</label>
                <div class="col-8">
                    <input type="text" id="start_date" name="start_date" placeholder="{{__('homepage.start_date')}}" required="required" class="form-control form-control-sm datetimepicker-input date" data-toggle="datetimepicker" data-target="#start_date">
                </div>
            </div>
            <div class="form-group row mb-2">
                <label for="end_date" class="col-4 col-form-label text-right">{{__('homepage.end_date')}}</label>
                <div class="col-8">
                    <input type="text" id="end_date" name="end_date" placeholder="{{__('homepage.end_date')}}" required="required" class="form-control form-control-sm datetimepicker-input date" data-toggle="datetimepicker" data-target="#end_date">
                </div>
            </div>
        </div>
        <div class="form-group row mb-2" hidden>
            <label for="status" class="col-4 col-form-label text-right">{{__('homepage.status')}}</label>
            <div class="col-8">
                <input type="text" id="status" name="status" placeholder="{{__('homepage.status')}}" class="form-control form-control-sm" readonly value="{{$general->status ?? ''}}">
            </div>
        </div>
    </div>
</div>
