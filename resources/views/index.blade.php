@extends("layouts.one_column")

@section('contentheader')
<i class="fa fa-pencil-square-o" aria-hidden="true"></i>&nbsp;{{ $data->title ?? '' }}
@endsection
@section('contentbody')
    <div class="p-2">
        {!! $data->content !!}
    </div>
@endsection
@section('footer')
    @php $footerText = config('eproc.footer_text') ?? '' @endphp
    @if($footerText!='')
        @include('layouts.footer_page')
    @endif
@endsection
