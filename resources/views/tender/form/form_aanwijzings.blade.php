<div class="form-group row mb-2">
    <label for="event_name" class="col-3 col-form-label text-right">{{__('tender.event_name')}}</label>
    <div class="col-9">
        <input id="id" name="id" type="hidden" value="">
        <input id="sequence_done" name="sequence_done" type="hidden" value="false">
        <input id="public_status" name="public_status" type="hidden" value="{{\App\Models\TenderAanwijzings::STATUS[1]}}">
        <input id="event_name" name="event_name" placeholder="{{__('tender.event_name')}}" type="text" required="required" class="form-control form-control-sm">
    </div>
</div>
<div class="form-group row mb-2">
    <label for="event_start" class="col-3 col-form-label text-right">{{__('tender.event_start')}}</label>
    <div class="col-9">
        <input type="text" id="event_start" name="event_start" class="form-control form-control-sm datetimepicker-input datetime" data-toggle="datetimepicker" data-target="#event_start" placeholder="{{__('tender.event_start')}}" required="required"/>
    </div>
</div>
<div class="form-group row mb-2">
    <label for="event_end" class="col-3 col-form-label text-right">{{__('tender.event_end')}}</label>
    <div class="col-9">
        <input type="text" id="event_end" name="event_end" class="form-control form-control-sm datetimepicker-input datetime" data-toggle="datetimepicker" data-target="#event_end" placeholder="{{__('tender.event_end')}}" required="required"/>
    </div>
</div>
<div class="form-group row mb-2">
    <label for="venue" class="col-3 col-form-label text-right">{{__('tender.venue')}}</label>
    <div class="col-9">
        <input id="venue" name="venue" placeholder="{{__('tender.venue')}}" type="text" required="required" class="form-control form-control-sm">
    </div>
</div>
<div class="form-group row mb-2">
    <label for="note" class="col-3 col-form-label text-right">{{__('tender.note')}}</label>
    <div class="col-9">
        <textarea id="note" name="note" placeholder="{{__('tender.note')}}" required="required" class="form-control form-control-sm"></textarea>
    </div>
</div>
<div class="edit-publish pt-2 hidden">
    <div style="text-align:center">
        <h5>{{__('tender.label_result_aanwijzing')}}</h5>
    </div>
    <div class="form-group row mb-2">
        <label for="result_attachment" class="col-3 col-form-label text-right">{{__('tender.attachment')}}</label>
        <div class="col-9">
            <input type="file" name="result_attachment" id="result_attachment" class="form-control form-control-sm attachment" />
            {{-- <div class="custom-file">
                <input type="file" id="result_attachment" name="result_attachment" class="custom-file-input custom-file-input-sm" disabled>
                <label id="result_attachment_label" class="custom-file-label" for="result_attachment">{{__('tender.attachment')}}</label>
            </div> --}}
        </div>
    </div>
    <div class="form-group row mb-2">
        <label for="result_description" class="col-3 col-form-label text-right">{{__('tender.description')}}</label>
        <div class="col-9">
            <textarea id="result_description" name="result_description" placeholder="{{__('tender.description')}}" class="form-control form-control-sm" disabled></textarea>
        </div>
    </div>
</div>
