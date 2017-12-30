@extends('layouts.app')

@section('content')
    <section class="section">

        <div class="container">
            @include('partials.page-header')

            @if (!have_posts())
                <div class="alert alert-warning">
                    {{ __('Sorry, no results were found.', 'sage') }}
                </div>
                {!! get_search_form(false) !!}
            @endif
            <div class="row">
                <div class="col-sm-4">
                    @while (have_posts()) @php(the_post())
                    @include('partials.content-'.get_post_type())
                    @endwhile
                </div>
            </div>

            {!! get_the_posts_navigation() !!}
        </div>
    </section>
@endsection
