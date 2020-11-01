<div class="row">
    <div class="col-sm-6">
        <input type="hidden" name="applicant_id" value="{{ $applicant->id }}"/>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group-sm row">
                    <label for="inputCompanyName" class="col-form-label-sm text-right col-sm-4">{{ __('homepage.company_name') }}<span class="font-danger">*</span></label>
                    <div class="col-sm-8">
                        <input type="text" name="company_name" class="form-control form-control-sm" id="inputCompanyName" placeholder="{{ __('homepage.company_name') }}" required=""/>
                    </div>
                </div>
            </div>
        </div>        
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group-sm row">
                    <label for="selCompanyType" class="col-form-label-sm text-right col-sm-4">{{ __('homepage.company_type') }} <span class="font-danger">*</span></label>
                    <div class="col-sm-8">
                        <select name="company_type_id" id="selCompanyType" class="form-control form-control-sm" required="">
                            <option value=""> -- Select -- </option>
                            <option value="1">PT - Local Vendor</option>
                            <option value="2">CV - Local Vendor</option>
                            <option value="3">Yayasan - Local Vendor</option>
                            <option value="4">Koperasi - Local Vendor</option>
                            <option value="5">Perum - Local Vendor</option>
                            <option value="6">Toko - Local Vendor / Personal Vendor</option>
                            <option value="7">Company - Overseas Vendor</option>
                            <option value="8">Others - Others</option>
                            <option value="9">Perorangan - Mr, Mrs & Personal Vendor</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group-sm row">
                    <label for="inputLocationCategory" class="col-form-label-sm text-right col-sm-4">{{ __('homepage.location_category') }}<span class="font-danger">*</span></label>
                    <div class="col-sm-8">
                        <input type="text" name="location_category" class="form-control form-control-sm" id="inputLocationCategory" placeholder="{{ __('homepage.location_category') }}" required="">
                    </div>
                </div>
            </div>
        </div>        
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group-sm row">
                    <label for="inputAddress" class="col-form-label-sm text-right col-sm-4">{{ __('homepage.address') }}<span class="font-danger">*</span></label>
                    <div class="col-sm-8">
                        <textarea name="address" rows="3" class="form-control form-control-sm" id="inputAddress" placeholder="{{ __('homepage.address') }}" required=""></textarea>
                    </div>
                </div>
            </div>
        </div>        
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group-sm row">
                    <label for="selCountry" class="col-form-label-sm text-right col-sm-4">{{ __('homepage.country') }} <span class="font-danger">*</span></label>
                    <div class="col-sm-8">
                        <select name="country" id="selCountry" class="form-control form-control-sm" required="">
                            <option value=""> -- Select -- </option>
                            <option value="Indonesia">Indonesia</option>
                            <option value="United States">United States</option>
                            <option value="Other">Others</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group-sm row">
                    <label for="selProvince" class="col-form-label-sm text-right col-sm-4">{{ __('homepage.province') }} <span class="font-danger">*</span></label>
                    <div class="col-sm-8">
                        <select name="province" id="selProvince" class="form-control form-control-sm" required="">
                            <option value=""> -- Select -- </option>
                            <option value="DKI Jakarta">DKI Jakarta</option>
                            <option value="Banten">Banten</option>
                            <option value="Jawa Barat">Jawa Barat</option>
                            <option value="Jawa Tengah">Jawa Tengah</option>
                            <option value="Jawa Timur">Jawa Timur</option>
                            <option value="Bali">Bali</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>        
    </div>
    <div class="col-sm-6">
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group-sm row">
                    <label for="selCity" class="col-form-label-sm text-right col-sm-4">{{ __('homepage.city') }} <span class="font-danger">*</span></label>
                    <div class="col-sm-8">
                        <select name="city" id="selCity" class="form-control form-control-sm" required="">
                            <option value=""> -- Select -- </option>
                            <option value="indonesia">Indonesia</option>
                            <option value="unitedstates">United States</option>
                            <option value="other">Others</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group-sm row">
                    <label for="selSubdistrict" class="col-form-label-sm text-right col-sm-4">{{ __('homepage.sub_district') }} <span class="font-danger">*</span></label>
                    <div class="col-sm-8">
                        <select name="sub_district" id="selSubdistrict" class="form-control form-control-sm" required="">
                            <option value=""> -- Select -- </option>
                            <option value="Kebayoran Baru">Kebayoran Baru</option>
                            <option value="Kebayoran Lama">Kebayoran Lama</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group-em row">
                    <label for="inputPostalCode" class="col-form-label-sm text-right col-sm-4">{{ __('homepage.postal_code') }} <span class="font-danger">*</span></label>
                    <div class="col-sm-8">
                        <input type="text" name="postal_code" class="form-control form-control-sm" id="inputPostalCode" required="">
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group-sm row">
                    <label for="inputPhoneNumber" class="col-form-label-sm text-right col-sm-4">{{ __('homepage.phone_number') }} <span class="font-danger">*</span></label>
                    <div class="col-sm-8">
                        <input type="text" name="phone_number" class="form-control form-control-sm" id="inputPhoneNumber" required="">
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group-sm row">
                    <label for="inputFaxNumber" class="col-form-label-sm text-right col-sm-4">{{ __('homepage.fax_number') }}</label>
                    <div class="col-sm-8">
                        <input type="text" name="fax_number" class="form-control form-control-sm" id="inputFaxNumber" required="">
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group-sm row">
                    <label for="inputWebsite" class="col-form-label-sm text-right col-sm-4">{{ __('homepage.website') }}</label>
                    <div class="col-sm-8">
                        <input type="text" name="company_site" class="form-control form-control-sm" id="inputWebsite" placeholder="[yourcompanysite].com">
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group-sm row">
                    <label for="inputCompanyEmail" class="col-form-label-sm text-right col-sm-4">{{ __('homepage.company_email') }}</label>
                    <div class="col-sm-8">
                        <input type="text" name="company_email" class="form-control form-control-sm" id="inputCompanyEmail" placeholder="[yourcompanyemail]" required="">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>