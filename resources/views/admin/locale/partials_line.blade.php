<tr class="{{$file}}" hidden>
    <td style='width:150px;max-width:150px;'>
    <a class="deleterow mr-1" href="javascript:void(0)" onClick="deleterow(this)"><i class="fas fa-trash"></i></a>
    <span>{{str_replace("][",".",$key)}}</span>
    </td>
    <td><textarea name="{{$default}}[{{$key}}]" class="form-control form-control-sm">{{$translate}}</textarea></td>
    @foreach($languages as $language)
    @if($language!==$default)
    <td><textarea name="{{$language}}[{{$key}}]" class="form-control form-control-sm">{{$data[$language][$file][$key] ?? ''}}</textarea></td>
    @endif
    @endforeach
</tr>
