<main>
    <div class="content" style="margin-top: 20px;">
        <table class="tbl1" width="100%" cellpadding="0" cellspacing="0" border="0" style="font-size: 9pt;">
            <thead>
                <tr>
                    <th width="10%">No PR</th>
                    <th>{{__('purchaserequisition.number')}}</th>
                    <th>Item Service</th>
                    <th>{{__('purchaserequisition.product_code')}}</th>
                    <th>{{__('purchaserequisition.description')}}</th>
                    <th>{{__('purchaserequisition.qty')}}</th>
                    <th>{{__('purchaserequisition.uom')}}</th>
                    <th>{{__('purchaserequisition.product_group_code')}}</th>
                    <th>{{__('purchaserequisition.est_unit_price')}}</th>
                    <th>{{__('purchaserequisition.currency_code')}}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($prItemList as $k => $v)
                    <tr>
                        <td style="text-align: center;">{{!empty($v->number) ? (int)$v->number : ''}}</td>
                        <td style="text-align: center;">{{!empty($v->line_number) ? (int)$v->line_number : ''}}</td>
                        <td></td>
                        <td style="text-align: center;">{{!empty($v->product_code) ? (int)$v->product_code : ''}}</td>
                        <td style="text-align: left; width: 275px;">{{$v->description}}</td>
                        <td style="text-align: right;">{{$v->qty}}</td>
                        <td>{{$v->uom}}</td>
                        <td>{{$v->product_group_code}}</td>
                        <td style="text-align: right;">{{$v->est_unit_price}}</td>
                        <td style="text-align: center;">{{$v->currency_code}}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</main>
