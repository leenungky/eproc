<div class="form-group row mb-2">
    <label for="document_name" class="col-3 col-form-label text-right">{{__('tender.document_name')}}</label>
    <div class="col-9">
        <input id="id" name="id" type="hidden" value="">
        <input id="sequence_done" name="sequence_done" type="hidden" value="false">
        <input id="document_name" name="document_name" placeholder="{{__('tender.document_name')}}" type="text" required="required" class="form-control form-control-sm">
    </div>
</div>
<div class="form-group row mb-2">
    <label for="description" class="col-3 col-form-label text-right">{{__('tender.description')}}</label>
    <div class="col-9">
        <textarea id="description" name="description" placeholder="{{__('tender.description')}}" required="required" class="form-control form-control-sm"></textarea>
    </div>
</div>
<div class="form-group row mb-2">
    <label for="attachment" class="col-3 col-form-label text-right">{{__('tender.attachment')}}</label>
    <div class="col-6">
        <input type="file" name="attachment" id="attachment" class="form-control form-control-sm attachment" />
    </div>
</div>
