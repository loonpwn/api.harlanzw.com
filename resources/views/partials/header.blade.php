<header data-headroom data-headroom data-offset="90">
    <nav class="navbar navbar-expand-lg navbar-light bg-light" >
        <div class="container">
            <a class="navbar-brand" href="/">HarlanZW</a>
            <nav class="nav-primary">
                @if (has_nav_menu('primary_navigation'))
                    {!! wp_nav_menu(['theme_location' => 'primary_navigation', 'menu_class' => 'nav']) !!}
                @endif
            </nav>
        </div>
    </nav>
</header>