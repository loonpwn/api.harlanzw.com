<header data-headroom data-headroom data-offset="90">
    <nav class="navbar navbar-expand-lg" >
        <a class="navbar-brand" href="/">
            <img class="lazyload logo" src="{{ \App\asset_path('images/favicon.png') }}" alt="HarlanZW">
        </a>
        <nav class="nav-primary">
            @if (has_nav_menu('primary_navigation'))
                {!! wp_nav_menu(['theme_location' => 'primary_navigation', 'menu_class' => 'nav']) !!}
            @endif
        </nav>
    </nav>
</header>
