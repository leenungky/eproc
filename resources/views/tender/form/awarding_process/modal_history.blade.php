<div id="popup-history" class="modal fade common_modal" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{__('tender.process.history_title')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table id="dt-submission-history" class="table table-sm table-bordered table-striped" style="width: : 100%">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th class="description">{{__('homepage.username')}}</th>
                            <th class="is_required">{{__('homepage.role')}}</th>
                            <th class="document">{{__('homepage.activity')}}</th>
                            <th class="attachment">{{__('homepage.activity_date')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer"></div>
        </div>
    </div>
</div>
