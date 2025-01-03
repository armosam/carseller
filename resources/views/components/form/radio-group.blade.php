@props(['label' => ''])

<div class="form-group @error($attributes['name']) has-error @enderror">
    <label>{{$label}}</label>
    {{ $slot }}

    @error($attributes['name'])
    <p class="error-message">{{ $message }}</p>
    @enderror
</div>
