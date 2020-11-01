/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

//Rather, do this:
require.config({
    waitSeconds: 0,
    baseUrl: '/assets/plugins',
    paths: {
        'jquery'                    : 'jquery/jquery-3.3.1.min',
        'architectui'               : '/assets/scripts/theme',
        'bootstrap'                 : 'bootstrap/dist/js/bootstrap.bundle.min',
        'datatables.net'            : 'datatables/DataTables-1.10.20/js/jquery.dataTables.min',
        'datatables.select'         : 'datatables/Select-1.3.1/js/dataTables.select.min',
        'datatablesb4'              : 'datatables/DataTables-1.10.20/js/dataTables.bootstrap4.min',
        'datatables.net-bs4'        : 'datatables/DataTables-1.10.20/js/dataTables.bootstrap4.min',
        'datatables.net-responsive' : 'datatables/Responsive-2.2.3/js/dataTables.responsive.min',
        'dtresponsiveb4'            : 'datatables/Responsive-2.2.3/js/responsive.bootstrap4.min',
        'datatables.fixed-column'   : 'datatables/FixedColumns-3.3.0/js/dataTables.fixedColumns.min',
        'datatables.rows-group'     : 'datatables/dataTables.RowsGroup',
        'bootstrap-fileinput'       : 'bootstrap-fileinput/js/fileinput.min',
        'bootstrap-fileinput-fas'   : 'bootstrap-fileinput/themes/fas/theme.min',
        'bootstrap-util'            : 'bootstrap/js/dist/util',
        'dt.plugin.select'          : 'datatables/Select-1.3.1/js/dataTables.select.min',
        'underscore'                : 'underscore/underscore-min',
        'metisMenu'                 : 'metismenu/metisMenu.min',
        // 'perfect-scrollbar'         : 'perfect-scrollbar.min',
        // 'popper'                    : 'popper.min',
        // 'fullcalendar'              : 'fullcalendar.min',
        'moment'                    : 'moment/moment-with-locales.min',
        'datetimepicker'            : 'datetime/tempusdominus-bootstrap-4.min',
        'select2'                   : 'select2/select2.min',
        'jquery-mask'               : 'jquery-mask/jquery.mask.min',
        'accounting'                : 'accounting/accounting',
        'summernote'                : 'summernote/summernote.min',
        'loadingoverlay'            : 'loadingoverlay.min',
        'autonumeric'               : 'autonumeric',
    },
    shim: {
        'datetimepicker': ['jquery','moment'],
        'metisMenu' : ['jquery'],
        "bootstrap-fileinput-fas" : ["bootstrap-fileinput"],
        "datatables.rows-group" : ["jquery"],
        "autonumeric" : ["jquery"],
    }
});


require(["moment"], function(moment){
    window.moment = moment;
});
require(['jquery',"bootstrap", "bootstrap-util", "metisMenu",'loadingoverlay','moment'],function(){
    require(['datetimepicker']);
});
require(['jquery',"bootstrap",'datatables.net'],function(){
    $("input, select, textarea").each(function(id,el){
        if(el.hasAttribute('required')){
            if($('label[for="'+$(this).attr('id')+'"]').length == 0){
                if($('label[for="'+$(this).attr('name')+'"] .font-danger').length == 0){
                    $('label[for="'+$(this).attr('name')+'"]').append('<span class="font-danger">*</span>');
                }
            }else{
                $('label[for="'+$(this).attr('id')+'"]').append('<span class="font-danger">*</span>');
            }
        }
    });

    jQuery.fn.dataTable.Api.register( 'sum()', function ( ) {
        return this.flatten().reduce( function ( a, b ) {
            if ( typeof a === 'string' ) {
                a = a.replace(/[^\d.-]/g, '') * 1;
            }
            if ( typeof b === 'string' ) {
                b = b.replace(/[^\d.-]/g, '') * 1;
            }

            return a + b;
        }, 0 );
    } );
});

require(["underscore"], function(_){
    window._ = _;
});

require(["accounting"], function(accounting){
    window.accounting = accounting;
    accounting.settings = {
        currency: {
            symbol : "Rp",
            // format: "%s%v",
            decimal : ",",
            thousand: ".",
            precision : 2
        },
        number: {
            precision : 2,
            thousand: ".",
            decimal : ","
        }
    };
});
