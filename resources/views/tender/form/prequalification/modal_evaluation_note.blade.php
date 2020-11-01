<div id="popup-evaluation" class="modal fade common_modal" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{__('tender.process.btn_evaluation_note')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group mb-2">
                    <label for="evaluation_notes"
                        class="col-form-label text-right">{{__('tender.process.btn_evaluation_note')}}</label>
                    <textarea name="evaluation_notes" rows="5" @if(!$editable || $statusProcess == '') disabled @endif
                        class="form-control form-control-sm"></textarea>
                </div>
                <div class="card">
                    <div class="card-header">
                        <div class="card-header-left">
                            <span class="heading-title">{{__('tender.evaluators')}}</span>
                        </div>
                    </div>
                    <div class="card-body no-padding">
                        <table id="dt-evaluator" class="table table-sm table-bordered table-striped" style="width: : 100%">
                            <thead>
                                <tr>
                                    <th class="description">{{__('homepage.fullname')}}</th>
                                    <th class="is_required">{{__('tender.schedule.fields.position')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($teams as $sign)
                                    <tr>
                                        <td>{{$sign->buyer_name}}</td>
                                        <td>{{$sign->position}}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>


            </div>
            <div class="modal-footer">
                @if($statusProcess != '')
                <button type="button" class="btn btn-sm btn-secondary btn-cancel" data-dismiss="modal">{{__('common.cancel')}}</button>
                <button type="button" class="btn btn-sm btn-primary btn-save">{{__('common.save')}}</button>
                @endif
            </div>
        </div>
    </div>
</div>
