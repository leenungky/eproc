<div id="popup-scoring" class="modal fade common_modal" role="dialog" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 50%;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{__('tender.process.scoring_title')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table id="dt-submission-scoring" class="table table-sm table-bordered table-striped" style="width: : 100%">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th class="criteria">{{__('tender.bidding.fields.criteria')}}</th>
                            <th class="weight">{{__('tender.bidding.fields.weight')}}</th>
                            <th class="scoring">{{__('tender.bidding.fields.score')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                @if($statusProcess == 'opened-pq')
                <button type="button" class="btn btn-sm btn-secondary btn-cancel" data-dismiss="modal">{{__('common.cancel')}}</button>
                <button type="button" class="btn btn-sm btn-primary btn-save">{{__('common.save')}}</button>
                @endif
            </div>
        </div>
    </div>
</div>
