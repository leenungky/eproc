<div id="tax-item" class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
            <h6 class="panel-title"><strong>{{__('tender.item_cost.title_tax')}}</strong></h6>
            </div>
            <div class="panel-body">
                <div class="" style="padding: 0">
                    <div class="form-group row mb-2" @if(!$editable) hidden @endif>
                        <label for="cost_type_id" class="col-2 col-form-label text-right">{{__('tender.item_cost.fields.tax_code')}}</label>
                        <div class="col-4">
                            <input type="hidden" name="id"/>
                            <select name="tax_code" class="custom-select custom-select-sm">
                                <option value=""></option>
                                @foreach($taxCodes as $val)
                                <option value="{{$val->tax_code}}">{{$val->description}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-2 text-left">
                            <button class="btn btn-cancel btn-light">{{__('common.cancel')}}</button>
                            <button class="btn btn-add btn-success">{{__('common.add')}}</button>
                        </div>
                    </div>

                    <table id="dt-tax-item" class="table table-sm table-bordered table-striped table-vcenter"
                        style="width: 100%">
                        <thead>
                            <tr>
                                <th></th>
                                <th>{{__('tender.item_cost.fields.tax_code')}}</th>
                                <th>{{__('tender.item_cost.fields.description')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <hr>
    </div>
</div>
