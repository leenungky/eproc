@section('accordionmenu')
@php
    $pages = [
        'administration_data' => [
            'general' => 'general',
            'deeds' => 'deeds',
            'shareholders' => 'shareholders',
            'bod-boc' => 'bod_boc',
            'business-permit' => 'business_permit',
            'pic' => 'person_in_charge',
        ],
        'competency_and_workexperience' => [
            'tools' => 'tools',
            'expert' => 'experts',
            'certification' => 'certifications',
            'competency' => 'competency',
            'work-experience' => 'work_experience',
        ],
        'finance_data' => [
            'bank-account' => 'bank_account',
            'financial' => 'financial_statements',
            'tax-document' => 'tax_documents',
        ]
    ];
@endphp

<div class="accordion">
    @foreach($pages as $page=>$subpages)
    @php ($keys = array_keys($subpages))
    <div class="card">
        <div class="card-header card-header-accordion" id="heading{{$page}}">
            <h5 class="mb-0">
                <a class="btn btn-link" data-toggle="collapse" data-target="#collapse{{$page}}" aria-expanded="true" aria-controls="collapse{{$page}}">
                    {{ __('homepage.'.$page) }}
                </a>
            </h5>
        </div>        
        <div id="collapse{{$page}}" class="collapse {!! in_array(Request::segment(4),$keys) ? 'show' : '' !!}" aria-labelledby="heading{{$page}}" data-parent=".accordion">
            <div class="">
                <ul class="vertical-nav-menu">
                    @foreach($subpages as $url=>$language)
                    <li><a class="btn btn-link {!! Request::segment(4) === $url ? 'mm-active' : '' !!}" data-url="{{ $url }}" href="{{ route(($registrationStatus ?? 'candidate').'.profile-detail', ['vendorid' => $candidate->vendor_id, 'submenu' => $url]) }}">{{ __('homepage.'.$language) }}</a></li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection
