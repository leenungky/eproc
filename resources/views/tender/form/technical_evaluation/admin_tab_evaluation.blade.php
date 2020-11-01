<div class="tab-body">
    <div class="has-footer has-tab" style="padding: 0;">
        <div id="card-evaluation" class="card">
            <div class="card-header">
                <div class="card-header-left">
                    <span class="heading-title">{{__('tender.process.tab_title_evaluation')}}</span>
                </div>
            </div>
            <div class="card-body overflow-hidden">
                <div class="">
                    <table id="dt-evaluation-vendor" class="table table-sm table-bordered table-striped table-vcenter">
                        <thead>
                            <tr>
                                @foreach ($tenderData['process_technical_evaluation']['fields3'] as $field)
                                    <th class="{{$field}}">{{__('tender.'.$field)}}</th>
                                @endforeach
                                <th class="status"></th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card card-tender-item" style="margin-top: 20px;">
                <div class="card-header">
                    <div class="card-header-left">
                        <span class="heading-title">{{__('tender.items')}}</span>
                    </div>
                    <div class="card-header-right">
                        <span class="heading-title"><small>{{__('tender.conditional_type')}} : {{__('tender.'.$conditionalType->toArray()[$tender->conditional_type])}}</small></span>
                    </div>
                </div>
                <div class="card-body overflow-hidden">
                    <div class="tender-items">
                        <table id="dt-evaluation-items" class="table table-sm table-bordered table-striped table-vcenter table-ellipsis">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th class="description">{{__('purchaserequisition.description')}}</th>
                                    <th class="number">{{__('purchaserequisition.number')}}</th>
                                    <th class="line_number">{{__('purchaserequisition.line_number')}}</th>
                                    <th class="product_code">{{__('purchaserequisition.product_code')}}</th>
                                    <th class="product_group_code" style="width: 50px;">{{__('purchaserequisition.product_group_code')}}</th>
                                    <th class="vendor">{{__('purchaserequisition.vendor')}}</th>
                                    <th class="description_vendor">{{__('purchaserequisition.description')}}</th>
                                    <th class="qty">{{__('purchaserequisition.qty')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card-body overflow-hiddens" style="margin-top: 20px;">
                    <div class="card-header-left">
                        <h5 class="heading-title">{{__('tender.item_summary')}}</h5>
                    </div>
                    <div class="tender-summary-item">
                        <table id="dt-evaluation-summary" class="table table-sm table-bordered table-striped table-vcenter table-ellipsis">
                            <thead>
                                <tr>
                                    <th class="vendor_code">{{__('tender.vendor_code')}}</th>
                                    <th class="vendor_name">{{__('tender.vendor_name')}}</th>
                                    <th class="compliance">Total {{__('purchaserequisition.qty')}}</th>
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
            <div class="app-footer-right">
                <a id="btn_print" target="_blank" href="" class="btn btn-outline-secondary mr-2"><i class="fa fa-file-excel"></i> TBE {{__('common.print')}}</a>
                <button class="btn btn-outline-secondary btn_evaluate_note">
                    <i class="fa fa-file"></i>&nbsp;&nbsp;&nbsp;{{__('tender.process.btn_evaluation_note')}}</button>
                @if($statusProcess == 'opened-3')
                <button class="btn btn-success ml-2 btn_finish" @if(!$canFinish) disabled @endif>
                    <i class="fa fa-paper-plane"></i>&nbsp;&nbsp;&nbsp;{{__('tender.process.btn_finish')}}</button>
                @endif
            </div>
        </div>
    </div>
</div>
