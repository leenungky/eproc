@if($editableItem)
<div class="form-area row">
    <div class="col-5">
        <div class="form-group mb-2">
            <label for="cost_type_id" class="col-form-label">{{__('tender.item_cost.fields.name')}}</label>
            <div>
                <input type="hidden" id="id" name="id"/>
                <input type="hidden" name="conditional_name" />
                <input type="hidden" name="calculation_pos" />
                <input type="hidden" name="conditional_type" value="{{$tender->conditional_type}}" />
                <select name="conditional_code" class="custom-select custom-select-sm">
                    <option value=""></option>
                    @foreach($conditionalTypeList as $val)
                    <option value="{{$val->type}}"
                        data-calculation-type="{{$val->calculation_type}}"
                        data-calculation-pos="{{$val->calculation_pos}}">{{$val->description}}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="col-3 g-percentage no-padding">
        <div class="form-group mb-2">
            <label for="percentage" class="col-form-label">{{__('tender.item_cost.fields.percentage')}}</label>
            <div class="">
                <input type="number" name="percentage" class="form-control form-control-sm" min="0" max="100" step=".01"/>
            </div>
        </div>
    </div>
    <div class="col-3 g-value no-padding">
        <div class="form-group mb-2">
            <label for="value" class="col-form-label">{{__('tender.item_cost.fields.value')}}</label>
            <div class="">
                <input type="number" name="value" class="form-control form-control-sm" min="0" step=".01"/>
            </div>
        </div>
    </div>
    <div class="col-4">
        <div class="form-group mb-2">
            <label for="value" class="col-form-label">&nbsp;</label>
            <div class="text-right">
                <button class="btn btn-cancel btn-light">{{__('common.cancel')}}</button>
                <button class="btn btn-add btn-success">{{__('common.add')}}</button>
            </div>

        </div>
    </div>
</div>
@endif
<div class="table-responsive" style="padding: 0">
    @if (session('status'))
    <div class="alert alert-success" role="alert">
        {{ session('status') }}
    </div>
    @endif

    <table id="dt-add-cost" class="table table-sm table-bordered table-striped table-vcenter" style="width: 100%">
        <thead>
            <tr>
                <th></th>
                <th>{{__('tender.item_cost.fields.name')}}</th>
                <th>{{__('tender.item_cost.fields.percentage')}}</th>
                <th>{{__('tender.item_cost.fields.value')}}</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
