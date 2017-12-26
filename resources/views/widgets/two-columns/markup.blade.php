<section class="section">
    <div class="container">
        <div class="row">
            <div class="col-sm-5 wysiwyg-container clearfix">
                {!! apply_filters('the_content', $wysiwyg_left) !!}
            </div>
            <div class="col-sm-7 wysiwyg-container clearfix">
                {!! apply_filters('the_content', $wysiwyg_right) !!}
            </div>
        </div>
    </div>
</section>
