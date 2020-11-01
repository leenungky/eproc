@php
$groupMenus = [
    'tender_process' => ['process_registration','process_prequalification','process_tender_evaluation','process_technical_evaluation','process_commercial_evaluation'],
];
$menus = [];
$grouName = '';
$menuKey = 0;
// dd($pages);
foreach ($pages as $key => $val) {
    $m = [
        'name' => $val,
        'is_active' => $type==$val ? 'mm-active' : '',
        'is_disabled' => !in_array($val,$availablePages) ? 'disabled' : '',
        'children' => false
    ];
    foreach ($groupMenus as $k => $sub) {
        if(in_array($val, $sub)) {
            $grouName = $k;
            break;
        }
    }
    if($grouName != ''){
        if(isset($menus[$menuKey-1]) && $grouName == $menus[$menuKey-1]['name']){
            $menus[$menuKey-1]['children'][] = $m;
            if($m['is_active'] == 'mm-active') $menus[$menuKey-1]['is_active'] = 'mm-active';
            if($m['is_disabled'] == '') $menus[$menuKey-1]['is_disabled'] = '';
        }else {
            $menus[$menuKey] = [
                'name' => $grouName,
                'is_active' => $m['is_active'],
                'is_disabled' => $m['is_disabled'],
                'children' => [$m],
            ];
            $menuKey++;
        }
        $grouName = '';
    }else{
        $menus[$menuKey] = $m;
        $menuKey++;
    }
}

@endphp
<ul id="tender-nav-menu" class="vertical-nav-menu">
    @foreach($menus as $page)
        @if($page['children'] != false)
            <li class="nav-item nav-group {{ $page['is_active'] }}">
                <a class="nav-link not-clickable {{ $page['is_disabled'] }}" aria-disabled="true">{{__('tender.'.$page['name'])}}</a>
            </li>
            @foreach($page['children'] as $subpage)
                {{-- @can('tender_'.$subpage['name'].'_read') --}}
                <li class="nav-item nav-sub">
                    <a class="nav-link {{ $subpage['is_disabled'] }} {{ $subpage['is_active'] }}"
                        href="{{ route('tender.show',['id'=>$tender->id, 'type' => $subpage['name']]) }}">
                        @if(in_array($subpage['name'],['process_technical_evaluation','process_commercial_evaluation']))
                        - {{ __('tender.'.$subpage['name'].'_'.strtolower($tender->submission_method)) }}
                        @else
                        - {{ __('tender.'.$subpage['name']) }}
                        @endif
                    </a>
                </li>
                {{-- @endcan --}}
            @endforeach
        @else
            {{-- @can('tender_'.$page['name'].'_read') --}}
            @if ($page['name'] != "po_creation")
                <li class="nav-item">
                    <a class="nav-link {{ $page['is_disabled'] }} {{ $page['is_active'] }}"
                        href="{{ route('tender.show',['id'=>$tender->id, 'type'=>$page['name']]) }}">
                        {{__('tender.'.$page['name'])}}
                    </a>
                </li>
            @else
                <li class="nav-item">
                    <a class="nav-link {{ $page['is_disabled'] }} {{ $page['is_active'] }}"
                        href="{{ route('po.show_po',['id'=>$tender->id, 'type'=>$page['name']]) }}">
                        {{__('tender.'.$page['name'])}}
                    </a>
                </li>
            @endif
            {{-- @endcan --}}
        @endif
    @endforeach
</ul>

@section('modules-scripts')
@parent
@include('layouts.datatableoption')

<script type="text/javascript">
require(['jquery'], function(){
    $('.tender-back-list').click(function(e){
        e.preventDefault();
        let backUrl = "{{ session()->has('tender_menu_back') ? session()->get('tender_menu_back') : route('tender.list') }}";
        window.location.href = backUrl;
    });
    setScrollTenderlNavMenu();


    $(document).ready(function(e){
        let elmNext = $('#nav-menu-body li.nav-item a.mm-active').parent('li.nav-item').next();
        if(elmNext.length > 0){
            let elmLink = elmNext.find('a.nav-link');
            if(elmLink.length == 0 || elmLink.hasClass('disabled')){
                $('#btn_next_flow').prop('disabled', true);
            }
        }
    })
});
function findTopLeft(element) {
    var rec = element.getBoundingClientRect();
    return {top: rec.top + window.scrollY, left: rec.left + window.scrollX};
}
function setScrollTenderlNavMenu(){
    var el = document.getElementById("nav-menu-body");
    var target = document.querySelector('#nav-menu-body li.nav-item a.mm-active');
    var coordNav = findTopLeft(el);
    var coordTarget = findTopLeft(target);
    el.scrollBy(coordTarget.left, coordTarget.top - (coordNav.top + 100));
}
function onClickNext(){
    let elmNext = $('#nav-menu-body li.nav-item a.mm-active').parent('li.nav-item').next();
    if(elmNext.length > 0){
        let elmLink = elmNext.find('a.nav-link');
        if(elmLink.length > 0 && !elmLink.hasClass('disabled')){
            location.href = elmLink.prop('href');
        }
    }
}
</script>
@endsection
