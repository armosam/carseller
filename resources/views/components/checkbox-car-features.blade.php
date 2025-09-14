@props(['car' => null])

<div class="row">
    <div class="col">
        @foreach($features as $feature_name => $feature_label)
            <label class="checkbox">
                <input type="checkbox" name="features[{{$feature_name}}]" value="1"
                    @checked(old('features.'.$feature_name, $car?->features->$feature_name)) />
                {{$feature_label}}
            </label>
            @if($loop->iteration % 6 == 0 && !$loop->last)
    </div>
    <div class="col">
        @endif
        @endforeach
    </div>
</div>
