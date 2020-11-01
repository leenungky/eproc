<div class="tab-body">
    <div class="has-footer has-tab" style="padding: 0">
        <div id="card-evaluation" class="card">
            <div class="card-header">
                <div class="card-header-left">
                    <span class="heading-title">{{__('tender.process.tab_title_evaluation')}}</span>
                </div>
            </div>
            <div class="card-body">
                <div class="">
                    <table id="dt-evaluation-vendor" class="table table-sm table-bordered table-striped table-vcenter table-wrap">
                        <thead>
                            <tr>
                                @foreach ($tenderData['process_prequalification']['fields3'] as $field)
                                    <th class="{{$field}}">{{__('tender.'.$field)}}</th>
                                @endforeach
                                <th class="status"></th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="app-footer">
        <div class="app-footer__inner">
            <div class="app-footer-left page-number">
                <div class="page_numbers" style="display:inherit"></div>
            </div>
            <div class="app-footer-right">
                <a id="btn_print" target="_blank" href="" class="btn btn-outline-secondary mr-2"><i class="fa fa-file-pdf"></i> {{__('common.print_pq')}}</a>
                <button class="btn btn-outline-secondary btn_evaluate_note">
                    <i class="fa fa-file"></i>&nbsp;&nbsp;&nbsp;{{__('tender.process.btn_evaluation_note')}}</button>

                @if($statusProcess == 'opened-pq')
                <button class="btn btn-success ml-2 btn_finish" @if(!$canFinish) disabled @endif>
                    <i class="fa fa-paper-plane"></i>&nbsp;&nbsp;&nbsp;{{__('tender.process.btn_finish')}}</button>
                @elseif($statusProcess == '')
                    <button class="btn btn-primary btn_next_flow ml-2">
                        {{__('tender.next')}} <i class="fa fa-arrow-right"></i></button>
                @endif
            </div>
        </div>
    </div>
</div>
