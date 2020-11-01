
<div class="card" style="margin-bottom: 20px;">
    <div class="card-body" >
        <div class="form-group row mb-2">
            <label for="event_start" class="col-3 col-form-label text-right">{{__('tender.vendor_code')}}</label>
            <div class="col-6">
                <input type="text" id="f_vendor_code" name="f_vendor_code"
                    class="form-control form-control-sm filter-change"
                    placeholder="{{__('tender.vendor_code')}}" />
            </div>
        </div>
        <div class="form-group row mb-2">
            <label for="event_end" class="col-3 col-form-label text-right">{{__('tender.vendor_name')}}</label>
            <div class="col-6">
                <input type="text" id="f_vendor_name" name="f_vendor_name"
                    class="form-control form-control-sm filter-change"
                    placeholder="{{__('tender.vendor_name')}}" />
            </div>
        </div>
        <div class="form-group row mb-2">
            <label for="purchase_org_id" class="col-3 col-form-label text-right">{{__('tender.filter_sos_label')}}</label>
            <div class="col-6">
                <select id="f_sos" name="f_sos" class="custom-select custom-select-sm">
                    <option value="">{{__('tender.filter_sos_label')}}</option>
                    @foreach ($scopeOfSupplies as $sos)
                        <option value="{{$sos->id}}">{{$sos->id.' - '.$sos->description}}</option>
                    @endforeach

                </select>
            </div>
        </div>
        <div class="form-group row mb-2">
            <label for="f_vendor_has_awarded" class="col-3 col-form-label text-right">&nbsp;</label>
            <div class="col-6">
                <div class="custom-control custom-checkbox custom-control-inline">
                    <input name="f_vendor_has_awarded" id="f_vendor_has_awarded" type="checkbox"
                        class="custom-control custom-control-input" value="1" >
                    <label for="f_vendor_has_awarded" class="custom-control-label">{{__('tender.vendor_has_awarded')}}</label>
                </div>
            </div>
        </div>
        {{-- <div class="form-group row">
            <label for="btn" class="col-3 col-form-label text-right">&nbsp;</label>
            <div class="col-6 text-right">
                <button id="btn_filter" class="btn btn-success">{{__('common.search')}}</button>
            </div>
        </div> --}}
    </div>
</div>

<div class="card" >
    <div class="card-body" style="padding-right: 0; padding-left: 0; padding-bottom: 0">
        <div style="padding: 0">
            <table id="datatable_vendor" class="table table-sm table-bordered table-striped table-vcenter table-wrap" style="width: 100%">
                <thead>
                    <tr>
                        <th></th>
                        <th>No</th>
                        <th>{{__('homepage.vendor_code')}}</th>
                        <th>{{__('homepage.vendor_name')}}</th>
                        <th>{{__('homepage.pic_full_name')}}</th>
                        <th>{{__('homepage.vendor_status')}}</th>
                        <th>{{__('homepage.vendor_evaluation_score')}}</th>
                        <th>{{__('homepage.scope_of_supply1')}}</th>
                        <th>{{__('homepage.scope_of_supply2')}}</th>
                        <th>{{__('homepage.scope_of_supply3')}}</th>
                        <th>{{__('homepage.scope_of_supply4')}}</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>

    </div>
    <div class="card-footer" style="padding-right: 0; padding-left: 0; padding-bottom: 0">
        <div class="app-footer">
            <div class="app-footer__innersss">
                <div class="app-footer-left">
                    <div id="vpage_numbers" style="display:inherit"></div>
                </div>
            </div>
        </div>
    </div>
</div>

