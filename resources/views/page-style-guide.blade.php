@extends('layouts.app')

@section('content')

    {{-- Pages have no explicit content as it all comes from widgets --}}
    @php
        do_action('acf-widget/render');
    @endphp

    <section class="section">
        <div class="container">

            <code>
                {{ \App\template('partials.bootstrap-html') }}
            </code>

            <h2>Example</h2>
            @include('partials.bootstrap-html')

        </div>
    </section>

@endsection
