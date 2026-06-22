@extends('layouts.admin')

@section('page_title')
    {{ $pageTitle }}
@endsection

@section('page_subtitle', 'Gum Dashrock Live View')

@push('styles')
    @if(isset($styles) && count($styles) > 0)
        @foreach($styles as $styleTag)
            {!! $styleTag !!}
        @endforeach
    @endif
@endpush

@section('content')
    {!! $content !!}
@endsection

@push('scripts')
    @if(isset($scripts) && count($scripts) > 0)
        @foreach($scripts as $scriptTag)
            {!! $scriptTag !!}
        @endforeach
    @endif
@endpush
