<div class="row">
    <div class="col-sm-6">
        <div class="form-group row">
            {!! Html::decode(Form::label('vendor_group', __('homepage.vendor_group'), ['class' => 'control-label col-form-label col-sm-4'])) !!}
            <div class="col-sm-8">
                <div class="custom-control custom-radio custom-control-inline">
                    <input name="vendor_group" id="vendor_group_0" type="radio" class="custom-control-input form-control" value="local" required=""> 
                    <label for="vendor_group_0" class="custom-control-label">{{__('homepage.local')}}</label>
                </div>
                <div class="custom-control custom-radio custom-control-inline">
                    <input name="vendor_group" id="vendor_group_1" type="radio" class="custom-control-input form-control" value="foreign" required="">
                    <label for="vendor_group_1" class="custom-control-label">{{__('homepage.foreign')}}</label>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6"></div>
    <div class="col-sm-12 mb-3 mt-1">
        <div class="hrline"></div>
    </div>        
    <div class="col-sm-6">
        <div class="form-group row">
            {!! Html::decode(Form::label('company_type_id', __('homepage.'.'company_type').' '.'', ['class' => 'control-label col-form-label col-sm-4'])) !!}
            <div class="col-sm-8">
                <select name="company_type_id" id="company_type_id" class="form-control form-control-sm full-width" required="true">
                    <option value="" selected="">-- Select --</option>
                    @foreach($selectCompanyType as $company)
                    <option data-category="{{ $company->category }}" value="{{ $company->id }}">{{ $company->company_type }} - {{ $company->description }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group row">
            {!! Html::decode(Form::label('vendor_name', __('homepage.'.'vendor_name').' '.'', ['class' => 'control-label col-form-label col-sm-4'])) !!}
            <div class="col-sm-8">
                {{ Form::text('vendor_name', null, array_merge(['class' => 'form-control form-control-sm'], ['id'=>'vendor_name', 'required' => true, 'autocomplete'=>'off', 'maxlength' => Config::get('tables.vendors.vendor_name')])) }}
            </div>
        </div>
        <div class="form-group row">
            {!! Html::decode(Form::label('president_director', __('homepage.'.'president_director').' '.'', ['class' => 'control-label col-form-label col-sm-4'])) !!}
            <div class="col-sm-8">
                {{ Form::text('president_director', null, array_merge(['class' => 'form-control form-control-sm'], ['id'=>'president_director', 'required' => true, 'autocomplete'=>'off', 'maxlength' => Config::get('tables.vendors.president_director')])) }}
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group row">
            {!! Html::decode(Form::label('purchase_org_id', __('homepage.'.'purchasing_organization').' '.'', ['class' => 'control-label col-form-label col-sm-4'])) !!}
            <div class="col-sm-8">
                {{ Form::select('purchase_org_id', $selectPurchasingOrg, '', ['placeholder' => '-- Please select --', 'class' => 'form-control form-control-sm full-width', 'required' => true]) }}
            </div>
        </div>                
    </div>
    <div class="col-sm-12 mb-3 mt-3">
        <div class="hrline"></div>
    </div>        
    <div class="col-sm-6">
        @php 
        $attributes = [
            [
                'id' => 'street','placeholder'=>__('homepage.street'),'required'=>'true', 'type'=>'input', 'autocomplete'=>'off', 'maxlength' => Config::get('tables.vendors.street')
            ],[
                'id' => 'address_1','placeholder'=>__('homepage.address_1'),'required'=>'true', 'type'=>'input', 'autocomplete'=>'off', 'maxlength' => Config::get('tables.vendors.address_1')
            ],[
                'id' => 'building_name','placeholder'=>__('homepage.building_name'), 'type'=>'input', 'autocomplete'=>'off', 'maxlength' => Config::get('tables.vendors.building_name')
            ],[
                'id' => 'kavling_floor_number','placeholder'=>__('homepage.kavling_number_floor'), 'type'=>'input', 'autocomplete'=>'off', 'maxlength' => Config::get('tables.vendors.kavling_floor_number')
            ],[
                'id' => 'address_2','placeholder'=>__('homepage.address_2'),'required'=>'true', 'type'=>'input', 'autocomplete'=>'off', 'maxlength' => Config::get('tables.vendors.address_2')
            ],[
                'id' => 'address_3','placeholder'=>__('homepage.address_3'),'required'=>'true', 'type'=>'input', 'autocomplete'=>'off', 'maxlength' => Config::get('tables.vendors.address_3')
            ],[
                'id' => 'village','placeholder'=>__('homepage.village'),'required'=>'true', 'type'=>'input', 'autocomplete'=>'off', 'maxlength' => Config::get('tables.vendors.village')
            ],[
                'id' => 'rt','placeholder'=>__('homepage.rt'),'required'=>'true', 'type'=>'input', 'autocomplete'=>'off', 'maxlength' => Config::get('tables.vendors.rt'),
                'oninput' => 'this.value = this.value.replace(/[^0-9]/g, "");',
             ],[
                'id' => 'rw','placeholder'=>__('homepage.rw'),'required'=>'true', 'type'=>'input', 'autocomplete'=>'off', 'maxlength' => Config::get('tables.vendors.rt'),
                'oninput' => 'this.value = this.value.replace(/[^0-9]/g, "");',
            ],[
                'id' => 'country','placeholder'=>__('homepage.country'),'required'=>'true', 'type'=>'select'
            ],[
                'id' => 'province','placeholder'=>__('homepage.province'),'required'=>'true', 'type'=>'select', 'disabled'
            ],[
                'id' => 'city','placeholder'=>__('homepage.city'),'required'=>'true', 'type'=>'select', 'disabled'
            ],[
                'id' => 'sub_district','placeholder'=>__('homepage.sub_district'),'required'=>'true', 'type'=>'select', 'disabled'
            ],[
                'id' => 'postal_code','placeholder'=>__('homepage.postal_code'),'required'=>'true', 'type'=>'input', 'autocomplete'=>'off', 'maxlength' => Config::get('tables.vendors.postal_code')
            ],
        ];
        @endphp
        @foreach($attributes as $k=>$attr)
            <div class="form-group row" id="field-{{ $attr['id'] }}">
                @if($attr['type'] == 'select')
                    @if ($attr['id'] == 'country')
                        {!! Html::decode(Form::label($attr['id'], $attr['placeholder'].' '.(!empty($attr['required']) ? '' : ''), ['class' => 'control-label col-form-label col-sm-4'])) !!}
                    @else
                        {!! Html::decode(Form::label($attr['id'], $attr['placeholder'].' '.(!empty($attr['required']) ? '' : ''), ['class' => 'control-label col-form-label col-sm-4 country-attr'])) !!}
                    @endif
                @else
                    @if ($attr['id'] == 'postal_code')
                        {!! Html::decode(Form::label($attr['id'], $attr['placeholder'].' '.(!empty($attr['required']) ? '' : ''), ['class' => 'control-label col-form-label col-sm-4 country-attr'])) !!}
                    @else
                        {!! Html::decode(Form::label($attr['id'], $attr['placeholder'].' '.(!empty($attr['required']) ? '' : ''), ['class' => 'control-label col-form-label col-sm-4'])) !!}
                    @endif
                @endif       
                <div class="col-sm-8">
                    @if($attr['type'] == 'select')
                        @switch($attr['id'])
                            @case('country')
                                @php $selectRegion = $selectCountry; @endphp
                                @break
                            @case('province')
                                @php $selectRegion = []; @endphp
                                @break
                            @case('city')
                                @php $selectRegion = []; @endphp
                                @break
                            @case('sub_district')
                                @php $selectRegion = []; @endphp
                                @break
                            @default
                                @php $selectRegion = []; @endphp
                                break
                        @endswitch
                        {{ Form::select($attr['id'], $selectRegion, '', array_merge(['placeholder' => '-- Please select --', 'class' => 'form-control form-control-sm full-width'], $attr)) }}
                    @else
                        {{ Form::text($attr['id'], null, array_merge(['class' => 'form-control form-control-sm'], $attr)) }}
                    @endif
                </div>
            </div>
        @endforeach        
    </div>    
    <div class="col-sm-6">
        @php 
        $attributes = [
            [
                'id' => 'house_number','placeholder'=>__('homepage.house_number'), 'type'=>'input', 'autocomplete'=>'off', 'maxlength' => Config::get('tables.vendors.house_number')
            ],[
                'id' => 'phone_number','placeholder'=>__('homepage.phone_number'),'required'=>'true', 'autocomplete'=>'off', 'maxlength' => Config::get('tables.vendors.phone_number')
            ],[
                'id' => 'fax_number','placeholder'=>__('homepage.fax_number'), 'autocomplete'=>'off', 'maxlength' => Config::get('tables.vendors.fax_number')
            ],[
                'id' => 'company_email','placeholder'=>__('homepage.company_email'), 'autocomplete'=>'off', 'maxlength' => Config::get('tables.vendors.company_email')
            ],[
                'id' => 'company_site','placeholder'=>__('homepage.company_site'), 'autocomplete'=>'off', 'maxlength' => Config::get('tables.vendors.company_site')
            ],[
                'id' => 'pic_full_name','placeholder'=>__('homepage.pic_full_name'),'required'=>'true', 'autocomplete'=>'off', 'maxlength' => Config::get('tables.vendors.pic_full_name')
            ],[
                'id' => 'pic_mobile_number','placeholder'=>__('homepage.pic_mobile_number'),'required'=>'true', 'autocomplete'=>'off', 'maxlength' => Config::get('tables.vendors.pic_mobile_number')
            ],[
                'id' => 'pic_email','placeholder'=>__('homepage.pic_email'),'required'=>'true', 'autocomplete'=>'off', 'maxlength' => Config::get('tables.vendors.pic_email')
            ]
        ];
        @endphp
        @foreach($attributes as $k=>$attr)
        <div class="form-group row" id="field-{{ $attr['id'] }}">
            {!! Html::decode(Form::label($attr['id'], $attr['placeholder'].' '.(!empty($attr['required']) ? '' : ''), ['class' => 'control-label col-form-label col-sm-4'])) !!}
            <div class="col-sm-8">
                @if($attr['id'] == 'phone_number' || $attr['id'] == 'fax_number')
                <div class="input-group">
                    {{ Form::text($attr['id'], null, array_merge(['class' => 'form-control form-control-sm phone-input'], $attr)) }}
                    {{ Form::text($attr['id'].'_ext', null, array_merge(['class' => 'form-control form-control-sm', 'placeholder'=>'Ext.', 'maxlength' => Config::get('tables.vendors.phone_number_ext'), 'oninput' => 'this.value = this.value.replace(/[^0-9]/g, "");'], ['style'=>'min-width:110px;max-width:110px'])) }}
                </div>
                <span class="help-block">Input an Phone No.[+62xxxxxxxx]</span>
                @elseif($attr['id'] == 'company_email' || $attr['id'] == 'pic_email')
                {{ Form::email($attr['id'], $value = null, array_merge(['class' => 'form-control form-control-sm'], $attr)) }}
                @elseif($attr['id'] == 'pic_mobile_number')
                {{ Form::text($attr['id'], null, array_merge(['class' => 'form-control form-control-sm phone-input'], $attr)) }}
                @else
                {{ Form::text($attr['id'], null, array_merge(['class' => 'form-control form-control-sm'], $attr)) }}
                @endif
            </div>
        </div>
        @endforeach
    </div>
    <div class="col-sm-12 mb-3 mt-3">
        <div class="hrline"></div>
    </div>
    <div class="col-sm-6">
        <div class="form-group row">
            {!! Html::decode(Form::label('tender_ref_number', __('homepage.tender_ref_number'), ['class' => 'control-label col-form-label col-sm-4'])) !!}
            <div class="col-sm-8">
                {{ Form::text('tender_ref_number', null, array_merge(['class' => 'form-control form-control-sm'], ['id' => 'house_number','placeholder'=>__('homepage.tender_ref_number'), 'autocomplete'=>'off', 'maxlength' => Config::get('tables.vendors.tender_ref_number')])) }}
            </div>
        </div>
        <div class="form-group row id_type" style="display:none">
            <label class="control-label col-form-label col-sm-4">{{ __('homepage.identification_type') }}</label>
            <div class="col-sm-8">
                <div class="custom-control custom-radio custom-control-inline">
                    <input name="identification_type" id="identification_type_tin" type="radio" class="custom-control-input" value="tin" required="">
                    <label for="identification_type_tin" class="custom-control-label">{{__('homepage.tax_identification_number')}}</label>
                </div>
                <div class="custom-control custom-radio custom-control-inline">
                    <input name="identification_type" id="identification_type_idcard" type="radio" class="custom-control-input" value="id-card" required=""> 
                    <label for="identification_type_idcard" class="custom-control-label">{{__('homepage.id_card')}}</label>
                </div>
            </div>
        </div>
        <div class="form-group row identity-field-area" style="display:none">
            <label for="identity_number" class="control-label col-form-label col-sm-4"></label>
            <div class="col-sm-8">
                {{ Form::text('identity_number', null, array_merge(['class' => 'form-control form-control-sm'], ['placeholder' => __('homepage.choose_identification_type'), 'autocomplete'=>'off', 'id' => 'identity_number', 'readonly'=>true, 'required' => true, 'maxlength' => Config::get('tables.vendors.identity_number')])) }}
            </div>
        </div>
        <div class="form-group row identity-field-area" style="display:none">
            <label for="identity_attachment" class="control-label col-form-label col-sm-4"></label>
            <div class="col-sm-8">
                <input type="file" name="identity_attachment" id="identity_attachment" class="form-control form-control-sm attachment" required=""/>
            </div>
        </div>
        <div class="form-group row pkp-field-area pkp_type" style="display:none">
            <label class="control-label col-form-label col-sm-4">{{ __('homepage.pkp_type') }}</label>
            <div class="col-sm-8">
                <div class="custom-control custom-radio custom-control-inline">
                    <input name="pkp_type" id="is_pkp" type="radio" class="custom-control-input" value="pkp" required="">
                    <label for="is_pkp" class="custom-control-label">{{__('homepage.pkp')}}</label>
                </div>
                <div class="custom-control custom-radio custom-control-inline">
                    <input name="pkp_type" id="is_non_pkp" type="radio" class="custom-control-input" value="non-pkp" required=""> 
                    <label for="is_non_pkp" class="custom-control-label">{{__('homepage.non_pkp')}}</label>
                </div>
            </div>
        </div>
        <div class="form-group row pkp-field-area has_pkp" style="display:none">
            <label for="pkp_number" class="control-label col-form-label col-sm-4">{{ __('homepage.pkp_number') }}</label>
            <div class="col-sm-8">
                {{ Form::text('pkp_number', null, array_merge(['class' => 'form-control form-control-sm'], ['placeholder' => __('homepage.pkp_number'), 'autocomplete'=>'off', 'id' => 'pkp_number', 'required' => true, 'maxlength' => Config::get('tables.vendors.pkp_number')])) }}
            </div>
        </div>
        <div class="form-group row pkp-field-area has_pkp" style="display:none">
            <label for="pkp_attachment" class="control-label col-form-label col-sm-4">{{ __('homepage.pkp_attachment_file') }}</label>
            <div class="col-sm-8">
                <input type="file" name="pkp_attachment" id="pkp_attachment" class="form-control form-control-sm attachment" required=""/>
            </div>
        </div>
        <div class="form-group row pkp-field-area has_non_pkp" style="display:none">
            <label for="non_pkp_number" class="control-label col-form-label col-sm-4">{{ __('homepage.non_pkp_number') }}</label>
            <div class="col-sm-8">
                {{ Form::text('non_pkp_number', null, array_merge(['class' => 'form-control form-control-sm'], ['placeholder' => __('homepage.non_pkp_number'), 'autocomplete'=>'off', 'id' => 'non_pkp_number', 'required' => true, 'readonly'=>true, 'maxlength' => Config::get('tables.vendors.non_pkp_number')])) }}
            </div>
        </div>
        <div class="form-group row tin-field-area" style="display:none">
            <label for="tax_identification_number" class="control-label col-form-label col-sm-4">{{ __('homepage.tax_identification_number') }}</label>
            <div class="col-sm-8">
                {{ Form::text('tax_identification_number', 'TIMAS Tax Identification Number', array_merge(['class' => 'form-control form-control-sm'], ['placeholder' => __('homepage.tax_identification_number'), 'autocomplete'=>'off', 'id' => 'tax_identification_number', 'required' => true, 'readonly'=>true, 'maxlength' => Config::get('tables.vendors.tax_identification_number')])) }}
            </div>
        </div>
    </div>
    <div class="col-sm-12 mb-3 mt-3">
        <div class="hrline"></div>
    </div>
    <div class="col-sm-6">
        <div class="form-group row">
            {{ Form::label('',null, ['class' => 'control-label col-form-label col-sm-4']) }}
            <div class="col-sm-8">
                <div class="button-group">
                <button type="button" id="btn-check" class="btn btn-sm btn-info"><i class="fas fa-square mr-2"></i>{{ __('homepage.term_and_condition') }}</button>
                <button type="button" id="btn-submit" class="btn btn-sm btn-success" hidden="">{{ __('homepage.submit') }}</button>                    
                </div>
            </div>
        </div>
    </div>
</div>
