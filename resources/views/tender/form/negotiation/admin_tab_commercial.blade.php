<div class="tab-body">
    <div class="has-footer has-tab">
        <div class="col-12" style="margin-top: 20px;">
            <div class="card card-submission">
                <div class="card-header">
                    <div class="card-header-left">
                        <span class="heading-title">{{__('tender.process.tab_title_negotiation')}}</span>
                    </div>
                </div>
                <div class="card-body overflow-hidden">
                    <div class="">
                        <table id="dt-negotiation-vendor" class="table table-sm table-bordered table-striped table-vcenter">
                            <thead>
                                <tr>
                                    @foreach ($tenderData['negotiation']['fields2'] as $field)
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

            <div class="card card-tender-item" style="margin-top: 20px;">
                <div class="card-header">
                    <div class="card-header-left">
                        <span class="heading-title">{{__('tender.items')}}</span>
                    </div>
                    <div class="card-header-right">
                        <span class="heading-title"><small>{{__('tender.conditional_type')}} :
                                {{__('tender.'.$conditionalType->toArray()[$tender->conditional_type])}}</small></span>
                    </div>
                </div>
                <div class="card-body overflow-hidden">
                    <div class="tender-items">
                        <table id="dt-negotiation-items" class="table table-sm table-bordered table-striped table-vcenter table-ellipsis">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th class="description">{{__('purchaserequisition.description')}}</th>
                                    <th class="number">{{__('purchaserequisition.number')}}</th>
                                    <th class="line_number">{{__('purchaserequisition.line_number')}}</th>
                                    <th class="product_code">{{__('purchaserequisition.product_code')}}</th>
                                    <th class="product_group_code">{{__('purchaserequisition.product_group_code')}}</th>
                                    <th class="vendor">{{__('purchaserequisition.vendor')}}</th>
                                    <th class="number">Version</th>
                                    <th class="description_vendor">{{__('purchaserequisition.description')}}</th>
                                    <th class="qty">{{__('purchaserequisition.qty')}}</th>
                                    <th class="price_unit">{{__('purchaserequisition.price_unit')}}</th>
                                    <th class="price_unit">{{__('purchaserequisition.est_unit_price')}}</th>
                                    <th class="overall_limit">{{__('purchaserequisition.overall_limit')}}</th>
                                    <th class="subtotal">{{__('purchaserequisition.additional_cost')}}</th>
                                    <th class="compliance">Total {{__('purchaserequisition.est_unit_price')}}</th>
                                    <th class="compliance">Total {{__('purchaserequisition.overall_limit')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card-body overflow-hidden" style="margin-top: 20px;">
                    <div class="card-header-left">
                        <h5 class="heading-title">{{__('tender.item_summary')}}</h5>
                    </div>
                    <div class="tender-summary-item">
                        <table id="dt-negotiation-summary" class="table table-sm table-bordered table-striped table-vcenter table-ellipsis">
                            <thead>
                                <tr>
                                    <th class="vendor_code">{{__('tender.vendor_code')}}</th>
                                    <th class="vendor_name">{{__('tender.vendor_name')}}</th>
                                    <th class="number">Version</th>
                                    <th class="compliance">Total {{__('purchaserequisition.additional_cost')}}</th>
                                    <th class="subtotal">Total {{__('purchaserequisition.est_unit_price')}}</th>
                                    <th class="subtotal">Total {{__('purchaserequisition.overall_limit')}}</th>
                                    <th class="number">{{__('purchaserequisition.currency_code')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
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
                <button class="btn btn_back_to btn-link mr-2"><i class="fa fa-arrow-left"></i> {{__('tender.process.btn_back_vendor')}}</button>
                <button class="btn btn_log btn-link"><i class="fa fa-history"></i> {{__('tender.process.btn_submission_log')}}</button>
            </div>
            <div class="app-footer-right button-detail" style="display: none;">
                <button class="btn btn_scoring btn-warning mr-2">
                    <i class="fa fa-balance-scale"></i>&nbsp;&nbsp;&nbsp; {{__('common.scoring')}}
                </button>
                <button class="btn btn_comment btn-outline-secondary mr-2">
                    <i class="fa fa-comments"></i> {{__('common.comment')}} <span>(0)</span>
                </button>
            </div>
            <div class="app-footer-right button-header">
                @include('tender.form.negotiation.admin_button_commercial')
            </div>
        </div>
    </div>
</div>
