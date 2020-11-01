<button id="btn_delete_modal" type="button" data-toggle="modal" data-target="#delete_modal" data-backdrop="static" data-keyboard="false" style="display:none"></button>
<div id="delete_modal" class="modal fade delete_modal" tabindex="-1" role="dialog" aria-labelledby="Hello" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteTitle"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="delete_form" name='delete'>
            <div class="modal-body">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">{{__('homepage.cancel')}}</button>
                <button type="button" id="btn_confirm" class="btn btn-sm btn-primary">{{__('homepage.confirm')}}</button>
            </div>
            </form>
        </div>
    </div>
</div>
