<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn btn-primary']) }} style="background-color: #08024a; border-color: #08024a;">
    {{ $slot }}
</button>