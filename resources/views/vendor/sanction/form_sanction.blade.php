<input type="hidden" id="id" name="id" value=""/>
<input type="hidden" id="vendor_id" name="vendor_id" value=""/>
<input type="hidden" id="vendor_profile_id" name="vendor_profile_id" value=""/>

<div class="page1 display-block row">
    <div class="col-6">
        <div class="form-group row mb-2">
            <label for="pic" class="col-3 col-form-label text-right">{{__('homepage.person_in_charge')}}</label>
            <div class="col-9">
                <input type="text" name="pic" id="pic" class="form-control form-control-sm" readonly>
            </div>
        </div>
        <div class="form-group row mb-2">
            <label for="sanction_type" class="col-3 col-form-label text-right">{{__('homepage.sanction_type')}}</label>
            <div class="col-9">
                <select id="sanction_type" name="sanction_type" class="custom-select custom-select-sm" required="required">
                    <option value="">-- Choose Sanction --</option>
                    @foreach($sanctionTypes as $key=>$value)
                    <option value="{{$key}}">{{$key}} ({{$value}})</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group row mb-2">
            <label for="valid_from_date" class="col-3 col-form-label text-right">{{__('homepage.valid_from_date')}}</label>
            <div class="col-9">
                <input type="text" id="valid_from_date" name="valid_from_date" placeholder="{{__('homepage.valid_from_date')}}" required="required" class="form-control form-control-sm datetimepicker-input date" data-toggle="datetimepicker" data-target="#valid_from_date">
            </div>
        </div>
        <div class="form-group row mb-2">
            <label for="valid_thru_date" class="col-3 col-form-label text-right">{{__('homepage.valid_thru_date')}}</label>
            <div class="col-9">
                <input type="text" id="valid_thru_date" name="valid_thru_date" placeholder="{{__('homepage.valid_thru_date')}}" required="required" class="form-control form-control-sm datetimepicker-input date" data-toggle="datetimepicker" data-target="#valid_thru_date">
            </div>
        </div>
    </div>
    <div class="col-6">
        <div class="form-group row mb-2">
            <label for="letter_number" class="col-3 col-form-label text-right">{{__('homepage.letter_number')}}</label>
            <div class="col-9">
                <input type="text" name="letter_number" id="letter_number" class="form-control form-control-sm" required>
            </div>
        </div>
        <div class="form-group row mb-2">
            <label for="description" class="col-3 col-form-label text-right">{{__('homepage.description')}}</label>
            <div class="col-9">
                <textarea id="description" name="description" placeholder="{{__('homepage.description')}}" class="form-control form-control-sm" required></textarea>
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
            <tr>
            <td colspan="2" class="text-center pad1x" style="padding: 8px;"><a id="attachment_filename" target="_blank"></a></td>
            </tr>
        </tbody>
    </table>
    <div class="form-group-sm row">
        <div class="col-sm-12">
            <input type="file" name="attachment" class="form-control form-control-sm" id="attachment" placeholder="{{ __('homepage.attachment') }}" required/>
        </div>
    </div>
</div>
