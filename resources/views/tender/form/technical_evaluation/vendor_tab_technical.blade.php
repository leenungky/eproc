<div class="tab-body">
    @if(!$technical['hasDocument'])
    <div id="card-schedule" class="card">
        <div class="card-body card-schedule" style="padding: 10px;">
            <button class="btn btn_new_doc btn-success" @if(!$isRegistered) disabled @endif>
                <i class="fa fa-plus"></i>&nbsp;&nbsp;&nbsp;{{__('tender.process.btn_new_tc')}}</button>
        </div>
    </div>
    @else
    <div class="has-footer has-tab" style="padding: 0;">
        {{-- <div class="alert alert-info alert-flat no-margin" role="alert">
            <p>{{__('tender.process.info_draft')}}</p>
        </div> --}}

        <div class="card card-tender-header" style="padding-top: 20px;">
            <div class="card-body">
                <div class="frmTenderHeader col-12 needs-validation">
                    <div class="row">
                        <div class="col-5">
                            <div class="form-group row mb-2">
                                <label for="quotation_number" class="col-6 col-form-label text-right">{{__('tender.process.fields.quotation_number')}}</label>
                                <div class="col-6">
                                    <input name="id" type="hidden" value="{{$technical['header']->id ?? ''}}" />
                                    <input name="vendor_id" type="hidden" value="{{$technical['header']->vendor_id ?? $vendor->id}}" />
                                    <input name="vendor_code" type="hidden" value="{{$technical['header']->vendor_code ?? $vendor->vendor_code}}" />
                                    <input name="status" type="hidden" value="{{$technical['header']->status ?? ''}}" />
                                    <input name="quotation_number" placeholder="{{__('tender.process.fields.quotation_number')}}" type="text"
                                        @if(!$enabledTc) disabled @endif required
                                        required="required" class="form-control form-control-sm" value="{{$technical['header']->quotation_number ?? ''}}" />
                                </div>
                            </div>
                            <div class="form-group row mb-2">
                                <label for="quotation_date" class="col-6 col-form-label text-right">{{__('tender.process.fields.quotation_date')}}</label>
                                <div class="col-6">
                                    <input name="quotation_date" type="text" readonly required
                                        class="form-control form-control-sm" @if(!$enabledTc) disabled @endif
                                        value="{{$technical['header']->quotation_date ?? $date_now}}" />
                                </div>
                            </div>

                            <div class="form-group row mb-2">
                                <label for="quotation_end_date" class="col-6 col-form-label text-right">{{__('tender.process.fields.quotation_end_date')}}</label>
                                <div class="col-6">
                                    <input name="quotation_end_date" type="text" readonly
                                        required="required" class="form-control form-control-sm" value="{{$technical['quo_validity_date']}}" />
                                </div>
                            </div>
                            <div class="form-group row mb-2">
                                <label for="quotation_note" class="col-6 col-form-label text-right">{{__('tender.process.fields.quotation_note')}}</label>
                                <div class="col-6">
                                    <textarea name="quotation_note" maxlength="40" class="form-control form-control-sm" @if(!$enabledTc) disabled @endif
                                        rows="3">{{$technical['header']->quotation_note ?? ''}}</textarea>
                                </div>
                            </div>
                            @if($tender->tkdn_option == 1)
                            <div class="form-group row mb-2">
                                <label for="tkdn_percentage" class="col-6 col-form-label text-right">{{__('tender.process.fields.tkdn_percentage')}}</label>
                                <div class="col-6">
                                    <input name="tkdn_percentage" type="text" maxlength="3" min="0" max="100" class="form-control form-control-sm" @if(!$enabledTc) disabled @endif
                                        value="{{$technical['header']->tkdn_percentage ?? ''}}" required step=".01"/>
                                </div>
                            </div>
                            @endif
                        </div>

                        <div class="col-6">
                            <div class="form-group mb-2">
                                <label for="quotation_file" class="col-form-label text-left">{{__('tender.process.fields.quotation_file')}}</label>
                                <div class="view file-bordered text-left" @if($enabledTc && empty($technical['header']->quotation_file)) hidden @endif>
                                    &nbsp;
                                    <a href="" class="float-right delete-h-file" @if(!$enabledTc) hidden @endif><i class="fa fa-edit"></i></a>
                                    <a class="text-nowrap" target="_blank" href="{{$storage.'/'.$technical['header']->quotation_file}}">
                                        {{\App\Helpers\App::baseName($technical['header']->quotation_file)}}
                                    </a>
                                </div>
                                <div class="edit" @if(!$enabledTc || !empty($technical['header']->quotation_file)) hidden @endif>
                                    <input type="file" name="quotation_file" class="form-control form-control-sm attachment" />
                                </div>
                            </div>

                            @if($tender->tkdn_option == 1)
                            <div class="form-group mb-2">
                                <label for="tkdn_file" class="col-form-label text-left">{{__('tender.process.fields.tkdn_file', ['type' => __('tender.'.strtolower($tender->tkdn))])}}</label>
                                <div class="view file-bordered text-left" @if($enabledTc && empty($technical['header']->tkdn_file)) hidden @endif>
                                    &nbsp;
                                    <a href="" class="float-right delete-h-file" @if(!$enabledTc) hidden @endif><i class="fa fa-edit"></i></a>
                                    <a class="text-nowrap" target="_blank" href="{{$storage.'/'.$technical['header']->tkdn_file}}">
                                        {{\App\Helpers\App::baseName($technical['header']->tkdn_file)}}
                                    </a>
                                </div>
                                <div class="edit" @if(!$enabledTc || !empty($technical['header']->tkdn_file)) hidden @endif>
                                    <input type="file" name="tkdn_file" class="form-control form-control-sm attachment" @if($enabledTc && empty($technical['header']->tkdn_file)) required @endif/>
                                </div>
                            </div>
                            @endif

                            {{-- @if($workflowValues[$actIndex] == $statusEnums[3]) --}}
                            @if($hasResubmission)
                            <div class="form-group mb-2">
                                <label for="proposed_item_file" class="col-form-label text-left">{{__('tender.process.fields.proposed_items')}}</label>
                                <div class="view file-bordered text-left" @if($enabledTc && empty($technical['header']->proposed_item_file)) hidden @endif>
                                    &nbsp;
                                    <a href="" class="float-right delete-h-file" @if(!$enabledTc) hidden @endif><i class="fa fa-edit"></i></a>
                                    <a class="text-nowrap" target="_blank" href="{{$storage.'/'.$technical['header']->proposed_item_file}}">
                                        {{\App\Helpers\App::baseName($technical['header']->proposed_item_file)}}
                                    </a>
                                </div>
                                <div class="edit" @if(!$enabledTc || !empty($technical['header']->proposed_item_file)) hidden @endif>
                                    <input type="file" name="proposed_item_file" class="form-control form-control-sm attachment" />
                                </div>
                            </div>
                            @endif
                        </div>

                        <div class="col-6">
                            <div class="form-group row mb-2">
                                <label for="btn-save-header" class="col-5 col-form-label text-right">&nbsp;</label>
                                <div class="col-7">
                                    @if($enabledTc)
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
            <div class="card-header">
                <div class="card-header-left">
                    <span class="heading-title">{{__('tender.items')}}</span>
                </div>
                <div class="card-header-right">
                    <a id="btn_item_detail" class="btn btn-sm btn-outline-secondary mr-2"
                        href="{{ route('tender.show', ['id' => $id, 'type' => $type, 'action' => 'detail-specification']) }}">
                        <i class="fas fa-plus mr-2" aria-hidden="true"></i>{{__('tender.item_specification.title')}}
                    </a>
                    @if($enabledTc)
                    <button class="btn btn-save-items btn-success" type="submit" disabled>
                        <i class="fa fa-save"></i> {{__('tender.save')}}</button>
                    @endif
                </div>
            </div>
            <div class="card-body card-schedule">
                <div class="">
                    <table id="dt-tc-items" class="table table-sm table-bordered table-striped table-vcenter">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th class="number">{{__('purchaserequisition.number')}}</th>
                                <th class="line_number">{{__('purchaserequisition.line_number')}}</th>
                                <th class="description">{{__('purchaserequisition.description')}}</th>
                                <th class="qty">{{__('purchaserequisition.qty')}}</th>
                                <th class="uom">{{__('purchaserequisition.uom')}}</th>
                                <th class="price_unit">{{__('purchaserequisition.price_unit')}}</th>
                                <th class="is_required">{{__('purchaserequisition.subtotal')}}</th>
                                <th>{{__('tender.process.compliance_label')}}</th>
                                <th class="deleteflg">{{__('purchaserequisition.deleteflg')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                <div class="card-header-right">
                    @if($enabledTc)
                    <button class="btn btn-save-items btn-success" type="submit" disabled>
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
                    <table id="dt-tc-document" class="table table-sm table-bordered table-striped table-vcenter">
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
                @if($statusProcess != 'registration-' && $tender->visibility_bid_document == 'PUBLIC')
                <button class="btn btn_log btn-link">
                    <i class="fa fa-history"></i> {{__('tender.process.btn_submission_log')}}</button>
                @endif
            </div>
            <div class="app-footer-right">
                <button class="btn btn_comment btn-outline-secondary mr-2">
                    <i class="fa fa-comments"></i> {{__('common.comment')}} <span>(0)</span>
                </button>
                @if(empty($statusProcess) && $next != $type)
                    <button class="btn btn-primary btn_next_flow">
                        {{__('tender.next')}} <i class="fa fa-arrow-right"></i></button>
                @elseif(!empty($statusProcess))
                    <button class="btn btn_delete_draft btn-secondary mr-3" @if(!$enabledTc) disabled @endif >
                        <i class="fa fa-trash"></i> {{__('tender.process.btn_delete_draft')}}</button>
                    @if(isset($workflowValues[$actIndex]) && $workflowValues[$actIndex] == 'request_resubmission')
                        <button class="btn btn_resubmit btn-success" @if(!$enabledTc) disabled @endif>
                            <i class="fa fa-paper-plane"></i>&nbsp;&nbsp;&nbsp;{{__('common.resubmit')}}</button>
                    @else
                        <button class="btn btn_submit btn-success" @if(!$enabledTc) disabled @endif>
                            <i class="fa fa-paper-plane"></i>&nbsp;&nbsp;&nbsp;{{__('common.submit')}}</button>
                    @endif
                @endif
            </div>
        </div>
    </div>
    @endif
</div>
