<div class="container">
    <section class="section author">
        <a href="/about/" target="_blank" class="author__name">
            <div class="author__image">
                {!! wp_get_attachment_image(\App\get_option_page_value('author_image'), ['45', '45']) !!}
            </div>
            Harlan Wilton
        </a>
        <span class="author__read-time">
        {{ get_page_reading_time() }}
        </span>

    </section>
</div>
