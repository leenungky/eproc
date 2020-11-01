@auth
@section('sidebar')
<div class="app-sidebar__inner">
    <ul class="vertical-nav-menu">
        @if (Auth::user()->user_type === 'vendor')
            <li class="app-sidebar__heading"><i class="fas fa-user mr-2"></i>{{__('homepage.company_profile')}}</li>
            <li>
                <a href="{{ route('profile.edit') }}" class="<?php if(Request::segment(2)=="edit-profile"){ echo "mm-active"; } ?>">
                    <i class="metismenu-icon pe-7s-display2"></i>
                    {{__('homepage.change_profile')}}
                </a>
            </li>
            <li>
                <a href="{{ route('profile.show') }}" class="<?php if(Request::segment(2)=="profile"){ echo "mm-active"; } ?>">
                    <i class="metismenu-icon pe-7s-display2"></i>
                    {{__('homepage.show_profile')}}
                </a>
            </li>
            <li>
                <a href="{{ route('vendor.sanction') }}" class="<?php if(Request::segment(2)=="sanction"){ echo "mm-active"; } ?>">
                    <i class="metismenu-icon pe-7s-display2"></i>
                    {{__('navigation.sanction')}}
                </a>
            </li>
            <li>
                <a href="{{ route('vendor.usermanagement') }}" class="<?php if(Request::segment(2)=="user-managemenr"){ echo "mm-active"; } ?>">
                    <i class="metismenu-icon pe-7s-display2"></i>
                    {{__('homepage.user_management')}}
                </a>
            </li>
            <li class="app-sidebar__heading"><i class="fas fa-bullhorn mr-2"></i>{{__('homepage.announcement')}}</li>
            <li>
                <a href="/">
                    <i class="metismenu-icon pe-7s-display2"></i>
                    {{-- {{__('homepage.general_vendor_info')}} --}}
                    {{__('homepage.announcement_open')}}
                </a>
            </li>
            <li>
                <a href="{{ route('announcement.tender') }}" class="<?php if(Request::segment(2)=="tender"){ echo "mm-active"; } ?>">
                    <i class="metismenu-icon pe-7s-display2"></i>
                    {{__('homepage.tender_invitation')}}
                </a>
            </li>
            <li class="app-sidebar__heading"><i class="fas fa-briefcase mr-2"></i>{{__('homepage.procurement')}}</li>
            <li>
                <a href="{{ route('announcement.tenderFollowed') }}" class="<?php if(Request::segment(2)=="tender-followed"){ echo "mm-active"; } ?>">
                    <i class="metismenu-icon pe-7s-display2"></i>
                    {{__('homepage.tender_followed')}}
                </a>
            </li>

            <li>
                <a href="/">
                    <i class="metismenu-icon pe-7s-display2"></i>
                    {{__('homepage.auctions')}}
                </a>
            </li>

        @else

            <li>
                <a href="{{URL::to('/home')}}">
                    <i class="metismenu-icon fas fa-tv"></i>
                    {{__('navigation.dashboard')}}
                </a>
            </li>
            <li class="<?php if(in_array(Route::currentRouteName(),["admin.vendors","admin.candidates","admin.applicants","vendor.sanction","applicant.profile","candidate.profile"])){ echo "mm-active"; } ?>">
                <a href="#">
                    <i class="metismenu-icon fas fa-cog"></i>
                    {{__('navigation.vendormanagement')}}</a>
                <ul>
                    <li>
                        <a href="{{ route('admin.vendors') }}" class="<?php if(Route::currentRouteName()=="admin.vendors"){ echo "mm-active"; } ?>">
                            <i class="metismenu-icon pe-7s-display2"></i>
                            {{__('navigation.vendor')}}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.candidates') }}" class="<?php if(in_array(Route::currentRouteName(),["admin.candidates","candidate.profile"])){ echo "mm-active"; } ?>">
                            <i class="metismenu-icon pe-7s-display2"></i>
                            {{__('navigation.candidates')}}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.applicants') }}" class="<?php if(in_array(Route::currentRouteName(),["admin.applicants","applicant.profile"])){ echo "mm-active"; } ?>">
                            <i class="metismenu-icon pe-7s-display2"></i>
                            {{__('navigation.applicants')}}
                        </a>
                    </li>
                    <li>
                        <a href="{{route('vendor.sanction')}}" class="<?php if(Route::currentRouteName()=="vendor.sanction"){ echo "mm-active"; } ?>">
                            <i class="metismenu-icon pe-7s-display2"></i>
                            {{__('navigation.sanction')}}
                        </a>
                    </li>
                </ul>
            </li>
            <li class="<?php if(in_array(Route::currentRouteName(),["vendor.evaluation.score","vendor.evaluation.criteria_group","vendor.evaluation.criteria","vendor.evaluation.evaluation","vendor.evaluation.evaluation_detail"])){ echo "mm-active"; } ?>">
                <a href="#">
                <i class="metismenu-icon fas fa-tasks"></i>
                    {{__('navigation.vendor_evaluation')}}</a>
                <ul>
                    <li>
                        <a href="{{route('vendor.evaluation.score')}}" class="<?php if(Route::currentRouteName()=="vendor.evaluation.score"){ echo "mm-active"; } ?>">
                            <i class="metismenu-icon pe-7s-display2"></i>
                            {{__('navigation.score_categories')}}
                        </a>
                    </li>
                    <li>
                        <a href="{{route('vendor.evaluation.criteria_group')}}" class="<?php if(Route::currentRouteName()=="vendor.evaluation.criteria_group"){ echo "mm-active"; } ?>">
                            <i class="metismenu-icon pe-7s-display2"></i>
                            {{__('navigation.criteria_group')}}
                        </a>
                    </li>
                    <li>
                        <a href="{{route('vendor.evaluation.criteria')}}" class="<?php if(Route::currentRouteName()=="vendor.evaluation.criteria"){ echo "mm-active"; } ?>">
                            <i class="metismenu-icon pe-7s-display2"></i>
                            {{__('navigation.criteria')}}
                        </a>
                    </li>
                    <li>
                        <a href="{{route('vendor.evaluation.evaluation')}}" class="<?php if(in_array(Route::currentRouteName(),["vendor.evaluation.evaluation","vendor.evaluation.evaluation_detail"])){ echo "mm-active"; } ?>">
                            <i class="metismenu-icon pe-7s-display2"></i>
                            {{__('navigation.evaluation')}}
                        </a>
                    </li>
                </ul>
            </li>
            @if(\App\Helpers\App::CanTenderManagement())
            <li class="<?php if(in_array(Request::segment(1), ["purchase-requisition","tender"]) ){ echo "mm-active"; } ?>">
                <a href="#">
                    <i class="metismenu-icon fas fa-business-time"></i>
                    {{__('navigation.tendermanagement')}}</a>
                <ul>
                    <li>
                        <a href="{{ route('pr.list') }}" class="<?php if(Request::segment(1)=="purchase-requisition"){ echo "mm-active"; } ?>">
                            <i class="metismenu-icon pe-7s-display2"></i>
                            {{__('navigation.purchaserequisiton')}}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('tender.list') }}" class="<?php if(Request::segment(1)=="tender"){ echo "mm-active"; } ?>">
                            <i class="metismenu-icon pe-7s-display2"></i>
                            {{__('navigation.tender')}}
                        </a>
                    </li>
                </ul>
            </li>
            @endif
            @canAny(['tender_create_po_create','tender_create_po_update', 'tender_create_po_read', 'tender_create_po_delete'])
            <li class="">
                <a href="/po">
                    <i class="metismenu-icon fas fa-business-time"></i>
                    Purchase Order</a>                
            </li>
            @endCan
            @canAny(['user_management','role_management','buyer_management'])
            <li class="<?php if(in_array(Route::currentRouteName(),["personnel.list","role.list","buyer.list"])){ echo "mm-active"; } ?>">
                <a href="#">
                    <i class="metismenu-icon fas fa-user-cog"></i>
                    {{__('navigation.usermanagement')}}
                </a>
                <ul>
                    @can('user_management')
                        <li>
                            <a href="{{ route('personnel.list') }}" class="<?php if(Route::currentRouteName()=="personnel.list"){ echo "mm-active"; } ?>">
                                <i class="metismenu-icon pe-7s-display2"></i>
                                {{__('navigation.user')}}
                            </a>
                        </li>
                    @endCan
                    @can('role_management')
                        <li>
                            <a href="{{ route('role.list') }}" class="<?php if(Route::currentRouteName()=="role.list"){ echo "mm-active"; } ?>">
                                <i class="metismenu-icon pe-7s-display2"></i>
                                {{__('navigation.role')}}
                            </a>
                        </li>
                    @endCan
                    @can('buyer_management')
                        <li>
                            <a href="{{ route('buyer.list') }}" class="<?php if(Route::currentRouteName()=="buyer.list"){ echo "mm-active"; } ?>">
                                <i class="metismenu-icon pe-7s-display2"></i>
                                {{__('navigation.buyer')}}
                            </a>
                        </li>
                    @endCan
                </ul>
            </li>
            @endCanAny
          
            @can('vendor_sanction_modify')
                <li class="<?php if(Route::currentRouteName()=="vendor.sanction_input"){ echo "mm-active"; } ?>">
                    <a href="{{route('vendor.sanction_input')}}">
                        <i class="metismenu-icon fas fa-exclamation-circle"></i>
                        {{__('navigation.input-sanction')}}
                    </a>
                </li>
            @endCan
            @canAny(['user_management'])
                <li class="<?php if(in_array(Route::currentRouteName(),["managepage","legacy","schedule.test"])){ echo "mm-active"; } ?>">
                    <a href="#">
                    <i class="metismenu-icon fas fa-cogs"></i>
                        {{__('navigation.system_management')}}</a>
                    <ul>
                        <li>
                            <a href="{{ route('managepage') }}" class="<?php if(in_array(Route::currentRouteName(),["managepage"])){ echo "mm-active"; } ?>">
                                <i class="metismenu-icon pe-7s-display2"></i>
                                {{__('navigation.page_management')}}
                            </a>
                        </li>
                        @role('Super Admin')
                        <li>
                            <a href="{{ route('legacy') }}" class="<?php if(in_array(Route::currentRouteName(),["legacy"])){ echo "mm-active"; } ?>">
                                <i class="metismenu-icon pe-7s-display2"></i>
                                Legacy
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('schedule.test') }}" class="<?php if(in_array(Route::currentRouteName(),["schedule.test"])){ echo "mm-active"; } ?>">
                                <i class="metismenu-icon pe-7s-display2"></i>
                                Schedule Test
                            </a>
                        </li>
                        @endrole
                    </ul>
                </li>
            @endCanAny

        @endif
    </ul>
