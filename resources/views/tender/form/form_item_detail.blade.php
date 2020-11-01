
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
                            @foreach ($tenderData['tender_'.$type]['fields'] as $k => $field)
                                @if($k < 12)
                                <tr>
                                    <th>{{__('purchaserequisition.'.$field)}}</th>
                                    <td>:</td>
                                    <td id="{{$field}}">
                                        {{-- <input type="number" name="{{$field}}" class="form-control form-control-sm" /> --}}
                                    </td>
                                </tr>
                                @endif
                            @endforeach
                        </table>
                    </div>
                    <div class="col-sm-4">
                        <table class="table table-borderless">
                            @foreach ($tenderData['tender_'.$type]['fields'] as $k => $field)
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
                            @foreach ($tenderData['tender_'.$type]['fields'] as $k => $field)
                                @if($k >= 24)
                                <tr>
                                    <th>{{__('purchaserequisition.'.$field)}}</th>
                                    <td>:</td>
                                    <td id="{{$field}}"></td>
                                </tr>
                                @endif
                            @endforeach
                        </table>
                    </div>
                </div>
                <hr>
            </div>
        </div>

        @include('tender.form.form_item_detail_text')
        @include('tender.form.form_item_detail_service')
        @include('tender.form.form_item_detail_tax')
        @include('tender.form.form_item_detail_cost')

    </div>

</div>

