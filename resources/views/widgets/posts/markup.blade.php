<?php
// get an array of WP_Posts
$posts = [];
if ($type_filter === 'recent') {
    $posts = \App\get_latest_posts($limit);
} else if ($type_filter === 'choose') {
    foreach ($posts_to_show as $post_id) {
        $posts[] = is_int($post_id) ? WP_Post::get_instance($post_id) : $post_id;
    }
}
$columns = 12 / $posts_per_row;
global $post;
?>

<section class="section">
    <div class="container">
        <h2>{{ $title }}
            <small> // <a href="/blog/">View All</a></small>
        </h2>
        <div class="card-group">
            @foreach($posts as $post)
                <?php
                setup_postdata($post);
                ?>

                @include('partials.content')
            @endforeach
        </div>
    </div>
</section>
