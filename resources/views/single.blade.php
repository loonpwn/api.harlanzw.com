@extends('layouts.app')

@section('content')

    @php
        do_action('acf-widget/render', ['include' => ['call-to-action']]);
    @endphp

    <div class="blog-container">
        {{-- Pages have no explicit content as it all comes from widgets --}}
        @php
            do_action('acf-widget/render', ['exclude' => ['call-to-action']]);
        @endphp
    </div>


@endsection
