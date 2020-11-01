<input type="hidden" name="id" value="">
<input type="hidden" name="evaluation_id" value="{{$general->id}}">
<input type="hidden" name="vendor_id" id="vendor_id" value="">
<table class="table table-sm table-bordered table-striped">
    <thead>
        <tr>
            <th>{{__('homepage.criteria_name')}}</th>
            <th>{{__('homepage.weighting')}}</th>
            <th>{{__('homepage.minimum_score')}}</th>
            <th>{{__('homepage.maximum_score')}}</th>
            <th>{{__('homepage.score')}}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($assignments as $assignment)
        <tr id="score_{{$assignment->criteria_id}}">
            <td>{{$assignment->criteria_name}}<input type="hidden" name="criteria[]" value="{{$assignment->criteria_id}}"></td>
            <td>{{$assignment->weighting}}</td>
            <td>{{$assignment->minimum_score}}</td>
            <td>{{$assignment->maximum_score}}</td>
            <td><input class="form-control form-control-sm" type="number" min="{{$assignment->minimum_score}}" max="{{$assignment->maximum_score}}" id="score-{{$assignment->criteria_id}}" name="score[]" required></td>
        </tr>
        @endforeach
    </tbody>
</table>
