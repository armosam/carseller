<select id="makerSelect" name="maker_id">
    <option value="">Select Maker</option>
    @foreach($makers as $maker)
        <option @selected($attributes->get('value') == $maker->id) value="{{$maker->id}}">
            {{$maker->name}}
        </option>
    @endforeach
</select>
