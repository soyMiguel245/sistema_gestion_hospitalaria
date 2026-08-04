@props(['value'])

<label {{ $attributes->merge(['class' => 'form-label fw-semibold']) }} style="color: #08024a;">
    {{ $value ?? $slot }}
</label>