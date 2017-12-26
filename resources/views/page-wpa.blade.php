@extends('layouts.app')

@section('content')


    <div class="container">
        <h1>Plugin Search Analytics</h1>

        <p>Instantly improve your ranking within WordPress plugin archive.</p>

        <form method="post">
            <h2>Find your current search score</h2>
            <label>
                Plugin URL
                <input name="plugin-url" required>
            </label>
            <label>
                Search term
                <input name="search-term" required>
            </label>
            <input name="action" value="plugin-search" type="hidden">
            <button type="submit">Find score</button>
        </form>
    </div>


@endsection
