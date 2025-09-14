<select id="stateSelect" name="state_id">
    <option value="">State</option>
    @foreach($states as $state)
        <option @selected($attributes->get('value') == $state->id) value="{{$state->id}}">
            {{$state->name}}
        </option>
    @endforeach
</select>
