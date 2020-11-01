<div class="card item-detail" style="margin-bottom: 20px;">
    <div class="card-body" >
        <div id="pr-item" class="row">
            <div class="col-sm-12">
                <div class="item-detail-title">
                    <h2 class="title-left"></h2><h3 class="title-right pull-right"></h3>
                </div>
                <hr>
                <div class="row detail-content">
                    <div class="col-sm-4">
                        <input type="hidden" name="id" />
                        <table class="table table-borderless">
                            @foreach ($tenderData[$type]['prlist'] as $k => $field)
                                @if($k < 12)
                                <tr>
                                    <th>{{__('purchaserequisition.'.$field)}}</th>
                                    <td>:</td>
                                    <td id="{{$field}}"></td>
                                </tr>
                                @endif
                            @endforeach
                        </table>
                    </div>
                    <div class="col-sm-4">
                        <table class="table table-borderless">
                            @foreach ($tenderData[$type]['prlist'] as $k => $field)
                                @if($k >= 12 && $k < 24)
                                <tr>
                                    <th>{{__('purchaserequisition.'.$field)}}</th>
                                    <td>:</td>
                                    <td id="{{$field}}"></td>
                                </tr>
                                @endif
                            @endforeach
                        </table>
                    </div>
                    <div class="col-sm-4">
                        <table class="table table-borderless">
                            @foreach ($tenderData[$type]['prlist'] as $k => $field)
                                @if($k >= 24)
                                <tr>
                                    <th>{{__('purchaserequisition.'.$field)}}</th>
                                    <td>:</td>
                                    <td id="{{$field}}"></td>
                                </tr>
                                @endif
                            @endforeach
                            <tr>
                                <th>{{__('tender.process.compliance_label')}}</th>
                                <td>:</td>
                                <td id="compliance">
                                    <div class="form-group no-margin">
                                        <select name="compliance" class="custom-select form-control form-control-sm" required disabled>
                                            <option value="">Select...</option>
                                            <option value="comply">Comply</option>
                                            <option value="deviate">Deviate</option>
                                            <option value="no_quote">No Quote</option>
                                        </select>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                <hr>
            </div>
        </div>

        @include('tender.form.tender_process.form_item_detail_text')
        @include('tender.form.tender_process.form_item_detail_service')
        @include('tender.form.tender_process.form_item_detail_tax')
        @include('tender.form.tender_process.form_item_detail_cost')

    </div>

</div>

