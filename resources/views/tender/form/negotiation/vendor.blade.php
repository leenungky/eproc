@php
$enabledNegotiation = false;
$arrStatus = \App\Models\TenderVendorSubmission::STATUS;
$negotiationTabEnable = !empty($tenderVendor->negotiation_status);

if(($tenderVendor->negotiation_status == "start"
|| $tenderVendor->negotiation_status == "request_resubmission"
|| $tenderVendor->negotiation_status == "submitted"
|| $tenderVendor->negotiation_status == "resubmitted")
&&(empty($negotiation['submissionData']) || in_array($negotiation['submissionData']->status, $arrStatus)))
{
$enabledNegotiation = $editableItem;
}

@endphp

@section('contentbody')
<div class="tender-content">
    <ul class="nav nav-tabs" id="negotiation-tab" role="tablist">
        <li class="nav-item" id="overview-li">
            <a class="nav-link active" id="overview-tab" data-toggle="tab" href="#overview-content" role="tab"
                aria-controls="overview" aria-selected="true">{{__('tender.process.tab_title_overview')}}</a>
        </li>
        <li class="nav-item" id="negotiation-li">
            <a class="nav-link @if(!$negotiationTabEnable) disabled @endif" id="negotiation-tab" data-toggle="tab"
                href="#negotiation-content" role="tab" aria-controls="negotiation"
                aria-selected="false">{{__('tender.process.tab_title_negotiation')}}</a>
        </li>
    </ul>
    <div class="tab-content" id="tab-negotiation">
        <div class="tab-pane fade active show" id="overview-content" role="tabpanel" aria-labelledby="overview-tab">
            @include('tender.form.negotiation.vendor_tab_overview')
        </div>
        <div class="tab-pane fade" id="negotiation-content" role="tabpanel" aria-labelledby="negotiation-tab">
            @include('tender.form.negotiation.vendor_tab_commercial')
        </div>
    </div>
</div>
@endsection

