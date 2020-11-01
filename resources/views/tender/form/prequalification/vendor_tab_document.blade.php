<div class="tab-body">
    @if(!$hasDocument)
    <div id="card-schedule" class="card">
        <div class="card-body card-schedule" style="padding: 10px;">
            <button id="btn_new_doc" class="btn btn-success" @if(!$isRegistered) disabled @endif>
                <i class="fa fa-plus"></i>&nbsp;&nbsp;&nbsp;{{__('tender.process.btn_new_pq')}}</button>
        </div>
    </div>
    @else
    <div class="has-footer has-tab" style="padding: 0">
        <div id="card-schedule" class="card">
            <div class="card-header">
                <div class="card-header-left">
                    <span class="heading-title">{{__('tender.bidding_document_requirements')}}</span>
                </div>
            </div>
            <div class="card-body card-schedule">
                <div class="">
                    <table id="dt-bid-doc-requirement" class="table table-sm table-bordered table-striped table-vcenter">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th class="description">{{__('tender.bidding.fields.description')}}</th>
                                <th class="is_required">{{__('tender.bidding.fields.is_required')}}</th>
                                <th class="attachment">{{__('tender.document')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="app-footer">
        <div class="app-footer__inner top-border">
            <div class="app-footer-left">
                @if($statusProcess != 'registration' && $tender->visibility_bid_document == 'PUBLIC')
                <button id="btn_log" class="btn btn-link"><i class="fa fa-history"></i> {{__('tender.process.btn_submission_log')}}</button>
                @endif
            </div>
            <div class="app-footer-right">
                <button id="btn_comment" class="btn btn-outline-secondary mr-2">
                    <i class="fa fa-comments"></i> {{__('common.comment')}} <span>(0)</span>
                </button>
                @if(empty($statusProcess) && $next != $type)
                    <button class="btn btn-primary btn_next_flow">
                        {{__('tender.next')}} <i class="fa fa-arrow-right"></i></button>
                @elseif(!empty($statusProcess))
                    <button id="btn_delete_draft" class="btn btn-secondary mr-3" @if(!$enabled) disabled @endif >
                        <i class="fa fa-trash"></i> {{__('tender.process.btn_delete_draft')}}</button>
                    @if(isset($workflowValues[2]) && $workflowValues[2] == 'request_resubmission')
                        <button id="btn_resubmit" class="btn btn-success" @if(!$enabled) disabled @endif>
                            <i class="fa fa-paper-plane"></i>&nbsp;&nbsp;&nbsp;{{__('common.resubmit')}}</button>
                    @else
                        <button id="btn_submit" class="btn btn-success" @if(!$enabled) disabled @endif>
                            <i class="fa fa-paper-plane"></i>&nbsp;&nbsp;&nbsp;{{__('common.submit')}}</button>
                    @endif
                @endif
            </div>
        </div>
    </div>
    @endif
</div>
