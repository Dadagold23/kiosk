@php
    $scripts = $scripts ?? [];
@endphp

@foreach($scripts as $script)
    <script src="{{ asset($script) }}"></script>
@endforeach
