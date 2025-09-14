<div class="row">
    @foreach($fuelTypes as $fuelType)
        <div class="col">
            <label class="inline-radio">
                <input type="radio" name="fuel_type_id" value="{{$fuelType->id}}"
                    @checked($attributes->get('value') == $fuelType->id) />
                {{$fuelType->name}}
            </label>
        </div>
        @if($loop->iteration % 4 == 0 && !$loop->last)
            </div><div class="row">
        @endif
    @endforeach
</div>
