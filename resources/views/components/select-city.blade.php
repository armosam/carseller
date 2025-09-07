<select id="citySelect" name="city_id">
    <option value="" style="display: block">City</option>
    @foreach($cities as $city)
        <option @selected($attributes->get('value') == $city->id) value="{{$city->id}}" data-parent="{{$city->state->id}}" style="display: none">
            {{$city->name}}
        </option>
    @endforeach
</select>
