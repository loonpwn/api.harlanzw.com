@extends('layouts.app')

@section('content')

    {{-- Pages have no explicit content as it all comes from widgets --}}
    @php
        do_action('acf-widget/render');
    @endphp

    <section class="section">

        <div class="container">

            @if (!have_posts())
                <div class="alert alert-warning">
                    {{ __('Sorry, no results were found.', 'sage') }}
                </div>
                {!! get_search_form(false) !!}
            @endif
            <div class="row">
                @while (have_posts()) @php(the_post())
                <div class="col-md-6">
                    @include('partials.content-'.get_post_type())
                </div>
                @endwhile
            </div>

            {!! get_the_posts_navigation() !!}
        </div>
    </section>
@endsection
