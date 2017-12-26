<section class="section">
    <div class="container">
        @if(!empty($title))
            <h2>{{ $title }}</h2>
        @endif
        <div class="row">

            <?php
            $gallery_posts = get_posts([
                'post_type' => FOOGALLERY_CPT_GALLERY,
                'post_status' => ['publish', 'draft'],
                'cache_results' => false,
                'nopaging' => true,
                'orderby' => 'title',
                'order' => 'ASC'
            ]);
            $galleries = [];
            foreach ($gallery_posts as $post) {
                $galleries[] = FooGallery::get($post);
            }
            ?>

            @foreach($galleries as $post)
                <?php
                setup_postdata($post);
                ?>
                <div class="col-lg-4 col-md-6">
                    @include('partials.content-gallery')
                </div>
            @endforeach
        </div>
    </div>
</section>
