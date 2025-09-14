<select name="year">
    <option value="">Year</option>
    @for($i = $year; $i >= ($year - 28); $i--)
        <option @selected($attributes->get('value') == $i) value="{{$i}}">
            {{$i}}
        </option>
    @endfor
</select>
