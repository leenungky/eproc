@section('contentbody')
@php
    $enabledTc = false;
    $enabledCom = false;
    $arrStatus = \App\Models\TenderVendorSubmission::STATUS;
    if($statusProcess == 'started-3' &&
    (empty($technical['submissionData']) || in_array($technical['submissionData']->status, $arrStatus)))
    {
        $enabledTc=true;
    }
    if(in_array($statusProcess, ['started-3','started-4','finish-3']) && // 'opened-3','finish-3'
    (empty($commercial['submissionData']) || in_array($commercial['submissionData']->status, $arrStatus)))
    {
        $enabledCom=true;
        if($statusProcess == "started-3"){
            $enabledCom= (!empty($workflowValues[2])) ? $workflowValues[2]!='request_resubmission' : true;
        }
    }
@endphp
<div class="tender-content">
    <ul class="nav nav-tabs" id="tender_evaluation-tab" role="tablist">
        <li class="nav-item" id="overview-li">
            <a class="nav-link" id="overview-tab" data-toggle="tab" href="#overview-content" role="tab"
                aria-controls="overview" aria-selected="true">{{__('tender.process.tab_title_overview')}}</a>
        </li>
        <li class="nav-item" id="technical-li">
            <a class="nav-link @if($statusProcess == 'registration-') disabled @endif"" id="technical-tab" data-toggle="tab" href="#technical-content" role="tab"
                aria-controls="technical" aria-selected="true">{{__('tender.process.tab_title_technical')}}</a>
        </li>
        <li class="nav-item" id="commercial-li">
            <a class="nav-link @if($statusProcess == 'registration-') disabled @endif"" id="commercial-tab" data-toggle="tab" href="#commercial-content" role="tab"
                aria-controls="commercial" aria-selected="true">{{__('tender.process.tab_title_commercial')}}</a>
        </li>
    </ul>
    <div class="tab-content" id="tab-tender_evaluation">
        <div class="tab-pane fade" id="overview-content" role="tabpanel" aria-labelledby="overview-tab">
            @include('tender.form.tender_evaluation.vendor_tab_overview')
        </div>
        <div class="tab-pane fade" id="technical-content" role="tabpanel" aria-labelledby="technical-tab">
            @include('tender.form.tender_evaluation.vendor_tab_technical')
        </div>
        <div class="tab-pane fade" id="commercial-content" role="tabpanel" aria-labelledby="commercial-tab">
            @include('tender.form.tender_evaluation.vendor_tab_commercial')
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
    Tabs = $('#tender_evaluation-tab li > a.nav-link');

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

    let currencyCode = $('select[name="currency_code"]').val();
    let decimalPlace = (currencyCode && currencyCode == 'IDR') ? 0 : 3;
    let arrFieldNumber = ['est_unit_price_vendor','overall_limit_vendor','est_unit_price','overall_limit','expected_limit'
                ,'price_unit','price_unit_vendor','qty_ordered'];

    var TabTechnical = new TabDocument(Object.assign(
        TabTechnicalOptions({{$enabledTc ? 'true' : 'false'}}),
        {
            tabSelector : '#technical-content',
            stageType : TechnicalType,
            dtDocSelector : '#dt-tc-document',
            dtItemSelector : '#dt-tc-items',
        }
    ));
    var TabCommercial = new TabDocument(Object.assign(
        TabCommercialOptions({{$enabledCom ? 'true' : 'false'}}),
        {
            tabSelector : '#commercial-content',
            stageType : CommercialType,
            dtDocSelector : '#dt-com-document',
            dtItemSelector : '#dt-com-items',
        }
    ));

    initLoad();

    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        if(e.target.id == 'technical-tab'){
            @if($technical['hasDocument'])
            TabSelected = TabTechnical;
            TabTechnical.editable = {{$enabledTc ? 'true' : 'false'}};
            if(TabTechnical.tableDocument == null){
                TabTechnical.initTableDoc({{$enabledTc}});
            }
            if(TabTechnical.tableItem == null){
                TabTechnical.initTableItem({{$enabledTc}});
            }else{
                TabTechnical.tableItem.ajax.reload();
                TabTechnical.tableItem.columns.adjust().draw();
            }
            TenderComments.selector = '#technical-content .btn_comment';
            TenderComments.loadData("{{$vendor->vendor_code}}", TechnicalType, true);
            @endif
        }else
        if(e.target.id == 'commercial-tab'){
            @if($commercial['hasDocument'])
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
            @endif
        }
    });

    initTab('tender_evaluation-tab');
    TabTechnical.init({{$enabledTc}});
    TabCommercial.init({{$enabledCom}});
});
</script>
@endsection

