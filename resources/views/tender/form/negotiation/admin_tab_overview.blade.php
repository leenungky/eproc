<div class="tab-body">
    <div class="has-footer has-tab" style="padding: 0">
        <div class="card card-submission">
            <div class="card-body overflow-hidden">
                <div class="">
                    <table id="dt-vendor-negotiation" class="table table-sm table-bordered table-striped table-vcenter"
                        style="width:100%">
                        <thead>
                            <tr>
                                @foreach ($tenderData['negotiation']['fields'] as $field)
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
        <div class="card-submission-detail" style="display: none;">
            <div class="card card-tender-header" style="padding-top: 20px;">
                <div class="card-body">
                    <div class="frmTenderHeader col-12 form-view">
                        <input name="currency_code_header" id="currency_code_header" type="hidden" />
                        <div class="row" style="overflow: hidden;">
                            <div class="col-5">
                                <dl class="row">
                                    <dt class="col-sm-7 text-right">{{__('tender.process.fields.quotation_number')}} :
                                    </dt>
                                    <dd class="col-sm-5 quotation_number">-</dd>

                                    <dt class="col-sm-7 text-right">{{__('tender.process.fields.quotation_date')}} :
                                    </dt>
                                    <dd class="col-sm-5 quotation_date">-</dd>

                                    <dt class="col-sm-7 text-right">{{__('tender.process.fields.quotation_end_date')}} :
                                    </dt>
                                    <dd class="col-sm-5 quotation_end_date">{{$negotiation['quo_validity_date'] ?? '-'}}
                                    </dd>

                                    <dt class="col-sm-7 text-right">{{__('tender.incoterm')}} :</dt>
                                    <dd class="col-sm-5 incoterm">-</dd>

                                    <dt class="col-sm-7 text-right">{{__('tender.process.fields.incoterm_location')}} :
                                    </dt>
                                    <dd class="col-sm-5 incoterm_location">-</dd>

                                    <dt class="col-sm-7 text-right">{{__('tender.process.fields.quotation_note')}} :
                                    </dt>
                                    <dd class="col-sm-5 quotation_note">-</dd>
                                </dl>
                            </div>
                            <div class="col-7">
                                <dl class="row">
                                    <dt class="col-sm-5 text-right">{{__('tender.process.fields.quotation_file')}} :
                                    </dt>
                                    <dd class="col-sm-7">
                                        <a class="quotation_file text-nowrap" href="" target="_blank"></a>
                                    </dd>

                                    @if($tender->bid_bond == 1)
                                    <dt class="col-sm-5 text-right">{{__('tender.process.fields.bid_bond_value')}} :</dt>
                                    <dd class="col-sm-7 bid_bond_value">-</dd>

                                    <dt class="col-sm-5 text-right">{{__('tender.process.fields.bid_bond_end_date')}} :
                                    </dt>
                                    <dd class="col-sm-7 bid_bond_end_date">-</dd>

                                    <dt class="col-sm-5 text-right">{{__('tender.process.fields.bid_bond_file')}} :</dt>
                                    <dd class="col-sm-7">
                                        <a class="bid_bond_file text-nowrap" href="" target="_blank"></a>
                                    </dd>
                                    @endif
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card card-tender-item" style="margin-top: 20px;">
                <div class="card-header">
                    <div class="card-header-left">
                        <span class="heading-title">{{__('tender.items')}}</span>
                    </div>
                    <div class="card-header-right" hiden>
                        @if($tender->conditional_type == 'CT1')
                        <button id="btn_additional_cost" class="btn btn-sm btn-outline-success mr-2" data-toggle="modal"
                            data-target="#formAddcost_modal" data-backdrop="static" data-keyboard="false">
                            <i class="fas fa-plus mr-2" aria-hidden="true"></i>{{__('tender.item_cost.title')}}</button>
                        @endif
                        {{-- <div class="text-right" style="width: 75px;">
                            <input class="form-control form-control-sm currency_code text-right" disabled />
                        </div> --}}
                    </div>
                </div>
                <div class="card-body">
                    <div class="">
                        <table id="dt-commercial-items"
                            class="table table-sm table-bordered table-striped table-vcenter table-wrap dataTable no-footer">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th class="number">{{__('purchaserequisition.number')}}</th>
                                    <th class="line_number">{{__('purchaserequisition.line_number')}}</th>
                                    <th class="description">{{__('purchaserequisition.description')}}</th>
                                    <th class="qty">{{__('purchaserequisition.qty')}}</th>
                                    <th class="uom">{{__('purchaserequisition.uom')}}</th>
                                    <th class="est_unit_price">{{__('purchaserequisition.est_unit_price')}}</th>
                                    <th class="overall_limit">{{__('purchaserequisition.overall_limit')}}</th>
                                    <th class="price_unit">{{__('purchaserequisition.price_unit')}}</th>
                                    <th class="subtotal">{{__('purchaserequisition.subtotal')}}</th>
                                    <th class="currency_code">{{__('purchaserequisition.currency_code')}}</th>
                                    <th class="compliance">{{__('purchaserequisition.compliance')}}</th>
                                    {{-- <th class="deleteflg">{{__('purchaserequisition.deleteflg')}}</th> --}}
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card card-tender-document" style="margin-top: 20px;">
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
                        <table id="dt-commercial-document"
                            class="table table-sm table-bordered table-striped table-vcenter dt-bid-doc-requirement"
                            style="width:100%">
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
    </div>

    <div class="app-footer">
        <div class="app-footer__inner">
            <div class="app-footer-left page-number">
                <div class="page_numbers" style="display:inherit"></div>
            </div>
            <div class="app-footer-left button-detail" style="display: none;">
                <button class="btn btn_back_to btn-link mr-2"><i class="fa fa-arrow-left"></i>
                    {{__('tender.process.btn_back_vendor')}}</button>
                <button class="btn btn_log btn-link"><i class="fa fa-history"></i>
                    {{__('tender.process.btn_submission_log')}}</button>
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
