<input type="hidden" id="id" name="id" value=""/>
<div class="form-group row mb-2">
    <label for="name" class="col-3 col-form-label text-right">{{__('homepage.name')}}</label>
    <div class="col-9">
        <input type="text" id="name" name="name" placeholder="{{__('homepage.name')}}" class="form-control form-control-sm" required>
    </div>
</div>
<div class="form-group row mb-2">
    <label for="categories_json" class="col-3 col-form-label text-right">{{__('homepage.vendor_selector')}}</label>
    <div class="col-9">
        <select id="categories_json" name="categories_json" class="form-control form-control-sm" required>
            <option value="">-- Choose Type --</option>
            @foreach($selectors as $selector)
            <option value="{{$selector}}">{{$selector}}</option>
            @endforeach
        </select>
    </div>
</div>
<div id="yearly" style="display:none">
    <div class="form-group row mb-2">
        <label for="po_count" class="col-3 col-form-label text-right">{{__('homepage.po_count')}}</label>
        <div class="col-9">
            <input type="number" min="0" value="0" id="po_count" name="po_count" placeholder="{{__('homepage.po_count')}}" class="form-control form-control-sm">
        </div>
    </div>
    <div class="form-group row mb-2">
        <label for="po_total" class="col-3 col-form-label text-right">{{__('homepage.po_total')}}</label>
        <div class="col-9">
            <input type="number" min="0" value="0" id="po_total" name="po_total" placeholder="{{__('homepage.po_total')}}" class="form-control form-control-sm">
        </div>
    </div>
</div>
<br>
<div class="pull-right mb-2">
    <a href="javascript:void(0)" id="addRow" class="btn btn-sm btn-success"><i class="fas fa-plus mr-2" aria-hidden="true"></i>{{__('homepage.create_new_entry')}}</a>
</div>
<table id="tbl_categories" class="table table-bordered table-striped table-sm">
<thead>
    <th>{{__('homepage.action')}}</th>
    <th>{{__('homepage.name')}}</th>
    <th>{{__('homepage.lowest_score_operator')}}</th>
    <th>{{__('homepage.lowest_score')}}</th>
    <th>{{__('homepage.highest_score_operator')}}</th>
    <th>{{__('homepage.highest_score')}}</th>
</thead>
<tbody>
</tbody>
</table>
