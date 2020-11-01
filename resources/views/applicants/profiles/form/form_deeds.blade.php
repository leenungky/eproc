<div class="page1 display-block">
    <input type="hidden" name="applicant_id" value="{{ $applicant->id }}"/>
    <div class="form-group-sm row">
        <label for="inputDeedType" class="col-form-label-sm text-right col-sm-4">{{ __('homepage.deeds_type') }}<span class="font-danger">*</span></label>
        <div class="col-sm-8">
            <input type="text" name="deed_type" class="form-control form-control-sm" id="inputDeedType" placeholder="{{ __('homepage.deeds_type') }}">
        </div>
    </div>
    <div class="form-group-sm row">
        <label for="deedNumber" class="col-form-label-sm text-right col-sm-4">{{ __('homepage.deeds_number') }}<span class="font-danger">*</span></label>
        <div class="col-sm-8">
            <input type="text" name="deed_number" class="form-control form-control-sm" id="deedNumber" placeholder="{{ __('homepage.deeds_number') }}" />
        </div>
    </div>
    <div class="form-group-sm row">
        <label for="deedDate" class="col-form-label-sm text-right col-sm-4">{{ __('homepage.deeds_date') }}<span class="font-danger">*</span></label>
        <div class="col-sm-8">
            <input type="text" name="deed_date" class="form-control form-control-sm" id="deedDate" placeholder="{{ __('homepage.deeds_date') }}" />
        </div>
    </div>
    <div class="form-group-sm row">
        <label for="notaryName" class="col-form-label-sm text-right col-sm-4">{{ __('homepage.notary_name') }}<span class="font-danger">*</span></label>
        <div class="col-sm-8">
            <input type="text" name="notary_name" class="form-control form-control-sm" id="notaryName" placeholder="{{ __('homepage.notary_name') }}" />
        </div>
    </div>
    <div class="form-group-sm row">
        <label for="skMenkumhamNumber" class="col-form-label-sm text-right col-sm-4">{{ __('homepage.sk_menkumham_number') }}<span class="font-danger">*</span></label>
        <div class="col-sm-8">
            <input type="text" name="sk_menkumham_number" class="form-control form-control-sm" id="skMenkumhamNumber" placeholder="{{ __('homepage.sk_menkumham_number') }}" />
        </div>
    </div>
    <div class="form-group-sm row">
        <label for="skMenkumhamDate" class="col-form-label-sm text-right col-sm-4">{{ __('homepage.sk_menkumham_date') }}<span class="font-danger">*</span></label>
        <div class="col-sm-8">
            <input type="text" name="sk_menkumham_date" class="form-control form-control-sm" id="skMenkumhamDate" placeholder="{{ __('homepage.sk_menkumham_date') }}" />
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
                <td colspan="2" class="text-center pad1x" style="padding: 8px; background-color: #fff3f3;"><i>{{ __('homepage.you_should_upload_related_document') }}</i></td>
            </tr>
        </tbody>
    </table>
    <div class="form-group-sm row">
        <!--<label for="attachment" class="col-form-label-sm text-right col-sm-4">{{ __('homepage.attachment') }}<span class="font-danger">*</span></label>-->
        <div class="col-sm-12">
            <input type="file" name="attachment" class="form-control form-control-sm" id="attachment" placeholder="{{ __('homepage.attachment') }}" />
        </div>
    </div>
</div>
