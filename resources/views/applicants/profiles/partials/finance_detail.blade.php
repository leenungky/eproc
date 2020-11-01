@php ($j=0)
@foreach($fields as $field)
<tr>
    @if($j==0) <td class="row{{$i}}" rowspan="{{count($fields)+1}}" class="text-center">{{ $i }}</td> @endif
    <td>{{ __('homepage.'.$field) }}</td>
    <td>
        @if(!is_null($current))
            @if(in_array($field,$attachmentList))
                @if(is_null($current->$field)) '' @else <a href="{{$storage.'/'.$current->$field}}" target="_blank">{{$current->$field}}</a> @endif 
            @else 
                {{ $current->$field ?? '' }} 
            @endif
        @endif
    </td>
    <td>
        @if(!is_null($new))
            @if(in_array($field,$attachmentList))
                @if(is_null($new->$field)) '' @else <a href="{{$storage.'/'.$new->$field}}" target="_blank">{{$new->$field}}</a> @endif 
            @else 
                {{ $new->$field ?? '' }} 
            @endif
        @endif
    </td>
    @if($j==0) <td class="text-center">Prepared</td> @else <td></td> @endif
    @if($j==0)
    <td class="row{{$i}}" rowspan="{{count($fields)+1}}" class="text-center">
        @if(isset($current) && isset($new))
        <button data-id="{{ $new->id }}" data-toggle="modal" data-target=".bd-common-form" data-backdrop="static" data-keyboard="false" onclick="revertEditedData(this);" class="btn btn-sm btn-link" tabindex="Undo Data Edited"><i class="fas fa-undo" aria-hidden="true"></i></button>
        @elseif(isset($current) && !isset($new))
        <button data-id="{{ $current->id }}" data-toggle="modal" data-target=".bd-common-form" data-backdrop="static" data-keyboard="false" onclick="editCurrentData(this);" class="btn btn-sm btn-link"><i class="fas fa-edit" aria-hidden="true"></i></button>
        @elseif(!isset($current) && isset($new))
        <button data-id="{{ $new->id }}" data-toggle="modal" data-target=".bd-common-form" data-backdrop="static" data-keyboard="false" onclick="editAddedData(this);" class="btn btn-sm btn-link" style="padding-left: 0px;"><i class="fas fa-edit" aria-hidden="true"></i></button>
        <button data-id="{{ $new->id }}" data-toggle="modal" data-target=".bd-common-form" data-backdrop="static" data-keyboard="false" onclick="deleteAddedData(this);" class="btn btn-sm btn-link" style="padding: 0px;"><i class="fas fa-trash-alt" aria-hidden="true"></i></button>
        @else
        @endif
    </td>
    @endif
</tr>
@php ($j++)
@endforeach
<tr style="background-color:#ddd">
    <td>{{__('homepage.finance_statement_attachment')}}</td>
    <td></td>
    <td></td>
    <td class="text-center"><a class="fs-showhide" data-index="{{$i}}" data-hidden='true'><i class="fas fa-plus"></i></a></td>
</tr>
@include('applicants.profiles.partials.finance_statement',['data'=>$merge, 'data1'=>$current, 'data2'=>$new, 'spacing'=>''])
<tr style="background-color: #d8d5d5;">
    <td colspan="6" style="padding: 2px;"></td>
</tr>
