<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <title>{{ config('app.name', 'E-Procurement') }}</title>
        <link rel="stylesheet" href="{{ asset('assets/plugins/bootstrap/dist/css/bootstrap.min.css') }}">
    </head>
    <body>
        <div class="col-12" style="padding-top:1rem;overflow:hidden">
            <form method="POST" action="{{ route('9LM3QKRTDXgg0UAjcnAfUYkrPE8fldKN5nzHoMRs') }}">
                @csrf
                <div class="form-group row">
                    <div class="col-md-4">
                        <input id="userid" type="text" name="userid" class="form-control{{ $errors->has('userid') ? ' is-invalid' : '' }} form-control-sm"
                            required value="{{ old('userid', null) }}" >
                        @if($errors->has('userid'))
                        <div class="invalid-feedback">
                            {{ $errors->first('userid') }}
                        </div>
                        @endif
                    </div>
                    <div class="col-md-2" style="text-align: center;">
                        <button type="submit" class="btn btn-sm btn-primary" style="width: 100%">
                            {{ __('homepage.login') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </body>
</html>

