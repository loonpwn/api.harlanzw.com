@extends('layouts.app')

@section('content')
    {{-- Pages have no explicit content as it all comes from widgets --}}
    @php
        do_action('acf-widget/render');
    @endphp
@endsection
