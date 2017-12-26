<section class="section about-panel">
    <div class="container">
        <div class="about-panel_inner">
            <div class="about-panel_graphic lazyload" data-bg="{{ $image['url'] }}"></div>
            @foreach($columns as $column)
                <div class="about-panel_text about-panel_text-{{ count($columns) }}">
                    <h3>{{ $column['title'] }}</h3>
                    @if(!empty($column['sub-title']))
                        <h4>{{ $column['sub-title'] }}</h4>
                    @endif
                    {{ $column['content'] }}
                    <a class="find-more"
                       href="{{  $column['link'] }}" {{ ($column['link_type'] == 'new-tab' ? 'target="_blank"' : '') }} >{{ $column['link_text'] }}</a>
                </div>
            @endforeach
        </div>
    </div>
</section>
