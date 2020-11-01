<div class="row">
    <div class="col-sm-6">
        <input type="hidden" name="vendor_profile_id" value="{{ $vendor->vendor_profiles_id }}"/>
        <input type="hidden" id="profileId" name="id" value=""/>
        <input type="hidden" id="editType" name="edit_type" value=""/>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group-sm row">
                    <label for="company_name" class="control-label col-form-label col-sm-4 text-right">{{ __('homepage.company_name') }}</label>
                    <div class="col-sm-8">
                        <input type="text" name="company_name" class="form-control form-control-sm" id="company_name" placeholder="{{ __('homepage.company_name') }}" required="" maxlength="{{ Config::get('tables.vendor_profile_generals.company_name') }}"/>
                    </div>
                </div>
            </div>
        </div>        
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group-sm row">
                    <label for="company_type_id" class="control-label col-form-label col-sm-4 text-right">{{ __('homepage.company_type') }}</label>
                    <div class="col-sm-8">
                        <select name="company_type_id" id="company_type_id" class="form-control form-control-sm" required="">
                            <option value=""> -- Select -- </option>
                            @foreach($companyTypes as $type)
                            <option value="{{$type->id}}">{{$type->company_type}} ({{$type->description}})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="row" style="display: none">
            <div class="col-sm-12">
                <div class="form-group-sm row">
                    <label for="location_category" class="control-label col-form-label col-sm-4 text-right">{{ __('homepage.location_category') }}</label>
                    <div class="col-sm-8">
                        <select name="location_category" id="location_category" class="form-control form-control-sm">
                            <option value=""> -- Select -- </option>
                            <option value="Head Office">{{ __('homepage.head_office') }}</option>
                            <option value="Representative Office">{{ __('homepage.representative_office') }}</option>
                            <option value="Branch Office">{{ __('homepage.branch_office') }}</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        @php 
        if($vendor->vendor_group == 'local'){
            $attributes = [
                [
                    'id' => 'street','placeholder'=>__('homepage.street'),'required'=>'true', 'type'=>'input', 'autocomplete'=>'off', 'maxlength' => Config::get('tables.vendor_profile_generals.street')
                ],[
                    'id' => 'house_number','placeholder'=>__('homepage.house_number'), 'type'=>'input', 'autocomplete'=>'off', 'maxlength' => Config::get('tables.vendor_profile_generals.house_number')
                ],[
                    'id' => 'building_name','placeholder'=>__('homepage.building_name'), 'type'=>'input', 'autocomplete'=>'off', 'maxlength' => Config::get('tables.vendor_profile_generals.building_name')
                ],[ 
                    'id' => 'kavling_floor_number','placeholder'=>__('homepage.kavling_floor_number'), 'type'=>'input', 'autocomplete'=>'off', 'maxlength' => Config::get('tables.vendor_profile_generals.kavling_floor_number')
                ],[
                    'id' => 'rt','placeholder'=>__('homepage.rt'),'required'=>'true', 'type'=>'input', 'autocomplete'=>'off', 'maxlength' => Config::get('tables.vendor_profile_generals.rt'),
                    'oninput' => 'this.value = this.value.replace(/[^0-9]/g, "");'
                ],[
                    'id' => 'rw','placeholder'=>__('homepage.rw'),'required'=>'true', 'type'=>'input', 'autocomplete'=>'off', 'maxlength' => Config::get('tables.vendor_profile_generals.rw'),
                    'oninput' => 'this.value = this.value.replace(/[^0-9]/g, "");'
                ],[
                    'id' => 'village','placeholder'=>__('homepage.village'),'required'=>'true', 'type'=>'input', 'autocomplete'=>'off', 'maxlength' => Config::get('tables.vendor_profile_generals.village')
                ]
            ];
        } else {
            $attributes = [
                [
                    'id' => 'address_1','placeholder'=>__('homepage.address_1'),'required'=>'true', 'type'=>'input', 'autocomplete'=>'off', 'maxlength' => Config::get('tables.vendor_profile_generals.address_1')
                ],[
                    'id' => 'address_2','placeholder'=>__('homepage.address_2'), 'type'=>'input', 'autocomplete'=>'off', 'maxlength' => Config::get('tables.vendor_profile_generals.address_2')
                ],[
                    'id' => 'address_3','placeholder'=>__('homepage.address_2'), 'type'=>'input', 'autocomplete'=>'off', 'maxlength' => Config::get('tables.vendor_profile_generals.address_3')
                ]
            ];
        }
        @endphp
        @foreach($attributes as $k=>$attr)
        <div class="form-group-sm row">
            @if (in_array($attr['placeholder'],[ __('homepage.rt'), __('homepage.rw'),  __('homepage.village')]))
            {!! Html::decode(Form::label($attr['id'],  $attr['placeholder'].'', ['class' => 'control-label col-form-label col-sm-4 text-right country-attr'])) !!}
            @else
                {!! Html::decode(Form::label($attr['id'],  $attr['placeholder'].'', ['class' => 'control-label col-form-label col-sm-4 text-right'])) !!}
            @endif
            <div class="col-sm-8">
                @if ($attr['placeholder']==__('homepage.rt_rw'))
                {{ Form::text($attr['id'], null, array_merge(['class' => 'form-control form-control-sm frm-country-attr'], $attr)) }}
                @else
                    {{ Form::text($attr['id'], null, array_merge(['class' => 'form-control form-control-sm'], $attr)) }}
                @endif
            </div>
        </div>
        @endforeach
        </div>
        <div class="col-sm-6">
        @php 
        if($vendor->vendor_group == 'local'){
            $attributes = [
                [
                    'id' => 'country','placeholder'=>__('homepage.country'),'required'=>'true', 'type'=>'select'
                ],[
                    'id' => 'province','placeholder'=>__('homepage.province'),'required'=>'true', 'type'=>'select', 'disabled'
                ],[
                    'id' => 'city','placeholder'=>__('homepage.city'),'required'=>'true', 'type'=>'select', 'disabled'
                ],[
                    'id' => 'sub_district','placeholder'=>__('homepage.sub_district'),'required'=>'true', 'type'=>'select', 'disabled'
                ],[
                    'id' => 'postal_code','placeholder'=>__('homepage.postal_code'),'required'=>'true', 'type'=>'input', 'autocomplete'=>'off'
                ],
            ];
        } else {
            $attributes = [
                [
                    'id' => 'country','placeholder'=>__('homepage.country'),'required'=>'true', 'type'=>'select'
                ],[
                    'id' => 'postal_code','placeholder'=>__('homepage.postal_code'),'required'=>'true', 'type'=>'input', 'autocomplete'=>'off'
                ],
            ];
        }
        @endphp
        @foreach($attributes as $k=>$attr)
        <div class="form-group-sm row">
            @if ($attr['id'] == 'country' || $attr['id'] == 'postal_code' )
                {!! Html::decode(Form::label($attr['id'], $attr['placeholder'], ['class' => 'control-label col-form-label col-sm-4 text-right'])) !!}
            @else
                {!! Html::decode(Form::label($attr['id'], $attr['placeholder'], ['class' => 'control-label col-form-label col-sm-4 text-right'])) !!}
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
                            @break
                    @endswitch
                    {{ Form::select($attr['id'], $selectRegion, '', array_merge(['placeholder' => '-- Please select --', 'class' => 'form-control form-control-sm full-width'], $attr)) }}
                @else                 
                    {{ Form::text($attr['id'], null, array_merge(['class' => 'form-control form-control-sm'], $attr)) }}
                @endif
            </div>
        </div>
        @endforeach
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group-sm row">
                    <label for="phone_number" class="control-label col-form-label col-sm-4 text-right">{{ __('homepage.phone_number') }}</label>
                    <div class="col-sm-8">
                        <input type="text" name="phone_number" class="form-control form-control-sm" id="phone_number" required="" maxlength="{{ Config::get('tables.vendor_profile_generals.phone_number') + 1 + Config::get('tables.vendor_profile_generals.phone_number_ext') }}" oninput='this.value=this.value.replace(/[^+0-9]/g, "")'/>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group-sm row">
                    <label for="fax_number" class="control-label col-form-label col-sm-4 text-right">{{ __('homepage.fax_number') }}</label>
                    <div class="col-sm-8">
                        <input type="text" name="fax_number" class="form-control form-control-sm" id="fax_number" maxlength="{{ Config::get('tables.vendor_profile_generals.phone_number') + 1 + Config::get('tables.vendor_profile_generals.phone_number_ext') }}" oninput='this.value=this.value.replace(/[^+0-9]/g, "")'/>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group-sm row">
                    <label for="website" class="control-label col-form-label col-sm-4 text-right">{{ __('homepage.website') }}</label>
                    <div class="col-sm-8">
                        <input type="text" name="website" class="form-control form-control-sm" id="website" placeholder="[yourcompanysite].com">
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group-sm row">
                    <label for="company_email" class="control-label col-form-label col-sm-4 text-right">{{ __('homepage.company_email') }}</label>
                    <div class="col-sm-8">
                        <input type="text" name="company_email" class="form-control form-control-sm" id="company_email" placeholder="[yourcompanyemail]">
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group-sm row display-none" >
            <label for="primary_data" class="col-form-label text-right col-sm-4">{{ __('homepage.primary_data') }}<span class="font-danger">*</span></label>
            <div class="col-sm-8">
                <div class="custom-control custom-checkbox mb-2 mt-1">
                    <input type="checkbox" class="custom-control-input" id="primary_data" name="primary_data">
                    <label class="custom-control-label" for="primary_data">{{ __('homepage.yes') }}</label>
                </div>
            </div>
        </div>
    </div>
</div>