@extends('layouts.app')

@section('content')


    <div class="container">
        <h1>Plugin Search Analytics</h1>

        <p>Instantly improve your ranking within WordPress plugin archive.</p>

        <div class="form-group">
            <label for="plugin-url">Plugin Slug</label>
            <input type="text" class="form-control" name="plugin-url" id="plugin-url" aria-describedby="plugin-url-help" placeholder="Enter Slug">
            <small id="plugin-url-help" class="form-text text-muted">This is the end of the URL for your plugin. Example https://wordpress.org/plugins/<strong>wordpress-seo</strong>/</small>
        </div>
        <div class="form-group">
            <label for="search-term">Search Term</label>
            <input type="password" class="form-control" id="search-term" aria-describedby="search-term-help" placeholder="Enter Search Term">
            <small id="search-term-help" class="form-text text-muted">What users are searching that should show your plugin.</small>
        </div>
        <input name="action" value="plugin-search" type="hidden">


        <button type="submit" class="btn btn-primary">Find score</button>

    </div>

@endsection
