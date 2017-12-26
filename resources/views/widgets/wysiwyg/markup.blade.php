<section class="section">
    <div class="{{ $width === 'contained' ? 'container' : '' }}">
        <div class="wysiwyg-container clearfix {{ isset($wysiwyg_background_colour) ? $wysiwyg_background_colour : '' }}">
            {!! apply_filters('the_content', $wysiwyg_content) !!}
        </div>
    </div>
</section>
