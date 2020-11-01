@extends('tender.show')

@section('contentheader')
<div class="card-header-left">
    <span class="heading-title">{{__('tender.'.$type)}}</span>
</div>
<div class="card-header-right" hiden>
    {{-- <span id="table_sum" class="badge badge-secondary" style="">Total HPS: xx.xxx.xxx</span> --}}
    <a id="btn_item_detail" class="btn btn-sm btn-outline-secondary ml-2" href="{{ route('tender.show', ['id' => $id, 'type' => $type, 'action' => 'detail-specification']) }}">
        <i class="fas fa-plus mr-2" aria-hidden="true"></i>{{__('tender.item_specification.title')}}
    </a>
    @if($tender->conditional_type == 'CT1')
    <button id="btn_additional_cost" class="btn btn-sm btn-outline-success ml-2" data-toggle="modal"
        data-target="#formAddcost_modal" data-backdrop="static" data-keyboard="false">
        <i class="fas fa-plus mr-2" aria-hidden="true"></i>{{__('tender.item_cost.title')}}
    </button>
    @endif
</div>
@endsection

@section('contentbody')
<div class="has-footer" style="padding: 0">
    @if (session('status'))
    <div class="alert alert-success" role="alert">
        {{ session('status') }}
    </div>
    @endif
    <table id="datatable_serverside" class="table table-sm table-bordered table-striped table-vcenter table-wrap">
        <thead>
            <tr>
                <th>{{__('purchaserequisition.action')}}</th>
                @foreach ($tenderData['tender_'.$type]['fields'] as $field)
                    <th>{{__('purchaserequisition.'.$field)}}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
<div class="app-footer">
    <div class="app-footer__inner">
        <div class="app-footer-left">
            <div id="page_numbers" style="display:inherit"></div>
        </div>
        <div class="app-footer-right">
            <ul class="nav">
                <li id="action_group" class="nav-item">
                    <button id="btn_next_flow" class="btn btn-primary">
                        {{__('tender.next')}} <i class="fa fa-arrow-right"></i></button>
                </li>
            </ul>
        </div>
    </div>
</div>
@endsection

@section('footer')
@endsection

@section('modals')
<?php
    $modal1 = [
        'title'=> __('tender.item_cost.title'),
        'contents'=>'',
        'form_layout'=>'tender.form.form_item_add_cost',
        'form_name'=>'formAddcost',
    ];
    $modal2 = [
        'title'=> '',
        'contents'=>'',
        'form_layout'=>'tender.form.form_item_detail',
        'form_name'=>'formItemDetail',
        'modal_class' => 'modal-xl'
    ];
?>

@include('layouts.modal_common',$modal1)
@include('layouts.modal_common',$modal2)
@include('layouts.modal_delete')
@endsection

@section('modules-scripts')
@parent
@include('layouts.datatableoption')

<script type="text/javascript">
var table;
var frmId = '#formAddcost';
var fields = {!! json_encode($tenderData['tender_'.$type]['fields']) !!};
var _baseurl = "{{ route('tender.dataItem', ['id' => $id, 'type' => $type]) }}";

