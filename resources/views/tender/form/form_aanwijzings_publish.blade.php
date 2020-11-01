<div class="alert alert-warning" role="alert">
    <b>{!!__('tender.aanwijzing_alert_publih_title')!!}</b><br/>
    {!!__('tender.aanwijzing_alert_publih_message')!!}
</div>
<div>
<input id="id" name="id" type="hidden" value="">
<input id="sequence_done" name="sequence_done" type="hidden" value="false">
<input id="status" name="public_status" type="hidden" value="{{\App\Models\TenderAanwijzings::STATUS[1]}}">
<table class="table table-bordered table-sm table-striped">
    <tr>
        <th style="width:150px" class="th-label">{{__('tender.event_name')}}</th>
        <td><span id="event_name"></span></td>
    </tr>
    <tr>
        <th class="th-label">{{__('tender.venue')}}</th>
        <td><span id="venue"></span></td>
    </tr>
    <tr>
        <th class="th-label">{{__('tender.event_start')}}</th>
        <td><span id="event_start"></span></td>
    </tr>
    <tr>
        <th class="th-label">{{__('tender.event_end')}}</th>
        <td><span id="event_end"></span></td>
    </tr>
</table>
