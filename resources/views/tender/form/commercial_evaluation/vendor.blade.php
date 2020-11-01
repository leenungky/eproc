@section('contentbody')
@php
    $enabledCom = false;
    $arrStatus = \App\Models\TenderVendorSubmission::STATUS;
    if(in_array($statusProcess,['started-4','started-3','opened-3']) &&
    (empty($commercial['submissionData']) || in_array($commercial['submissionData']->status, $arrStatus)))
    {
        $enabledCom=true;
        if($statusProcess == "started-3"){
            $enabledCom= (!empty($workflowValues[2])) ? $workflowValues[2]!='request_resubmission' : true;
        }
    }
@endphp
<div class="tender-content">
    <ul class="nav nav-tabs" id="commercial_evaluation-tab" role="tablist">
        <li class="nav-item" id="overview-li">
            <a class="nav-link" id="overview-tab" data-toggle="tab" href="#overview-content" role="tab"
                aria-controls="overview" aria-selected="true">{{__('tender.process.tab_title_overview')}}</a>
        </li>
        <li class="nav-item" id="commercial-li">
            <a class="nav-link @if($statusProcess == 'registration-') disabled @endif"" id="commercial-tab" data-toggle="tab" href="#commercial-content" role="tab"
                aria-controls="commercial" aria-selected="true">{{__('tender.process.tab_title_document')}}</a>
        </li>
    </ul>
    <div class="tab-content" id="tab-commercial_evaluation">
        <div class="tab-pane fade" id="overview-content" role="tabpanel" aria-labelledby="overview-tab">
            @include('tender.form.commercial_evaluation.vendor_tab_overview')
        </div>
        <div class="tab-pane fade" id="commercial-content" role="tabpanel" aria-labelledby="commercial-tab">
            @include('tender.form.commercial_evaluation.vendor_tab_commercial')
        </div>
    </div>
</div>
@endsection


@section('modules-scripts')
@parent
@include('tender.form.tender_process_vendor')
<script type="text/javascript">
require(['datetimepicker',"bootstrap-fileinput-fas",'autonumeric'], function(datetimepicker){
    var TechnicalType = 3;
    var CommercialType = 4;
    Tabs = $('#commercial_evaluation-tab li > a.nav-link');

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

    var TabCommercial = new TabDocument(Object.assign(
        TabCommercialOptions({{$enabledCom ? 'true' : 'false'}}),
        {
            tabSelector : '#commercial-content',
            stageType : CommercialType,
            dtDocSelector : '#dt-com-document',
            dtItemSelector : '#dt-com-items',
        }
    ));

    @if($commercial['hasDocument'])
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        if(e.target.id == 'commercial-tab'){
            TabSelected = TabCommercial;
            TabCommercial.editable = {{$enabledCom ? 'true' : 'false'}};
            if(TabCommercial.tableDocument == null){
                TabCommercial.initTableDoc({{$enabledCom}});
            }
            if(TabCommercial.tableItem == null){
                TabCommercial.initTableItem({{$enabledCom}});
            }else{
                TabCommercial.tableItem.ajax.reload();
                TabCommercial.tableItem.columns.adjust().draw();
            }
            TenderComments.selector = TabCommercial.tabSelector + ' .btn_comment';
            TenderComments.loadData("{{$vendor->vendor_code}}", TabCommercial.stageType, true);
        }
    });
    @endif
    initTab('commercial_evaluation-tab', {{$enabledCom}});
    TabCommercial.init({{$enabledCom}});
});

</script>
@endsection

