<!doctype html>
<html @php(language_attributes())>
@include('partials.head')
<body @php(body_class())>

<!-- Google Tag Manager (noscript) -->
<noscript>
    <iframe src="https://www.googletagmanager.com/ns.html?id=GTM-T573KVJ"
            height="0" width="0" style="display:none;visibility:hidden"></iframe>
</noscript>
<!-- End Google Tag Manager (noscript) -->

@php(do_action('get_header'))
@include('partials.header')


<div class="jumbotron jumbotron-fluid mb-xl px-lg-md text-white doc-jumbotron" id="doc_jumbotron"
     style="background-image: url(https://staging.bensanfordmedia.com/app/uploads/2017/10/BSanfordPunakaiki.jpg)">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-9 col-xl-6">
                <h1 class="typography-display-4">{{ get_the_title() }}</h1>
                <p class="font-weight-light typography-title">Lightweight, flexible component for showcasing hero unit
                    style content.</p>
            </div>
        </div>
    </div>
</div>


<div class="wrap container" role="document">
    <div class="content">
        <main class="main">
            @yield('content')
        </main>
        @if (App\display_sidebar())
            <aside class="sidebar">
                @include('partials.sidebar')
            </aside>
        @endif
    </div>
</div>
@php(do_action('get_footer'))
@include('partials.footer')
@php(wp_footer())
</body>
</html>
