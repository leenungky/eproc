<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=0.9">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta HTTP-EQUIV="REFRESH" content="7205,url=/logoutuser">

        <title>{{ config('app.name', 'E-Procurement') }}</title>

        <!-- Scripts -->
        <script src="{{ asset('js/script.js?v=17') }}"></script>
        <script src="{{ asset('assets/plugins/requirejs/require.js') }}"></script>
        <script src="{{ asset('assets/plugins/requirejs/requirejs-config.js?v=14') }}"></script>
        <script src="{{ asset('js/script-event.js?v=15') }}"></script>


        <!-- Additional APP Scripts / Custom -->
        @yield('scripts')

        <!-- Fonts -->
        <link rel="dns-prefetch" href="//fonts.gstatic.com">
        <link rel="icon" href="{{asset('timas-icon.png')}}" type="image/png" sizes="16x16">
        <!-- Styles -->
        <link href="{{ asset('theme.css') }}" rel="stylesheet">
        <link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome/css/fontawesome.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/plugins/bootstrap/dist/css/bootstrap.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/plugins/datatables/datatables.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/plugins/bootstrap-fileinput/css/fileinput-rtl.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/plugins/bootstrap-fileinput/css/fileinput.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/plugins/datetime/tempusdominus-bootstrap-4.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/plugins/select2/select2.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/plugins/select2/select2-bootstrap4.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/plugins/select2/select2-bootstrap.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/plugins/datatables/FixedColumns-3.3.0/css/fixedColumns.bootstrap4.min.css') }}">
        <link href="{{ asset('css/'.(in_array(config('eproc.theme'),['style','style2']) ? config('eproc.theme') : 'style').'.css?v=14') }}" rel="stylesheet">
        @yield('styles')
    </head>
    <body>
        <div class="app-container app-theme-white body-tabs-shadow fixed-sidebar fixed-header @if(is_null(Auth::user())) @if(Route::currentRouteName()=='main')card-light mt-frontpage @endif @endif ">
            <div class="app-header header-shadow bg-night-sky header-text-light">
                <div class="app-header__logo">
                    <a class="navbar-brand" href="{{ url('/') }}"><div class="logo-src"></div></a>
                    @auth
                    <div class="header__pane ml-auto">
                        <div>
                            <button type="button" class="hamburger close-sidebar-btn hamburger--elastic" data-class="closed-sidebar">
                                <span class="hamburger-box">
                                    <span class="hamburger-inner"></span>
                                </span>
                            </button>
                        </div>
                    </div>
                    @endauth
                </div>
                <div class="app-header__mobile-menu">
                    <div>
                        <button type="button" class="hamburger hamburger--elastic mobile-toggle-nav">
                            <span class="hamburger-box">
                                <span class="hamburger-inner"></span>
                            </span>
                        </button>
                    </div>
                </div>
                <div class="app-header__menu">
                    <span>
                        <button type="button" class="btn-icon btn-icon-only btn btn-primary btn-sm mobile-toggle-header-nav">
                            <span class="btn-icon-wrapper">
                                <i class="fa fa-ellipsis-v fa-w-6"></i>
                            </span>
                        </button>
                    </span>
                </div>
                <div class="app-header__content">
                    <div class="app-header-left">
                        <ul class="header-menu nav">
                            <li class="nav-item">
                                <a href="{{ Auth::user() ? route('announcement.open') : route('main') }}" class="nav-link">
                                    <i class="nav-link-icon fa fa-bullhorn"> </i>
                                    {{--__('homepage.home')--}}
                                    {{__('homepage.announcement')}}
                                </a>
                            </li>
                            <li class="btn-group nav-item">
                                <a href="#" id="guide" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link">
                                    <i class="nav-link-icon fa fa-book"></i>
                                    {{__('homepage.guide')}}
                                    <i class="nav-link-icon fa fa-angle-down ml-1"></i>
                                </a>
                                <div class="dropdown-menu" aria-labelledby="guide">
                                    <a class="dropdown-item" href="{{ url('/guide/user_manual') }}">{{ __('homepage.guide_user_manual') }}</a>
                                    <a class="dropdown-item" href="{{ url('/guide/integrity_pact') }}">{{ __('homepage.guide_integrity_pact') }}</a>
                                    <a class="dropdown-item" href="{{ url('/guide/terms_conditions') }}">{{ __('homepage.guide_terms_condition') }}</a>
                                </div>
                            </li>
                            <li class="dropdown nav-item">
                                <a href="#" id="procedure" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link">
                                    <i class="nav-link-icon fa fa-question-circle"></i>
                                    {{__('homepage.procedure')}}
                                    <i class="nav-link-icon fa fa-angle-down ml-1"></i>
                                </a>
                                <div class="dropdown-menu" aria-labelledby="procedure">
                                    <a class="dropdown-item" href="{{ url('/procedure/registration') }}">{{ __('homepage.procedure_registration') }}</a>
                                    <!-- <a class="dropdown-item" href="{{ url('/procedure/qualification') }}">{{ __('homepage.procedure_qualification') }}</a>
                                    <a class="dropdown-item" href="{{ url('/procedure/buying') }}">{{ __('homepage.procedure_buying') }}</a>
                                    <a class="dropdown-item" href="{{ url('/procedure/timas') }}">{{ __('homepage.procedure_timas') }}</a> -->
                                </div>
                            </li>
                            <li class="dropdown nav-item" style="display:none">
                                <a href="#" id="announcement" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link">
                                    <i class="nav-link-icon fa fa-cog"></i>
                                    {{__('homepage.announcement')}}
                                    <i class="nav-link-icon fa fa-angle-down ml-1"></i>
                                </a>
                                <div class="dropdown-menu" aria-labelledby="announcement">
                                    {{-- <a class="dropdown-item" href="{{ url('/announcement/tender') }}">{{ __('homepage.announcement_tender') }}</a> --}}
                                    <a class="dropdown-item" href="{{ url('/announcement/open') }}">{{ __('homepage.announcement_open') }}</a>
                                </div>
                            </li>
                            <li class="dropdown nav-item">
                                <a href="{{ url('/contact') }}" class="nav-link">
                                    <i class="nav-link-icon fa fa-comment"></i>
                                    {{__('homepage.contact')}}
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="app-header-right">
                        <!-- <div class="header-btn-lg pr-0"> -->
                        <div class="widget-content p-0">
                            <div class="widget-content-wrapper">
                                <div class="widget-content-left">
                                    <div class="btn-group">
                                        <a id="navbarDropdown" class="dropdown-toggle nav-link" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                            {{ __('homepage.language') }} <span class="caret"></span>
                                        </a>

                                        <div class="dropdown-menu language dropdown-menu-right" aria-labelledby="navbarDropdown">
                                            <a class="dropdown-item" href="{{ url('/locale/id') }}">{{ __('homepage.indonesian') }}</a>
                                            <a class="dropdown-item" href="{{ url('/locale/en') }}">{{ __('homepage.english') }}</a>
                                        </div>
                                    </div>
                                    <div class="btn-group">
                                        @auth
                                            <a id="navbarDropdownUser" class="dropdown-toggle nav-link header-user-info" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <strong>{{ Auth::user()->name }}</strong> <span class="caret"></span>
                                            </a>
                                            <div class="dropdown-menu user-info dropdown-menu-right" aria-labelledby="navbarDropdownUser">
                                                @if (Auth::user()->user_type=='vendor')
                                                <a class="dropdown-item" href="{{ route('vendor.usermanagement') }}">User Account</a>
                                                @else
                                                <a class="dropdown-item" href="{{ route('personnel.usermanagement') }}">User Account</a>
                                                @endif
                                                <div tabindex="-1" class="dropdown-divider"></div>
                                                <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                                    {{ __('Logout') }}
                                                </a>
                                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                                    @csrf
                                                </form>
                                            </div>
                                        @else
                                            @if (Route::has('register'))
                                            <a class="nav-link <?php
                                            if (Request::segment(1) === "applicants" && Request::segment(2) === "register") {
                                                echo "mm-active";
                                            }
                                            ?>" href="{{ route('registration') }}">{{ __('homepage.register') }}</a>
                                            @endif
                                            <a class="nav-link <?php
                                            if (Request::segment(1) == "login" || Request::segment(1) == "") {
                                                echo "mm-active";
                                            }
                                            ?>" href="{{ route('login') }}">{{ __('homepage.login') }}</a>
                                        @endauth
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- </div>         -->
                    </div>
                </div>
            </div>
            <div class="border5" style="display:none"></div>
            <div class="app-main">
                <div class="app-sidebar sidebar-shadow">
                    <div class="app-header__logo">
                        <div class="logo-src"></div>
                        <div class="header__pane ml-auto">
                            <div>
                                <button type="button" class="hamburger close-sidebar-btn hamburger--elastic" data-class="closed-sidebar">
                                    <span class="hamburger-box">
                                        <span class="hamburger-inner"></span>
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="app-header__mobile-menu">
                        <div>
                            <button type="button" class="hamburger hamburger--elastic mobile-toggle-nav">
                                <span class="hamburger-box">
                                    <span class="hamburger-inner"></span>
                                </span>
                            </button>
                        </div>
                    </div>
                    <div class="app-header__menu">
                        <span>
                            <button type="button" class="btn-icon btn-icon-only btn btn-primary btn-sm mobile-toggle-header-nav">
                                <span class="btn-icon-wrapper">
                                    <i class="fa fa-ellipsis-v fa-w-6"></i>
                                </span>
                            </button>
                        </span>
                    </div>
                    <div class="scrollbar-sidebar @if(Auth::user()) bg-voproc @endif">
                        @yield('sidebar')
                    </div>
                </div>
                <div class="app-main__outer">
                    @if(Route::currentRouteName()=='main' && is_null(Auth::user()))
                    <div class="frontimage"></div>
                    @endif
                    <div class="app-main__inner">
                        @yield('content')
                    </div>
                    <div class="app-wrapper-footer">
                        @yield('footer')
                    </div>
                </div>
            </div>
        </div>
        @yield('modals')

        <script type="text/javascript">
        require(["metisMenu", "autonumeric"],function(){
            $(".vertical-nav-menu").metisMenu();
            $('.hamburger').click(function(){
                $(this).toggleClass("is-active");
                if(document.body.clientWidth<1250){
                    $('.app-container').toggleClass("sidebar-mobile-open");
                }else{
                    $('.app-container').toggleClass("closed-sidebar");
                }
            })
            if(document.body.clientWidth<1250){
                $(".app-container").addClass("closed-sidebar-mobile closed-sidebar");
            }else{
                $(".app-container").removeClass("closed-sidebar-mobile closed-sidebar");
            }
            $('.mobile-toggle-header-nav').click(function(){
                $(this).toggleClass("active");
                $('.app-header__content').toggleClass('header-mobile-open');
            })
        });
        </script>
        @yield('modules-scripts')
    </body>
</html>
