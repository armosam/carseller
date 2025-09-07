<select name="mileage">
    <option value="">Any Mileage</option>
    @foreach($mileages as $value => $text)
        <option @selected($attributes->get('value') == $value) value="{{$value}}">
            {{$text}}
        </option>
    @endforeach
</select>
