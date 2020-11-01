<input type="hidden" name="applicant_id" value="{{ $applicant->id }}"/>
<input type="hidden" id="profileId" name="id" value=""/>
<input type="hidden" id="editType" name="edit_type" value=""/>
<div class="form-group row mb-2">
    <label for="bank_name" class="col-3 col-form-label text-right">{{__('homepage.bank_name')}}</label>
    <div class="col-9">
        <select id="bank_name" name="bank_name" class="custom-select custom-select-sm" required="required">
            @foreach($banks as $key=>$value)
            <option value="{{$key}}">{{$value}}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="form-group row mb-2">
    <label for="account_number" class="col-3 col-form-label text-right">{{__('homepage.account_number')}}</label>
    <div class="col-9">
        <input type="text" id="account_number" name="account_number" placeholder="{{__('homepage.account_number')}}" required="required" class="form-control form-control-sm">
    </div>
</div>
<div class="form-group row mb-2">
    <label for="account_holder_name" class="col-3 col-form-label text-right">{{__('homepage.account_holder_name')}}</label>
    <div class="col-9">
        <input type="text" id="account_holder_name" name="account_holder_name" placeholder="{{__('homepage.account_holder_name')}}" required="required" class="form-control form-control-sm">
    </div>
</div>
<div class="form-group row mb-2">
    <label for="bank_address" class="col-3 col-form-label text-right">{{__('homepage.bank_address')}}</label>
    <div class="col-9">
        <textarea id="bank_address" name="bank_address" placeholder="{{__('homepage.bank_address')}}" required="required" class="form-control form-control-sm"></textarea>
    </div>
</div>
<div class="form-group row mb-2">
    <label for="currency" class="col-3 col-form-label text-right">{{__('homepage.currency')}}</label>
    <div class="col-9">
        <select id="currency" name="currency" class="custom-select custom-select-sm" required="required">
            @foreach($currencies as $key=>$value)
            <option value="{{$key}}">{{$value}}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="form-group row mb-2">
    <label for="bank_statement_letter" class="col-3 col-form-label text-right">{{__('homepage.bank_statement_letter')}}</label>
    <div class="col-9">
        <div class="custom-file">
            <input type="file" id="bank_statement_letter" name="bank_statement_letter" required="required" class="custom-file-input custom-file-input-sm">
            <label id="bank_statement_letter_label" class="custom-file-label" for="bank_statement_letter">{{__('homepage.bank_statement_letter')}}</label>
        </div>
        <a target="_blank" id="bank_statement_letter_filename"></a>
    </div>
</div>
