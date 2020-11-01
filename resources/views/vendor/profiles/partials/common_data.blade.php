<table class="table table-sm table-striped table-bordered">
    <thead>
        <tr>
            <th style="width: 40px;">No</th>
            <th style="width: 150px;">{{ __('homepage.detail') }}</th>
            <th style="width: 214px;">{{ __('homepage.current_data') }}</th>
            <th style="width: 214px;">{{ __('homepage.new_data') }}</th>
            <th style="width: 80px;">{{ __('homepage.status') }}</th>
            <th style="width: 40px;">{{ __('homepage.action') }}</th>
        </tr>
    </thead>
    <tbody>
        @if( $profiles->count() > 0 )
            <?php $i = 1; ?>
            @foreach ($profiles as $profile)
                @if($profile->is_current_data)
                    <!-- Check has an edited data or not -->
                    @foreach ($profiles as $newdata)
                        @if($profile->id == $newdata->parent_id)
                            <?php //$i++; ?>
                            @include('vendor.profiles.partials.common_detail', ['current'=>$profile, 'new'=>$newdata])
                            <?php //$i++; ?>
                            @break;
                        @else
                            @if ($loop->last)
                                @include('vendor.profiles.partials.common_detail', ['current'=>$profile, 'new'=>null])
                                <?php $i++; ?>
                            @endif
                        @endif
                    @endforeach
                @else
                    @if($profile->parent_id == 0)
                        @include('vendor.profiles.partials.common_detail', ['current'=>null, 'new'=>$profile])
                        <?php $i++; ?>
                    @endif
                @endif        
            @endforeach        
        @else
            @php ($i=1)
            @include('vendor.profiles.partials.common_detail', ['current'=>null, 'new'=>null])
        @endif
    </tbody>
</table>
