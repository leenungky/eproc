<div class="row">
    <div class="col-6">
        <input type='hidden' id='userid' name='userid'>
        <div class="form-group row mb-2">
            <label for="text" class="col-3 col-form-label text-right">{{__('personnel.name')}}</label>
            <div class="col-9">
                <input id="name" name="name" placeholder="{{__('personnel.name')}}" type="text" readonly="readonly" class="form-control form-control-sm">
            </div>
        </div>
        <div class="form-group row">
            <label for="new_password" class="control-label col-form-label col-sm-3 text-right">{{__('homepage.new_password')}}</label>
            <div class="col-sm-9 input-group input-group-sm">
                <input type="password" id="new_password" name="new_password" class="form-control form-control-sm" required pattern="^(?=.{8,})(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).*">
                <div class="input-group-append">
                    <i class="togglepassword input-group-text fas fa-eye"></i>
                </div>
            </div>
        </div>
        <div class="form-group row">
            <label for="repeat_new_password" class="control-label col-form-label col-sm-3 text-right">{{__('homepage.repeat_new_password')}}</label>
            <div class="col-sm-9 input-group input-group-sm">
                <input type="password" id="repeat_new_password" name="repeat_new_password" class="form-control form-control-sm" required>
                <div class="input-group-append">
                    <i class="togglepassword input-group-text fas fa-eye"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6">
        {!!__('homepage.password_change_guidelines')!!}
    </div>
</div>
