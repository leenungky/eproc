<div class="tab-body">
    <div class="has-footer has-tab" style="padding: 0">
        <div class="alert alert-info alert-flat no-margin" role="alert" @if($tender->visibility_bid_document != 'PUBLIC') hidden @endif>
            <p>{{__('tender.process.info_awarding_null')}}</p>
        </div>
    </div>
    <div class="app-footer">
        <div class="app-footer__inner">
            <div class="app-footer-left"></div>
            <div class="app-footer-right">
                @if($statusProcess == "" && $next != $type)
                <button class="btn btn-primary btn_next_flow">
                    {{__('tender.next')}} <i class="fa fa-arrow-right"></i></button>
                @endif
            </div>
        </div>
    </div>
</div>
