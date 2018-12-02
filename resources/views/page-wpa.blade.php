@extends('layouts.app')

@section('content')

    @php
        do_action('acf-widget/render', ['include' => ['wysiwyg', 'call-to-action']]);
    @endphp

    <div class="container">

        @if(empty($plugin))
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
                <input name="action" value="plugin-search" type="hidden">


                <button type="submit" class="btn btn-primary">Find score</button>
            </form>
        @elseif(!isset($wpa_output['error']))

            <article class="card w-50 plugin-card">
                <div class="entry-thumbnail">
                    <a href="https://wordpress.org/plugins/{{ $plugin->slug }}/" rel="bookmark">
                        <style type="text/css">#plugin-icon-contact-form-7 { background-image: url('https://ps.w.org/{{ $plugin->slug }}/assets/icon-128x128.png?rev=984007'); }@media only screen and (-webkit-min-device-pixel-ratio: 1.5), only screen and (min-resolution: 144dpi) { #plugin-icon-contact-form-7 { background-image: url('https://ps.w.org/{{ $plugin->slug }}/assets/icon-256x256.png?rev=984007'); } }</style>
                        <div class="plugin-icon" id="plugin-icon-contact-form-7"></div>
                    </a>
                </div>
                <div class="card-header">
                    <h2 class="card-title">
                        <a href="https://wordpress.org/plugins/{{ $plugin->slug }}/" rel="bookmark">
                            {{ $plugin->name }}
                        </a>
                    </h2>
                </div>
                <div class="card-body">

                    <div class="plugin-rating">
                        <div class="wporg-ratings">
                            <span class="dashicons dashicons-star-filled" style="color:#ffb900"></span>
                            {{ round($plugin->rating / 20, 2) }}
                            <span class="rating-count">(<a href="https://wordpress.org/support/plugin/contact-form-7/reviews/">{{ $plugin->num_ratings }}<span class="screen-reader-text"> total ratings</span></a>)</span>
                        </div>

                    </div>

                    <div class="card-text">
                        <p>{!! $plugin->excerpt !!}</p>
                    </div>

                </div>
                <div class="text-muted card-footer">
                    <p class="plugin-author">
                        <i class="dashicons dashicons-admin-users"></i> {!! $plugin->author !!}
                    </p>
                    <span class="active-installs">
			<i class="dashicons dashicons-chart-area"></i>
                        {{ $plugin->active_installs }} active installations		</span>
                    <span class="tested-with">
				<i class="dashicons dashicons-wordpress-alt"></i>
				Tested with {{ $plugin->tested }}		</span>
                </div>
            </article>

            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a
                        ga-on="click"
                        ga-event-category="WPA"
                        ga-event-action="Tab - Keyword Rankings"
                        class="nav-link active" href="#keywords" role="tab" data-toggle="tab">Keyword Rankings</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link"
                       ga-on="click"
                       ga-event-category="WPA"
                       ga-event-action="Tab - Plugin Suggestions"
                       href="#plugin-suggestions" role="tab" data-toggle="tab">Plugin Suggestions</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link"
                       ga-on="click"
                       ga-event-category="WPA"
                       ga-event-action="Tab - Track Rank"
                       data-toggle="modal" data-target="#coming-soon"
                      >Track Rank</a>
                </li>
            </ul>

            <div class="tab-content">

                <div role="tabpanel" class="tab-pane fade active show" id="keywords">

                    <div class="card-body">

                        <p>
                            The keywords here are search terms users would put in to find your plugin. These are taken
                            by the specified plugin tags by default but you are able to change them below.
                        </p>
                        <p>Rank is the position that the plugin appears for any given search term.</p>

                        <div class="card-columns">
                            @foreach($terms as $keyword => $term)
                                <div class="card border-{{ $term['colour'] }}">
                                    <div class="card-header">
                                        <h2 class="card-title">
                                            {{ $keyword }}
                                        </h2>
                                        <p class="card-subtitle font-weight-light">{{ $term['score'] }}/{{ $term['max_score'] }}</p>

                                    </div>
                                    <div class="card-body">
                                        <p class="rank">Rank {{ $term['rank']['rank'] }}</p>
                                        <ul class="list-unstyled font-weight-light">
                                            @foreach($term['recommendations'] as $recommendation)
                                                <li>{{ $recommendation }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    <div class="card-footer">
                                        <p>{{ $term['rank']['total'] }} Plugins With Term</p>
                                        <div class="progress">
                                            <div class="progress-bar" role="progressbar" style="width: {{ $term['percentage'] }}%;" aria-valuenow="{{ $term['percentage'] }}" aria-valuemin="0" aria-valuemax="100">{{ $term['percentage'] }}%</div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <form class="form">

                            <div class="form-group">
                                <label for="search-terms">Search Terms</label>
                                <select class="form-control select2"
                                        id="search-terms"
                                        name="search-terms"
                                        multiple="multiple"
                                        data-tags="true"
                                >
                                    @foreach($terms as $keyword => $term)
                                        <option selected="selected">{{ $keyword }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <button type="button" data-toggle="modal" data-target="#coming-soon" class="btn btn-primary">Search</button>
                        </form>

                    </div>

                </div>

                <div role="tabpanel" class="tab-pane fade" id="plugin-suggestions">
                    <div class="card-body">

                        <ul>
                            @foreach($recommendations as $recommendation)
                                <li>{{ $recommendation }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            <a href="/wpa/" class="btn btn-primary">Back</a>

            <div class="modal" id="coming-soon">
                <div class="modal-dialog modal-sm" role="document">
                    <div class="modal-content">
                        <div class="modal-body">
                            <p class="text-black-secondary typography-subheading">Coming soon!</p>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-outline-info" data-dismiss="modal" type="button">Close</button>
                        </div>
                    </div>
                </div>
            </div>


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
