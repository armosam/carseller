@props(['label' => ''])

<div class="form-group @error($attributes['name']) has-error @enderror">
    @if($label)
        <label>{{ $label }}</label>
    @endif
    <input {{ $attributes }} />
    @error($attributes['name'])
    <p class="error-message">{{ $message }}</p>
    @enderror
</div>