require(["datatablesb4","dt.plugin.select",'datetimepicker'], function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    var DTTable = function(elmId){
        let SELF = this;
        this.table = null;
        this.init = function(callback, data){
            let dtOptions = getDTOptions();
            let options = {
                deferRender: dtOptions.deferRender,
                rowId: dtOptions.rowId,
                lengthChange: false,
                searching: false,
                processing: true,
                pageLength: 50,
                language: dtOptions.language,
                rowCallback: function(row, data){
                    var idxFisrt = 0;
                    if(data.number === $('td:eq(0)', row).text()){
                        idxFisrt = idxFisrt + 1;
                    }

                    $('td:eq('+ (8 - idxFisrt) +')', row).html(formatQty(data.qty));
                    $('td:eq('+ (10 - idxFisrt) +')', row).html(formatCurrency(data.est_unit_price, data.currency_code));
                    $('td:eq('+ (11 - idxFisrt) +')', row).html(formatDecimal(data.price_unit));
                    $('td:eq('+ (13 - idxFisrt) +')', row).html(formatCurrency(data.subtotal, data.currency_code));
                    $('td:eq('+ (30 - idxFisrt) +')', row).html(formatQty(data.qty_ordered));
                    $('td:eq('+ (32 - idxFisrt) +')', row).html(formatCurrency(data.overall_limit, data.currency_code));
                    $('td:eq('+ (33 - idxFisrt) +')', row).html(formatCurrency(data.expected_limit, data.currency_code));
                },
                ajax : {
                    url : "{{ route('tender.dataItem', ['id' => $id, 'type' => $type]) }}",
                    complete : function(){},
                },
                columns: [
                    {
                        data: 'id', name: 'id',"width": 50,"className": 'text-center',
                        'visible' : @if($editable) true @else false @endif,
                        @if($editable)
                        "render": function ( data, type, row ) {
                            let _tpl = '';
                            @if($canDelete)
                            _tpl += '<a class="col-action col-delete deleteRow" style="cursor:pointer"><i class="fa fa-trash"></i></a>';
                            @endif
                            return _tpl;
                        },
                        @endif
                    },
                    @foreach ($tenderData['tender_'.$type]['fields'] as $field)
                    @if($field == 'deleteflg')
                    {
                        data: 'deleteflg', name: 'deleteflg', "width": 100, className: 'text-center',
                        render: function(data, type, row, dt){
                            let _tpl = data;
                            if(data == 'x' || data == 'X'){
                                _tpl = '<i class="fa fa-check"></i>';
                            }
                            return _tpl;
                        }},
                    @else
                    {data: '{{$field}}', name: '{{$field}}'},
                    @endif
                    @endforeach
                ],
                "order": [[ 1, "asc" ],[ 2, "asc" ]],
                columnDefs:[
                    {
                        "render": function ( data, type, row, dt ) {
                            var column = dt.settings.aoColumns[dt.col].data;
                            switch(column){
                                case 'line_number':
                                    return '<a href="" class="open-detail" >'+data+'</a>';
                                break;
                                default:
                                    return data;
                                break;
                            }
                        },
                        "targets": "_all"
                    },
                ],
            };
            options.createdRow = function(row,data,index){
                if(data.deleteflg == 'x' || data.deleteflg == 'X'){
                    $(row).addClass("bg-warning");
                }
            };
            //## Initilalize Datatables
            SELF.table = $('#' + elmId).DataTable(options);
            let tabId = elmId;
            if(typeof callback == 'function'){
                callback(elmId);
            }
            return SELF.table;
        },
        this.sum= function(col, callback){
            if(typeof callback == 'function'){
                $('#' + elmId).on( 'draw.dt', function () {
                    callback(SELF.table.column(col).data().sum());
                });
            }else{
                return SELF.table.column(col).data().sum();
            }
        }
    };

    var DTTableItem = function(elmId){
        let SELF = this;
        this.IsChanged = false;
        this.OriginalData = [];
        this.elmId = elmId;
        this.table = null;
        this.options = {};
        this.init = function(callbcak){
            SELF.IsChanged =false;
            let dtOptions = getDTOptions();
            let options = Object.assign({
                deferRender: dtOptions.deferRender,
                rowId: dtOptions.rowId,
                lengthChange: false,
                searching: false,
                language: dtOptions.language,
                autoWidth: false,
            }, SELF.options);
            //## Initilalize Datatables
            SELF.table = $('#' + elmId).DataTable(options);
            let tabId = elmId;
            if(typeof callback == 'function'){
                callback(elmId);
            }
            return SELF.table;
        },
        this.reload = function(_url){
            SELF.IsChanged =false;
            $.ajax({
                url : _url,
                contentType: "application/json; charset=utf-8",
                dataType: "json",
                beforeSend: function( xhr ) {
                    Loading.Show('#' + elmId + ' tbody');
                }
            }).done(function(response, textStatus, jqXhr) {
                SELF.OriginalData = response.data;
                SELF.table.rows.add( response.data ).draw();
            }).fail(defaultAjaxFail)
            .always(function(jqXHR, textStatus, errorThrown) {
                Loading.Hide('#' + elmId + ' tbody');
            });
        };
        this.isDataChanged = function(){
            return SELF.IsChanged;
        }
    };

    var ItemsPage = {
        table : null,
        selectedRow : null,
        init : function(){
            var SELF = this;
            SELF.table = new DTTable('datatable_serverside')
                .init(function(elmId){
                    $('#page_numbers').ready(function () {
                        $('#' + elmId +'_paginate').appendTo($('#page_numbers'));
                        $('#' + elmId +'_info').css("padding", ".375rem .75rem").appendTo($('#page_numbers'));
                    });
                });
            $('#btn_next_flow').click(function(){
                onClickNext();
            });

            // action column
            $('#datatable_serverside tbody').on('click','.open-detail', function(e){
                e.preventDefault();
                let dtrow = SELF.table.row($(this).parents('tr')).data();
                SELF.selectedRow = dtrow;
                SELF.openDetailRow(dtrow);
            });
            $('#datatable_serverside tbody').on('click','.deleteRow', function(e){
                e.preventDefault();
                let dtrow = SELF.table.row($(this).parents('tr')).data();
                SELF.deleteRow(dtrow);
            });
        },
        openDetailRow : function(dtrow){
            $('#formItemDetail_modal .modal-title').html('PR ' + dtrow.number + ' / ' + dtrow.line_number );
            $('#formItemDetail_modal .title-left').html('PR ' + dtrow.number);
            $('#formItemDetail_modal .title-right').html(dtrow.line_number);
            $('#pr-item input[name="id"]').val(dtrow.id)
            for(let ix in fields){
                @if($editable)
                if(fields[ix] == 'qty' && dtrow.item_category == 0){
                    $('#formItemDetail_modal #pr-item #' + fields[ix])
                            .html('<input name="'+fields[ix]+'" class="form-control form-control-sm" value="'+dtrow.qty+'" />');
                }else if(fields[ix] == 'deleteflg'){
                    let checked = (dtrow.deleteflg == 'x' || dtrow.deleteflg == 'X') ? 'checked' : '';
                    $('#formItemDetail_modal #pr-item #' + fields[ix])
                            .html('<input name="'+fields[ix]+'" type="checkbox" '+checked+' class="" value="X" />');
                }else{
                    $('#formItemDetail_modal #pr-item #' + fields[ix]).html(dtrow[fields[ix]]);
                }
                @else
                $('#formItemDetail_modal #pr-item #' + fields[ix]).html(dtrow[fields[ix]]);
                @endif
            }
            $('#formItemDetail_modal').modal();
        },
        deleteRow : function(dtrow){
            var SELF = this;
            let rowinfo = dtrow['number'] + ' / '+dtrow['line_number'];
            $('#delete_modal .modal-title').text("Delete "+rowinfo);
            $('#delete_modal .modal-body').text("Are you sure to delete "+rowinfo+"?");
            $('#btn_delete_modal').click();
            $('#delete_modal #btn_confirm').off('click').on('click', function () {
                $.ajax({
                    type: 'DELETE',
                    url: "{{ route('tender.show',['id'=>$id,'type'=>$type]) }}/"+dtrow['id'],
                    cache : false,
                    beforeSend: function( xhr ) {
                        Loading.Show();
                    }
                }).done(function(response) {
                    if(response.success){
                        $('#delete_modal .close').click();
                        showAlert(rowinfo+" deleted", "success", 3000);
                        SELF.table.ajax.reload();
                        // resetForm();
                    }else{
                        showAlert(rowinfo+" not deleted", "danger", 3000);
                    }
                }).always(function(jqXHR, textStatus, errorThrown) {
                    Loading.Hide();
                });
                return false;
            });
        },
        reloadTable : function(){
            this.table.ajax.reload();
        },
    }

    var FormCostPage = {
        table : null,
        selectedRow : null,
        initTable : function(callback){
            var SELF = this;
            var elmId = 'dt-add-cost';
            let dtOptions = getDTOptions();
            let options = {
                deferRender: dtOptions.deferRender,
                rowId: dtOptions.rowId,
                lengthChange: false,
                searching: false,
                language: dtOptions.language,
                autoWidth: false,
                columns: [
                    {
                        data: 'id', name: 'id',"width": 50,"className": 'text-center',
                        'visible' : @if($editable) true @else false @endif,
                        @if($editable)
                            "render": function ( data, type, row ) {
                                return ''+
                                '<a href="" class="col-action col-edit editRow mr-2" ><i class="fa fa-edit"></i></a>'+
                                '<a href="" class="col-action col-delete deleteRow"><i class="fa fa-trash"></i></a>';
                            },
                        @endif
                    },
                    {data: 'calculation_pos', name: 'calculation_pos',"visible": false},
                    {data: 'conditional_code', name: 'conditional_code',"visible": false},
                    {data: 'conditional_type', name: 'conditional_type',"visible": false},
                    {data: 'conditional_name', name: 'conditional_name'},
                    {
                        data: 'percentage', name: 'percentage',"width": 250,
                        render: function (data, type, row, meta) {
                            if(parseFloat(data || 0) > 0)
                                return formatPercentage(data);
                            else
                                return "";
                        },
                    },
                    {
                        data: 'value', name: 'value',"width": 250,
                        render: function (data, type, row, meta) {
                            if(parseFloat(data || 0) > 0)
                                return formatAmmount(data, getCurrencyCode());
                            else
                                return "";
                        },
                    },
                ],
                "order": [[ 1, "asc" ]],
            };
            //## Initilalize Datatables
            SELF.table = $('#' + elmId).DataTable(options);
            let tabId = elmId;
            if(typeof callback == 'function'){
                callback(elmId);
            }
            return SELF.table;
        },
        init : function(){
            var SELF = this;
            SELF.initTable(function(elmId){
                $('#vpage_numbers').ready(function () {
                    $('#' + elmId +'_paginate').appendTo($('#vpage_numbers'));
                    $('#' + elmId +'_info').css("padding", ".375rem .75rem").appendTo($('#vpage_numbers'));
                });
            });

            // action form
            $(frmId+'_modal').on("shown.bs.modal", function () {
                try{
                    SELF.resetForm();
                    SELF.reloadTable();
                    initInputQty();
                    initInputDecimal(getCurrencyCode());
                    initInputPercentage();
                }catch{}
            });
            $("#btn_additional_cost").click(function(){
                isEdit = false;
                SELF.resetForm();
                SELF.table.rows().remove().draw();
            });
            $(frmId+'-save').click(function(){
                if(SELF.validateSubmit()){
                    SELF.submit(function(){
                        $(frmId+'_modal .close').click();
                        ItemsPage.reloadTable();
                        SELF.resetForm();
                        $(frmId+'_fieldset').attr("disabled",false);
                    });
                }
            });

            @if(!$editable)
            $(frmId+'-save').hide();
            @endif

            // action column
            $('#dt-add-cost tbody').on('click','.editRow', function(e){
                e.preventDefault();
                let dtrow = SELF.table.row($(this).parents('tr')).data();
                SELF.selectedRow = SELF.table.row($(this).parents('tr'));
                $('#dt-add-cost tbody .deleteRow').show();
                $(this).parents('tr').find('.deleteRow').hide();
                SELF.editRow(dtrow);
            });
            $('#dt-add-cost tbody').on('click','.deleteRow', function(e){
                e.preventDefault();
                let dtrow = SELF.table.row($(this).parents('tr'));
                SELF.deleteRow(dtrow);
            });

            // action form cost
            $(frmId+'_modal').on('change','select[name="conditional_code"]', function(e){
                let selected = $(this).find(":selected");
                let conditional_name = selected.text();
                let clculation_type = selected.data('calculation-type');
                let clculation_post = selected.data('calculation-pos');
                $(frmId+'_modal input[name="conditional_name"]').val(conditional_name);
                $(frmId+'_modal input[name="calculation_pos"]').val(clculation_post);
                if(clculation_type == 1){
                    $(frmId+'_modal div.g-percentage').show();
                    $(frmId+'_modal div.g-value').hide();
                }else{
                    $(frmId+'_modal div.g-percentage').hide();
                    $(frmId+'_modal div.g-value').show();
                }
            })
            $(frmId+'_modal button.btn-add').click(function(e){
                e.preventDefault();
                if(SELF.validateRow()){
                    SELF.saveRow( $(frmId+'_modal input[name="id"]').val());
                    SELF.resetForm();
                }
            });
            $(frmId+'_modal button.btn-cancel').click(function(e){
                e.preventDefault();
                SELF.resetForm();
                $('#dt-add-cost tbody .deleteRow').show();
            });
        },
        reloadTable : function(){
            let SELF = this;
            var elmId = 'dt-add-cost';
            let _url = "{{ route('tender.dataItem', ['id' => $id, 'type' => $type]) }}";
            let _type = "{{$tender->conditional_type}}";

            $.ajax({
                url : _url + '?data_type=4&cost_type=' + _type,
                contentType: "application/json; charset=utf-8",
                dataType: "json",
                beforeSend: function( xhr ) {
                    Loading.Show('#' + elmId + ' tbody');
                }
            }).done(function(response, textStatus, jqXhr) {
                SELF.table.rows.add( response.data ).draw();
            }).fail(defaultAjaxFail)
            .always(function(jqXHR, textStatus, errorThrown) {
                Loading.Hide('#' + elmId + ' tbody');
            });
        },
        resetForm : function(){
            $(frmId+'_modal input[name="id"]').val('');
            $(frmId+'')[0].reset();
            this.selectedRow = null;
            $(frmId+'_modal div.g-percentage').hide();
            $(frmId+'_modal div.g-value').hide();
            $(frmId+'_modal button.btn-add').html("{{__('common.add')}}");
        },
        saveRow : function(id){
            var SELF = this;
            let isEdit = true;
            if(!id || id==''){
                isEdit = false;
                id = (new Date()).getTime();
            }else{
                id = parseInt(id);
            }

            let selected = $(frmId+'_modal select[name="conditional_code"]').find(":selected");
            let clculation_type = selected.data('calculation-type');
            let data = {
                id : id,
                conditional_name : $(frmId+'_modal input[name="conditional_name"]').val() || '',
                calculation_pos : $(frmId+'_modal input[name="calculation_pos"]').val() || '',
                conditional_code : $(frmId+'_modal select[name="conditional_code"]').val() || '',
                conditional_type : $(frmId+'_modal input[name="conditional_type"]').val() || '',
                // percentage : $(frmId+'_modal input[name="percentage"]').val() || '',
                // value : $(frmId+'_modal input[name="value"]').val() || '',
                percentage : getAutonumricValue($(frmId+'_modal input[name="percentage"]')) || '',
                value : getAutonumricValue($(frmId+'_modal input[name="value"]')) || '',
            };
            if(clculation_type == 1){
                data.value = null;
            }else{
                data.percentage = null;
            }

            if(isEdit == true){
                SELF.selectedRow.data( data ).draw();
            }else{
                SELF.table.row.add(data).draw();
            }
            $('#dt-add-cost tbody .deleteRow').show();
        },
        validateRow : function(){
            var SELF = this;
            let valid = true;
            let conditionalCode = $(frmId+'_modal select[name="conditional_code"]').val();
            if(!conditionalCode || conditionalCode == ''){
                valid = false;
                showAlert("Name is required", "warning");
            }
            let selected = $(frmId+'_modal select[name="conditional_code"]').find(":selected");
            let clculation_type = selected.data('calculation-type');

            // let percentage = $(frmId+'_modal input[name="percentage"]').val();
            let percentage = getAutonumricValue($(frmId+'_modal input[name="percentage"]'));
            if(clculation_type == 1 && percentage > 100){
                valid = false;
                showAlert("Max percentage is 100", "warning");
            }
            if(clculation_type == 1 && percentage < 0){
                valid = false;
                showAlert("Min percentage is 0", "warning");
            }

            let count = this.table.rows().count();
            let _data = this.table.rows().data();
            let oldConditionalCode = SELF.selectedRow ? SELF.selectedRow.data().conditional_code : ''; // SELF.selectedRow.conditional_code;

            if(count > 0 && (conditionalCode != '' && conditionalCode!=oldConditionalCode)){
                for(let ix=0;ix<count;ix++){
                    if(conditionalCode == _data[ix].conditional_code){
                        showAlert("Duplicate " + _data[ix].conditional_name, "warning");
                        return false;
                    }
                }
            }

            return valid;
        },
        editRow : function(dtrow){
            var SELF = this;
            $(frmId+'_modal input[name="id"]').val(dtrow.id);
            $(frmId+'_modal input[name="conditional_name"]').val(dtrow.conditional_name);
            $(frmId+'_modal input[name="calculation_pos"]').val(dtrow.calculation_pos);
            $(frmId+'_modal select[name="conditional_code"]').val(dtrow.conditional_code);
            $(frmId+'_modal select[name="conditional_code"]').trigger('change');
            $(frmId+'_modal input[name="percentage"]').val(formatPercentage(dtrow.percentage));
            // $(frmId+'_modal input[name="value"]').val(dtrow.value);
            $(frmId+'_modal input[name="value"]').val(formatNumberByCurrency(dtrow.value, getCurrencyCode()));

            $(frmId+'_modal input[name="calculation_pos"]').val(dtrow.calculation_pos);
            $(frmId+'_modal button.btn-add').html("{{__('common.update')}}");
        },
        deleteRow : function(dtrow){
            dtrow.remove().draw();
            this.selectedRow = null;
        },
        validateSubmit : function(){
            let valid = true;
            if(this.table.rows().count() <= 0){
                valid = false;
                showAlert("Please input one or more data", "warning");
            }
            return valid;
        },
        submit : function(callback)
        {
            let SELF = this;
            //SUBMIT
            let dataTable = SELF.table.rows().data();
            let additonalCost = [];
            for(let ix=0;ix<SELF.table.rows().count();ix++){
                additonalCost[ix] = dataTable[ix];
                delete additonalCost[ix]['id'];
            }

            let params = {
                item : null,
                cost : additonalCost,
            };

            $.ajax({
                url : "{{ route('tender.save', ['id'=>$id, 'type'=>$type]) }}",
                type : 'POST',
                data : JSON.stringify(params),
                contentType: "application/json; charset=utf-8",
                dataType: "json",
                beforeSend: function( xhr ) {
                    $(frmId+'_fieldset').attr("disabled",true);
                    Loading.Show();
                }
            }).done(function(response, textStatus, jqXhr) {
                if(response.success){
                    if(typeof callback == 'function'){
                        callback();
                    }
                    showAlert("Data saved.", "success", 3000);
                }else{
                    showAlert("Data not saved.", "danger", 3000);
                }
            }).fail(defaultAjaxFail)
            .always(function(jqXHR, textStatus, errorThrown) {
                $(frmId+'_fieldset').attr("disabled",false);
                Loading.Hide();
            });
        },
    }

    var ItemDetailPage = {
        ServiceTable : null,
        TaxTable : null,
        CostTable : null,
        FormTaxCode : {
            selectedRow : null,
            resetForm : function(){
                this.selectedRow = null;
                $('#tax-item input[name="id"]').val('');
                $('#tax-item select[name="tax_code"]').val('');
                $('#tax-item button.btn-add').html("{{__('common.add')}}");
            },
            editRow : function(dtrow){
                $('#tax-item input[name="id"]').val(dtrow.id);
                $('#tax-item select[name="tax_code"]').val(dtrow.tax_code);
                $('#tax-item button.btn-add').html("{{__('common.update')}}");
            },
            deleteRow : function(selectedRow){
                selectedRow.remove().draw();
                this.selectedRow = null;
            },
            validateRow : function(){
                let SELF = this;
                let valid = true;
                let taxCode = $('#tax-item select[name="tax_code"]').val();
                if(!taxCode || taxCode == ''){
                    valid = false;
                    showAlert("Tax Code is required", "warning");
                }

                let count = ItemDetailPage.TaxTable.table.rows().count();
                let _data = ItemDetailPage.TaxTable.table.rows().data();
                let oldTaxCode = SELF.selectedRow ? SELF.selectedRow.data().tax_code : '';
                if(count > 0 && (taxCode != '' && oldTaxCode != taxCode)){
                    for(let ix=0;ix<count;ix++){
                        if(taxCode == _data[ix].tax_code){
                            showAlert("Duplicate " + _data[ix].description, "warning");
                            return false;
                        }
                    }
                }
                return valid;
            },
            saveRow : function(id){
                let isEdit = true;
                if(!id || id==''){
                    isEdit = false;
                    id = (new Date()).getTime();
                }else{
                    id = parseInt(id);
                }
                let data = {
                    id : id,
                    tax_code : $('#tax-item select[name="tax_code"]').val() || '',
                    description : $('#tax-item select[name="tax_code"]').find(':selected').text() || '',
                };
                if(isEdit == true){
                    this.selectedRow.data( data ).draw();
                }else{
                    ItemDetailPage.TaxTable.table.row.add(data).draw();
                }
                ItemDetailPage.TaxTable.IsChanged = true;
                $('#dt-tax-item tbody .deleteRow').show();
            },
            init : function(){
                let SELF = this;
                // action column item - tax code
                $('#dt-tax-item tbody').on('click','.editRow', function(e){
                    e.preventDefault();
                    let dtrow = ItemDetailPage.TaxTable.table.row($(this).parents('tr')).data();
                    SELF.selectedRow = ItemDetailPage.TaxTable.table.row($(this).parents('tr'));
                    $('#dt-tax-item tbody .deleteRow').show();
                    $(this).parents('tr').find('.deleteRow').hide();
                    SELF.editRow(dtrow);
                });
                $('#dt-tax-item tbody').on('click','.deleteRow', function(e){
                    e.preventDefault();
                    SELF.selectedRow = ItemDetailPage.TaxTable.table.row($(this).parents('tr'));
                    SELF.deleteRow(SELF.selectedRow);
                });

                // action form item - tax code
                $('#tax-item button.btn-add').click(function(e){
                    e.preventDefault();
                    if(SELF.validateRow()){
                        SELF.saveRow( $('#tax-item input[name="id"]').val());
                        SELF.resetForm();
                    }
                });
                $('#tax-item button.btn-cancel').click(function(e){
                    e.preventDefault();
                    SELF.resetForm();
                    $('#dt-tax-item tbody .deleteRow').show();
                });
            },
        },
        FormCost : {
            selectedRow : null,
            resetForm : function(){
                this.selectedRow = null;
                $('#cost-item input[name="id"]').val('');
                $('#cost-item select[name="conditional_code"]').val('');
                $('#cost-item select[name="conditional_code"]').trigger('change');
                $('#cost-item input[name="conditional_name"]').val('');
                $('#cost-item input[name="calculation_pos"]').val('');
                $('#cost-item input[name="percentage"]').val('');
                $('#cost-item input[name="value"]').val('');

                $('#cost-item div.g-percentage').hide();
                $('#cost-item div.g-value').hide();
                $('#cost-item button.btn-add').html("{{__('common.add')}}");
            },
            editRow : function(dtrow){
                $('#cost-item input[name="id"]').val(dtrow.id);
                $('#cost-item select[name="conditional_code"]').val(dtrow.conditional_code);
                $('#cost-item select[name="conditional_code"]').trigger('change');
                $('#cost-item input[name="conditional_name"]').val(dtrow.conditional_name);
                $('#cost-item input[name="calculation_pos"]').val(dtrow.calculation_pos);
                $('#cost-item input[name="percentage"]').val(formatPercentage(dtrow.percentage));
                // $('#cost-item input[name="value"]').val(dtrow.value);
                $('#cost-item input[name="value"]').val(formatNumberByCurrency(dtrow.value, getCurrencyCode()));

                $('#cost-item button.btn-add').html("{{__('common.update')}}");
            },
            deleteRow : function(selectedRow){
                selectedRow.remove().draw();
                this.selectedRow = null;
            },
            validateRow : function(editMode){
                var SELF = this;
                let valid = true;
                let conditionalCode = $('#cost-item select[name="conditional_code"]').val();
                if(!conditionalCode || conditionalCode == ''){
                    valid = false;
                    showAlert("Name is required", "warning");
                }

                let selected = $('#cost-item select[name="conditional_code"]').find(":selected");
                let clculation_type = selected.data('calculation-type');
                // let percentage = $('#cost-item input[name="percentage"]').val();
                let percentage = getAutonumricValue($('#cost-item input[name="percentage"]'));

                if(clculation_type == 1 && percentage > 100){
                    valid = false;
                    showAlert("Max percentage is 100", "warning");
                }
                if(clculation_type == 1 && percentage < 0){
                    valid = false;
                    showAlert("Min percentage is 0", "warning");
                }

                let count = ItemDetailPage.CostTable.table.rows().count();
                let _data = ItemDetailPage.CostTable.table.rows().data();

                let oldConditionalCode = SELF.selectedRow ? SELF.selectedRow.data().conditional_code : ''; // SELF.selectedRow.conditional_code;
                if(count > 0 && (conditionalCode != '' && conditionalCode!=oldConditionalCode)){
                    for(let ix=0;ix<count;ix++){
                        if(conditionalCode == _data[ix].conditional_code){
                            showAlert("Duplicate " + _data[ix].conditional_name, "warning");
                            return false;
                        }
                    }
                }
                return valid;
            },
            saveRow : function(id){
                let isEdit = true;
                if(!id || id==''){
                    isEdit = false;
                    id = (new Date()).getTime();
                }else{
                    id = parseInt(id);
                }

                let selected = $('#cost-item select[name="conditional_code"]').find(":selected");
                let clculation_type = selected.data('calculation-type');
                let data = {
                    id : id,
                    conditional_name : $('#cost-item input[name="conditional_name"]').val() || '',
                    calculation_pos : $('#cost-item input[name="calculation_pos"]').val() || '',
                    conditional_code : $('#cost-item select[name="conditional_code"]').val() || '',
                    conditional_type : $('#cost-item input[name="conditional_type"]').val() || '',
                    // percentage : $('#cost-item input[name="percentage"]').val() || '',
                    // value : $('#cost-item input[name="value"]').val() || '',
                    percentage : getAutonumricValue($('#cost-item input[name="percentage"]')) || '',
                    value : getAutonumricValue($('#cost-item input[name="value"]')) || '',
                };
                if(clculation_type == 1){
                    data.value = null;
                }else{
                    data.percentage = null;
                }

                if(isEdit == true){
                    this.selectedRow.data( data ).draw();
                }else{
                    ItemDetailPage.CostTable.table.row.add(data).draw();
                }
                ItemDetailPage.CostTable.IsChanged = true;
                $('#dt-cost-item tbody .deleteRow').show();
            },
            init : function(){
                var SELF = this;
                // action column item - tax code
                $('#dt-cost-item tbody').on('click','.editRow', function(e){
                    e.preventDefault();
                    let dtrow = ItemDetailPage.CostTable.table.row($(this).parents('tr')).data();
                    SELF.selectedRow = ItemDetailPage.CostTable.table.row($(this).parents('tr'));
                    console.log(SELF.selectedRow);
                    $('#dt-cost-item tbody .deleteRow').show();
                    $(this).parents('tr').find('.deleteRow').hide();
                    SELF.editRow(dtrow);
                });
                $('#dt-cost-item tbody').on('click','.deleteRow', function(e){
                    e.preventDefault();
                    SELF.selectedRow = ItemDetailPage.CostTable.table.row($(this).parents('tr'));
                    SELF.deleteRow(SELF.selectedRow);
                });

                // action form item - tax code
                $('#cost-item select[name="conditional_code"]').on('change', function(e){
                    let selected = $(this).find(":selected");
                    let conditional_name = selected.text();
                    let clculation_type = selected.data('calculation-type');
                    let clculation_post = selected.data('calculation-pos');
                    $('#cost-item input[name="conditional_name"]').val(conditional_name);
                    $('#cost-item input[name="calculation_pos"]').val(clculation_post);
                    if(clculation_type == 1){
                        $('#cost-item div.g-percentage').show();
                        $('#cost-item div.g-value').hide();
                    }else{
                        $('#cost-item div.g-percentage').hide();
                        $('#cost-item div.g-value').show();
                    }
                })
                $('#cost-item button.btn-add').click(function(e){
                    e.preventDefault();
                    if(SELF.validateRow()){
                        SELF.saveRow( $('#cost-item input[name="id"]').val());
                        SELF.resetForm();
                    }
                });
                $('#cost-item button.btn-cancel').click(function(e){
                    e.preventDefault();
                    SELF.resetForm();
                    $('#dt-cost-item tbody .deleteRow').show();
                });
            },
        },
        init : function(){
            var SELF = this;
            $('#formItemDetail_modal').on("shown.bs.modal", function () {
                try{
                    SELF.resetForm();
                    SELF.reloadTable();
                    SELF.ForceCloseModal = false;
                    SELF.initModalShow();
                }catch(e){
                    console.error(e);
                }
            });

            // table item tax
            SELF.TaxTable = new DTTableItem('dt-tax-item');
            SELF.TaxTable.options = {
                columns: [
                    {
                        data: 'id', name: 'id',"width": 50,"className": 'text-center',
                        'visible' : @if($editable) true @else false @endif,
                        @if($editable)
                            "render": function ( data, type, row ) {
                                return ''+
                                '<a href="" class="col-action col-edit editRow mr-2" ><i class="fa fa-edit"></i></a>'+
                                '<a href="" class="col-action col-delete deleteRow"><i class="fa fa-trash"></i></a>';
                            },
                        @endif
                    },
                    {data: 'tax_code', name: 'tax_code',"visible": true},
                    {data: 'description', name: 'description',"visible": true},
                ],
                "order": [[ 1, "asc" ]],
            };
            SELF.TaxTable.init(function(elmId){});
            // table item addional cost
            SELF.CostTable = new DTTableItem('dt-cost-item');
            SELF.CostTable.options = {
                columns: [
                    {
                        data: 'id', name: 'id',"width": 50,"className": 'text-center',
                        'visible' : @if($editable) true @else false @endif,
                        @if($editable)
                            "render": function ( data, type, row ) {
                                return ''+
                                '<a href="" class="col-action col-edit editRow mr-2" ><i class="fa fa-edit"></i></a>'+
                                '<a href="" class="col-action col-delete deleteRow"><i class="fa fa-trash"></i></a>';
                            },
                        @endif
                    },
                    {data: 'calculation_pos', name: 'calculation_pos',"visible": false},
                    {data: 'conditional_code', name: 'conditional_code',"visible": false},
                    {data: 'conditional_type', name: 'conditional_type',"visible": false},
                    {data: 'conditional_name', name: 'conditional_name'},
                    {
                        data: 'percentage', name: 'percentage',"width": 250,
                        render: function (data, type, row, meta) {
                            if(parseFloat(data || 0) > 0)
                                return formatPercentage(data);
                            else
                                return "";
                        },
                    },
                    {
                        data: 'value', name: 'value',"width": 250,
                        render: function (data, type, row, meta) {
                            if(parseFloat(data || 0) > 0)
                                return formatAmmount(data, getCurrencyCode());
                            else
                                return "";
                        },
                    },
                ],
                "order": [[ 1, "asc" ]],
            };
            SELF.CostTable.init(function(elmId){});
            // table item services
            SELF.ServiceTable = new DTTableItem('dt-service-item');
            SELF.ServiceTable.options = {
                columns: [
                    {data: 'EXTROW', name: 'EXTROW',"visible": true},
                    {data: 'KTEXT1', name: 'KTEXT1',"visible": true},
                    {data: 'MENGE', name: 'MENGE',"visible": true},
                    {data: 'MEINS', name: 'MEINS'},
                    {data: 'BRTWR', name: 'BRTWR',"visible": true},
                    {data: 'WAERS', name: 'WAERS',"width": 250},
                    {data: 'COST_CODE', name: 'COST_CODE',"visible": true},
                    {data: 'COST_DESC', name: 'COST_DESC',"visible": true},
                ],
                "order": [[ 3, "asc" ]],
            };
            SELF.ServiceTable.init(function(elmId){});

            @if(!$editable)
            $('#formItemDetail-save').hide();
            @else
            $('#formItemDetail-save').show();
            $('#formItemDetail-save').click(function(e){
                e.preventDefault();
                if(SELF.validateSubmit()){
                    SELF.submit(function(){
                        $('#formItemDetail_modal .close').click();
                        ItemsPage.reloadTable();
                        SELF.resetForm();
                    });
                }
            });

            // $('textarea[name="item_text"]').keyup(function() {
            $('textarea[name="item_text"]').on('input keydown keyup focus',function() {
                let lines = inputItemTextLength($(this).val(), 132);
                $(this).val(lines.join(''));
            });
            @endif

            SELF.FormTaxCode.init();
            SELF.FormCost.init();
        },
        initModalShow : function(){
            let SELF = this;
            $('#pr-item input[name="qty"]').change(function(e){
                SELF.onChangeSelectedQty(ItemsPage.selectedRow);
            });
            initInputQty();
            initInputDecimal(getCurrencyCode());
            initInputPercentage();
        },
        resetForm : function(){
            this.FormTaxCode.resetForm();
            this.FormCost.resetForm();
        },
        reloadTable : function(){
            this.reloadItemText();
            if(ItemsPage.selectedRow && ItemsPage.selectedRow.item_category == 0){
                $('#service-item').hide();
            }else{
                $('#service-item').show();
                if(this.ServiceTable){
                    this.ServiceTable.table.clear().draw();
                    let _Url = _baseurl + "?data_type=1&number="+ItemsPage.selectedRow.number+"&line_number="+ItemsPage.selectedRow.line_number;
                    this.ServiceTable.reload(_Url);
                }
            }
            if(this.TaxTable){
                this.TaxTable.table.clear().draw();
                let _taxUrl = _baseurl + "?data_type=3&pr_id="+ItemsPage.selectedRow.id;
                this.TaxTable.reload(_taxUrl);
            }
            if(this.CostTable){
                this.CostTable.table.clear().draw();
                let _costUrl = _baseurl + "?data_type=4&pr_id="+ItemsPage.selectedRow.id+"&cost_type="+$('#cost-item input[name="conditional_type"]').val();
                this.CostTable.reload(_costUrl);
            }
        },
        reloadItemText : function(){
            let _url = _baseurl + "?data_type=2&item_id="+ItemsPage.selectedRow.line_id;
            $.ajax({
                url : _url,
                contentType: "application/json; charset=utf-8",
                dataType: "json",
                beforeSend: function( xhr ) {
                    Loading.Show('#text-item textarea[name="item_text"]');
                }
            }).done(function(response, textStatus, jqXhr) {
                let itemText = '';
                var newline = String.fromCharCode(13, 10);
                if(response.data && response.data.length > 0){
                    for(let ix in response.data){
                        itemText += response.data[ix].TEXT_LINE + response.data[ix].TEXT_FORM.replace('*',newline);
                    }
                }
                $('#text-item textarea[name="item_text"]').val(itemText);
                $('#text-item textarea[name="item_text"]').attr('data-val', itemText);
            }).fail(defaultAjaxFail)
            .always(function(jqXHR, textStatus, errorThrown) {
                Loading.Hide('#text-item textarea[name="item_text"]');
            });
        },
        onChangeSelectedQty : function(dtrow){
            var qty = getAutonumricValue($('#pr-item input[name="qty"]')); // get value from format autonumric

            let maxQty = Number(dtrow.qty) + Number(dtrow.qty_available);
            if(Number(qty) > maxQty){
                showAlert("Max QTY is "+maxQty, "warning", 3000);
                $('#pr-item input[name="qty"]').val(formatQty(dtrow.qty));
                return false;
            }
            if(Number(qty) <= 0){
                showAlert("Min QTY is 1", "warning", 3000);
                $('#pr-item input[name="qty"]').val(formatQty(dtrow.qty));
                return false;
            }
            return true;
        },
        validateSubmit : function(){
            let SELF = this;
            let valid = true;
            var qty = getAutonumricValue($('#pr-item input[name="qty"]')); // get value from format autonumric
            var deleteflg = $('#pr-item input[name="deleteflg"]').is(":checked") ? 'X' : '';
            let itemText = $('textarea[name="item_text"]').val();
            let itemTextOld = $('textarea[name="item_text"]').data('val');
            if(!SELF.TaxTable.isDataChanged()
                && !SELF.CostTable.isDataChanged()
                && ItemsPage.selectedRow.qty == qty
                && ItemsPage.selectedRow.deleteflg == deleteflg
                && itemTextOld == itemText){
                    valid = false;
                showAlert("There are no data changes", "warning");
            }
            if( SELF.onChangeSelectedQty(ItemsPage.selectedRow) == false){
                valid = false;
            }
            return valid;
        },
        submit : function(callback)
        {
            let SELF = this;
            //SUBMIT
            let taxCodes = [];
            let countTax = SELF.TaxTable.table.rows().count();
            if(countTax > 0){
                let TaxTable = SELF.TaxTable.table.rows().data();
                for(let ix=0;ix<countTax;ix++){
                    taxCodes[ix] = TaxTable[ix];
                    delete taxCodes[ix]['id'];
                }
            }

            let additonalCost = [];
            let countCost = SELF.CostTable.table.rows().count();
            if(countCost > 0){
                let CostTable = SELF.CostTable.table.rows().data();
                for(let ix=0;ix<countCost;ix++){
                    additonalCost[ix] = CostTable[ix];
                    delete additonalCost[ix]['id'];
                }
            }

            let params = {
                item : {
                    id : $('#pr-item input[name="id"]').val(),
                    // qty : $('#pr-item input[name="qty"]').val(),
                    qty : getAutonumricValue($('#pr-item input[name="qty"]')),
                    deleteflg : $('#pr-item input[name="deleteflg"]').is(":checked") ? 'X' : '',
                },
                cost : additonalCost,
                tax : taxCodes,
                item_text : $('textarea[name="item_text"]').val(),
            };

            $.ajax({
                url : "{{ route('tender.save', ['id'=>$id, 'type'=>$type]) }}",
                type : 'POST',
                data : JSON.stringify(params),
                contentType: "application/json; charset=utf-8",
                dataType: "json",
                beforeSend: function( xhr ) {
                    Loading.Show();
                }
            }).done(function(response, textStatus, jqXhr) {
                if(response.success){
                    ItemDetailPage.TaxTable.IsChanged = false;
                    ItemDetailPage.CostTable.IsChanged = false;
                    if(typeof callback == 'function'){
                        callback();
                    }
                    showAlert("Data saved.", "success", 3000);
                }else{
                    showAlert("Data not saved.", "danger", 3000);
                }
            }).fail(defaultAjaxFail)
            .always(function(jqXHR, textStatus, errorThrown) {
                Loading.Hide();
            });
        },
    }

    $(document).ready(function(){
        ItemsPage.init();
        FormCostPage.init();
        ItemDetailPage.init();
    }); //document.ready
});

</script>
@endsection
@section('styles')
@parent
<style>
#table_sum{
    padding: .5em 1em;
    line-height: 1.5;
    border-radius: 1em;
}
</style>
@endsection
