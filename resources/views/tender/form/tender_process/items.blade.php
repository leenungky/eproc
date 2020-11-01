<script type="text/javascript">
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
                "paging":   false,
                "info":     false
            }, SELF.options);
            //## Initilalize Datatables
            SELF.table = $('#' + elmId).DataTable(options);
            let tabId = elmId;
            if(typeof callback == 'function'){
                callback(elmId);
            }
            return SELF.table;
        },
        this.reload = function(_url, stageType){
            SELF.IsChanged =false;
            if(!stageType || stageType == '') stageType = TabSelected.stageType;
            $.ajax({
                url : _url + '&action_type=submission-items&stage_type='+stageType,
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
                "paging":   false,
                "info":     false,
                columns: [
                    {
                        data: 'id', name: 'id',"width": 50,"className": 'text-center',
                        'visible' : @if($editableItem) true @else false @endif,
                        @if($editableItem)
                            "render": function ( data, type, row ) {
                                return ''+
                                '<a href="" class="col-action col-edit editRow mr-2" ><i class="fa fa-edit"></i></a>'+
                                '<a href="" class="col-action col-delete deleteRow"><i class="fa fa-trash"></i></a>';
                            },
                        @endif
                    },
                    {data: 'conditional_name', name: 'conditional_name', "width": 250},
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
                        data: 'value', name: 'value',"width": 100,
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

            $("#btn_additional_cost").click(function(){
                isEdit = false;
                SELF.resetForm();
                SELF.table.rows().remove().draw();
            });
            $('#formAddcost-save').click(function(){
                if(SELF.validateSubmit()){
                    SELF.submit(function(){
                        $('#formAddcost_modal .close').click();
                        TabSelected.tableItem.ajax.reload();
                        SELF.resetForm();
                        $('#formAddcost_fieldset').attr("disabled",false);
                    });
                }
            });

            @if(!$editableItem)
            $('#formAddcost-save').hide();
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
            $('#formAddcost_modal').on('change','select[name="conditional_code"]', function(e){
                let selected = $(this).find(":selected");
                let conditional_name = selected.text();
                let clculation_type = selected.data('calculation-type');
                let clculation_post = selected.data('calculation-pos');
                $('#formAddcost_modal input[name="conditional_name"]').val(conditional_name);
                $('#formAddcost_modal input[name="calculation_pos"]').val(clculation_post);
                if(clculation_type == 1){
                    $('#formAddcost_modal div.g-percentage').show();
                    $('#formAddcost_modal div.g-value').hide();
                }else{
                    $('#formAddcost_modal div.g-percentage').hide();
                    $('#formAddcost_modal div.g-value').show();
                }
            })
            $('#formAddcost_modal button.btn-add').click(function(e){
                e.preventDefault();
                if(SELF.validateRow()){
                    SELF.saveRow( $('#formAddcost_modal input[name="id"]').val());
                    SELF.resetForm();
                }
            });
            $('#formAddcost_modal button.btn-cancel').click(function(e){
                e.preventDefault();
                SELF.resetForm();
                $('#dt-add-cost tbody .deleteRow').show();
            });
        },
        reloadTable : function(vendorId, stageType, actionView){
            let SELF = this;
            var elmId = 'dt-add-cost';
            let _type = "{{$tender->conditional_type}}";
            if(!stageType || stageType == '') stageType = TabSelected.stageType;

            let _url = URLDatatable + '?data_type=4&vendor_id='+vendorId+'&cost_type=' + _type + '&action_type=submission-items&stage_type='+stageType;
            if(actionView){
                _url = _url + "&actionView=" + actionView;
            }
            SELF.table.clear().draw();
            $.ajax({
                url : _url,
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
            $('#formAddcost_modal input[name="id"]').val('');
            $('#formAddcost')[0].reset();
            this.selectedRow = null;
            $('#formAddcost_modal div.g-percentage').hide();
            $('#formAddcost_modal div.g-value').hide();
            $('#formAddcost_modal button.btn-add').html("{{__('common.add')}}");
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

            let selected = $('#formAddcost_modal select[name="conditional_code"]').find(":selected");
            let clculation_type = selected.data('calculation-type');
            let data = {
                id : id,
                conditional_name : $('#formAddcost_modal input[name="conditional_name"]').val() || '',
                calculation_pos : $('#formAddcost_modal input[name="calculation_pos"]').val() || '',
                conditional_code : $('#formAddcost_modal select[name="conditional_code"]').val() || '',
                conditional_type : $('#formAddcost_modal input[name="conditional_type"]').val() || '',
                // percentage : $('#formAddcost_modal input[name="percentage"]').val() || '',
                // value : $('#formAddcost_modal input[name="value"]').val() || '',
                percentage : getAutonumricValue($('#formAddcost_modal input[name="percentage"]')) || '',
                value : getAutonumricValue($('#formAddcost_modal input[name="value"]')) || '',
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
            $('#dt-cost-item tbody .deleteRow').show();
        },
        validateRow : function(){
            var SELF = this;
            let valid = true;
            let conditionalCode = $('#formAddcost_modal select[name="conditional_code"]').val();
            if(!conditionalCode || conditionalCode == ''){
                valid = false;
                showAlert("Name is required", "warning");
            }
            let selected = $('#formAddcost_modal select[name="conditional_code"]').find(":selected");
            let clculation_type = selected.data('calculation-type');

            // let percentage = $('#formAddcost_modal input[name="percentage"]').val();
            let percentage = getAutonumricValue($('#formAddcost_modal input[name="percentage"]'));

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
            $('#formAddcost_modal input[name="id"]').val(dtrow.id);
            $('#formAddcost_modal input[name="conditional_name"]').val(dtrow.conditional_name);
            $('#formAddcost_modal input[name="calculation_pos"]').val(dtrow.calculation_pos);
            $('#formAddcost_modal select[name="conditional_code"]').val(dtrow.conditional_code);
            $('#formAddcost_modal select[name="conditional_code"]').trigger('change');
            $('#formAddcost_modal input[name="percentage"]').val(formatPercentage(dtrow.percentage));
            // $('#formAddcost_modal input[name="value"]').val(dtrow.value);
            $('#formAddcost_modal input[name="value"]').val(formatNumberByCurrency(dtrow.value, getCurrencyCode()));

            $('#formAddcost_modal input[name="calculation_pos"]').val(dtrow.calculation_pos);
            $('#formAddcost_modal button.btn-add').html("{{__('common.update')}}");
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
                action_type : 'save-tender-items',
                stage_type : TabSelected.stageType,
                data_type : 1,
            };

            $.ajax({
                url : "{{ route('tender.save', ['id'=>$id, 'type'=>$type]) }}",
                type : 'POST',
                data : JSON.stringify(params),
                contentType: "application/json; charset=utf-8",
                dataType: "json",
                beforeSend: function( xhr ) {
                    $('#formAddcost_fieldset').attr("disabled",true);
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
                $('#formAddcost_fieldset').attr("disabled",false);
                Loading.Hide();
            });
        },
        onModalShown : function(enable){
            let SELF = this;
            if(!enable){
                $('#formAddcost_modal-save').hide();
            }else{
                $('#formAddcost_modal-save').show();
            }
            SELF.table.column(0).visible(enable);

            var currency_code = $("select[name=currency_code]").val();
            initInputDecimal(currency_code);
            initInputQty();
            initInputPercentage();
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
                    value : getAutonumricValue($('#cost-item input[name="value"]')),
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

            // table item tax
            SELF.TaxTable = new DTTableItem('dt-tax-item');
            SELF.TaxTable.options = {
                columns: [
                    {
                        data: 'id', name: 'id',"width": 50,"className": 'text-center',
                        'visible' : @if($editableItem) true @else false @endif,
                        @if($editableItem)
                            "render": function ( data, type, row ) {
                                let deviate = $('#pr-item select[name="compliance"]').val()=='deviate';
                                return ''+
                                '<a href="" class="col-action col-edit editRow mr-2" '+(deviate?'':'hidden')+'><i class="fa fa-edit"></i></a>'+
                                '<a href="" class="col-action col-delete deleteRow" '+(deviate?'':'hidden')+'><i class="fa fa-trash"></i></a>';
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
                        'visible' : @if($editableItem) true @else false @endif,
                        @if($editableItem)
                            "render": function ( data, type, row ) {
                                let deviate = $('#pr-item select[name="compliance"]').val()=='deviate';
                                return ''+
                                '<a href="" class="col-action col-edit editRow mr-2" '+(deviate?'':'hidden')+'><i class="fa fa-edit"></i></a>'+
                                '<a href="" class="col-action col-delete deleteRow" '+(deviate?'':'hidden')+'><i class="fa fa-trash"></i></a>';
                            },
                        @endif
                    },
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
                        data: 'value', name: 'value',"width": 100,
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

            @if(!$editableItem)
            $('#formItemDetail-save').hide();
            @else
            $('#formItemDetail-save').show();
            // action form
            $('#formItemDetail-save').click(function(e){
                e.preventDefault();
                if(SELF.validateSubmit()){
                    SELF.submit(function(){
                        $('#formItemDetail_modal .close').click();
                        TabSelected.tableItem.ajax.reload();
                        SELF.resetForm();
                    });
                }
            });
            $('textarea[name="item_text"]').on('input keydown keyup focus',function() {
                let lines = inputItemTextLength($(this).val(), 132);
                $(this).val(lines.join(''));
            });
            @endif

            SELF.FormTaxCode.init();
            SELF.FormCost.init();
        },
        onModalShown : function(enable){
            let SELF = this;
            let selectedData = TabSelected.tableItemSelectedRow ? TabSelected.tableItemSelectedRow.data() : null;
            $('#pr-item input[name="qty"]').change(function(e){
                SELF.onChangeSelectedQty(selectedData);
            });
            // $('#pr-item input[name="qty"]').decimalQty(13,3);

            if(!enable){
                $('#formItemDetail-save').hide();
                $('#pr-item select[name="compliance"]').prop('disabled', true);
            }else{
                $('#formItemDetail-save').show();
                if(selectedData.compliance != "deviate"){
                    $('#formItemDetail-save').hide();
                }
                let disable = (selectedData.deleteflg != 'x' && selectedData.deleteflg != 'X') ? false : true;
                $('#pr-item select[name="compliance"]').prop('disabled', disable);
                $('#pr-item select[name="compliance"]').change(function(e){
                    // console.log(TabSelected.tabSelector);
                    if(TabSelected.tabSelector == '#technical-content'){
                        SELF._onTcComplianceChange(e, selectedData);
                    }else if(TabSelected.tabSelector == '#commercial-content'){
                        SELF._onComComplianceChange(e, selectedData);
                    }else if(TabSelected.tabSelector == '#negotiation-content'){
                        SELF._onNegoComplianceChange(e, selectedData);
                    }
                });
                initInputQty();
            }
        },
        _onTcComplianceChange : function(e, selectedData){
            let compliance = $(e.target, 'option:selected').val();
            let description = $('#pr-item input[name="description"]');
            let qty = $('#pr-item input[name="qty"]');
            switch(compliance){
                case 'comply' :
                    description.prop('readonly', true);
                    qty.prop('readonly', true);
                    description.val(selectedData.description);
                    qty.val(formatQty(selectedData.qty, selectedData.currency_code_vendor));
                    $('#formItemDetail_modal textarea[name="item_text"]').prop('disabled', true);
                    break;
                case 'deviate' :
                    description.prop('readonly', false);
                    qty.prop('readonly', selectedData.item_category != 0);
                    description.val(selectedData.description_vendor);
                    qty.val(formatQty(selectedData.qty_vendor, selectedData.currency_code_vendor));
                    $(this).parents('tr').find('input[name="description"]').val(selectedData.description_vendor);
                    $(this).parents('tr').find('input[name="qty"]').val(formatQty(selectedData.qty_vendor));
                    $('#formItemDetail_modal textarea[name="item_text"]').prop('disabled', false);
                    break;
                case 'no_quote' :
                    description.prop('readonly', true);
                    qty.prop('readonly', true);
                    description.val(selectedData.description);
                    qty.val(formatQty(0, selectedData.currency_code_vendor));
                    $('#formItemDetail_modal textarea[name="item_text"]').prop('disabled', true);
                    break;
            }
            $('#formItemDetail-save').show();
        },
        _onComComplianceChange : function(e, selectedData){
            let compliance = $(e.target, 'option:selected').val();
            let est_unit_price = $('#pr-item input[name="est_unit_price"]');
            let overall_limit = $('#pr-item input[name="overall_limit"]');
            // let price_unit = $('#pr-item input[name="price_unit"]');

            est_unit_price.val(formatNumberByCurrency(selectedData.est_unit_price, selectedData.currency_code));
            overall_limit.val(formatNumberByCurrency(selectedData.overall_limit, selectedData.currency_code));
            $('#formItemDetail_modal #cost-item .form-area').prop('hidden', true);
            $('#formItemDetail_modal #tax-item .form-area').prop('hidden', true);
            $('#formItemDetail_modal #cost-item .col-action').prop('hidden', true);
            $('#formItemDetail_modal #tax-item .col-action').prop('hidden', true);
            switch(compliance){
                case 'comply' :
                    est_unit_price.prop('readonly', true);
                    overall_limit.prop('readonly', true);
                    // price_unit.prop('readonly', true);
                    break;
                case 'deviate' :
                    if(selectedData.item_category == 0){
                        est_unit_price.prop('readonly', false);
                        overall_limit.prop('readonly', true);
                    }else{
                        est_unit_price.prop('readonly', true);
                        overall_limit.prop('readonly', false);
                    }
                    // price_unit.prop('readonly', false);
                    est_unit_price.val(formatNumberByCurrency(selectedData.est_unit_price_vendor, selectedData.currency_code_vendor));
                    overall_limit.val(formatNumberByCurrency(selectedData.overall_limit_vendor, selectedData.currency_code_vendor));
                    $('#formItemDetail_modal #cost-item .form-area').prop('hidden', false);
                    $('#formItemDetail_modal #tax-item .form-area').prop('hidden', false);
                    $('#formItemDetail_modal #cost-item .col-action').prop('hidden', false);
                    $('#formItemDetail_modal #tax-item .col-action').prop('hidden', false);
                    break;
                case 'no_quote' :
                    est_unit_price.prop('readonly', true);
                    overall_limit.prop('readonly', true);
                    // price_unit.prop('readonly', true);
                    est_unit_price.val(formatNumberByCurrency(0, selectedData.currency_code_vendor));
                    overall_limit.val(formatNumberByCurrency(0, selectedData.currency_code_vendor));
                    break;
            }
            $('#formItemDetail-save').show();
        },
        _onNegoComplianceChange : function(e, selectedData){
            let compliance = $(e.target, 'option:selected').val();
            let est_unit_price = $('#pr-item input[name="est_unit_price"]');
            let overall_limit = $('#pr-item input[name="overall_limit"]');
            // let price_unit = $('#pr-item input[name="price_unit"]');

            est_unit_price.val(formatNumberByCurrency(selectedData.est_unit_price, selectedData.currency_code));
            overall_limit.val(formatNumberByCurrency(selectedData.overall_limit, selectedData.currency_code));
            $('#formItemDetail_modal #cost-item .form-area').prop('hidden', true);
            $('#formItemDetail_modal #tax-item .form-area').prop('hidden', true);
            $('#formItemDetail_modal #cost-item .col-action').prop('hidden', true);
            $('#formItemDetail_modal #tax-item .col-action').prop('hidden', true);
            switch(compliance){
                case 'comply' :
                    est_unit_price.prop('readonly', true);
                    overall_limit.prop('readonly', true);
                    // price_unit.prop('readonly', true);
                    break;
                case 'deviate' :
                    if(selectedData.item_category == 0){
                        est_unit_price.prop('readonly', false);
                        overall_limit.prop('readonly', true);
                    }else{
                        est_unit_price.prop('readonly', true);
                        overall_limit.prop('readonly', false);
                    }
                    // price_unit.prop('readonly', false);
                    est_unit_price.val(formatNumberByCurrency(selectedData.est_unit_price_vendor, selectedData.currency_code_vendor));
                    overall_limit.val(formatNumberByCurrency(selectedData.overall_limit_vendor, selectedData.currency_code_vendor));
                    $('#formItemDetail_modal #cost-item .form-area').prop('hidden', false);
                    $('#formItemDetail_modal #tax-item .form-area').prop('hidden', false);
                    $('#formItemDetail_modal #cost-item .col-action').prop('hidden', false);
                    $('#formItemDetail_modal #tax-item .col-action').prop('hidden', false);
                    break;
                case 'no_quote' :
                    est_unit_price.prop('readonly', true);
                    overall_limit.prop('readonly', true);
                    // price_unit.prop('readonly', true);
                    est_unit_price.val(formatNumberByCurrency(0, selectedData.currency_code_vendor));
                    overall_limit.val(formatNumberByCurrency(0, selectedData.currency_code_vendor));
                    break;
            }
            $('#formItemDetail-save').show();
        },
        resetForm : function(){
            this.FormTaxCode.resetForm();
            this.FormCost.resetForm();
        },
        reloadTable : function(vendorId, stageType, actionView){
            let selectedData = TabSelected.tableItemSelectedRow ? TabSelected.tableItemSelectedRow.data() : null;
            this.reloadItemText(vendorId, stageType, actionView);
            if(selectedData && selectedData.item_category == 0){
                $('#service-item').hide();
            }else{
                $('#service-item').show();
                if(this.ServiceTable){
                    this.ServiceTable.table.clear().draw();
                    let _url = URLDatatable + '?data_type=1&vendor_id='+vendorId+'&number='+selectedData.number+'&compliance='+selectedData.compliance+'&line_number='+selectedData.line_number;
                    if(actionView){
                        _url = _url + "&actionView=" + actionView;
                    }
                    this.ServiceTable.reload(_url, stageType);
                }
            }
            if(this.TaxTable){
                this.TaxTable.table.clear().draw();
                let _taxUrl = URLDatatable + '?data_type=3&vendor_id='+vendorId+'&compliance='+selectedData.compliance+'&pr_id='+selectedData.id;
                if(actionView){
                    _taxUrl = _taxUrl + "&actionView=" + actionView;
                }
                this.TaxTable.reload(_taxUrl, stageType);
            }
            if(this.CostTable){
                this.CostTable.table.clear().draw();
                let _costUrl = URLDatatable + '?data_type=4&vendor_id='+vendorId+'&compliance='+selectedData.compliance+'&pr_id='+selectedData.id+"&cost_type="+$('#cost-item input[name="conditional_type"]').val();
                if(actionView){
                    _costUrl = _costUrl + "&actionView=" + actionView;
                }
                this.CostTable.reload(_costUrl, stageType);
            }
        },
        reloadItemText : function(vendorId, stageType, actionView){
            let selectedData = TabSelected.tableItemSelectedRow ? TabSelected.tableItemSelectedRow.data() : null;
            let _url = URLDatatable + '?data_type=2&vendor_id='+vendorId+'&pr_id='+selectedData.id;
            if(!stageType || stageType == '') stageType = TabSelected.stageType;
            if(actionView){
                _url = _url + "&actionView=" + actionView;
            }
            $.ajax({
                url : _url + '&action_type=submission-items&compliance='+selectedData.compliance+'&stage_type='+stageType,
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
            }).fail(defaultAjaxFail)
            .always(function(jqXHR, textStatus, errorThrown) {
                Loading.Hide('#text-item textarea[name="item_text"]');
            });
        },
        onChangeSelectedQty : function(dtrow){
            // let qty = $('#pr-item input[name="qty"]').val();
            let qty = getAutonumricValue($('#pr-item input[name="qty"]'));

            if(qty && !validQtyItem(qty)){
                // $('#pr-item input[name="qty"]').val(dtrow.qty_vendor);
                return false;
            }
            return true;
        },
        validateSubmit : function(){
            let SELF = this;
            let valid = true;
            let selectedData = TabSelected.tableItemSelectedRow ? TabSelected.tableItemSelectedRow.data() : null;
            if( SELF.onChangeSelectedQty(selectedData) == false){
                valid = false;
            }
            return valid;
        },
        submit : function(callback)
        {
            let SELF = this;
            let selectedData = TabSelected.tableItemSelectedRow ? TabSelected.tableItemSelectedRow.data() : null;

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
                'vendor_id' : $(TabSelected.tabSelector + ' input[name="vendor_id"]').val(),
                'vendor_code' : $(TabSelected.tabSelector + ' input[name="vendor_code"]').val(),
                item : Object.assign(selectedData, {
                    description_vendor : $('#pr-item input[name="description"]').val() || '',
                    // qty_vendor : $('#pr-item input[name="qty"]').val() || '',
                    qty_vendor : getAutonumricValue($('#pr-item input[name="qty"]')) || '',
                    price_unit_vendor : $('#pr-item input[name="price_unit"]').val() || '',
                    // est_unit_price_vendor : $('#pr-item input[name="est_unit_price"]').val() || '',
                    // overall_limit_vendor : $('#pr-item input[name="overall_limit"]').val() || '',
                    est_unit_price_vendor : getAutonumricValue($('#pr-item input[name="est_unit_price"]')) || '',
                    overall_limit_vendor : getAutonumricValue($('#pr-item input[name="overall_limit"]')) || '',
                    compliance : $('#pr-item select[name="compliance"]').val() || '',
                }),
                cost : additonalCost,
                tax : taxCodes,
                item_text : $('textarea[name="item_text"]').val(),

                action_type : 'save-tender-items',
                stage_type : TabSelected.stageType,
                data_type : 1,
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
    };
</script>
