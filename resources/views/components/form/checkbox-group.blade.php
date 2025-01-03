<div class="form-group @error($attributes['name']) has-error @enderror">
    {{ $slot }}

    @error($attributes['name'])
    <p class="error-message">{{ $message }}</p>
    @enderror
</div>
