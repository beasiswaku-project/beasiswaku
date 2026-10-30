@props(['name', 'id'])

@error($name)
    <p id="{{ $id }}-error" class="mt-1.5 text-sm text-red-600" data-error>{{ $message }}</p>
@enderror
