<select id="modelSelect" name="model_id">
    <option value="" style="display: block">Model</option>
    @foreach($models as $model)
        <option @selected($attributes->get('value') == $model->id) value="{{$model->id}}" data-parent="{{$model->maker->id}}" style="display: none">
            {{$model->name}}
        </option>
    @endforeach
</select>
