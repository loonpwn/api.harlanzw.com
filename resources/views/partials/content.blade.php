<div class="card">
    <a href="{{ get_the_permalink() }}">
    <img class="card-img lazyload" data-src="{!! get_the_post_thumbnail_url(get_the_ID(), 'medium_large') !!}" alt="{{ get_the_title() }}" style="height: 180px; width: 100%; display: block;">
    <div class="card-body">
        <h4 class="card-title">{{ get_the_title() }}</h4>
    </div>
</div>
