@foreach($data as $key=>$name)
    @if(is_array($name))
        <tr class="fs_detail{{$i}}" hidden>
            <td>{!!$spacing!!}{{ __('homepage.'.$key) }}</td>
            <td class="text-right">{{ null!==$data1 ? $data1->$key ?? '' : ''}}</td>
            <td class="text-right">{{ null!==$data2 ? $data2->$key ?? '' : ''}}</td>
            <td></td>
        </tr>
    @include('applicants.profiles.partials.finance_statement',['data'=>$name, 'data1'=>$data1, 'data2'=>$data2, 'spacing'=>$spacing.'&nbsp;&nbsp;&nbsp;&nbsp;'])
    @else
        <tr class="fs_detail{{$i}}" hidden>
            <td>{!!$spacing!!}{{ __('homepage.'.$name) }}</td>
            <td class="text-right">{{ null!==$data1 ? $data1->$name ?? '' : ''}}</td>
            <td class="text-right">{{ null!==$data2 ? $data2->$name ?? '' : ''}}</td>
            <td></td>
        </tr>
    @endif
@endforeach
