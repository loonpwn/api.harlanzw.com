<section class="section section--padded">
    <div class="container">
        @if(!empty($title))
            <h2>{{ $title }}</h2>
        @endif
        <div class="owl-carousel owl-theme" data-items="5" data-loop="true" data-margin="15" data-autoplay="true">
            @foreach(BSMClients\Model\Clients::find_all() as $client)
                <div class="item">{!! wp_get_attachment_image(get_post_thumbnail_id($client->ID), 'medium_large') !!}</div>
            @endforeach
        </div>
    </div>
</section>