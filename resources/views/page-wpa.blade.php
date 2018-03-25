@extends('layouts.app')

@section('content')

    @php
        do_action('acf-widget/render', ['include' => ['wysiwyg', 'call-to-action']]);
    @endphp

    <div class="container">

        <?php
        global $wpa_output;
        ?>
        @if(empty($wpa_output))
            <form
                ga-on="submit,change"
                ga-event-category="WPA"
                ga-event-action="Submit"
                method="get">

                <div class="form-group">
                    <label class="form-control-label" for="plugin-url">Plugin Slug</label>
                    <input
                        ga-on="change"
                        ga-event-category="WPA"
                        ga-event-action="Plugin URL"
                        required
                        type="text" class="form-control" name="plugin-url" id="plugin-url" aria-describedby="plugin-url-help" placeholder="Enter Slug">
                    <small id="plugin-url-help" class="form-text text-muted">This is the end of the URL for your plugin. Example https://wordpress.org/plugins/<strong>wordpress-seo</strong>/</small>
                </div>
                <div class="form-group">
                    <label class="form-control-label" for="search-term">Search Term</label>
                    <input
                        ga-on="change"
                        ga-event-category="WPA"
                        ga-event-action="Search Term"
                        required
                        type="text" class="form-control" name="search-term" id="search-term" aria-describedby="search-term-help" placeholder="Enter Search Term">
                    <small id="search-term-help" class="form-text text-muted">What users are searching that should show your plugin.</small>
                </div>
                <input name="action" value="plugin-search" type="hidden">


                <button type="submit" class="btn btn-primary">Find score</button>
            </form>
        @elseif(!isset($wpa_output['error']))
            <p>Looking at the search score for <strong>{{ $_GET['plugin-url'] }}</strong> with search term <strong>{{ $_GET['search-term'] }}</strong>.</p>

            <h3>Current Score</h3>
            <p class="score">{{ $wpa_output['score'] }}</p>
            <h3>Potential Score</h3>
            <p class="score">{{ $wpa_output['max_score'] }}</p>
            <div class="card" style="margin-bottom: 1rem">
                <div class="card-body">
                    <h3 class="card-title">Recommendations</h3>
                    @if(!empty($wpa_output['recommendations']))
                        <ul>
                            {!! '<li>' . implode('</li><li>', $wpa_output['recommendations']) . '</li>' !!}
                        </ul>
                    @else
                        <div class="alert alert-warning">
                            <p>No optimizations could be found for this search term.</p>
                        </div>
                    @endif
                </div>
            </div>

            <a href="/wpa/" class="btn btn-link">Search Again</a>

            <h3>Log</h3>
            <code>
                {!! $wpa_output['log'] !!}
            </code>
        @else
            <div class="alert alert-danger">
                <p>{!! $wpa_output['error'] !!}</p>
            </div>
            <a href="/wpa/" class="btn btn-link">Search Again</a>
        @endif

    </div>

    @php
        do_action('acf-widget/render', ['exclude' => ['wysiwyg', 'call-to-action']]);
    @endphp
@endsection
