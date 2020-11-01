@foreach($data as $key=>$name)
    @if(is_array($name))
        <tr class="fs_detail{{$i}}" hidden>
            <td>{!!$spacing!!}{{ __('homepage.'.$key) }}</td>
            <td class="text-right">{{ null!==$data1 ? (isset($data1->$key)?number_format($data1->$key,2,',','.'):'') ?? '' : ''}}</td>
            <td class="text-right">{{ null!==$data2 ? (isset($data2->$key)?number_format($data2->$key,2,',','.'):'') ?? '' : ''}}</td>
            <td></td>
        </tr>
    @include('vendor.profiles.partials.finance_statement',['data'=>$name, 'data1'=>$data1, 'data2'=>$data2, 'spacing'=>$spacing.'&nbsp;&nbsp;&nbsp;&nbsp;'])
    @else
        <tr class="fs_detail{{$i}}" hidden>
            <td>{!!$spacing!!}{{ __('homepage.'.$name) }}</td>
            <td class="text-right">{{ null!==$data1 ? (isset($data1->$name)?number_format($data1->$name,2,',','.'):'') ?? '' : ''}}</td>
            <td class="text-right">{{ null!==$data2 ? (isset($data2->$name)?number_format($data2->$name,2,',','.'):'') ?? '' : ''}}</td>
            <td></td>
        </tr>
    @endif
@endforeach
