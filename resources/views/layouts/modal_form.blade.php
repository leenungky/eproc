<div id="{{$form_name}}_modal" class="modal fade {{ $data_target }}" tabindex="-1" role="dialog" aria-labelledby="Hello" aria-hidden="true">
    <fieldset id="{{$form_name}}_fieldset">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">{{$title}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="{{__('homepage.cancel')}}">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="{{$form_name}}" name='{{$form_name}}' enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        {!!$contents!!}
                        @if(View::exists($form_layout))
                            @include($form_layout)
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button id="{{$form_name}}-cancel" type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">{{__('homepage.cancel')}}</button>
                        <button id="{{$form_name}}-save" type="button" class="btn btn-sm btn-primary">{{__('homepage.save')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </fieldset>
</div>
