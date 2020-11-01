<div id="text-item" class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
            <h6 class="panel-title"><strong>{{__('tender.item_cost.title_item_text')}}</strong></h6>
            </div>
            <div class="panel-body">
                <div class="" style="padding: 0">
                    <div class="form-group row mb-2">
                        {{-- <label for="item_text" class="col-2 col-form-label text-right">&nbsp;</label> --}}
                        <div class="col-12">
                            <textarea name="item_text" class="form-control form-control-sm"
                                data-val="" @if(!$editable) disabled @endif
                                rows="8"
                            ></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <hr>
    </div>
</div>
