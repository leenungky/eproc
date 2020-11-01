<div class="tab-body">
    <div class="has-footer has-tab" style="padding: 0">
        <div id="card-submission" class="card">
            <div class="card-header">
                <div class="card-header-left">
                    <span class="heading-title">{{__('tender.process.bid_opening')}}</span>
                </div>
            </div>
            <div class="card-body overflow-hidden">
                <div class="">
                    <table id="dt-document-vendor" class="table table-sm table-bordered table-striped table-vcenter">
                        <thead>
                            <tr>
                                @foreach ($tenderData['process_prequalification']['fields2'] as $field)
                                    <th class="{{$field}}">{{__('tender.'.$field)}}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div id="card-submission-detail" class="card" style="display: none;">
            <div class="card-header">
                <div class="card-header-left">
                    <span class="heading-title">{{__('tender.bidding_document_requirements')}}</span>
                </div>
                <div class="card-header-right">
                    <small class="vendor-title">{{__('tender.bidding_document_requirements')}}</small>
                </div>
            </div>
            <div class="card-body overflow-hidden">
                <div class="">
                    <table id="dt-bid-doc-requirement" class="table table-sm table-bordered table-striped table-vcenter" style="width:100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th class="description">{{__('tender.bidding.fields.description')}}</th>
                                <th class="is_required">{{__('tender.bidding.fields.is_required')}}</th>
                                <th class="document">{{__('tender.document')}}</th>
                                <th class="attachment">{{__('tender.process.tab_title_evaluation')}}</th>
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
        <div class="app-footer__inner">
            <div class="app-footer-left page-number">
                <div class="page_numbers" style="display:inherit"></div>
            </div>
            <div class="app-footer-left button-detail" style="display: none;">
                <button id="btn_back_to" class="btn btn-link mr-2"><i class="fa fa-arrow-left"></i> {{__('tender.process.btn_back_vendor')}}</button>
                <button id="btn_log" class="btn btn-link"><i class="fa fa-history"></i> {{__('tender.process.btn_submission_log')}}</button>
            </div>
            <div class="app-footer-right button-detail" style="display: none;">
                <button id="btn_scoring" class="btn btn-warning mr-2">
                    <i class="fa fa-balance-scale"></i>&nbsp;&nbsp;&nbsp; {{__('common.scoring')}}
                </button>
                <button id="btn_comment" class="btn btn-outline-secondary mr-2">
                    <i class="fa fa-comments"></i> {{__('common.comment')}} <span>(0)</span>
                </button>
            </div>
            <div class="app-footer-right button-header">
                @include('tender.form.prequalification.admin_button')
            </div>
        </div>
    </div>
</div>
