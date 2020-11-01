<main>
    <div class="content">
        {{-- <h3 class="content-title">Persyaratan dan Teknis Harga</h3>
        <div>
            <p>Persyaratan dan Teknis Harga</p>
        </div> --}}

        <h3 class="content-title">{{ __('tender.bidding_document_requirements')}}</h3>
        <div>
            <p>{{ __('tender.bidding_document_requirements')}}</p>
            <table class="tbl1" width="100%" cellpadding="0" cellspacing="0" border="0" style="font-size: 9pt;">
                <thead>
                    <tr>
                        <th style="width: 50px;height: 35px;">No</th>
                        <th style="width: 100px;">{{ __('tender.bidding.fields.description')}}</th>
                        <th style="width: 275px;">{{ __('tender.bidding.fields.stage_type')}}</th>
                        <th>{{ __('tender.bidding.fields.submission_method')}}</th>
                        <th style="width: 100px;">{{ __('tender.bidding.fields.is_required')}}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($bidDocument as $k => $v)
                        <tr>
                            <td style="width: 10%; text-align: center;">{{$k+1}}</td>
                            <td>{{$v->description}}</td>
                            <td>{{ __('tender.status_stage_2.'. \App\Models\TenderWeighting::TYPE[$v->stage_type]) }}</td>
                            <td>{{ __('tender.status_submission.'. \App\Models\TenderWeighting::TYPE[$v->submission_method]) }}</td>
                            <td>{{ $v->is_required ? __('common.yes') : __('common.no') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</main>
