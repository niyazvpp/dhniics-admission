<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn btn-blue']) }}>
    {{ $slot }}
</button>