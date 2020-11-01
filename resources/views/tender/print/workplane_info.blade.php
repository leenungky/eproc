@php
    $regSchedule = $schedules->where('type', 1)->first();
@endphp
<main>
    <div class="content" style=""">
        <div style="position:absolute; left:0pt; width:400pt;">
            <h3 class="content-title">{{ __('tender.print.procurement_info')}}</h3>
            <table class="tbl-detail" width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td width="250px">{{ __('tender.title')}}</td>
                    <td width="10px">:</td>
                    <td>{{$tender->title}}</td>
                </tr>
                <tr>
                    <td>{{ __('tender.purchase_organization')}}</td><td>:</td>
                    <td>{{$tender->purchase_organization}}</td>
                </tr>
                <tr>
                    <td>{{ __('tender.purchase_group')}}</td><td>:</td>
                    <td>{{$tender->internal_organization}}</td>
                </tr>
                <tr>
                    <td>{{ __('tender.location')}}</td><td>:</td>
                    <td>{{$tender->location}}</td>
                </tr>

                <tr>
                    <td>{{ __('tender.incoterm')}}</td><td>:</td>
                    <td>{{$tender->incoterm}}</td>
                </tr>
                <tr>
                    <td>{{ __('tender.prequalification')}}</td><td>:</td>
                    <td>{{ $tender->prequalification==1 ? __('tender.yes') :__('tender.no') }}</td>
                </tr>
                <tr>
                    <td>{{ __('tender.method')}}</td><td>:</td>
                    <td>{{__('tender.'.$tender->tender_method_value)}}</td>
                </tr>
                <tr>
                    <td>{{ __('tender.evaluation_method')}}</td><td>:</td>
                    <td>{{__('tender.'.$tender->evaluation_method_value)}}</td>
                </tr>
                <tr>
                    <td>{{ __('tender.eauction')}}</td><td>:</td>
                    <td>{{ $tender->eauction==1 ? __('tender.yes') :__('tender.no') }}</td>
                </tr>
                <tr>
                    <td>{{ __('tender.validity_quotation')}}</td><td>:</td>
                    <td>{{$tender->validity_quotation}} Hari</td>
                </tr>
                <tr>
                    <td>{{ __('tender.submission_method')}}</td><td>:</td>
                    <td>{{__('tender.'.$tender->submission_method_value)}}</td>
                </tr>
                <tr>
                    <td>{{ __('tender.bid_bond')}}</td><td>:</td>
                    <td>{{ $tender->bid_bond==1 ? __('tender.yes') :__('tender.no') }}</td>
                </tr>
            </table>
        </div>

        <div style="margin-left:400pt;">
            <h3 class="content-title">{{ __('tender.print.procurement_schedule')}}</h3>
            <table class="tbl-detail" width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td width="250px">Registration Opening Date</td>
                    <td width="10px">:</td>
                    <td>{{$regSchedule->start_date ?? '-'}}</td>
                </tr>
                <tr>
                    <td>Registration Closing Date</td><td>:</td>
                    <td>{{$regSchedule->end_date ?? '-'}}</td>
                </tr>
                <tr>
                    <td>Tender Submission Opening Date</td><td>:</td>
                    <td>-</td>
                </tr>
                <tr>
                    <td>Tender Submission Closing Date</td><td>:</td>
                    <td>-</td>
                </tr>
                <tr>
                    <td>Pre-Bid Meeting Date</td><td>:</td>
                    <td>-</td>
                </tr>
                <tr>
                    <td>{{__('tender.aanwijzing')}} Location</td><td>:</td>
                    <td>-</td>
                </tr>
                <tr>
                    <td>Contact Person {{__('tender.aanwijzing')}}</td><td>:</td>
                    <td>-</td>
                </tr>
                <tr>
                    <td>Bid Opening Date</td><td>:</td>
                    <td>-</td>
                </tr>
                <tr>
                    <td>Bid Opening Location</td><td>:</td>
                    <td>-</td>
                </tr>
                <tr>
                    <td>Contact Person Bid Opening</td><td>:</td>
                    <td>-</td>
                </tr>
            </table>
        </div>
    </div>
</main>
