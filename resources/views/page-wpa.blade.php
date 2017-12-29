@extends('layouts.app')

@section('content')


    <div class="container">
        <h1>Plugin SEO</h1>

        <p>Instantly improve your ranking within WordPress plugin archive. For information on how these formulas work checkout the article <a href="https://freemius.com/blog/seo-on-new-plugin-repository/" target="_blank">here</a>.</p>

        <?php
        global $wpa_output;
        ?>
        @if(empty($wpa_output))
            <form method="post">

                <div class="form-group">
                    <label class="form-control-label" for="plugin-url">Plugin Slug</label>
                    <input type="text" class="form-control" name="plugin-url" id="plugin-url" aria-describedby="plugin-url-help" placeholder="Enter Slug">
                    <small id="plugin-url-help" class="form-text text-muted">This is the end of the URL for your plugin. Example https://wordpress.org/plugins/<strong>wordpress-seo</strong>/</small>
                </div>
                <div class="form-group">
                    <label class="form-control-label" for="search-term">Search Term</label>
                    <input type="text" class="form-control" name="search-term" id="search-term" aria-describedby="search-term-help" placeholder="Enter Search Term">
                    <small id="search-term-help" class="form-text text-muted">What users are searching that should show your plugin.</small>
                </div>
                <input name="action" value="plugin-search" type="hidden">


                <button type="submit" class="btn btn-primary">Find score</button>
            </form>
        @else
            <p>Looking at the search score for <strong>{{  $_POST['plugin-url'] }}</strong> with search term <strong>{{  $_POST['search-term'] }}</strong>.</p>

            <h3>Current Score</h3>
            <p class="score">{{ $wpa_output['score'] }}</p>
            <h3>Potential Score</h3>
            <p class="score">{{ $wpa_output['max_score'] }}</p>
            <div class="card" style="margin-bottom: 1rem">
                <div class="card-body">
                    <h3 class="card-title">Recommendations</h3>
                    <ul>
                        {!! '<li>' . implode('</li><li>', $wpa_output['recommendations']) . '</li>' !!}
                    </ul>
                </div>
            </div>

            <a href="/wpa/" class="btn btn-link">Search Again</a>

            <h3>Log</h3>
            <code>
                {!! $wpa_output['log'] !!}
            </code>
        @endif

    </div>

@endsection
