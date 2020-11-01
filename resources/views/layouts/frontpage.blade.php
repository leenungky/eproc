@extends('layouts.app')

@include('layouts.navigation')

@section('content')
<div class="card card-menu">
    <div class="card-header" style="padding-top:9px">
        {{ __('homepage.login') }}
    </div>
    <div id="nav-menu-body" class="card-body p-4">
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
        <div class="recommended text-center" style="padding:15px 0;font-size:smaller;flex-grow:0">
            <hr>{!! __('navigation.recommended_browser') !!}
        </div>
    </div>
</div>
<div class="card card-content">
    <div class="card-header">
        @yield('contentheader')
    </div>
    <div class="card-body">
        @yield('contentbody')
    </div>
</div>

@endsection
