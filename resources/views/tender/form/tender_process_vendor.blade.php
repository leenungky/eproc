<script type="text/javascript">
    var Tabs = null;
    var TabSelected = null;
    var URL = "{{ route('tender.save', ['id' => $id, 'type' => $type]) }}";
    var URLDatatable = "{{ route('tender.dataItem', ['id' => $id, 'type' => $type]) }}";
    let maxUploadSize = parseInt(`{{ config('eproc.upload_file_size') }}`);
    var fileinputOptions = {'theme': 'fas', 'showUpload':false, 'showPreview':false,'previewFileType':'any', initialPreview : [],initialPreviewConfig: [], maxFileSize : maxUploadSize};
    var firstLoad = true;
    var PRListField = {!! json_encode($tenderData[$type]['prlist']) !!};

    var TabDocument = function(options){
        this.tabSelector = options.tabSelector;
        this.stageType = options.stageType;

        this.dtDocSelector = options.dtDocSelector;
        this.dtItemSelector = options.dtItemSelector;
        this.tableItem = null;
        this.tableItemSelectedRow = null;
        this.tableDocument = null;
        this.editable = false;
        this.submitBatch = options.submitBatch;

        this.errorMessage = '';

        this.init = function(enableDoc){
            var SELF = this;
            // action tableDocument
            SELF.initActionDoc(enableDoc);

            // action tableItem
            SELF.initActionItem(enableDoc);

            @if($tender->conditional_type == 'CT1')
            $('#formAddcost_modal .form-area').prop('hidden', false);
            $('#formAddcost_modal #formAddcost-save').prop('hidden', false);
            if(!enableDoc){
                $('#formAddcost_modal .form-area').prop('hidden', true);
                $('#formAddcost_modal #formAddcost-save').prop('hidden', true);
            }
            @endif

            $(SELF.tabSelector + ' .card-tender-header input').change(function(){
                $(SELF.tabSelector + ' .btn-save-header').prop('disabled', false);
            });
            $(SELF.tabSelector + ' .card-tender-header textarea').change(function(){
                $(SELF.tabSelector + ' .btn-save-header').prop('disabled', false);
            });
            $(SELF.tabSelector + ' .card-tender-header select').change(function(){
                $(SELF.tabSelector + ' .btn-save-header').prop('disabled', false);
            });
            $(SELF.tabSelector + ' .delete-h-file').on('click', function(e){
                e.preventDefault();
                let frmGroup = $(this).parents('.form-group');
                frmGroup.children('.view').attr('hidden', true);
                frmGroup.children('.edit').attr('hidden', false);
                // $(SELF.tabSelector + ' .btn-save-header').prop('disabled', false);
                frmGroup.find('input[type=file]').trigger('click');
            });
            $(SELF.tabSelector + ' .btn-save-header').click(function(e){
                SELF.saveHeader(e);
            });
            $(SELF.tabSelector + ' select[name="currency_code"]').change(function(e){
                SELF.saveHeader(e, function(SELF){
                    SELF.tableItem.ajax.reload();
                });
            });

            $(SELF.tabSelector + ' .btn-save-items').click(function(e){
                SELF.saveItems(e, enableDoc);
            });
            $(SELF.tabSelector + ' .btn_delete_draft').click(function(e){
                $('#delete_modal .modal-title').text("Delete Document");
                $('#delete_modal .modal-body').text("Are you sure to delete Document ?");
                $('#btn_delete_modal').click();
                $('#delete_modal #btn_confirm').off('click').on('click', function (e) {
                    $(SELF.tabSelector + ' .btn_delete_draft').prop('disabled', true);
                    submit({action_type : 'delete-all-submission-detail', 'stage_type' : SELF.stageType},function(){
                        Loading.Show();
                        location.href = "{{ route('tender.show', ['id'=>$id, 'type' => $type]) }}"+ SELF.tabSelector.replace('-content', '');
                        location.reload(true);
                        $(SELF.tabSelector + ' .btn_delete_draft').prop('disabled', false);
                    });
                    return false;
                });
            });
            $(SELF.tabSelector + ' .btn_new_doc').click(function(){
                $(SELF.tabSelector + ' .btn_new_doc').prop('disabled', true);
                let data = {action_type : 'request-submission-detail', stage_type: SELF.stageType};
                submit(data, function(){
                    Loading.Show();
                    location.href = "{{ route('tender.show', ['id'=>$id, 'type' => $type]) }}"+ SELF.tabSelector.replace('-content', '');
                    location.reload(true);
                    $(SELF.tabSelector + ' .btn_new_doc').prop('hidden', true);
                });
            });
            $(SELF.tabSelector + ' .btn_submit').click(function(e){
                e.preventDefault();
                if(SELF.validateSubmit()){
                    $('#delete_modal .modal-title').text("Submit Draft");
                    $('#delete_modal .modal-body').text("Make sure all documents have been uploaded.");
                    $('#btn_delete_modal').click();
                    $('#delete_modal #btn_confirm').off('click').on('click', function () {
                        $(SELF.tabSelector + ' .btn_submit').prop('disabled', true);
                        if(typeof SELF.submitBatch === "function"){
                            return SELF.submitBatch(SELF, function(){
                                submit({action_type : 'submit-submission-detail', 'stage_type' : SELF.stageType},function(){
                                    $('#delete_modal .close').click();
                                    Loading.Show();
                                    location.href = "{{ route('tender.show', ['id'=>$id, 'type' => $type]) }}"+ SELF.tabSelector.replace('-content', '');
                                    location.reload(true);
                                    $(SELF.tabSelector + ' .btn_submit').prop('disabled', false);
                                });
                                return false;
                            });
                        }else{
                            submit({action_type : 'submit-submission-detail', 'stage_type' : SELF.stageType},function(){
                                $('#delete_modal .close').click();
                                Loading.Show();
                                location.href = "{{ route('tender.show', ['id'=>$id, 'type' => $type]) }}"+ SELF.tabSelector.replace('-content', '');
                                location.reload(true);
                                $(SELF.tabSelector + ' .btn_submit').prop('disabled', false);
                            });
                            return false;
                        }
                    });
                }else{
                    showAlert(SELF.errorMessage, "danger", 3000);
                }
            });
            $(SELF.tabSelector + ' .btn_resubmit').click(function(e){
                if(SELF.validateSubmit()){
                    $('#delete_modal .modal-title').text("Resubmit Document");
                    $('#delete_modal .modal-body').text("Make sure all documents have been uploaded.");
                    $('#btn_delete_modal').click();
                    $('#delete_modal #btn_confirm').off('click').on('click', function () {
                        $(SELF.tabSelector + ' .btn_resubmit').prop('disabled', true);

                        if(typeof SELF.submitBatch === "function"){
                            return SELF.submitBatch(SELF, function(){
                                submit({action_type : 'resubmit-submission-detail', 'stage_type' : SELF.stageType},function(){
                                    $('#delete_modal .close').click();
                                    Loading.Show();
                                    location.href = "{{ route('tender.show', ['id'=>$id, 'type' => $type]) }}"+ SELF.tabSelector.replace('-content', '');
                                    location.reload(true);
                                    $(SELF.tabSelector + ' .btn_resubmit').prop('disabled', false);
                                });
                                return false;
                            });
                        }else{
                            submit({action_type : 'resubmit-submission-detail', 'stage_type' : SELF.stageType},function(){
                                $('#delete_modal .close').click();
                                Loading.Show();
                                location.href = "{{ route('tender.show', ['id'=>$id, 'type' => $type]) }}"+ SELF.tabSelector.replace('-content', '');
                                location.reload(true);
                                $(SELF.tabSelector + ' .btn_resubmit').prop('disabled', false);
                            });
                            return false;
                        }
                    });
                }else{
                    showAlert("Please complete the document.", "danger", 3000);
                }
            });
            @if($tender->visibility_bid_document == 'PUBLIC')
            $(SELF.tabSelector + ' .btn_log').on('click',function(e){
                e.preventDefault();
                $('#popup-history').modal('show');
            });
            @endif
            $(SELF.tabSelector + ' .btn_comment').on('click',function(e){
                e.preventDefault();
                $('#popup-comments textarea[name="comments"]').val('');
                $('#popup-comments').modal('show');
                $('#popup-comments .message-list').animate({ scrollTop: 10000 }, 500);
            });


            initInputPercentage();

            $(document).ajaxStop(function() {
                if(firstLoad){
                    SELF.setupButton(SELF, enableDoc);
                    firstLoad = false;
                }
            });
        };
        this.initTableItem = function(enableDoc){
            if(this.tableItem == null){
                options.initTableItem(this);
            }
        };
        this.initTableDoc = function(enableDoc){
            let SELF = this;
            let dtOptions = getDTOptions();
            let _url = "{{ route('tender.dataItem', ['id' => $id, 'type' => $type]) }}?stage_type="+SELF.stageType;
            let options = {
                deferRender: dtOptions.deferRender,
                rowId: dtOptions.rowId,
                lengthChange: false,
                searching: false,
                paging: false,
                info: false,
                language: dtOptions.language,
                ajax : _url + '&action_type=submission-detail',
                fixedColumns: true,
                columns: [
                    {
                        data: 'id', name: 'id',"width": 50,"className": 'text-center',
                        render: function (data, type, row, meta) {
                            return parseInt(meta.row) + parseInt(meta.settings._iDisplayStart) + 1;
                        }
                    },
                    {data: 'description', name: 'description',"width": 300},
                    {data: 'is_required_text', name: 'is_required_text', "width": 100},
                    {
                        data: 'attachment', name: 'attachment',
                        render: function (data, type, row, meta) {
                            if(data && data!= ''){
                                let _tpl = '<a target="_blank" class="btn btn-link float-left" href="{{$storage}}/'+data+'">'+data.fileName()+'</a>';
                                if(enableDoc)
                                    _tpl += '<a href="" class="btn btn-link float-right delete-document"><i class="fa fa-trash"></i></a>';
                                return '<div>' + _tpl + '</div>';
                            }else{
                                let _tpl = '';
                                if(enableDoc){
                                    _tpl = '<div class="input-group input-group-sm">' +
                                        '<div class="custom-file">' +
                                            '<input type="file" id="attachment-'+row.id +'" class="attachment" name="result_attachment" ' +
                                                'class="custom-file-input custom-file-input-sm" data-id="'+row.id +'">' +
                                            '<label id="attachment-'+row.id +'-label" class="custom-file-label" ' +
                                                'for="attachment-'+row.id +'"></label>' +
                                        '</div>' +
                                        '<div class="input-group-prepend"><button class="btn btn-sm btn-success upload" data-id="'+row.id +'">Upload</button></div>'+
                                    '</div>';
                                }
                                return _tpl;
                            }
                        }
                    },
                ],
            };
            //## Initilalize Datatables
            SELF.tableDocument = $(SELF.dtDocSelector).DataTable(options);
            $(".card-tender-document .page_numbers").ready(function () {
                $(SELF.dtDocSelector + "_paginate").appendTo($(".card-tender-document .page_numbers"));
                $(SELF.dtDocSelector+ "_info").css("padding", ".375rem .75rem").appendTo($(".card-tender-document .page_numbers"));
            });
        };
        this.initActionDoc = function(enableDoc){
            let SELF = this;
            $(SELF.dtDocSelector + ' tbody').on('change','.attachment', function(e){
                let id = $(this).data('id');
                let files = $(this).prop('files');
                if(files && files.length > 0){
                    $('#attachment-'+id+'-label').text(files[0].name);
                }
            });
            $(SELF.dtDocSelector + ' tbody').on('click','.upload', function(e){
                let dtrow = SELF.tableDocument.row($(this).parents('tr')).data();
                let file = $(this).parents('div.input-group').find('input[type="file"]')[0].files[0];
                if(file){
                    let formData = new FormData();
                    formData.append('action_type', 'upload-submission-detail');
                    formData.append('stage_type', SELF.stageType);
                    if(dtrow.id != null && dtrow.id != ''){
                        formData.append('id', dtrow.id);
                    }
                    formData.append('line_id', dtrow.line_id);
                    if(dtrow.order != null && dtrow.order != ''){
                        formData.append('order', dtrow.order);
                    }
                    formData.append('attachment', file, setFileName(file, dtrow.description+'_'+dtrow.vendor_code));

                    submitUpload(formData, function(){
                        SELF.tableDocument.ajax.reload(function(json){
                            SELF.setupButton(SELF, enableDoc);
                        });
                    }, SELF.dtDocSelector);
                }else{
                    showAlert("Document is required.", "warning", 3000);
                }
            });
            $(SELF.dtDocSelector + ' tbody').on('click','.delete-document', function(e){
                e.preventDefault();
                let dtrow = SELF.tableDocument.row($(this).parents('tr')).data();
                let selectedRow = SELF.tableDocument.row($(this).parents('tr'));
                dtrow.attachment = '';
                submit({action_type : 'delete-submission-detail',id : dtrow.id, 'stage_type' : SELF.stageType},
                function(response){
                    dtrow.id = response.data.id;
                    selectedRow.data( dtrow ).draw();
                    SELF.setupButton(SELF, enableDoc);
                }, SELF.dtDocSelector);
            });
        };
        this.initActionItem = function(enableDoc){
            let SELF = this;
            // action tableItem
            $(SELF.dtItemSelector + '.btn-save-items').prop('disabled', true);
            $(SELF.dtItemSelector + ' tbody').on('click','.open-detail', function(e){
                e.preventDefault();
                SELF.tableItemSelectedRow = SELF.tableItem.row($(this).parents('tr'));
                SELF.openItemDetailRow(SELF.tableItemSelectedRow.data());
            });
            options.initActionItem(this);
        };
        this.validateSubmit = function(){
            let SELF = this;
            let valid = true;
            if( typeof options.validateSubmit == 'function' ){
                valid = options.validateSubmit(SELF);
            }
            // validate document

            if(valid){
                let dataDoc = SELF.tableDocument ? SELF.tableDocument.rows().data() : [];
                for(let ix=0;ix<dataDoc.length;ix++){
                    if((!dataDoc[ix].attachment || dataDoc[ix].attachment == '') && dataDoc[ix].is_required == true){
                        SELF.errorMessage = "Please complete the document.";
                        valid = false;
                        break;
                    }
                }
            }
            return valid;
        };
        this.saveHeader = function(e, callback){
            let SELF = this;
            let valid = true;
            let inputs = $(SELF.tabSelector + ' .frmTenderHeader').find('input, select');
            if(inputs.length > 0){
                for(let ix=0;ix<inputs.length;ix++){
                    if(inputs[ix].hasAttribute('required') && $(inputs[ix]).val() == ''){
                        showAlert($(SELF.tabSelector + ' label[for="'+$(inputs[ix]).attr('name')+'"]').text() + " is required", "danger", 3000);
                        valid = false;break;
                    }
                }
            }

            // tab technical

            // let tkdnPercentage = $(SELF.tabSelector + ' .frmTenderHeader input[name="tkdn_percentage"]').val();
            let tkdnPercentage = getAutonumricValue($(SELF.tabSelector + ' .frmTenderHeader input[name="tkdn_percentage"]'));
            if(tkdnPercentage && tkdnPercentage > 100){
                showAlert("TKDN Overral Percentage (%) max is 100", "danger", 3000);
                valid = false;
            }

            if(valid){
                options.saveHeader(e, SELF, callback);
            }
        };
        this.saveItems = function(e, enableDoc, callback){
            let SELF = this;

            let _tdata = SELF.tableItem.data();
            let items = [];
            for(let ix=0;ix<_tdata.length;ix++){
                items.push(_tdata[ix]);
            }
            let data = {
                'action_type' : 'save-tender-items',
                'stage_type' : SELF.stageType,
                'vendor_id' : $(SELF.tabSelector + ' input[name="vendor_id"]').val(),
                'vendor_code' : $(SELF.tabSelector + ' input[name="vendor_code"]').val(),
                'items' : items
            };
            $(SELF.tabSelector + ' .btn-save-items').prop('disabled', true);

            submit(data, function(){
                SELF.tableItem.ajax.reload();
                $('.card-tender-item .btn-save-items').prop('disabled', true);
                SELF.setupButton(SELF, enableDoc);
                $(SELF.tabSelector + ' .btn-save-items').prop('disabled', false);
                if(typeof callback === "function"){
                    callback(data, SELF);
                }
            });
        };
        this.setupButton = function(SELF, enableDoc){
            if(enableDoc){
                if(SELF.validateSubmit() == false){
                    $(SELF.tabSelector + ' .btn_submit').prop('disabled', true);
                    $(SELF.tabSelector + ' .btn_resubmit').prop('disabled', true);
                    $(SELF.tabSelector + ' .btn_delete_draft').prop('disabled', true);
                }else{
                    $(SELF.tabSelector + ' .btn_submit').prop('disabled', false);
                    $(SELF.tabSelector + ' .btn_resubmit').prop('disabled', false);
                    $(SELF.tabSelector + ' .btn_delete_draft').prop('disabled', false);
                }
            }
        };

        this.openItemDetailRow = function(dtrow){
            $('#formItemDetail_modal .modal-title').html('PR ' + dtrow.number + ' / ' + dtrow.line_number );
            $('#formItemDetail_modal .title-left').html('PR ' + dtrow.number);
            $('#formItemDetail_modal .title-right').html(dtrow.line_number);
            $('#pr-item input[name="id"]').val(dtrow.id);

            if(typeof options.openItemDetailRow == 'function'){
                options.openItemDetailRow(this, PRListField, dtrow);
            }else{
                for(let ix in PRListField){
                    $('#formItemDetail_modal #pr-item #' + PRListField[ix]).html(dtrow[PRListField[ix]]);
                }
            }
            $('#formItemDetail_modal').modal();
        };
        this.deleteDraft = function(e, callback){
            let SELF = this;
            if(typeof options.deleteHeader == 'function'){
                options.deleteHeader(e, SELF, callback);
            }
        };
    };

    var arrFieldNumber = ['est_unit_price_vendor','overall_limit_vendor','est_unit_price','overall_limit','expected_limit'
                ,'price_unit','price_unit_vendor','qty_ordered'];
    var TabTechnicalOptions = function(enableEdit){
        return {
            initTableItem : function(TabClass){
                let SELF = TabClass;
                let dtOptions = getDTOptions();
                let _url = "{{ route('tender.dataItem', ['id' => $id, 'type' => $type]) }}?stage_type="+SELF.stageType;
                let options = {
                    deferRender: dtOptions.deferRender,
                    rowId: dtOptions.rowId,
                    lengthChange: false,
                    searching: false,
                    processing: true,
                    paging: false,
                    info: false,
                    language: dtOptions.language,
                    ajax : _url + '&action_type=submission-items',
                    fixedColumns: true,
                    drawCallback: function(settings){
                        initInputQty();
                    },
                    // order : [[ 1, "asc" ],[ 2, "asc" ]],
                    columns: [
                        {
                            data: 'id', name: 'id',"width": 15,"className": 'text-center',
                            render: function (data, type, row, meta) {
                                return parseInt(meta.row) + parseInt(meta.settings._iDisplayStart) + 1;
                            }
                        },
                        {data: 'number', name: 'number',"width": 100},
                        {
                            data: 'line_number', name: 'line_number', "width": 100, className : 'td-value',
                            render : function ( data, type, row, dt ) {
                                return '<a href="" class="open-detail" >'+data+'</a>';
                            },
                        },
                        {
                            data: 'description_vendor', name: 'description_vendor', "width": 300,className : 'td-value',
                            render: function ( data, type, row ) {
                                let _tpl = data;
                                if(enableEdit && (row.deleteflg != 'x' && row.deleteflg != 'X')){
                                    let readonly = 'readonly';
                                    if(row.compliance == 'deviate'){
                                        readonly = ''
                                    }
                                    _tpl = '<input name="description" type="description" maxlength="40" class="form-control form-control-sm" '+readonly+' value="'+data+'" />';
                                }
                                return _tpl;
                            },
                        },
                        {
                            data: 'qty_vendor', name: 'qty_vendor', "width": 250,className : 'td-value',
                            render: function ( data, type, row ) {
                                let _dataFormatter = formatQty(data, row.currency_code_vendor);
                                let _tpl = _dataFormatter;
                                if(enableEdit && (row.deleteflg != 'x' && row.deleteflg != 'X')){
                                    let readonly = 'readonly';
                                    if(row.compliance == 'deviate' && row.item_category == 0){
                                        readonly = ''
                                    }
                                    _tpl = '<input name="qty" type="number" data-currency="'+row.currency_code_vendor+'" class="form-control form-control-sm" '+readonly+' value="'+(data || 0)+'" />';
                                }
                                return _tpl;
                            },
                        },
                        {data: 'uom', name: 'uom', "width": 100},
                        {
                            data: 'price_unit', name: 'price_unit', "width": 100,
                            render: function ( data, type, row ) {
                                let total = data || 0;
                                return formatDecimal(total, row.currency_code_vendor);
                            },
                        },
                        {
                            data: 'subtotal', name: 'subtotal', "width": 100,
                            render: function ( data, type, row ) {
                                let _tpl = formatDecimal(data, row.currency_code_vendor);
                                return _tpl;
                            },
                        },
                        {
                            data: 'compliance',name: 'compliance',"width": 150,
                            render: function ( data, type, row ) {
                                let _tpl = row.compliance_text;
                                if(enableEdit && (row.deleteflg != 'x' && row.deleteflg != 'X')){
                                    _tpl = '<div class="form-group no-margin"><select name="compliance" class="custom-select form-control form-control-sm" required>' +
                                        '<option value="">Select...</option>' +
                                        '<option value="comply" '+(data=="comply" ? "selected" : "") +'>Comply</option>' +
                                        '<option value="deviate" '+(data=="deviate" ? "selected" : "")+'>Deviate</option>' +
                                        '<option value="no_quote" '+(data=="no_quote" ? "selected" : "")+'>No Quote</option>' +
                                        '</select></div>';
                                }
                                return _tpl;
                            },
                        },
                        {data: 'deleteflg', name: 'deleteflg', "width": 100, className: 'text-center',render: renderDeleteFlg, visible : false,},
                    ],
                };
                options.createdRow = function(row,data,index){
                    if(data.deleteflg == 'x' || data.deleteflg == 'X'){
                        $(row).addClass("bg-warning");
                    }
                }
                SELF.tableItem = $(SELF.dtItemSelector).DataTable(options);
                $(".card-tender-item .page_numbers").ready(function () {
                    $(SELF.dtItemSelector + "_paginate").appendTo($(".card-tender-item .page_numbers"));
                    $(SELF.dtItemSelector + "_info").css("padding", ".375rem .75rem").appendTo($(".card-tender-item .page_numbers"));
                });
            },
            initActionItem : function(TabClass){
                let SELF = TabClass;
                // action tableItem
                $(SELF.dtItemSelector + ' tbody').on('change','select[name="compliance"]', function(e){
                    let dtrow = SELF.tableItem.row($(this).parents('tr')).data();
                    let selectedRow = SELF.tableItem.row($(this).parents('tr'));
                    let compliance = $(this, 'option:selected').val();
                    let description = $(this).parents('tr').find('input[name="description"]');
                    let qty = $(this).parents('tr').find('input[name="qty"]');

                    switch(compliance){
                        case 'comply' :
                            description.prop('readonly', true);
                            qty.prop('readonly', true);
                            $(this).parents('tr').find('input[name="description"]').val(dtrow.description);
                            $(this).parents('tr').find('input[name="qty"]').val(formatQty(dtrow.qty));
                            break;
                        case 'deviate' :
                            description.prop('readonly', false);
                            qty.prop('readonly', dtrow.item_category != 0);
                            $(this).parents('tr').find('input[name="description"]').val(dtrow.description_vendor);
                            $(this).parents('tr').find('input[name="qty"]').val(formatQty(dtrow.qty_vendor));
                            break;
                        case 'no_quote' :
                            description.prop('readonly', true);
                            qty.prop('readonly', true);
                            $(this).parents('tr').find('input[name="description"]').val(dtrow.description);
                            $(this).parents('tr').find('input[name="qty"]').val(formatQty(0));
                            break;
                    }

                    SELF.tableItem.cell({row: selectedRow.index(), column: 8}).data(compliance);
                    SELF.tableItem.draw();
                    $('.card-tender-item .btn-save-items').prop('disabled', false);
                });
                $(SELF.dtItemSelector + ' tbody').on('change','input[name="description"]', function(e){
                    let selectedRow = SELF.tableItem.row($(this).parents('tr'));
                    let description = $(this,'input[name="description"]').val();
                    SELF.tableItem.cell({row: selectedRow.index(), column: 3}).data(description);
                    SELF.tableItem.draw();
                    $('.card-tender-item .btn-save-items').prop('disabled', false);
                });
                $(SELF.dtItemSelector + ' tbody').on('change','input[name="qty"]', function(e){
                    let selectedRow = SELF.tableItem.row($(this).parents('tr'));
                    // let qty = $(this,'input[name="qty"]').val();
                    let qty = getAutonumricValue($(this,'input[name="qty"]'));

                    SELF.tableItem.cell({row: selectedRow.index(), column: 4}).data(qty);
                    SELF.tableItem.draw();
                    $('.card-tender-item .btn-save-items').prop('disabled', false);
                });
            },
            openItemDetailRow : function(TabClass, fields, dtrow){
                let SELF = TabClass;

                if(dtrow.compliance == 'deviate' && enableEdit && (dtrow.deleteflg != 'x' && dtrow.deleteflg != 'X')){
                    for(let ix in fields){
                        if(fields[ix] == 'qty') {
                            let qtyVendor = formatQty(dtrow.qty_vendor || dtrow.qty);
                            let html = qtyVendor;
                            if(dtrow.item_category == 0){
                                html = '<input name="'+fields[ix]+'" class="form-control form-control-sm" type="number" value="'+(dtrow.qty_vendor || dtrow.qty)+'" />';
                            }else{
                                html = '<input name="'+fields[ix]+'" class="form-control form-control-sm" type="number" readonly value="'+(dtrow.qty_vendor || dtrow.qty)+'" />';
                            }

                            $('#formItemDetail_modal #pr-item #' + fields[ix]).html(html);
                        }else if(fields[ix] == 'description'){ // && dtrow.item_category == 0
                            $('#formItemDetail_modal #pr-item #' + fields[ix])
                                .html('<input name="'+fields[ix]+'" class="form-control form-control-sm" value="'+dtrow.description_vendor+'" />');
                        }else{
                            let fieldValue = dtrow[fields[ix]];
                            if(arrFieldNumber.includes(fields[ix])){
                                fieldValue = formatDecimal(dtrow[fields[ix]], (dtrow.currency_code_vendor || dtrow.currency_code));
                            }
                            $('#formItemDetail_modal #pr-item #' + fields[ix]).html(fieldValue);
                        }
                    }
                    $('#formItemDetail_modal textarea[name="item_text"]').prop('disabled', false);
                }else{
                    for(let ix in fields){
                        if(fields[ix] == 'qty'){
                            let qty = dtrow.compliance == 'no_quote' ? 0 : formatQty(dtrow.qty);
                            let html = qty;
                            if(enableEdit){
                                html = '<input name="'+fields[ix]+'" class="form-control form-control-sm" type="number" readonly value="'+dtrow.qty+'" />';
                            }
                            $('#formItemDetail_modal #pr-item #' + fields[ix]).html(html);
                        }else if(fields[ix] == 'description'){
                            let html = dtrow.description_vendor;
                            if(enableEdit){
                                html = '<input name="'+fields[ix]+'" class="form-control form-control-sm" type="text" readonly value="'+dtrow.description+'" />';
                            }
                            $('#formItemDetail_modal #pr-item #' + fields[ix]).html(html);
                        }else{
                            let fieldValue = dtrow[fields[ix]];
                            if(arrFieldNumber.includes(fields[ix])){
                                fieldValue = formatDecimal(dtrow[fields[ix]], (dtrow.currency_code_vendor || dtrow.currency_code));
                            }
                            $('#formItemDetail_modal #pr-item #' + fields[ix]).html(fieldValue);
                        }
                    }
                    $('#formItemDetail_modal textarea[name="item_text"]').prop('disabled', true);
                }

                $('#formItemDetail_modal #pr-item select[name="compliance"]').val(dtrow.compliance);
                $('#formItemDetail_modal #pr-item select[name="compliance"] option').show();
                $('#formItemDetail_modal #pr-item select[name="compliance"]').prop('disabled', true);

                $('#formItemDetail_modal #tax-item .form-area').prop('hidden', true);
                $('#formItemDetail_modal #cost-item .form-area').prop('hidden', true);

                ItemDetailPage.TaxTable.table.column(0).visible(false);
                ItemDetailPage.CostTable.table.column(0).visible(false);
            },
            saveHeader : function(e, TabClass){
                let SELF = TabClass;
                let formData = new FormData();
                let vendorCode = $(SELF.tabSelector + ' input[name="vendor_code"]').val();
                formData.append('action_type', 'save-tender-header');
                formData.append('stage_type', SELF.stageType);
                formData.append('id', $(SELF.tabSelector + ' input[name="id"]').val());
                formData.append('vendor_id', $(SELF.tabSelector + ' input[name="vendor_id"]').val());
                formData.append('vendor_code', $(SELF.tabSelector + ' input[name="vendor_code"]').val());
                formData.append('quotation_number', $(SELF.tabSelector + ' input[name="quotation_number"]').val());
                formData.append('quotation_date', $(SELF.tabSelector + ' input[name="quotation_date"]').val());
                formData.append('quotation_note', $(SELF.tabSelector + ' textarea[name="quotation_note"]').val());
                // formData.append('tkdn_percentage', $(SELF.tabSelector + ' input[name="tkdn_percentage"]').val() || 0);
                formData.append('tkdn_percentage', getAutonumricValue($(SELF.tabSelector + ' input[name="tkdn_percentage"]')) || 0);
                formData.append('status', $(SELF.tabSelector + ' input[name="status"]').val());

                let suffix = vendorCode+'_'+SELF.stageType;
                let quotation_file = $(SELF.tabSelector + ' input[name="quotation_file"]'); //[0].files[0];
                let proposed_item_file = $(SELF.tabSelector + ' input[name="proposed_item_file"]'); // [0].files[0];
                if(quotation_file[0].files[0]){
                    formData.append('quotation_file', quotation_file[0].files[0], setFileName(quotation_file[0].files[0], 'quotation_file_'+suffix));
                }
                if(proposed_item_file[0] && proposed_item_file[0].files[0]){
                    formData.append('proposed_item_file', proposed_item_file[0].files[0], setFileName(proposed_item_file[0].files[0], 'proposed_item_file_'+suffix));
                }

                @if($tender->tkdn_option == 1)
                let tkdn_file = $(SELF.tabSelector + ' input[name="tkdn_file"]'); // [0].files[0];
                if(tkdn_file[0].files[0]){
                    formData.append('tkdn_file', tkdn_file[0].files[0], setFileName(tkdn_file[0].files[0], 'tkdn_file_'+suffix));
                }
                @endif

                submitUpload(formData, function(){
                    Loading.Show();
                    $(SELF.tabSelector + ' .btn-save-header').prop('disabled', true);
                    location.href = "{{ route('tender.show', ['id'=>$id, 'type' => $type]) }}"+ SELF.tabSelector.replace('-content', '');
                    location.reload(true);
                });
            },
            validateSubmit : function(TabClass){
                let SELF = TabClass;
                // header validation
                let quoNumber = $(SELF.tabSelector + ' input[name="quotation_number"]').val();
                if( !quoNumber || quoNumber == ''){
                    SELF.errorMessage = "{{__('validation.required',['attribute' => __('tender.process.fields.quotation_number')])}}";
                    return false;
                }

                //items validation
                let dataItems = SELF.tableItem ? SELF.tableItem.rows().data() : [];
                for(let ix=0;ix<dataItems.length;ix++){
                    if((dataItems[ix].deleteflg != 'x' && dataItems[ix].deleteflg != 'X')){
                        if(!dataItems[ix].qty_vendor || dataItems[ix].qty_vendor.length > 16){
                            SELF.errorMessage = "Max Qty 13 digit and 3 decimal";
                            return false;
                        }
                        if(!dataItems[ix].compliance || dataItems[ix].compliance == ''){
                            SELF.errorMessage = "Please complete the tender items. Compliance is required";
                            return false;
                        }
                    }
                }
                return true;
            },
        }
    }

    var TabCommercialOptions = function(enableEdit){
        return {
            initTableItem : function(TabClass){
                let SELF = TabClass;
                let dtOptions = getDTOptions();
                let _url = "{{ route('tender.dataItem', ['id' => $id, 'type' => $type]) }}?stage_type="+SELF.stageType;
                let options = {
                    deferRender: dtOptions.deferRender,
                    rowId: dtOptions.rowId,
                    lengthChange: false,
                    searching: false,
                    paging: false,
                    info: false,
                    language: dtOptions.language,
                    ajax : _url + '&action_type=submission-items',
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
                    // scrollY : "800px",
                    scrollX : true,
                    scrollCollapse : true,
                    fixedColumns : true,
                    columns: [
                        {
                            data: 'id', name: 'id',"width": 15,"className": 'text-center',
                            render: function (data, type, row, meta) {
                                return parseInt(meta.row) + parseInt(meta.settings._iDisplayStart) + 1;
                            }
                        },
                        {data: 'number', name: 'number',"width": 25},
                        {
                            data: 'line_number', name: 'line_number', "width": 25,
                            render : function ( data, type, row, dt ) {
                                return '<a href="" class="open-detail" >'+data+'</a>';
                            },
                        },
                        {data: 'description_vendor', name: 'description_vendor', "width": 250},
                        {
                            data: 'qty_vendor', name: 'qty_vendor', "width": 20,
                            render: function (data, type, row, meta) {
                                return formatQty(data, row.currency_code_vendor);
                            }
                        },
                        {data: 'uom', name: 'uom', "width": 20},
                        {
                            data: 'est_unit_price_vendor', name: 'est_unit_price_vendor', "width": 150,
                            render: function ( data, type, row ) {
                                let _tpl = formatCurrency(data, row.currency_code_vendor);
                                if(enableEdit && (row.deleteflg != 'x' && row.deleteflg != 'X')){
                                    let readonly = 'readonly';
                                    if(row.compliance == 'deviate' && row.item_category == 0)readonly = '';
                                    _tpl = '<input name="est_unit_price" type="text" class="form-control form-control-sm" '+readonly+' value="'+data+'" />';
                                }
                                return _tpl;
                            },
                        },
                        {
                            data: 'overall_limit_vendor', name: 'overall_limit_vendor', "width": 150,
                            render: function ( data, type, row ) {
                                let _tpl = formatCurrency(data, row.currency_code_vendor);
                                if(enableEdit && (row.deleteflg != 'x' && row.deleteflg != 'X')){
                                    let readonly = 'readonly';
                                    if(row.compliance == 'deviate' && row.item_category != 0)readonly = '';
                                    _tpl = '<input name="overall_limit" type="text" class="form-control form-control-sm" '+readonly+' value="'+data+'" />';
                                }
                                return _tpl;
                            },
                        },
                        {
                            data: 'price_unit_vendor', name: 'price_unit_vendor', "width": 75,
                            render: function (data, type, row, meta) {
                                let total = data || 0;
                                return formatDecimal(total, row.currency_code_vendor);
                            }
                        },
                        {
                            data: 'subtotal_vendor', name: 'subtotal_vendor', "width": 150,
                            render: function (data, type, row, meta) {
                                return formatCurrency(data, row.currency_code_vendor);
                            }
                        },
                        {data: 'currency_code_vendor', name: 'currency_code_vendor', "width": 40},
                        {
                            data: 'compliance',name: 'compliance',"width": 100,
                            render: function ( data, type, row ) {
                                let _tpl = row.compliance_text;
                                if(enableEdit && (row.deleteflg != 'x' && row.deleteflg != 'X')){
                                    _tpl = '<div class="form-group no-margin"><select name="compliance" class="custom-select form-control form-control-sm" required>' +
                                        '<option value="">Select...</option>' +
                                        '<option value="deviate" '+(data=="deviate" ? "selected" : "")+'>Deviate</option>' +
                                        '<option value="no_quote" '+(data=="no_quote" ? "selected" : "")+'>No Quote</option>' +
                                        '</select></div>';
                                }
                                return _tpl;
                            },
                        },
                        {data: 'deleteflg', name: 'deleteflg', "width": 100, className: 'text-center',render: renderDeleteFlg, visible : false,},
                    ],
                };
                options.createdRow = function(row,data,index){
                    if(data.deleteflg == 'x' || data.deleteflg == 'X'){
                        $(row).addClass("bg-warning");
                    }
                }
                SELF.tableItem = $(SELF.dtItemSelector).DataTable(options);
                $(".card-tender-item .page_numbers").ready(function () {
                    $(SELF.dtItemSelector + "_paginate").appendTo($(".card-tender-item .page_numbers"));
                    $(SELF.dtItemSelector + "_info").css("padding", ".375rem .75rem").appendTo($(".card-tender-item .page_numbers"));
                });
            },
            initActionItem : function(TabClass){
                let SELF = TabClass;
                // action tableItem
                $(SELF.dtItemSelector + ' tbody').on('change','select[name="compliance"]', function(e){
                    let dtrow = SELF.tableItem.row($(this).parents('tr')).data();
                    let selectedRow = SELF.tableItem.row($(this).parents('tr'));
                    let compliance = $(this, 'option:selected').val();
                    let est_unit_price = $(this).parents('tr').find('input[name="est_unit_price"]');
                    let overall_limit = $(this).parents('tr').find('input[name="overall_limit"]');
                    let price_unit = $(this).parents('tr').find('input[name="price_unit"]');

                    $(this).parents('tr').find('input[name="est_unit_price"]').val(formatNumberByCurrency(dtrow.est_unit_price, dtrow.currency_code));
                    $(this).parents('tr').find('input[name="overall_limit"]').val(formatNumberByCurrency(dtrow.overall_limit, dtrow.currency_code));

                    switch(compliance){
                        case 'comply' :
                            est_unit_price.prop('readonly', true);
                            overall_limit.prop('readonly', true);
                            price_unit.prop('readonly', true);
                            break;
                        case 'deviate' :
                            if(selectedRow.data().item_category == 0){
                                est_unit_price.prop('readonly', false);
                                overall_limit.prop('readonly', true);
                            }else{
                                est_unit_price.prop('readonly', true);
                                overall_limit.prop('readonly', false);
                            }
                            price_unit.prop('readonly', false);
                            $(this).parents('tr').find('input[name="est_unit_price"]').val(formatNumberByCurrency(dtrow.est_unit_price_vendor, dtrow.currency_code_vendor));
                            $(this).parents('tr').find('input[name="overall_limit"]').val(formatNumberByCurrency(dtrow.overall_limit_vendor, dtrow.currency_code_vendor));
                            break;
                        case 'no_quote' :
                            est_unit_price.prop('readonly', true);
                            overall_limit.prop('readonly', true);
                            price_unit.prop('readonly', true);
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
                    // let qty = $(this,'input[name="overall_limit"]').val();
                    let qty = getAutonumricValue($(this,'input[name="overall_limit"]'));
                    SELF.tableItem.cell({row: selectedRow.index(), column: 7}).data(qty);
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
            },
            openItemDetailRow : function(TabClass, fields, dtrow){
                let SELF = TabClass;
                if(dtrow.compliance == 'deviate' && enableEdit){
                    for(let ix in fields){
                        let html = '';
                        switch(fields[ix]){
                            case 'qty':
                                let qty_vendor = formatQty(dtrow.qty_vendor, dtrow.currency_code_vendor);
                                $('#formItemDetail_modal #pr-item #' + fields[ix]).html(qty_vendor);
                                break;
                            case 'description':
                                $('#formItemDetail_modal #pr-item #' + fields[ix]).html(dtrow.description_vendor);
                                break;
                            case 'price_unit':
                                let price_unit_vendor = formatDecimal(dtrow.price_unit_vendor, dtrow.currency_code_vendor);
                                $('#formItemDetail_modal #pr-item #' + fields[ix])
                                .html('<input type="hidden" name="'+fields[ix]+'" class="form-control form-control-sm" value="'+dtrow.price_unit_vendor+'" />'+price_unit_vendor);
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
                        let html = '';
                        switch(fields[ix]){
                            case 'qty':
                                $('#formItemDetail_modal #pr-item #' + fields[ix]).html(formatQty(dtrow.qty_vendor, dtrow.currency_code_vendor));
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
                                if(enableEdit && dtrow.item_category == 0){
                                    html = '<input name="'+fields[ix]+'" class="form-control form-control-sm" type="number" readonly value="'+dtrow.est_unit_price_vendor+'" />';
                                }
                                $('#formItemDetail_modal #pr-item #' + fields[ix]).html(html);
                                break;
                            case 'overall_limit':
                                let overall_limit = formatDecimal(dtrow.overall_limit_vendor, dtrow.currency_code_vendor);
                                html = dtrow.overall_limit_vendor;
                                if(enableEdit && dtrow.item_category != 0){
                                    html = '<input name="'+fields[ix]+'" class="form-control form-control-sm" type="number" readonly value="'+dtrow.overall_limit_vendor+'" />';
                                }
                                $('#formItemDetail_modal #pr-item #' + fields[ix]).html(html);
                                break;
                            case 'currency_code':
                                var curr_code = dtrow['currency_code_vendor'] || dtrow['currency_code'];
                                $('#formItemDetail_modal #pr-item #' + fields[ix]).html(curr_code);
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
                $('#formItemDetail_modal #pr-item select[name="compliance"]').val(dtrow.compliance);
                $('#formItemDetail_modal #pr-item select[name="compliance"] option[value="comply"]').hide();
                $('#formItemDetail_modal #pr-item select[name="compliance"]').prop('disabled', true);
                $('#formItemDetail_modal textarea[name="item_text"]').prop('disabled', true);

                @if($editableItem)
                ItemDetailPage.TaxTable.table.column(0).visible(enableEdit);
                ItemDetailPage.CostTable.table.column(0).visible(enableEdit);
                if(FormCostPage.table != null){
                    FormCostPage.table.column(0).visible(enableEdit);
                }
                @endif

                initInputDecimal(dtrow.currency_code_vendor);
                initInputQty();
                initInputPercentage();
            },
            saveHeader : function(e, TabClass, callback){
                let SELF = TabClass;
                let formData = new FormData();
                let vendorCode = $(SELF.tabSelector + ' input[name="vendor_code"]').val();
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

                let suffix = vendorCode+'_'+SELF.stageType;
                let quotation_file = $(SELF.tabSelector + ' input[name="quotation_file"]'); //[0].files[0];
                if(quotation_file[0].files[0]){
                    formData.append('quotation_file', quotation_file[0].files[0], setFileName(quotation_file[0].files[0], 'quotation_file_'+suffix));
                }
                @if($tender->bid_bond == 1)
                let bid_bond_file = $(SELF.tabSelector + ' input[name="bid_bond_file"]'); // [0].files[0];
                if(bid_bond_file[0].files[0]){
                    formData.append('bid_bond_file', bid_bond_file[0].files[0], setFileName(bid_bond_file[0].files[0], 'bid_bond_file_'+suffix));
                }
                formData.append('bid_bond_value', $(SELF.tabSelector + ' input[name="bid_bond_value"]').val());
                formData.append('bid_bond_end_date', $(SELF.tabSelector + ' input[name="bid_bond_end_date"]').val());
                @endif

                submitUpload(formData, function(){
                    $(SELF.tabSelector + ' .btn-save-header').prop('disabled', true);
                    if(typeof callback == 'function'){
                        callback(SELF)
                    }else{
                        Loading.Show();
                        location.href = "{{ route('tender.show', ['id'=>$id, 'type' => $type]) }}"+ SELF.tabSelector.replace('-content', '');
                        location.reload(true);
                    }
                });
            },
            validateSubmit : function(TabClass){
                let SELF = TabClass;
                // header validation
                let quoNumber = $(SELF.tabSelector + ' input[name="quotation_number"]').val();
                if( !quoNumber || quoNumber == ''){
                    SELF.errorMessage = "{{__('validation.required',['attribute' => __('tender.process.fields.quotation_number')])}}";
                    return false;
                }

                //items validation
                let dataItems = SELF.tableItem ? SELF.tableItem.rows().data() : [];
                for(let ix=0;ix<dataItems.length;ix++){
                    if((dataItems[ix].deleteflg != 'x' && dataItems[ix].deleteflg != 'X')){
                        if(!dataItems[ix].compliance || dataItems[ix].compliance == ''){
                            SELF.errorMessage = "Please complete the tender items. Compliance is required";
                            return false;
                        }
                    }
                }
                return true;
            },
        }
    }

    initLoad = function(){
        $('button.btn_next_flow').click(function(){
            onClickNext();
        });
        $(".attachment").fileinput(fileinputOptions);
        $('#popup-comments .btn-save').click(function(e){
            let data = {
                action_type : 'save-comments',
                stage_type : TabSelected.stageType,
                // to : TabSelected.selectedRow.vendor_code,
                comments : $('#popup-comments textarea[name="comments"]').val() || '',
            };
            submit(data, function(response){
                $('#popup-comments textarea[name="comments"]').val('');
                TenderComments.data['{{$vendor->vendor_code}}'] = response.data;
                TenderComments.loadData('{{$vendor->vendor_code}}', TabSelected.stageType);
                $('#popup-comments .message-list').animate({ scrollTop: 10000 }, 500);
            });
            return false;
        });
        $('#popup-history').on("shown.bs.modal", function () {
            let _url = "{{ route('tender.dataItem', ['id' => $id, 'type' => $type]) }}?action_type=submission-history";
            _url += '&stage_type=' + TabSelected.stageType;
            TableHistory.initTable("{{$vendor->id}}", _url);
        });
    };
    submit = function(data, callback, selector){
        let _url = "{{ route('tender.save', ['id'=>$id, 'type'=>$type]) }}";
        $.ajax({
            url : _url,
            type : 'POST',
            data : JSON.stringify(data),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            beforeSend: function( xhr ) {
                Loading.Show(selector);
            }
        }).done(function(response, textStatus, jqXhr) {
            if(response.success){
                if(typeof callback == 'function') callback(response);
                showAlert("Document saved.", "success", 3000);
            }else{
                showAlert("Document not saved. "+response.message, "danger", 3000);
                location.href = "{{ route('tender.show', ['id'=>$id, 'type' => $type]) }}"+ TabSelected.tabSelector.replace('-content', '');
                location.reload(true);
            }
        }).fail(function(jqXHR, textStatus, errorThrown){
            let message = "Data not saved.";
            try{
                console.log(jqXHR);
                let status = jqXHR.status;
                message = jqXHR.responseJSON.message;
                if(status==401){
                    // window.location.href = 'login';
                    // return;
                }
            }catch(e){
                message = jqXHR.status + ' ' + jqXHR.statusText;
            }
            showAlert(message, "danger", 3000);
            // if(typeof callback == 'function') {
            //     callback(jqXHR.responseJSON);
            // }else{

            // }
            location.href = "{{ route('tender.show', ['id'=>$id, 'type' => $type]) }}"+ TabSelected.tabSelector.replace('-content', '');
            location.reload(true);
        })
        .always(function(jqXHR, textStatus, errorThrown) {
            Loading.Hide(selector);
        });
    }
    submitUpload = function(frmData, callback, selector){
        let _url = "{{ route('tender.save', ['id'=>$id, 'type'=>$type]) }}";
        $.ajax({
            url : _url,
            type : 'POST',
            data : frmData,
            cache : false,
            enctype: 'multipart/form-data',
            processData: false,
            contentType: false,
            beforeSend: function( xhr ) {
                Loading.Show(selector);
            }
        }).done(function(response, textStatus, jqXhr) {
            if(response.success){
                if(typeof callback == 'function') callback(response);
                showAlert("Document saved.", "success", 3000);
            }else{
                let message = "Document not saved.";
                if(response.message && response.message != ''){
                    message = response.message;
                };
                showAlert(message, "danger", 3000);
                location.href = "{{ route('tender.show', ['id'=>$id, 'type' => $type]) }}"+ TabSelected.tabSelector.replace('-content', '');
                location.reload(true);
            }
        }).fail(function(jqXHR, textStatus, errorThrown){
            defaultAjaxFail(jqXHR, textStatus, errorThrown, function(){
                location.href = "{{ route('tender.show', ['id'=>$id, 'type' => $type]) }}"+ TabSelected.tabSelector.replace('-content', '');
                location.reload(true);
            })
        })
        // }).fail(defaultAjaxFail)
        .always(function(jqXHR, textStatus, errorThrown) {
            console.log(jqXHR);
            Loading.Hide(selector);
        });
    }
    initTab = function(TabID){
        var urlParams = window.location.href.split('#');
        if(urlParams && urlParams.length > 1){
            $('#' +TabID+ ' li > a#' + urlParams[1] + '-tab.nav-link').tab('show');
        }else{
            $(Tabs[0]).tab('show');
        }
        ItemDetailPage.init();
        $('#formItemDetail_modal').on("shown.bs.modal", function () {
            try{
                ItemDetailPage.resetForm();
                ItemDetailPage.reloadTable('{{$vendor->id}}');
                ItemDetailPage.ForceCloseModal = false;
                ItemDetailPage.onModalShown(TabSelected.editable);
            }catch(e){
                console.error(e);
            }
        });
        @if($tender->conditional_type == 'CT1')
            FormCostPage.init();
            $('#formAddcost_modal').on("shown.bs.modal", function () {
                try{
                    FormCostPage.resetForm();
                    FormCostPage.reloadTable('{{$vendor->id}}');
                    FormCostPage.onModalShown(TabSelected.editable);
                }catch{}
            });
        @endif
    };
    renderDeleteFlg = function(data, type, row, dt){
        let _tpl = data;
        if(data == 'x' || data == 'X'){
            _tpl = '<i class="fa fa-check"></i>';
        }
        return _tpl;
    };
    function setFileName(file, prefixName){
        // let re = /(?:\.([^.]+))?$/;
        // let ext = re.exec(file.name)[1];
        // let fileName = prefixName.replace(/ /g, '_') + '_{{$id}}';
        // return fileName + '.' + ext
        return file.name;
    }
    function validQtyItem(qty){
        var qtyLength = qty.split(".");
        if(typeof qtyLength == 'string' && qtyLength.length <=1){
            qtyLength = qtyLength.split(",");
        }
        if(qty && qty.length > 17){
            // showAlert("Max Qty 13 digit and 3 decimal.", "warning", 3000);
            return false;
        }
        if((qtyLength[0] != undefined && qtyLength[0].length > 13) || (qtyLength[1] != undefined && qtyLength[1].length > 3)){
            // showAlert("Max Qty 13 digit and 3 decimal.", "warning", 3000);
            return false;
        }
        return true;
    };
    function validTkdn(val){
        if(val){
            if(val > 100){
                // showAlert("Max Qty 13 digit and 3 decimal.", "warning", 3000);
                return false;
            }
        }
        return true;
    };
</script>
@include('tender.form.tender_process.items')
