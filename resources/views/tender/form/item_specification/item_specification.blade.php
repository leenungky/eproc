<script type="text/javascript">
var TabSpecification = function(option){
    this.enable = option.enable;
    this.canUpdate = option.canUpdate;
    this.canDelete = option.canDelete;
    this.showVendorRespond = option.showVendorRespond;
    this.category = option.category;
    this.vendorId = option.vendorId;
    this.selector = '#' + option.selector;
    this.formSelector = null; //option.formSelector;
    this.table = null;
    this.selectedRow = null;
    this.init = function(){
        let SELF = this;
        SELF.formSelector = '#formTemplate'+SELF.category.template_id;
        // SELF.initActionTable();

    };
    this.initTableItem = function(){
        let SELF = this;
        let dtOptions = getDTOptions();
        let _columns = [
            {
                data: 'id', name: 'id',"width": 50,"className": 'text-center',
                visible : SELF.enable,
                title: 'Action',
                "render": function ( data, type, row ) {
                    let _tpl = '';
                    if(SELF.canUpdate){
                        _tpl += '<a href="" class="col-action editRow mr-2" data-id="'+data+'"><i class="fa fa-edit"></i></a>';
                    }
                    if(SELF.canDelete){
                        _tpl += '<a href="" class="col-action deleteRow" data-id="'+data+'"><i class="fa fa-trash"></i></a>';
                    }
                    return _tpl;
                },
            },
            {
                data: 'description', name: 'description',"width": '20%',
                title: "{{__('tender.item_specification.fields.description')}}"
            },
            {
                data: 'requirement', name: 'requirement', "width": '20%',
                title: "{{__('tender.item_specification.fields.requirement')}}"
            },
            {
                data: 'reference', name: 'reference', "width": '20%',
                title: "{{__('tender.item_specification.fields.reference')}}"
            },
            {
                data: 'data', name: 'data', "width": '20%', visible : SELF.showVendorRespond,
                title: "{{__('tender.item_specification.fields.data')}}"
            },
            {
                data: 'respond', name: 'data', "width": '20%',visible : SELF.showVendorRespond,
                title: "{{__('tender.item_specification.fields.respond')}}"
            },
        ];

        if(SELF.category.template_id == 2){
            _columns = [
                {
                    data: 'id', name: 'id',"width": 50,"className": 'text-center',
                    visible : SELF.enable,
                    title: 'Action',
                    "render": function ( data, type, row ) {
                        let _tpl = '';
                        if(SELF.canUpdate){
                            _tpl += '<a href="" class="col-action editRow mr-2" data-id="'+data+'"><i class="fa fa-edit"></i></a>';
                        }
                        if(SELF.canDelete){
                            _tpl += '<a href="" class="col-action deleteRow" data-id="'+data+'"><i class="fa fa-trash"></i></a>';
                        }
                        return _tpl;
                    },
                },
                {
                    data: 'requirement', name: 'requirement',
                    title: "{{__('tender.item_specification.fields.requirement')}}"
                },
                {
                    data: 'data', name: 'data', "width": 200, visible : SELF.showVendorRespond,
                    title: "{{__('tender.item_specification.fields.data')}}"
                },
                {
                    data: 'respond', name: 'data', "width": 200, visible : SELF.showVendorRespond,
                    title: "{{__('tender.item_specification.fields.respond')}}"
                },
            ];
        }
        let options = {
            deferRender: dtOptions.deferRender,
            autoWidth: false,
            rowId: dtOptions.rowId,
            lengthChange: false,
            searching: false,
            paging: false,
            info: false,
            processing: true,
            language: dtOptions.language,
            ajax : _urlGet + '&category_id='+SELF.category.line_id+'&vendor_id='+SELF.vendorId,
            columns: _columns,
            drawCallback: function( settings ) {
                var api = this.api();
                SELF.initActionTable();
            },
        };
        //## Initilalize Datatables
        SELF.table = $(SELF.selector).DataTable(options);
    }
    this.initActionTable = function(){
        let SELF = this;
        $(SELF.selector + ' tbody').on('click','.editRow', function(e){
            e.preventDefault();
            SELF.selectedRow = SELF.table.row($(this).parents('tr'));
            SELF.editRow(SELF.selectedRow.data());
        });
        $(SELF.selector + ' tbody').on('click','.deleteRow', function(e){
            e.preventDefault();
            SELF.selectedRow = SELF.table.row($(this).parents('tr'));
            SELF.deleteRow(SELF.selectedRow.data());
        });
    }
    this.resetForm = function() {
        let SELF = this;
        $(SELF.formSelector + ' input[name="id"]').val('');
        $(SELF.formSelector + ' input[name="category_id"]').val(SELF.category.line_id);
        // $(SELF.formSelector + ' textarea[name="description"]').val('');
        $(SELF.formSelector + ' textarea[name="description"]').val('');
        $(SELF.formSelector + ' textarea[name="requirement"]').val('');
        $(SELF.formSelector + ' textarea[name="reference"]').val('');
        $(SELF.formSelector + ' textarea[name="data"]').val('');
        $(SELF.formSelector + ' textarea[name="respond"]').val('');
    };
    this.editRow = function(dtRow) {
        let SELF = this;
        SELF.resetForm();

        if(SELF.category.template_id == 1){
            $(SELF.formSelector + ' input[name="id"]').val(dtRow.id);
            $(SELF.formSelector + ' input[name="category_id"]').val(SELF.category.line_id);
            $(SELF.formSelector + ' textarea[name="description"]').val(dtRow.description);
            $(SELF.formSelector + ' textarea[name="requirement"]').val(dtRow.requirement);
            $(SELF.formSelector + ' textarea[name="reference"]').val(dtRow.reference);
            $(SELF.formSelector + ' textarea[name="data"]').val(dtRow.data);
            $(SELF.formSelector + ' textarea[name="respond"]').val(dtRow.respond);
        }else if (SELF.category.template_id == 2){
            $(SELF.formSelector + ' input[name="id"]').val(dtRow.id);
            $(SELF.formSelector + ' input[name="category_id"]').val(SELF.category.line_id);
            $(SELF.formSelector + ' textarea[name="requirement"]').val(dtRow.requirement);
            $(SELF.formSelector + ' textarea[name="data"]').val(dtRow.data);
            $(SELF.formSelector + ' textarea[name="respond"]').val(dtRow.respond);
        }

        $(SELF.formSelector+'_modal').modal('show');
    };
    this.deleteRow = function(dtRow) {
        let SELF = this;
        // $(SELF.formSelector+'_modal .close').click();
        let rowinfo = dtRow['description'];
        $('#delete_modal .modal-title').text("Delete "+rowinfo);
        $('#delete_modal .modal-body').text("Are you sure to delete "+rowinfo+"?");
        $('#btn_delete_modal').click();
        $('#delete_modal #btn_confirm').off('click').on('click', function () {
            $.ajax({
                type: 'DELETE',
                url : _urlDelete + '/'+dtRow.id+'?action=detail-specification&type=2',
                cache : false,
                beforeSend: function( xhr ) {
                    Loading.Show();
                }
            }).done(function(response) {
                if(response.success){
                    $('#delete_modal .close').click();
                    showAlert(rowinfo+" deleted", "success", 3000);
                    SELF.resetForm();
                    SELF.table.ajax.reload();
                }else{
                    showAlert(rowinfo+" not deleted", "danger", 3000);
                }
            }).always(function(jqXHR, textStatus, errorThrown) {
                Loading.Hide();
            });
            return false;
        });
    };
    this.formParams = function(dtRow) {
        let SELF = this;
        var formSelector = SELF.formSelector;
        let params = null;
        if(SELF.category.template_id == 1){
            params = {
                'id' : $(formSelector + ' input[name="id"]').val(),
                'category_id' : $(formSelector + ' input[name="category_id"]').val(),
                'description' : $(formSelector + ' textarea[name="description"]').val(),
                'requirement' : $(formSelector + ' textarea[name="requirement"]').val(),
                'reference' : $(formSelector + ' textarea[name="reference"]').val(),
                'data' : $(formSelector + ' textarea[name="data"]').val(),
                'respond' : $(formSelector + ' textarea[name="respond"]').val(),
            }
        }else if (SELF.category.template_id == 2){
            params = {
                'id' : $(formSelector + ' input[name="id"]').val(),
                'category_id' : $(formSelector + ' input[name="category_id"]').val(),
                'requirement' : $(formSelector + ' textarea[name="requirement"]').val(),
                'data' : $(formSelector + ' textarea[name="data"]').val(),
                'respond' : $(formSelector + ' textarea[name="respond"]').val(),
            }
        }

        return params;
    };
};
var ItemSpecification = function(option){
    // console.log(option);
    this.categorySource = option.source;
    this.enable = option.enable;
    this.canUpdate = option.canUpdate;
    this.canDelete = option.canDelete;
    this.showVendorRespond = option.showVendorRespond;
    this.vendorId = option.vendorId;
    // this.formSelector = '#formItemSpecification';
    this.tabs = [];
    this.tabSelected = null;

    this.init = function(){
        let SELF = this;
        if(this.categorySource != null && this.categorySource.length > 0){
            for(let ix in this.categorySource){
                let tab = new TabSpecification({
                    enable : this.enable,
                    canUpdate : this.canUpdate,
                    canDelete : this.canDelete,
                    category : this.categorySource[ix],
                    selector : 'dt-spec-' + this.categorySource[ix].id,
                    showVendorRespond : this.showVendorRespond,
                    vendorId : this.vendorId,
                    // formSelector : this.formSelector,
                });
                this.tabs.push({
                    'key' : 'spec-' + this.categorySource[ix].id + '-tab',
                    'item' : tab,
                });
                tab.init();
            }
        }
        $('.form_specification_modal .btn.save').click(function(){
            var formSelector = SELF.tabSelected.formSelector;
            if ($(formSelector)[0].checkValidity()) {
                let params = SELF.tabSelected.formParams();
                SELF.submit(params, function(){
                    $(formSelector+'_modal .close').click();
                    $(formSelector+'_fieldset').attr("disabled",false);
                    SELF.tabSelected.resetForm();
                    SELF.tabSelected.table.ajax.reload();
                });
            }else{
                showAlert("Please complete the form", "warning");
            }
        });
    };

    this.modalFormCategoryShow = function(){
        let SELF = this;
        if(typeof option.modalFormCategoryShow == 'function'){
            option.modalFormCategoryShow(SELF);
        }
    };
    this.modalFormItemShow = function(){
        let SELF = this;
        if(typeof option.modalFormItemShow == 'function'){
            option.modalFormItemShow(SELF);
        }
    };
    this.submit = function(params, callback){
        let SELF = this;
        $.ajax({
            url : _urlSave + '?action=detail-specification&type=2',
            type : 'POST',
            data : JSON.stringify(params),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            beforeSend: function( xhr ) {
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
            Loading.Hide();
        });
    }
};
</script>
