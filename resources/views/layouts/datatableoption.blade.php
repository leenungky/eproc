<script type="text/javascript">
toggledDTButtons = "";
function getDTOptions(){ 
    return {
            processing: true,
            serverSide: true,
            deferRender: true,
            searchDelay: 500,
            info:false,
            select: {
                style: 'multi',
                info: false
            },
            ajax: {
                url: "",
                type: "POST"
            },
            rowId: 'id',
            columns: [],
            columnDefs: [],
            language:{
                "emptyTable":     "{{__('datatable.emptyTable')}}",
                "info":           "{{__('datatable.info')}}",
                "infoEmpty":      "{{__('datatable.infoEmpty')}}",
                "infoFiltered":   "{{__('datatable.infoFiltered')}}",
                "lengthMenu":     "{{__('datatable.lengthMenu')}}",
                "loadingRecords": "{{__('datatable.loadingRecords')}}",
                "processing":     "{{__('datatable.processing')}}",
                "search":         "{{__('datatable.search')}}",
                "zeroRecords":    "{{__('datatable.zeroRecords')}}",
                "paginate": { 
                    "first":      "{{__('datatable.first')}}",
                    "last":       "{{__('datatable.last')}}",
                    "next":       "{{__('datatable.next')}}",
                    "previous":   "{{__('datatable.previous')}}",
                },
            },
            drawCallback: function (settings) {
                var api = this.api();
                if(typeof(table)!='undefined'){
                    $("#input-page").val(table.page() + 1).attr('max', api.page.info().pages ? api.page.info().pages : 9999);
                }
            },
    }    
}
</script>