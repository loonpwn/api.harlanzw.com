<div class="card">
    <a href="{{ get_the_permalink() }}">
        {!! wp_get_attachment_image(get_post_thumbnail_id(get_the_ID()),  'post-thumbnail', '', ['class' => 'card-img lazyload']) !!}
        <div class="card-body">
            <h4 class="card-title">{{ get_the_title() }}</h4>
        </div>
    </a>
</div>
