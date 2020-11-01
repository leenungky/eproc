<div id="service-item" class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
            <h6 class="panel-title"><strong>{{__('tender.item_cost.title_service')}}</strong></h6>
            </div>
            <div class="panel-body">
                <div class="" style="padding: 0">
                    <table id="dt-service-item" class="table table-sm table-bordered table-striped table-vcenter"
                        style="width: 100%">
                        <thead>
                            <tr>
                                @foreach ($tenderData['tender_'.$type]['service_fields'] as $field)
                                    <th>{{__('purchaserequisition.'.$field)}}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <hr>
    </div>
</div>