@section('modules-scripts')
@parent
@include('tender.form.tender_process_vendor')
<script type="text/javascript">
    @if($enabledNegotiation)
        var enabledNegotiation = true;
    @else
        var enabledNegotiation = false;
    @endif

    require(['datetimepicker',"bootstrap-fileinput-fas",'autonumeric'], function(datetimepicker){
    var NegotiationCommercialType = 6;
    Tabs = $('#navigation-tab li > a.nav-link');

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $(document).ajaxStart(function() {
        Loading.Show();
    });
    $(document).ajaxComplete(function() {
        Loading.Hide();
    });

    initLoad();

    let arrFieldNumber = ['est_unit_price_vendor','overall_limit_vendor','est_unit_price','overall_limit','expected_limit'
                ,'price_unit','price_unit_vendor','qty_ordered'];
    var TabNegotiationComm = new TabDocument({
        tabSelector : '#negotiation-content',
        stageType : NegotiationCommercialType,
        dtDocSelector : '#dt-com-document',
        dtItemSelector : '#dt-com-items',
        openItemDetailRow : function(SELF, fields, dtrow){
            var canEdit = enabledNegotiation && (dtrow.deleteflg || "").trim().toLowerCase() != 'x';

            if(dtrow.compliance == 'deviate' && canEdit){
                for(let ix in fields){
                    switch(fields[ix]){
                        case 'qty':
                            $('#formItemDetail_modal #pr-item #' + fields[ix]).html(formatQty(dtrow.qty_vendor));
                            break;
                        case 'description':
                            $('#formItemDetail_modal #pr-item #' + fields[ix]).html(dtrow.description_vendor);
                            break;
                        case 'price_unit':
                            $('#formItemDetail_modal #pr-item #' + fields[ix])
                            .html('<input type="hidden" name="'+fields[ix]+'" class="form-control form-control-sm" value="'+dtrow.price_unit_vendor+'" />'+formatDecimal(dtrow.price_unit_vendor, dtrow.currency_code_vendor));
                            break;
                        case 'est_unit_price':
                            // let est_unit_price = formatNumberByCurrency(dtrow.est_unit_price_vendor, dtrow.currency_code_vendor);
                            let est_unit_price = dtrow.est_unit_price_vendor;
                            if(dtrow.item_category == 0){
                                $('#formItemDetail_modal #pr-item #' + fields[ix])
                                .html('<input type="number" name="'+fields[ix]+'" class="form-control form-control-sm" value="'+est_unit_price+'" />');
                            }else{
                                // $('#formItemDetail_modal #pr-item #' + PRListField[ix]).html(dtrow.est_unit_price_vendor);
                                $('#formItemDetail_modal #pr-item #' + fields[ix])
                                .html('<input type="number" name="'+fields[ix]+'" class="form-control form-control-sm" readonly value="'+est_unit_price+'" />');
                            }
                            break;
                        case 'overall_limit':
                            // let overall_limit = formatNumberByCurrency(dtrow.overall_limit_vendor, dtrow.currency_code_vendor);
                            let overall_limit = dtrow.overall_limit_vendor;
                            if(dtrow.item_category == 0){
                                // $('#formItemDetail_modal #pr-item #' + PRListField[ix]).html(dtrow.overall_limit_vendor);
                                $('#formItemDetail_modal #pr-item #' + fields[ix])
                                .html('<input type="number" name="'+fields[ix]+'" class="form-control form-control-sm" readonly value="'+overall_limit+'" />');
                            }else{
                                $('#formItemDetail_modal #pr-item #' + fields[ix])
                                .html('<input type="number" name="'+fields[ix]+'" class="form-control form-control-sm" value="'+overall_limit+'" />');
                            }

                            break;
                        case 'currency_code':
                            $('#formItemDetail_modal #pr-item #' + fields[ix]).html(dtrow['currency_code_vendor']);
                            break;
                        default:
                            let fieldValue = dtrow[fields[ix]];
                            if(arrFieldNumber.includes(fields[ix])){
                                console.log(fields[ix]);
                                fieldValue = formatDecimal(dtrow[fields[ix]], dtrow.currency_code_vendor);
                            }
                            $('#formItemDetail_modal #pr-item #' + fields[ix]).html(fieldValue);
                            break;
                    }
                }
                $('#formItemDetail_modal #tax-item .form-area').prop('hidden', false);
                $('#formItemDetail_modal #cost-item .form-area').prop('hidden', false);
            }else{
                for(let ix in fields){
                    switch(fields[ix]){
                        case 'qty':
                            $('#formItemDetail_modal #pr-item #' + fields[ix]).html(formatQty(dtrow.qty_vendor));
                            break;
                        case 'description':
                            $('#formItemDetail_modal #pr-item #' + fields[ix]).html(dtrow.description_vendor);
                            break;
                        case 'price_unit':
                            $('#formItemDetail_modal #pr-item #' + fields[ix]).html(formatDecimal(dtrow.price_unit_vendor, dtrow.currency_code_vendor));
                            break;
                        case 'est_unit_price':
                            let est_unit_price = formatDecimal(dtrow.est_unit_price_vendor, dtrow.currency_code_vendor);
                            html = est_unit_price;
                            if(enabledNegotiation && dtrow.item_category == 0){
                                html = '<input name="'+fields[ix]+'" class="form-control form-control-sm" type="number" readonly value="'+dtrow.est_unit_price_vendor+'" />';
                            }
                            $('#formItemDetail_modal #pr-item #' + fields[ix]).html(html);
                            break;
                        case 'overall_limit':
                            let overall_limit = formatDecimal(dtrow.overall_limit_vendor, dtrow.currency_code_vendor);
                            html = dtrow.overall_limit_vendor;
                            if(enabledNegotiation && dtrow.item_category != 0){
                                html = '<input name="'+fields[ix]+'" class="form-control form-control-sm" type="number" readonly value="'+dtrow.overall_limit_vendor+'" />';
                            }
                            $('#formItemDetail_modal #pr-item #' + fields[ix]).html(html);
                            break;
                        default:
                            let fieldValue = dtrow[fields[ix]];
                            if(arrFieldNumber.includes(fields[ix])){
                                fieldValue = formatDecimal(dtrow[fields[ix]], dtrow.currency_code_vendor);
                            }
                            $('#formItemDetail_modal #pr-item #' + fields[ix]).html(fieldValue);
                            break;
                    }
                }
                $('#formItemDetail_modal #tax-item .form-area').prop('hidden', true);
                $('#formItemDetail_modal #cost-item .form-area').prop('hidden', true);
            }
            $('#formItemDetail_modal textarea[name="item_text"]').prop('disabled', true);
            $('#formItemDetail_modal #pr-item select[name="compliance"]').val(dtrow.compliance);
            $('#formItemDetail_modal #pr-item select[name="compliance"] option[value="comply"]').hide();
            $('#formItemDetail_modal #pr-item select[name="compliance"]').prop('disabled', true);

            if(canEdit){
                $('#formItemDetail_modal #formItemDetail-save').show();
                $('#formItemDetail_modal #formItemDetail-save').prop("disabled", false);
            }
            else{
                $('#formItemDetail_modal #formItemDetail-save').hide();
                $('#formItemDetail_modal #formItemDetail-save').prop("disabled", true);
            }

            ItemDetailPage.TaxTable.table.column(0).visible(canEdit);
            ItemDetailPage.CostTable.table.column(0).visible(canEdit);

            initInputDecimal(dtrow.currency_code_vendor);
            initInputQty();
            initInputPercentage();
        },
        initTableItem : function(SELF){
            let dtOptions = getDTOptions();
            let _url = "{{ route('tender.dataItem', ['id' => $id, 'type' => $type]) }}?stage_type="+SELF.stageType;
            let options = {
                deferRender: dtOptions.deferRender,
                rowId: dtOptions.rowId,
                lengthChange: false,
                searching: false,
                paging: false,
                language: dtOptions.language,
                ajax : _url + '&action_type=submission-items',
                scrollX : true,
                scrollCollapse : true,
                fixedColumns: true,
                initComplete : function(settings, json) {
                    if(json.data && json.data.length > 0){
                        if($(SELF.tabSelector + ' select[name="currency_code"]').val() == ""){
                            $(SELF.tabSelector + ' select[name="currency_code"]').val(json.data[0].currency_code);
                            initInputDecimal(json.data[0].currency_code);
                        }else{
                            initInputDecimal(json.data[0].currency_code_vendor);
                        }
                    }
                    initInputQty();
                    initInputPercentage();
                },
                drawCallback: function(settings){
                    if(settings.aoData[0] && settings.aoData[0]._aData){
                        if($(SELF.tabSelector + ' select[name="currency_code"]').val() == ""){
                            $(SELF.tabSelector + ' select[name="currency_code"]').val(settings.aoData[0]._aData.currency_code);
                            initInputDecimal(settings.aoData[0]._aData.currency_code);
                        }else{
                            initInputDecimal(settings.aoData[0]._aData.currency_code_vendor);
                        }
                    }
                    initInputQty();
                    initInputPercentage();
                },
                columns: [
                    {
                        data: 'id', name: 'id',"width": 15,"className": 'text-center',
                        render: function (data, type, row, meta) {
                            return parseInt(meta.row) + parseInt(meta.settings._iDisplayStart) + 1;
                        }
                    },
                    {data: 'number', name: 'number',"width": 25},
                    {
                        data: 'line_number', name: 'line_number', "width": 50,
                        render : function ( data, type, row, dt ) {
                            return '<a href="" class="open-detail" >'+data+'</a>';
                        },
                    },
                    {
                        data: 'description_vendor', name: 'description_vendor', "width": 300,
                    },
                    {
                        data: 'qty_vendor', name: 'qty_vendor', "width": 20,
                        render: function (data, type, row, meta) {
                            return formatQty(data);
                        }
                    },
                    {data: 'uom', name: 'uom', "width": 20},
                    {
                        data: 'est_unit_price_vendor', name: 'est_unit_price_vendor', "width": 150,
                        render: function ( data, type, row ) {
                            let _tpl = formatCurrency(data, row.currency_code_vendor);
                            if(enabledNegotiation && (row.deleteflg || "").trim().toLowerCase() != 'x'){
                                let readonly = 'readonly';

                                if(row.compliance == 'deviate' && parseInt(row.item_category) == 0)
                                    readonly = '';

                                _tpl = '<input name="est_unit_price" type="number" class="form-control form-control-sm" '+readonly+' value="'+data+'" />';
                            }

                            return _tpl;
                        },
                    },
                    {
                        data: 'overall_limit_vendor', name: 'overall_limit_vendor', "width": 150,
                        render: function ( data, type, row ) {
                            let _tpl = formatCurrency(data, row.currency_code_vendor);
                            if(enabledNegotiation && (row.deleteflg || "").trim().toLowerCase() != 'x'){
                                let readonly = 'readonly';

                                if(row.compliance == 'deviate' && parseInt(row.item_category) != 0)
                                    readonly = '';

                                _tpl = '<input name="overall_limit" type="number" class="form-control form-control-sm" '+readonly+' value="'+data+'" />';
                            }

                            return _tpl;
                        },
                    },
                    {
                        data: 'price_unit_vendor', name: 'price_unit_vendor', "width": 75,
                        render: function (data, type, row, meta) {
                            let total = data || 0;
                            return formatDecimal(total);
                        }
                    },
                    {
                        data: 'subtotal_vendor', name: 'subtotal_vendor', "width": 75,
                        render: function (data, type, row, meta) {
                            return formatCurrency(data, row.currency_code_vendor);
                        }
                    },
                    {data: 'currency_code_vendor', name: 'currency_code_vendor', "width": 40},
                    {
                        data: 'compliance',name: 'compliance',"width": 100,
                        render: function ( data, type, row ) {
                            let _tpl = row.compliance_text;

                            if(enabledNegotiation && (row.deleteflg || "").trim().toLowerCase() != 'x'){
                                _tpl = '<div class="form-group no-margin"><select name="compliance" class="custom-select form-control form-control-sm" required>' +
                                    '<option value="">Select...</option>' +
                                    // '<option value="comply" '+(data=="comply" ? "selected" : "") +'>Comply</option>' +
                                    '<option value="deviate" '+(data=="deviate" ? "selected" : "")+'>Deviate</option>' +
                                    '<option value="no_quote" '+(data=="no_quote" ? "selected" : "")+'>No Quote</option>' +
                                    '</select></div>';
                            }

                            return _tpl;
                        },
                    },
                    // {data: 'deleteflg', name: 'deleteflg', "width": 100, className: 'text-center',render: renderDeleteFlg},
                ],
            };
            options.createdRow = function(row,data,index){
                if((data.deleteflg || "").trim().toLowerCase() == 'x'){
                    $(row).addClass("bg-warning");
                }
            }
            //## Initilalize Datatables
            SELF.tableItem = $(SELF.dtItemSelector).DataTable(options);
            $(".card-tender-item .page_numbers").ready(function () {
                $(SELF.dtItemSelector + "_paginate").appendTo($(".card-tender-item .page_numbers"));
                $(SELF.dtItemSelector + "_info").css("padding", ".375rem .75rem").appendTo($(".card-tender-item .page_numbers"));
            });
        },
        initActionItem : function(SELF){
            // action tableItem
            $(SELF.dtItemSelector + ' tbody').on('click','.open-detail', function(e){
                e.preventDefault();
                // let dtrow = SELF.table.row($(this).parents('tr')).data();
                SELF.tableItemSelectedRow = SELF.tableItem.row($(this).parents('tr'));
                SELF.openItemDetailRow(SELF.tableItemSelectedRow.data());
            });

            // action tableItem
            $(SELF.dtItemSelector + ' tbody').on('change','select[name="compliance"]', function(e){
                let dtrow = SELF.tableItem.row($(this).parents('tr')).data();
                let selectedRow = SELF.tableItem.row($(this).parents('tr'));
                let compliance = $(this, 'option:selected').val();
                let est_unit_price = $(this).parents('tr').find('input[name="est_unit_price"]');
                let price_unit = $(this).parents('tr').find('input[name="price_unit"]');
                let description_vendor = $(this).parents('tr').find('input[name="description_vendor"]');
                let overall_limit = $(this).parents('tr').find('input[name="overall_limit"]');

                switch(compliance){
                    case 'comply' :
                        est_unit_price.prop('readonly', true);
                        overall_limit.prop('readonly', true);
                        // price_unit.prop('readonly', true);
                        break;
                    case 'deviate' :
                        if(selectedRow.data().item_category == 0){
                            est_unit_price.prop('readonly', false);
                            overall_limit.prop('readonly', true);
                        }else{
                            est_unit_price.prop('readonly', true);
                            overall_limit.prop('readonly', false);
                        }
                        // price_unit.prop('readonly', false);
                        est_unit_price.val(formatNumberByCurrency(dtrow.est_unit_price_vendor, dtrow.currency_code_vendor))
                        overall_limit.val(formatNumberByCurrency(dtrow.overall_limit_vendor, dtrow.currency_code_vendor))
                        break;
                    case 'no_quote' :
                        est_unit_price.prop('readonly', true);
                        overall_limit.prop('readonly', true);
                        est_unit_price.val(formatNumberByCurrency(0, dtrow.currency_code_vendor))
                        overall_limit.val(formatNumberByCurrency(0, dtrow.currency_code_vendor))
                        // price_unit.prop('readonly', true);
                        break;
                }
                SELF.tableItem.cell({row: selectedRow.index(), column: 11}).data(compliance);
                SELF.tableItem.draw();
                $('.card-tender-item .btn-save-items').prop('disabled', false);
            });
            $(SELF.dtItemSelector + ' tbody').on('change','input[name="est_unit_price"]', function(e){
                let selectedRow = SELF.tableItem.row($(this).parents('tr'));
                // let qty = $(this,'input[name="est_unit_price"]').val();
                let qty = getAutonumricValue($(this,'input[name="est_unit_price"]'));
                SELF.tableItem.cell({row: selectedRow.index(), column: 6}).data(qty);
                SELF.tableItem.draw();
                $('.card-tender-item .btn-save-items').prop('disabled', false);
            });
            // $(SELF.dtItemSelector + ' tbody').decimalQty(15,3, 'input[name="est_unit_price"]');
            $(SELF.dtItemSelector + ' tbody').on('change','input[name="overall_limit"]', function(e){
                let selectedRow = SELF.tableItem.row($(this).parents('tr'));
                // let overall_limit = $(this,'input[name="overall_limit"]').val();
                let overall_limit = getAutonumricValue($(this,'input[name="overall_limit"]'));
                SELF.tableItem.cell({row: selectedRow.index(), column: 7}).data(overall_limit);
                SELF.tableItem.draw();
                $('.card-tender-item .btn-save-items').prop('disabled', false);
            });
            // $(SELF.dtItemSelector + ' tbody').decimalQty(15,3, 'input[name="overall_limit"]');

            $(SELF.dtItemSelector + ' tbody').on('change','input[name="price_unit"]', function(e){
                let selectedRow = SELF.tableItem.row($(this).parents('tr'));
                let qty = $(this,'input[name="price_unit"]').val();
                SELF.tableItem.cell({row: selectedRow.index(), column: 8}).data(qty);
                SELF.tableItem.draw();
                $('.card-tender-item .btn-save-items').prop('disabled', false);
            });
            // $(SELF.dtItemSelector + ' tbody').on('change','input[name="description_vendor"]', function(e){
            //     let selectedRow = SELF.tableItem.row($(this).parents('tr'));
            //     let description_vendor = $(this,'input[name="description_vendor"]').val();
            //     SELF.tableItem.cell({row: selectedRow.index(), column: 3}).data(description_vendor);
            //     SELF.tableItem.draw();
            // });
        },
        saveHeader : function(e, SELF, callback){
            let formData = new FormData();
            formData.append('action_type', 'save-tender-header');
            formData.append('stage_type', SELF.stageType);
            formData.append('id', $(SELF.tabSelector + ' input[name="id"]').val());
            formData.append('vendor_id', $(SELF.tabSelector + ' input[name="vendor_id"]').val());
            formData.append('vendor_code', $(SELF.tabSelector + ' input[name="vendor_code"]').val());
            formData.append('quotation_number', $(SELF.tabSelector + ' input[name="quotation_number"]').val());
            formData.append('quotation_date', $(SELF.tabSelector + ' input[name="quotation_date"]').val());
            formData.append('quotation_note', $(SELF.tabSelector + ' textarea[name="quotation_note"]').val());
            formData.append('incoterm', $(SELF.tabSelector + ' select[name="incoterm"]').val() || 0);
            formData.append('incoterm_location', $(SELF.tabSelector + ' input[name="incoterm_location"]').val());
            formData.append('status', $(SELF.tabSelector + ' input[name="status"]').val());
            formData.append('currency_code', $(SELF.tabSelector + ' select[name="currency_code"]').val());


            let quotation_file = $(SELF.tabSelector + ' input[name="quotation_file"]');
            // let proposed_item_file = $(SELF.tabSelector + ' input[name="proposed_item_file"]');
            if(quotation_file[0].files[0]){
                formData.append('quotation_file', quotation_file[0].files[0]);
            }

            @if($tender->bid_bond == 1)
            let bid_bond_file = $(SELF.tabSelector + ' input[name="bid_bond_file"]');
            if(bid_bond_file[0].files[0]){
                formData.append('bid_bond_file', bid_bond_file[0].files[0]);
            }
            formData.append('bid_bond_value', $(SELF.tabSelector + ' input[name="bid_bond_value"]').val());
            formData.append('bid_bond_end_date', $(SELF.tabSelector + ' input[name="bid_bond_end_date"]').val());
            @endif

            submitUpload(formData, function(){
                $(SELF.tabSelector + ' .btn-save-header').prop('disabled', true);
                if(typeof callback === "function"){
                    callback(SELF);
                }else{
                    location.href = "{{ route('tender.show', ['id'=>$id, 'type' => $type]) }}"+ SELF.tabSelector.replace('-content', '');
                    location.reload(true);
                }
            });
        },
        validateSubmit : function(SELF){
            // header validation
            let quoNumber = $(SELF.tabSelector + ' input[name="quotation_number"]').val();
            if( !quoNumber || quoNumber == ''){
                SELF.errorMessage = "{{__('validation.required',['attribute' => __('tender.process.fields.quotation_number')])}}";
                return false;
            }

            //items validation
            let dataItems = SELF.tableItem.rows().data();
            for(let ix=0;ix<dataItems.length;ix++){
                if((dataItems[ix].deleteflg != 'x' && dataItems[ix].deleteflg != 'X')){
                    if(!dataItems[ix].compliance || dataItems[ix].compliance == '' || dataItems[ix].compliance == '0'){
                        SELF.errorMessage = "Please complete the tender items.";
                        return false;
                    }
                }
            }
            return true;
        },
        submitBatch : function(SELF, callback){
            SELF.saveHeader(null, function(SELF){
                SELF.saveItems(null, enabledNegotiation, function(data, SELF){
                    if(typeof callback === "function"){
                        callback();
                    }
                });
            });
        }
    });
    @if($negotiation['hasDocument'])
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        if(e.target.id == 'negotiation-tab'){
            TabSelected = TabNegotiationComm;
            TabNegotiationComm.editable = {{$enabledNegotiation ? 'true' : 'false'}};
            if(TabNegotiationComm.tableDocument == null){
                TabNegotiationComm.initTableDoc({{$enabledNegotiation}});
            }
            if(TabNegotiationComm.tableItem == null){
                TabNegotiationComm.initTableItem({{$enabledNegotiation}});
            }else{
                TabNegotiationComm.tableItem.ajax.reload();
                TabNegotiationComm.tableItem.columns.adjust().draw();
            }
            TenderComments.selector = TabNegotiationComm.tabSelector + ' .btn_comment';
            TenderComments.loadData("{{$vendor->vendor_code}}", TabNegotiationComm.stageType, true);
        }
    });
    @endif
    initTab('negotiation-tab');
    TabNegotiationComm.init({{$enabledNegotiation}});
});
</script>
@endsection
