@extends('layouts.one_column')

@section('contentheader')
    <i class="fa fa-list mr-2"></i>Translation Manager
    {{config('locale')}}
@endsection

@section('contentbody')
    <select id="filename">
        <option value="">-- Choose File --</option>
    @foreach($data[$default] as $file => $deflines)
        <option value="frm{{$file}}">{{$file}}</option>
    @endforeach
    </select><br><br>
    @foreach($data[$default] as $file => $deflines)
    <form id="frm{{$file}}" name="{{$file}}" method="POST" class="frmTranslation" style="display:none">
        <input type="hidden" name="file" value="{{$file}}">@csrf
        @foreach($languages as $language)
        <input type="hidden" name="languages[]" value="{{$language}}">@csrf
        @endforeach
        <table class="table table-sm table-striped table-bordered">
        <tr style="background-color:#ddd" >
            <td colspan="{{1+count($languages)}}">
                <a class="toggle mr-2" data-class="{{$file}}" href="javascript:void(0)"><i class="fas fa-caret-right"></i> <b>File: {{$file}}</b></a>
                <a class="save mr-1" data-class="{{$file}}" href="javascript:void(0)"><i class="fas fa-save"></i></a>
                <a class="addrow mr-1" data-class="{{$file}}" href="javascript:void(0)"><i class="fas fa-plus-square"></i></a>
            </td>
        </tr>
        <tr>
            <th>Key</th>
            @foreach($languages as $language)
            <th>{{$language}}</th>
            @endforeach
        </tr>
        @foreach($deflines as $key=>$deftranslate)
        @include('admin.locale.partials_manager',['key'=>$key,'translate'=>$deftranslate])
        @endforeach
        </table>
    </form>
    @endforeach
@endsection

@section('modules-scripts')
<script>
require(["jquery"], function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $(function(){
        $('#filename').change(function(){
            $('.frmTranslation').hide();
            $('#'+$(this).val()).show();
        });
        $('.toggle').click(function(){
            if($(this).find('i').hasClass('fa-caret-right')){
                $('.'+$(this).data('class')).attr('hidden',false);
                $(this).find('i').removeClass('fa-caret-right').addClass('fa-caret-down');
            }else{
                $('.'+$(this).data('class')).attr('hidden',true);
                $(this).find('i').removeClass('fa-caret-down').addClass('fa-caret-right');
            }
        });
        $('.save').click(function(){
            $('#frm'+$(this).data('class'))
                .attr('method','POST')
                .attr('action',`{{ route('storelocale') }}`)
                .submit();
        });
        $('.addrow').click(function(){
            let key = window.prompt("Please insert language key to add","key");
            if(!!key){
                let row = template(key, $(this).data('class'));
                $('#frm'+$(this).data('class')).find('table').append(row);
            }
        })
        @if(Session::has('success'))
        showAlert("{!!Session::get('success')!!}",'success',2000);
        @php(Session::forget('success'))
        @endif
    });
});
function deleterow(obj){
    if(confirm('Delete key "'+$(obj).next().text()+'" ?')){
        $(obj).closest('tr').remove();
    }
}
function template(key, classname){
    let out = '<tr class="'+classname+'">';
    let key_no_dot = key.replace(/\./g,"][");
        out+='<td style="width:150px;max-width:150px;">';
        out+='<a class="deleterow mr-1" href="javascript:void(0)" onClick="deleterow(this)"><i class="fas fa-trash"></i></a>';
        out+='<span>'+key+'</span>';
        out+='</td>';
        out+='<td><textarea name="{{$default}}['+key_no_dot+']" class="form-control form-control-sm"></textarea></td>';
    @foreach($languages as $language)
    @if($language!==$default)
        out+='<td><textarea name="{{$language}}['+key_no_dot+']" class="form-control form-control-sm"></textarea></td>';
    @endif
    @endforeach
    out+='</tr>';
    return out;
}
</script>
@endsection
