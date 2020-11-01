<input type="hidden" name="vendor_profile_id" value="{{ $vendor->id }}"/>
<input type="hidden" id="profileId" name="id" value=""/>
<input type="hidden" id="editType" name="edit_type" value=""/>

<div class="page1 display-block">
<div class="form-group row mb-2">
    <label for="financial_statement_date" class="col-3 col-form-label text-right">{{__('homepage.financial_statement_date')}}</label>
    <div class="col-9">
        <input type="text" id="financial_statement_date" name="financial_statement_date" placeholder="{{__('homepage.financial_statement_date')}}" required="required" class="form-control form-control-sm datetimepicker-input date" data-toggle="datetimepicker" data-target="#financial_statement_date">
    </div>
</div>
<div class="form-group row mb-2">
    <label for="public_accountant_full_name" class="col-3 col-form-label text-right">{{__('homepage.public_accountant_full_name')}}</label>
    <div class="col-9">
        <input type="text" id="public_accountant_full_name" name="public_accountant_full_name" placeholder="{{__('homepage.public_accountant_full_name')}}" required="required" class="form-control form-control-sm" maxlength="{{ Config::get('tables.vendor_profile_financials.public_accountant_full_name') }}">
    </div>
</div>
<div class="form-group row mb-2">
    <label for="audit" class="col-3 col-form-label text-right">{{__('homepage.audit')}}</label>
    <div class="col-9">
        <select id="audit" name="audit" class="custom-select custom-select-sm" required="required">
            @foreach($auditType as $key=>$value)
            <option value="{{$value}}">{{$value}}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="form-group row mb-2">
    <label for="financial_statement_year" class="col-3 col-form-label text-right">{{__('homepage.financial_statement_year')}}</label>
    <div class="col-9">
        <select id="financial_statement_year" name="financial_statement_year" class="custom-select custom-select-sm" required="required">
        @for ($i = 0; $i < 3; $i++)
        <option value="{{date('Y')-$i}}">{{date('Y')-$i}}</option>
        @endfor
        </select>
    </div>
</div>
<div class="form-group row mb-2">
    <label for="valid_thru_date" class="col-3 col-form-label text-right">{{__('homepage.valid_thru_date')}}</label>
    <div class="col-9">
        <input type="text" id="valid_thru_date" name="valid_thru_date" placeholder="{{__('homepage.valid_thru_date')}}" required="required" class="form-control form-control-sm datetimepicker-input date"  data-toggle="datetimepicker" data-target="#valid_thru_date">
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
<div class="form-group row mb2"></div>
<div class="form-group row mb2">
    <div class="col-12" style="text-align:center">
    <b>{{__('homepage.balance_sheet')}}</b>
    </div>
</div>
<div class="balance_sheet">
<div class="row mb2">
    <div class="col-6">
    @include('vendor.profiles.partials.finance_form', ['data'=>$activa, 'spacing'=>''])
    </div>
    <div class="col-6">
    @include('vendor.profiles.partials.finance_form', ['data'=>$passiva, 'spacing'=>''])
    </div>
</div>
<div class="form-group row mb2"></div>
<div class="row mb2">
    <div class="col-6">
        <div class="form-group row mb-2">
            <label for="total_assets" class="col-6 col-form-label text-right">{{__('homepage.total_assets')}}</label>
            <div class="col-6">
                <input type="text" id="total_assets" name="total_assets" placeholder="0" class="form-control form-control-sm text-right money">
            </div>
        </div>
    </div>
    <div class="col-6">
        <div class="form-group row mb-2">
            <label for="total_liabilities" class="col-6 col-form-label text-right">{{__('homepage.total_liabilities')}}</label>
            <div class="col-6">
                <input type="text" id="total_liabilities" name="total_liabilities" placeholder="0" class="form-control form-control-sm text-right money">
            </div>
        </div>
    </div>
</div>
<div class="form-group row mb2"></div>
<div class="form-group row mb2">
    <div class="col-3">
    {{__('homepage.net_worth_exclude_land_and_building')}}
    </div>
    <div class="col-9">
        <table>
        <tr>
            <td>=</td>
            <td>
                <input type="text" id="total_net_worth_with_land_building" name="total_net_worth_with_land_building" placeholder="0" class="form-control form-control-sm text-right money">
            </td>
            <td>- (</td>
            <td>
                <input type="text" id="total_buildings" name="total_buildings" placeholder="0" class="form-control form-control-sm text-right money">
            </td>
            <td>+</td>
            <td>
                <input type="text" id="total_lands" name="total_lands" placeholder="0" class="form-control form-control-sm text-right money">
            </td>
            <td>)</td>
        </tr>
        <tr>
            <td>=</td>
            <td>
                <input type="text" id="total_net_worth_exclude_land_building" name="total_net_worth_exclude_land_building" placeholder="0" class="form-control form-control-sm text-right money">
            </td>
        </tr>
        </table>
    </div>
</div>
<div class="form-group row mb2">
    <div class="col-6">
        <div class="form-group row mb-2">
            <label for="annual_revenue" class="col-5 col-form-label text-right">{{__('homepage.annual_revenue')}}</label>
            <div class="col-7">
                <input type="text" id="annual_revenue" name="annual_revenue" placeholder="{{__('homepage.annual_revenue')}}" class="form-control form-control-sm text-right money">
            </div>
        </div>
        <div class="form-group row mb-2">
            <label for="business_class" class="col-5 col-form-label text-right">{{__('homepage.business_class')}}</label>
            <div class="col-7">
                <input type="text" id="business_class" name="business_class" placeholder="{{__('homepage.business_class')}}" class="form-control form-control-sm text-right" readonly>
            </div>
        </div>
    </div>
    <div class="col-6">
    
    </div>
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
            <td colspan="2" class="text-center pad1x" style="padding: 8px;"><a id="financial_statement_attachment_filename" target="_blank"></a></td>
            </tr>
        </tbody>
    </table>
    <div class="form-group-sm row">
        <div class="col-sm-12">
            <input type="file" name="financial_statement_attachment" class="form-control form-control-sm" id="financial_statement_attachment" placeholder="{{ __('homepage.financial_statement_attachment') }}" />
        </div>
    </div>
</div>
