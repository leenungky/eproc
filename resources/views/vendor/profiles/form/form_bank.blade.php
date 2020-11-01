<input type="hidden" name="vendor_profile_id" value="{{ $vendor->id }}"/>
<input type="hidden" id="profileId" name="id" value=""/>
<input type="hidden" id="editType" name="edit_type" value=""/>

<div class="page1 display-block">
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
        <input type="text" id="account_number" name="account_number" placeholder="{{__('homepage.account_number')}}" required="required" class="form-control form-control-sm" maxlength="{{ Config::get('tables.vendor_profile_bank_accounts.account_number') }}">
    </div>
</div>
<div class="form-group row mb-2">
    <label for="account_holder_name" class="col-3 col-form-label text-right">{{__('homepage.account_holder_name')}}</label>
    <div class="col-9">
        <input type="text" id="account_holder_name" name="account_holder_name" placeholder="{{__('homepage.account_holder_name')}}" required="required" class="form-control form-control-sm" maxlength="{{ Config::get('tables.vendor_profile_bank_accounts.account_number') }}">
    </div>
</div>
<div class="form-group row mb-2">
    <label for="bank_address" class="col-3 col-form-label text-right">{{__('homepage.bank_address')}}</label>
    <div class="col-9">
        <textarea id="bank_address" name="bank_address" placeholder="{{__('homepage.bank_address')}}" required="required" class="form-control form-control-sm" maxlength="{{ Config::get('tables.vendor_profile_bank_accounts.bank_address') }}"></textarea>
    </div>
</div>
<div class="form-group row mb-2">
    <label for="currency" class="col-3 col-form-label text-right">{{__('homepage.currency')}}</label>
    <div class="col-9">
        <select id="currency" name="currency" class="custom-select custom-select-sm" required="required">
            @foreach($currencies as $key=>$value)
            <option value="{{$key}}">{{$key.' - '.$value}}</option>
            @endforeach
        </select>
    </div>
</div>
</div>
<div class="page2 display-none">
    <table  class="table table-sm table-striped table-bordered">
        <thead>
            <tr>
                <th>{{ __('homepage.attachment') }}</th>
                <th>{{ __('homepage.action') }}</th>
            </tr>
        </thead>
        <tbody>
            <!-- <tr>
                <td colspan="2" class="text-center pad1x" style="padding: 8px; background-color: #fff3f3;"><i>{{ __('homepage.you_should_upload_related_document') }}</i></td>
            </tr> -->
            <tr>
            <td colspan="2" class="text-center pad1x" style="padding: 8px;"><a id="bank_statement_letter_filename" target="_blank"></a></td>
            </tr>
        </tbody>
    </table>
    <div class="form-group-sm row">
        <div class="col-sm-12">
            <input type="file" name="bank_statement_letter" class="form-control form-control-sm" id="bank_statement_letter" placeholder="{{ __('homepage.bank_statement_letter') }}" />
        </div>
    </div>
</div>
