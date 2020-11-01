<div class="tab-body">
    @if(!$negotiation['hasDocument'])
    <div id="card-schedule" class="card">
        <div class="card-body card-schedule" style="padding: 10px;">
            <button class="btn btn_new_doc btn-success"
                @if(!$enabledNegotiation) disabled @endif>
                <i class="fa fa-plus"></i>&nbsp;&nbsp;&nbsp;{{__('tender.process.btn_new_nc')}}</button>
        </div>
    </div>
    @else
    <div class="has-footer has-tab" style="padding: 0">
        <div class="card card-tender-header" style="padding-top: 20px;">
            <div class="card-body">
                <div class="frmTenderHeader form col-12 needs-validation">
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group row mb-2">
                                <label for="quotation_number" class="col-5 col-form-label text-right">{{__('tender.process.fields.quotation_number')}}</label>
                                <div class="col-7">
                                    <input name="id" type="hidden" value="{{$negotiation['header']->id ?? ''}}" />
                                    <input name="vendor_id" type="hidden" value="{{$negotiation['header']->vendor_id ?? $vendor->id}}" />
                                    <input name="vendor_code" type="hidden" value="{{$negotiation['header']->vendor_code ?? $vendor->vendor_code}}" />
                                    <input name="status" type="hidden" value="{{$negotiation['header']->status ?? ''}}" />
                                    <input name="currency_code_header" id="currency_code_header" type="hidden" value="{{$negotiation['header']->currency_code ?? ''}}" />
                                    <input name="quotation_number" placeholder="{{__('tender.process.fields.quotation_number')}}" type="text"
                                        @if(!$enabledNegotiation) disabled @endif required="required"
                                        class="form-control form-control-sm" value="{{$negotiation['header']->quotation_number ?? ''}}" />
                                </div>
                            </div>
                            <div class="form-group row mb-2">
                                <label for="quotation_date" class="col-5 col-form-label text-right">{{__('tender.process.fields.quotation_date')}}</label>
                                <div class="col-7">
                                    <input name="quotation_date" type="text" readonly required="required"
                                        class="form-control form-control-sm" @if(!$enabledNegotiation) disabled @endif
                                        value="{{$negotiation['header']->quotation_date ?? $date_now}}" />
                                </div>
                            </div>

                            <div class="form-group row mb-2">
                                <label for="quotation_end_date" class="col-5 col-form-label text-right">{{__('tender.process.fields.quotation_end_date')}}</label>
                                <div class="col-7">
                                    <input name="quotation_end_date" type="text" readonly required="required"
                                        class="form-control form-control-sm" value="{{$negotiation['quo_validity_date']}}" />
                                </div>
                            </div>
                            <div class="form-group row mb-2">
                                <label for="incoterm" class="col-5 col-form-label text-right">{{__('tender.incoterm')}}</label>
                                <div class="col-7">
                                    <select id="incoterm" name="incoterm" class="custom-select custom-select-sm" @if(!$enabledNegotiation) disabled @endif>
                                        @php $vendorIncoterm = $negotiation['header']->incoterm ?? $tender->incoterm @endphp
                                        @foreach ($negotiation['incotermOptions'] as $key=>$value)
                                        <option value="{{$key}}" @if($key==$vendorIncoterm) selected @endif>{{__($value)}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row mb-2">
                                <label for="incoterm_location" class="col-5 col-form-label text-right">{{__('tender.process.fields.incoterm_location')}}</label>
                                <div class="col-7">
                                    <input name="incoterm_location" type="text" class="form-control form-control-sm" @if(!$enabledNegotiation) disabled @endif
                                        value="{{$negotiation['header']->incoterm_location ?? $tender->location}}" />
                                </div>
                            </div>
                            <div class="form-group row mb-2">
                                <label for="quotation_note" class="col-5 col-form-label text-right">{{__('tender.process.fields.quotation_note')}}</label>
                                <div class="col-7">
                                    <textarea name="quotation_note" class="form-control form-control-sm" @if(!$enabledNegotiation) disabled @endif
                                        rows="3">{{$negotiation['header']->quotation_note ?? ''}}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="form-group mb-2">
                                <label for="quotation_file" class="col-form-label text-left">{{__('tender.process.fields.quotation_file')}}</label>
                                <div class="view file-bordered text-left" @if($enabledNegotiation && empty($negotiation['header']->quotation_file)) hidden @endif>
                                    <a href="" class="float-right delete-h-file" @if(!$enabledNegotiation) hidden @endif><i class="fa fa-edit"></i></a>
                                    <a class="text-nowrap" target="_blank" href="{{$storage.'/'.$negotiation['header']->quotation_file}}">
                                        {{\App\Helpers\App::baseName($negotiation['header']->quotation_file)}}
                                    </a>
                                </div>
                                <div class="edit" @if(!$enabledNegotiation || !empty($negotiation['header']->quotation_file)) hidden @endif>
                                    <input type="file" name="quotation_file" class="form-control form-control-sm attachment" />
                                </div>
                            </div>

                            @if($tender->bid_bond == 1)
                            <div class="row">
                                <div class="form-group col-12 mb-2">
                                    <label for="bid_bond_value" class="col-form-label text-left">{{__('tender.process.fields.bid_bond_value')}}</label>
                                    <input name="bid_bond_value" type="text" equired="required"
                                            class="form-control form-control-sm" value="{{$negotiation['header']->bid_bond_value ?? ''}}" />
                                </div>
                                <div class="form-group col-12 mb-2">
                                    <label for="bid_bond_end_date" class="col-form-label text-left">{{__('tender.process.fields.bid_bond_end_date')}}</label>
                                    <input name="bid_bond_end_date" type="text" readonly
                                            required="required" class="form-control form-control-sm" value="{{$negotiation['quo_validity_date']}}" />
                                </div>
                            </div>
                            <div class="form-group mb-2">
                                <label for="bid_bond_file" class="col-form-label text-left">{{__('tender.process.fields.bid_bond_file')}}</label>
                                <div class="view file-bordered text-left" @if($enabledNegotiation && empty($negotiation['header']->bid_bond_file)) hidden @endif>
                                    <a href="" class="float-right delete-h-file" @if(!$enabledNegotiation) hidden @endif><i class="fa fa-edit"></i></a>
                                    <a class="text-nowrap" target="_blank" href="{{$storage.'/'.$negotiation['header']->bid_bond_file}}">
                                        {{\App\Helpers\App::baseName($negotiation['header']->bid_bond_file)}}
                                    </a>
                                </div>
                                <div class="edit" @if(!$enabledNegotiation || !empty($negotiation['header']->bid_bond_file)) hidden @endif>
                                    <input type="file" name="bid_bond_file" class="form-control form-control-sm attachment" />
                                </div>
                            </div>
                            @endif
                        </div>

                        <div class="col-6">
                            <div class="form-group row mb-2">
                                <label for="btn-save-header" class="col-5 col-form-label text-right">&nbsp;</label>
                                <div class="col-7">
                                    @if($enabledNegotiation)
                                    <button class="btn btn-save-header btn-success mr-2" type="submit" disabled>
                                        <i class="fa fa-save"></i> {{__('tender.save')}}</button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-tender-item" style="margin-top: 20px;">
            <div class="card-header custom">
                <div class="card-header-left">
                    <span class="heading-title">{{__('tender.items')}}</span>
                </div>
                <div class="card-header-right" hiden>
                    @if($tender->conditional_type == 'CT1')
                    <button id="btn_additional_cost" class="btn btn-sm btn-outline-success mr-2" data-toggle="modal"
                        data-target="#formAddcost_modal" data-backdrop="static" data-keyboard="false">
                        <i class="fas fa-plus mr-2" aria-hidden="true"></i>{{__('tender.item_cost.title')}}</button>
                    @endif
                    <div>
                        <select name="currency_code" @if(!$enabledNegotiation) disabled @endif class="custom-select custom-select-sm">
                            <option value=""></option>
                            @foreach($negotiation['currencies'] as $k => $val)
                            <option value="{{$k}}"
                                @if(!empty($negotiation['header']) && $negotiation['header']->currency_code == $k)) selected @endif>{{$k}} - {{$val}}</option>
                            @endforeach
                        </select>
                    </div>
                    @if($enabledNegotiation)
                    <button class="btn btn-save-items btn-success ml-2 mr-3" type="submit">
                        <i class="fa fa-save"></i> {{__('tender.save')}}</button>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <div class="">
                    <table id="dt-com-items" class="table table-sm table-bordered table-striped table-vcenter table-ellipsis">
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
            <div class="card-footer">
                <div class="card-header-right">
                    @if($enabledNegotiation)
                    <button class="btn btn-save-items btn-success" type="submit">
                        <i class="fa fa-save"></i> {{__('tender.save')}}</button>
                    @endif
                </div>
            </div>
        </div>

        <div class="card card-tender-document" style="margin-top: 20px;">
            <div class="card-header">
                <div class="card-header-left">
                    <span class="heading-title">{{__('tender.bidding_document_requirements')}}</span>
                </div>
            </div>
            <div class="card-body card-schedule">
                <div class="">
                    <table id="dt-com-document" class="table table-sm table-bordered table-striped table-vcenter">
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
        <div class="app-footer__inner">
            <div class="app-footer-left">
                @if($statusProcess != 'registration' && $tender->visibility_bid_document == 'PUBLIC')
                <button class="btn btn_log btn-link">
                    <i class="fa fa-history"></i> {{__('tender.process.btn_submission_log')}}</button>
                @endif
            </div>
            <div class="app-footer-right">
                <button class="btn btn_comment btn-outline-secondary mr-2">
                    <i class="fa fa-comments"></i> {{__('common.comment')}} <span>(0)</span>
                </button>
                @if(!empty($statusProcess) && $workflowValues[$actIndex] != "finish" && $workflowValues[$actIndex] != "complete")
                    <button class="btn btn_delete_draft btn-secondary mr-2" @if(!$enabledNegotiation) disabled @endif >
                        <i class="fa fa-trash"></i> {{__('tender.process.btn_delete_draft')}}</button>
                    @if($tenderVendor->negotiation_status == 'request_resubmission' || $tenderVendor->negotiation_status == 'resubmitted'|| $tenderVendor->negotiation_status == 'open_resubmission')
                        <button class="btn btn_resubmit btn-success mr-2" @if(!$enabledNegotiation) disabled @endif>
                            <i class="fa fa-paper-plane"></i>&nbsp;&nbsp;&nbsp;{{__('common.resubmit')}}</button>
                    @else
                        <button class="btn btn_submit btn-success mr-2" @if(!$enabledNegotiation) disabled @endif>
                            <i class="fa fa-paper-plane"></i>&nbsp;&nbsp;&nbsp;{{__('common.submit')}}</button>
                    @endif
                @endif
                @if($next != $type)
                    <button class="btn btn-primary btn_next_flow">{{__('tender.next')}} <i class="fa fa-arrow-right"></i></button>
                @endif
            </div>
        </div>
    </div>
    @endif
</div>