</div>
@endsection
@else
@section('sidebar')
<div class="app-sidebar__inner" style="display:flex;flex-direction:column;height:100%">
    <div class="" id="login" style="flex-grow:1">
        <div class="card-header row">{{ __('homepage.login') }}</div>

        <div class="card-body" style="padding-top:1rem;overflow:hidden">
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="form-group row">
                    <div class="col-md-12">
                        <input id="userid" type="text" class="form-control form-control-sm" name="userid" value="{{ old('userid') }}" required autocomplete="userid" autofocus placeholder="{{ __('homepage.userid') }}"/>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-12">
                        <input id="password" type="password" class="form-control form-control-sm" name="password" required autocomplete="current-password" placeholder="{{ __('homepage.password') }}">
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-md-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                            <label class="form-check-label" for="remember">
                                {{ __('homepage.rememberme') }}
                            </label>
                        </div>
                    </div>
                </div>

                <div class="form-group row mb-0">
                    <div class="col-md-12" style="text-align: center;">
                        <button type="submit" class="btn btn-sm btn-primary" style="width: 100%">
                            {{ __('homepage.login') }}
                        </button>

                        @if (Route::has('password.request'))
                        <a class="btn btn-sm btn-link" href="{{ route('password.request') }}">
                            {{ __('homepage.forgot_your_password') }} ?
                        </a>
                        @endif
                        <br>or<br>
                        <a class="btn btn-sm btn-link" href="{{ route('registration') }}">
                            {{ __('homepage.partner_registration') }}
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="recommended text-center" style="padding:15px;font-size:smaller;flex-grow:0">
        <hr>{!! __('navigation.recommended_browser') !!}
    </div>
</div>
@endsection
@endauth
