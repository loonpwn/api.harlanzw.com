<div class="parallax-container" style="height: {{ $height }}px; position: relative; overflow: hidden">
    {!! wp_get_attachment_image($image, 'full', '', [
    'class' => 'parallax',
    'data-center' =>  $center,
     'data-intensity' => $intensity
    ])  !!}
</div>
