@extends('layouts.one_column')

@section('contentheader')
{{ __('homepage.login') }}
@endsection

@section('contentbody')
@if (session('login_error'))
    <div class="alert alert-danger">
        {{ session('login_error') }}
    </div>
@endif
@php
session()->forget('login_error');
@endphp
<div class="p-4">
    <form method="POST" action="{{ route('login') }}">
        @csrf
        
        <div class="form-group row">
            <label for="userids" class="col-md-4 col-form-label text-md-right">{{ __('homepage.userid') }}</label>

            <div class="col-md-6">
                <input id="userids" type="text" class="form-control form-control-sm @error('userid') is-invalid @enderror" name="userid" value="{{ old('userid') }}" required autocomplete="userid" autofocus />

                @error('userid')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>

        <div class="form-group row">
            <label for="passwords" class="col-md-4 col-form-label text-md-right">{{ __('homepage.password') }}</label>

            <div class="col-md-6">
                <input id="passwords" type="password" class="form-control form-control-sm @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>

        <div class="form-group row">
            <div class="col-md-6 offset-md-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                    <label class="form-check-label" for="remember">
                        {{ __('homepage.rememberme') }}
                    </label>
                </div>
            </div>
        </div>

        <div class="form-group row mb-0">
            <div class="col-md-8 offset-md-4">
                <button type="submit" class="btn btn-sm btn-primary">
                    {{ __('homepage.login') }}
                </button>

                @if (Route::has('password.request'))
                    <a class="btn btn-sm btn-link" href="{{ route('password.request') }}">
                        {{ __('homepage.forgot_your_password') }} ?
                    </a>
                @endif
            </div>
        </div>
    </form>
</div>
@endsection
