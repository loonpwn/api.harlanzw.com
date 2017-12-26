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

<main class="wrap content main" role="document">
    @yield('content')
</main>
@php(do_action('get_footer'))
@include('partials.footer')
@php(wp_footer())
</body>
</html>
