<select name="fuel_type_id">
    <option value="">Fuel Type</option>
    @foreach($fuelTypes as $fuelType)
        <option @selected($attributes->get('value') == $fuelType->id) value="{{$fuelType->id}}">
            {{$fuelType->name}}
        </option>
    @endforeach
</select>
